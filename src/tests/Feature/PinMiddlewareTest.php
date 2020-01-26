<?php


namespace Tests\Feature;

use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\WithUserTestCase;

class PinMiddlewareTest extends WithUserTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testGetWithPin()
    {
        $company = factory(Company::class)->create(['user_id' => $this->user['id']]);

        $response = $this->json('GET', '/scan?pin=' . $company['pin'], ['Accept' => 'application/json']);

        $response->assertStatus(200);
    }
}
