<?php

namespace App\Http\Controllers;

use App\Models\VillaType;
use App\Services\BookingCreationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use RuntimeException;

class BookingController extends Controller
{
    /**
     * Menampilkan formulir pemesanan.
     */
    public function create(Request $request): View
    {
        $validated = $request->validate([
            'villa_type_id' => ['required', 'integer', 'exists:villa_types,id'],
            'check_in' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'check_out' => ['required', 'date_format:Y-m-d', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $villa = VillaType::query()
            ->where('is_active', true)
            ->with('branch')
            ->findOrFail($validated['villa_type_id']);

        if ((int) $validated['guests'] > $villa->max_capacity) {
            throw ValidationException::withMessages([
                'guests' => 'Jumlah tamu melebihi kapasitas villa.',
            ]);
        }

        return view('bookings.create', [
            'villa' => $villa,
            'checkIn' => $validated['check_in'],
            'checkOut' => $validated['check_out'],
            'guests' => (int) $validated['guests'],
        ]);
    }

    /**
     * Memproses pemesanan baru.
     */
    public function store(
        Request $request,
        BookingCreationService $bookingService
    ): RedirectResponse {
        $data = $request->validate([
            'villa_type_id' => [
                'required',
                'integer',
                'exists:villa_types,id',
            ],
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
            'guest_count' => [
                'required',
                'integer',
                'min:1',
                'max:20',
            ],
            'customer_name' => [
                'required',
                'string',
                'max:150',
            ],
            'customer_email' => [
                'required',
                'email',
                'max:150',
            ],
            'customer_phone' => [
                'required',
                'string',
                'max:30',
            ],
            'special_requests' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        try {
            $booking = $bookingService->create([
                'villa_type_id' => (int) $data['villa_type_id'],
                'check_in' => $data['check_in'],
                'check_out' => $data['check_out'],
                'guest_count' => (int) $data['guest_count'],
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'special_requests' => $data['special_requests'] ?? null,
                'source' => 'website',
            ]);
        } catch (InvalidArgumentException | RuntimeException $exception) {
            throw ValidationException::withMessages([
                'booking' => $exception->getMessage(),
            ]);
        }

        return redirect()->route('bookings.show', [
            'token' => $booking->public_token,
        ]);
    }


    /**
     * Menampilkan ringkasan booking berdasarkan token publik.
     */
    public function show(string $token): View
    {
        $booking = \App\Models\Booking::query()
            ->with([
                'branch',
                'bookingUnits.villaUnit.villaType',
                'bookingUnits.nights',
            ])
            ->where('public_token', $token)
            ->firstOrFail();

        return view('bookings.show', compact('booking'));
    }

}
