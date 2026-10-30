<?php

namespace App\Http\Controllers;

use App\Models\VillaType;
use App\Services\AvailabilityService;
use App\Services\PricingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use RuntimeException;

class VillaController extends Controller
{
    /**
     * Menampilkan daftar villa dan hasil pencarian.
     */
    public function index(
        Request $request,
        AvailabilityService $availabilityService,
        PricingService $pricingService
    ): View {
        $searching = $request->has('check_in')
            || $request->has('check_out')
            || $request->has('guests');

        $validated = [];

        if ($searching) {
            $validated = $request->validate([
                'check_in' => [
                    'required',
                    'date_format:Y-m-d',
                    'after_or_equal:today',
                ],
                'check_out' => [
                    'required',
                    'date_format:Y-m-d',
                    'after:check_in',
                ],
                'guests' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:20',
                ],
            ], [
                'check_in.required' => 'Tanggal check-in wajib dipilih.',
                'check_in.after_or_equal' => 'Check-in tidak boleh di masa lalu.',
                'check_out.required' => 'Tanggal check-out wajib dipilih.',
                'check_out.after' => 'Check-out harus setelah check-in.',
                'guests.required' => 'Jumlah tamu wajib dipilih.',
                'guests.min' => 'Jumlah tamu minimal 1 orang.',
                'guests.max' => 'Jumlah tamu maksimal 20 orang.',
            ]);
        }

        $villas = VillaType::query()
            ->where('is_active', true)
            ->with([
                'branch',
                'images' => function ($query) {
                    $query->orderByDesc('is_primary')
                        ->orderBy('sort_order');
                },
                'pricingRules' => function ($query) {
                    $query->where('is_active', true);
                },
            ])
            ->latest()
            ->get();

        $searchResults = collect();
        $searchError = null;

        if ($searching && !empty($validated)) {
            $checkIn = $validated['check_in'];
            $checkOut = $validated['check_out'];
            $guests = (int) $validated['guests'];

            foreach ($villas as $villa) {
                if ($villa->max_capacity < $guests) {
                    continue;
                }

                try {
                    $availableUnits = $availabilityService->availableUnits(
                        $villa->id,
                        $checkIn,
                        $checkOut
                    );

                    if ($availableUnits->isEmpty()) {
                        continue;
                    }

                    $pricing = $pricingService->calculate(
                        $villa->id,
                        $checkIn,
                        $checkOut
                    );

                    $searchResults->push([
                        'villa' => $villa,
                        'available_units' => $availableUnits,
                        'available_count' => $availableUnits->count(),
                        'pricing' => $pricing,
                    ]);
                } catch (InvalidArgumentException | RuntimeException $exception) {
                    report($exception);

                    $searchError = 'Sebagian harga belum dapat dihitung. '
                        . 'Silakan coba tanggal lain atau hubungi pengelola.';
                }
            }
        }

        return view('villas.index', compact(
            'villas',
            'searching',
            'validated',
            'searchResults',
            'searchError'
        ));
    }

    /**
     * Menampilkan detail satu tipe villa.
     */
    public function show(
        Request $request,
        string $slug,
        AvailabilityService $availabilityService,
        PricingService $pricingService
    ): View {
        $villa = VillaType::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'branch',
                'images' => function ($query) {
                    $query->orderByDesc('is_primary')
                        ->orderBy('sort_order');
                },
                'facilities',
            ])
            ->firstOrFail();

        $searching = $request->has('check_in')
            || $request->has('check_out')
            || $request->has('guests');

        $validated = [];
        $availableCount = null;
        $pricing = null;
        $searchError = null;

        if ($searching) {
            $validated = $request->validate([
                'check_in' => [
                    'required',
                    'date_format:Y-m-d',
                    'after_or_equal:today',
                ],
                'check_out' => [
                    'required',
                    'date_format:Y-m-d',
                    'after:check_in',
                ],
                'guests' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:20',
                ],
            ], [
                'check_in.required' => 'Tanggal check-in wajib dipilih.',
                'check_in.after_or_equal' => 'Check-in tidak boleh di masa lalu.',
                'check_out.required' => 'Tanggal check-out wajib dipilih.',
                'check_out.after' => 'Check-out harus setelah check-in.',
                'guests.required' => 'Jumlah tamu wajib dipilih.',
                'guests.min' => 'Jumlah tamu minimal 1 orang.',
                'guests.max' => 'Jumlah tamu maksimal 20 orang.',
            ]);

            if ((int) $validated['guests'] > $villa->max_capacity) {
                throw ValidationException::withMessages([
                    'guests' => 'Jumlah tamu melebihi kapasitas villa.',
                ]);
            }

            try {
                $availableUnits = $availabilityService->availableUnits(
                    $villa->id,
                    $validated['check_in'],
                    $validated['check_out']
                );

                $availableCount = $availableUnits->count();

                $pricing = $pricingService->calculate(
                    $villa->id,
                    $validated['check_in'],
                    $validated['check_out']
                );
            } catch (InvalidArgumentException | RuntimeException $exception) {
                report($exception);

                $searchError = 'Harga atau ketersediaan belum dapat '
                    . 'diperiksa. Silakan coba tanggal lain atau hubungi '
                    . 'pengelola villa.';
            }
        }

        return view('villas.show', compact(
            'villa',
            'searching',
            'validated',
            'availableCount',
            'pricing',
            'searchError'
        ));
    }
}
