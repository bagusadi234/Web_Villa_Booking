<?php

namespace App\Services;

use App\Models\PricingRule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;
use RuntimeException;

class PricingService
{
    /**
     * Prioritas harga:
     * 1. Special date
     * 2. High season
     * 3. Weekend (Jumat dan Sabtu)
     * 4. Weekday
     */
    public function calculate(
        int $villaTypeId,
        string $checkIn,
        string $checkOut
    ): array {
        $startDate = Carbon::parse($checkIn)->startOfDay();
        $endDate = Carbon::parse($checkOut)->startOfDay();

        if ($startDate->greaterThanOrEqualTo($endDate)) {
            throw new InvalidArgumentException(
                'Tanggal check-out harus setelah tanggal check-in.'
            );
        }

        $rules = PricingRule::query()
            ->where('villa_type_id', $villaTypeId)
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        if ($rules->isEmpty()) {
            throw new RuntimeException(
                'Aturan harga untuk tipe villa ini belum tersedia.'
            );
        }

        $nights = [];
        $total = 0;

        for (
            $date = $startDate->copy();
            $date->lt($endDate);
            $date->addDay()
        ) {
            $rule = $this->findRuleForDate($rules, $date);

            if (!$rule) {
                throw new RuntimeException(
                    'Aturan harga belum tersedia untuk tanggal '
                    . $date->toDateString()
                );
            }

            $price = (int) $rule->price;

            if ($price <= 0) {
                throw new RuntimeException(
                    'Harga harus lebih besar dari nol untuk tanggal '
                    . $date->toDateString()
                );
            }

            $nights[] = [
                'stay_date' => $date->toDateString(),
                'nightly_price' => $price,
                'price_type' => $rule->rule_type,
            ];

            $total += $price;
        }

        return [
            'villa_type_id' => $villaTypeId,
            'check_in' => $startDate->toDateString(),
            'check_out' => $endDate->toDateString(),
            'total_nights' => count($nights),
            'nights' => $nights,
            'subtotal' => $total,
        ];
    }

    /**
     * Memilih aturan dengan prioritas tertinggi untuk satu malam.
     */
    private function findRuleForDate(
        Collection $rules,
        Carbon $date
    ): ?PricingRule {
        // Prioritas 1: Special date.
        $rule = $this->findDateRule(
            $rules,
            'special_date',
            $date
        );

        if ($rule) {
            return $rule;
        }

        // Prioritas 2: High season.
        $rule = $this->findDateRule(
            $rules,
            'high_season',
            $date
        );

        if ($rule) {
            return $rule;
        }

        // Prioritas 3: Weekend, yaitu Jumat dan Sabtu.
        if ($date->isFriday() || $date->isSaturday()) {
            return $this->findBaseRule($rules, 'weekend');
        }

        // Prioritas 4: Weekday.
        return $this->findBaseRule($rules, 'weekday');
    }

    /**
     * Mencari aturan tanggal yang mencakup malam menginap.
     *
     * Jika aturan dengan prioritas sama bertumpuk, pilih aturan
     * dengan start_date terbaru. Jika tanggal awal sama, gunakan
     * ID terbesar agar hasilnya konsisten.
     */
    private function findDateRule(
        Collection $rules,
        string $ruleType,
        Carbon $date
    ): ?PricingRule {
        return $rules
            ->filter(function (PricingRule $rule) use (
                $ruleType,
                $date
            ) {
                if (
                    $rule->rule_type !== $ruleType
                    || !$rule->start_date
                    || !$rule->end_date
                ) {
                    return false;
                }

                $start = Carbon::parse(
                    $rule->start_date
                )->startOfDay();

                $end = Carbon::parse(
                    $rule->end_date
                )->startOfDay();

                if ($end->lt($start)) {
                    throw new RuntimeException(
                        'Rentang tanggal aturan harga tidak valid '
                        . '(ID: ' . $rule->id . ').'
                    );
                }

                return $date->betweenIncluded($start, $end);
            })
            ->sort(function (
                PricingRule $a,
                PricingRule $b
            ) {
                $dateComparison = strcmp(
                    (string) $b->start_date,
                    (string) $a->start_date
                );

                return $dateComparison !== 0
                    ? $dateComparison
                    : $b->id <=> $a->id;
            })
            ->first();
    }

    /**
     * Mengambil aturan weekday/weekend.
     *
     * Jika terdapat aturan aktif ganda, hentikan perhitungan
     * daripada memilih harga secara sembarangan.
     */
    private function findBaseRule(
        Collection $rules,
        string $ruleType
    ): ?PricingRule {
        $matchingRules = $rules
            ->where('rule_type', $ruleType)
            ->filter(fn (PricingRule $rule) =>
                $rule->start_date === null
                && $rule->end_date === null
            )
            ->values();

        if ($matchingRules->count() > 1) {
            throw new RuntimeException(
                'Terdapat lebih dari satu aturan harga aktif untuk '
                . $ruleType . '. Periksa konfigurasi harga.'
            );
        }

        return $matchingRules->first();
    }
}
