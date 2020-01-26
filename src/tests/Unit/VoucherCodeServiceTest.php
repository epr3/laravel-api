<?php


namespace Tests\Unit;

use App\Models\User;
use App\Models\Event;
use App\Models\Voucher;
use App\Models\VoucherCode;
use App\Services\VoucherCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\WithUserTestCase;

class VoucherCodeServiceTest extends WithUserTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateVoucherCodesBatch()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);

        $voucher = factory(Voucher::class)->create(['event_id' => $event['id']]);

        $this->be($this->user);

        $voucherCodeService = app(VoucherCodeService::class);

        $testVoucherCodes = $voucherCodeService->createVoucherCodeBatch($voucher['id'], 50);

        $this->assertCount(50, $testVoucherCodes);

        $this->assertDatabaseHas('voucher_codes', [
            'voucher_id' => $voucher['id']
        ]);
    }

    public function testCreateVoucherCodesBatchReturnsCount()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);

        $voucher = factory(Voucher::class)->create(['event_id' => $event['id']]);
        factory(VoucherCode::class, 30)->create(['voucher_id' => $voucher['id']]);

        $this->be($this->user);

        $voucherCodeService = app(VoucherCodeService::class);

        $testVoucherCodes = $voucherCodeService->createVoucherCodeBatch($voucher['id'], 50);

        $this->assertCount(50, $testVoucherCodes);
    }
}
