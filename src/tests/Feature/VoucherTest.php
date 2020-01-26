<?php


namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\WithUserTestCase;

class VoucherTest extends WithUserTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateVoucher()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $voucher = factory(Voucher::class)->make(['event_id' => $event['id']]);

        $response = $this->actingAs($this->user, 'api')
            ->json(
                'POST',
                '/api/events/' . $event['id'] . '/vouchers',
                [
                    'name' => $voucher['name'],
                    'discount_type' => $voucher['discount_type'],
                    'discount_amount' => $voucher['discount_amount'],
                    'tickets' => []
                ],
                ['Accept' => 'application/json']
            );

        $response->assertStatus(201)
            ->assertJson(['data' => [
                'id' => true,
                'name' => true
            ]]);
    }

    public function testGetVoucher()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $voucher = factory(Voucher::class)->create(['event_id' => $event['id']]);

        $response = $this->actingAs($this->user, 'api')
            ->json('GET', '/api/events/' . $event['id'] . '/vouchers/' . $voucher['id'], ['Accept' => 'application/json']);

        $response->assertStatus(200)
            ->assertJson(['data' => [
                'id' => true,
                'name' => true
            ]]);
    }

    public function testGetVoucherFail()
    {
        $users = factory(User::class, 2)->create(['role_id' => $this->role['id']]);
        $event = factory(Event::class)->create(['user_id' => $users[0]['id']]);
        $voucher = factory(Voucher::class)->create(['event_id' => $event['id']]);

        $response = $this->actingAs($users[1], 'api')
            ->json('GET', '/api/events/' . $event['id'] . '/vouchers/' . $voucher['id'], ['Accept' => 'application/json']);

        $response->assertStatus(403);
    }

    public function testGetVouchers()
    {
        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        factory(Voucher::class, 5)->create(['event_id' => $event['id']]);

        $response = $this->actingAs($this->user, 'api')
            ->json('GET', '/api/events/' . $event['id'] . '/vouchers', ['Accept' => 'application/json']);

        $response->assertStatus(200)
            ->assertJson(['data' => true]);
    }

    public function testUpdateVoucher()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $voucher = factory(Voucher::class)->create(['event_id' => $event['id']]);

        $response = $this->actingAs($this->user, 'api')
            ->json(
                'PUT',
                '/api/events/' . $event['id'] . '/vouchers/' . $voucher['id'],
                [
                    'name' => $voucher['name'],
                    'discount_type' => $voucher['discount_type'],
                    'discount_amount' => $voucher['discount_amount'],
                    'tickets' => []
                ],
                ['Accept' => 'application/json']
            );

        $response->assertStatus(200)
            ->assertJson(['data' => [
                'id' => true,
                'name' => true
            ]]);
    }

    public function testUpdateVoucherFail()
    {
        $users = factory(User::class, 2)->create(['role_id' => $this->role['id']]);
        $event = factory(Event::class)->create(['user_id' => $users[0]['id']]);
        $voucher = factory(Voucher::class)->create(['event_id' => $event['id']]);

        $response = $this->actingAs($users[1], 'api')
            ->json(
                'PUT',
                '/api/events/' . $event['id'] . '/vouchers/' . $voucher['id'],
                [
                    'name' => $voucher['name'],
                    'discount_type' => $voucher['discount_type'],
                    'discount_amount' => $voucher['discount_amount'],
                    'tickets' => []
                ],
                ['Accept' => 'application/json']
            );

        $response->assertStatus(403);
    }

    public function testDeleteVoucher()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $voucher = factory(Voucher::class)->create(['event_id' => $event['id']]);

        $response = $this->actingAs($this->user, 'api')
            ->json('DELETE', '/api/events/' . $event['id'] . '/vouchers/' . $voucher['id'], ['Accept' => 'application/json']);

        $response->assertStatus(204);
    }

    public function testDeleteVoucherFail()
    {
        $users = factory(User::class, 2)->create(['role_id' => $this->role['id']]);
        $event = factory(Event::class)->create(['user_id' => $users[0]['id']]);
        $voucher = factory(Voucher::class)->create(['event_id' => $event['id']]);

        $response = $this->actingAs($users[1], 'api')
            ->json('DELETE', '/api/events/' . $event['id'] . '/vouchers/' . $voucher['id'], ['Accept' => 'application/json']);

        $response->assertStatus(403);
    }
}
