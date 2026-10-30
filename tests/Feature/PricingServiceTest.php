<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\PricingRule;
use App\Models\VillaType;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class PricingServiceTest extends TestCase
{
    use RefreshDatabase;

    private PricingService $pricingService;
    private VillaType $villaType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pricingService = app(PricingService::class);

        $branch = Branch::create([
            'name' => 'Cabang Harga Test',
            'slug' => 'cabang-harga-test',
            'is_active' => true,
        ]);

        $this->villaType = VillaType::create([
            'branch_id' => $branch->id,
            'name' => 'Villa Harga Test',
            'slug' => 'villa-harga-test',
            'bedrooms' => 1,
            'bathrooms' => 1,
            'standard_capacity' => 2,
            'max_capacity' => 4,
            'is_active' => true,
        ]);
    }

    private function createRule(
        string $type,
        int $price,
        ?string $startDate = null,
        ?string $endDate = null
    ): PricingRule {
        return PricingRule::create([
            'villa_type_id' => $this->villaType->id,
            'rule_type' => $type,
            'price' => $price,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => true,
        ]);
    }

    public function test_special_date_overrides_high_season_and_weekend(): void
    {
        // Jumat, 6 November 2026.
        $this->createRule('weekday', 500000);
        $this->createRule('weekend', 650000);
        $this->createRule(
            'high_season',
            800000,
            '2026-11-06',
            '2026-11-06'
        );
        $this->createRule(
            'special_date',
            900000,
            '2026-11-06',
            '2026-11-06'
        );

        $result = $this->pricingService->calculate(
            $this->villaType->id,
            '2026-11-06',
            '2026-11-07'
        );

        $this->assertSame(900000, $result['subtotal']);
        $this->assertSame('special_date', $result['nights'][0]['price_type']);
    }

    public function test_high_season_overrides_weekend(): void
    {
        // Jumat, 6 November 2026.
        $this->createRule('weekday', 500000);
        $this->createRule('weekend', 650000);
        $this->createRule(
            'high_season',
            800000,
            '2026-11-06',
            '2026-11-06'
        );

        $result = $this->pricingService->calculate(
            $this->villaType->id,
            '2026-11-06',
            '2026-11-07'
        );

        $this->assertSame(800000, $result['subtotal']);
        $this->assertSame('high_season', $result['nights'][0]['price_type']);
    }

    public function test_weekend_rule_is_used_on_friday(): void
    {
        $this->createRule('weekday', 500000);
        $this->createRule('weekend', 650000);

        $result = $this->pricingService->calculate(
            $this->villaType->id,
            '2026-11-06',
            '2026-11-07'
        );

        $this->assertSame(650000, $result['subtotal']);
        $this->assertSame('weekend', $result['nights'][0]['price_type']);
    }

    public function test_weekday_rule_is_used_as_fallback(): void
    {
        $this->createRule('weekday', 500000);

        $result = $this->pricingService->calculate(
            $this->villaType->id,
            '2026-11-02',
            '2026-11-03'
        );

        $this->assertSame(500000, $result['subtotal']);
        $this->assertSame('weekday', $result['nights'][0]['price_type']);
    }



    public function test_missing_price_rule_throws_exception(): void
    {
        // Ada aturan weekday, tetapi tidak berlaku pada 2 November.
        $this->createRule(
            'weekday',
            500000,
            '2026-11-03',
            '2026-11-30'
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Aturan harga belum tersedia untuk tanggal 2026-11-02'
        );

        $this->pricingService->calculate(
            $this->villaType->id,
            '2026-11-02',
            '2026-11-03'
        );
    }
    

    public function test_duplicate_active_weekday_rules_throw_exception(): void
    {
        $this->createRule('weekday', 500000);
        $this->createRule('weekday', 550000);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Terdapat lebih dari satu aturan harga aktif untuk weekday.'
        );

        $this->pricingService->calculate(
            $this->villaType->id,
            '2026-11-02',
            '2026-11-03'
        );
    }
}
