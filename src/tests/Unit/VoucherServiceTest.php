<?php


namespace Tests\Unit;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Event;
use App\Models\Voucher;
use App\Services\VoucherService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\WithUserTestCase;

class VoucherServiceTest extends WithUserTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateVoucher()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);
        $voucher = factory(Voucher::class)->make(['event_id' => $event['id']]);

        $this->be($this->user);

        $voucherService = app(VoucherService::class);

        $testVoucher = $voucherService->createVoucher($voucher->attributesToArray(), [$ticket['id']]);

        $this->assertDatabaseHas('vouchers', [
            'id' => $testVoucher['id']
        ]);
    }

    public function testGetVoucher()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $voucher = factory(Voucher::class)->create(['event_id' => $event['id']]);

        $this->be($this->user);

        $voucherService = app(VoucherService::class);

        $testVoucher = $voucherService->getVoucher($voucher['id']);

        $this->assertDatabaseHas('vouchers', [
            'id' => $testVoucher['id']
        ]);
    }

    public function testGetVouchers()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        factory(Voucher::class, 5)->create(['event_id' => $event['id']]);

        $this->be($this->user);

        $voucherService = app(VoucherService::class);

        $testVouchers = $voucherService->getVouchers(['event_id' => $event['id']]);

        $this->assertCount(5, $testVouchers);
    }

    public function testGetVoucherFail()
    {

        $this->expectException(ModelNotFoundException::class);

        $this->be($this->user);

        $voucherService = app(VoucherService::class);

        $voucherService->getVoucher('20');
    }

    public function testDeleteVoucherFail()
    {

        $this->expectException(ModelNotFoundException::class);

        $this->be($this->user);

        $voucherService = app(VoucherService::class);

        $voucherService->deleteVoucher('20');
    }

    public function testUpdateVoucherFail()
    {

        $this->expectException(ModelNotFoundException::class);

        $this->be($this->user);

        $voucherService = app(VoucherService::class);

        $voucherService->updateVoucher('20', ['name' => 'randomName'], []);
    }

    public function testDeleteVoucher()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $voucher = factory(Voucher::class)->create(['event_id' => $event['id']]);

        $this->be($this->user);

        $voucherService = app(VoucherService::class);

        $testVoucher = $voucherService->deleteVoucher($voucher['id']);

        $this->assertDatabaseMissing('vouchers', [
            'id' => $testVoucher['id']
        ]);
    }

    public function testUpdateVoucher()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $voucher = factory(Voucher::class)->create(['event_id' => $event['id']]);

        $this->be($this->user);

        $voucherService = app(VoucherService::class);

        $testVoucher = $voucherService->updateVoucher($voucher['id'], ['name' => 'randomName'], []);

        $this->assertDatabaseHas('vouchers', [
            'id' => $testVoucher['id'],
            'name' => $testVoucher['name']
        ]);
    }
}
