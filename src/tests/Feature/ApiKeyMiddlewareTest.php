<?php


namespace Tests\Feature;

use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\WithUserTestCase;

class ApiKeyMiddlewareTest extends WithUserTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testGetWithApiKey()
    {
        $company = factory(Company::class)->create(['user_id' => $this->user['id']]);

        $response = $this->json('GET', '/key?api_key=' . $company['api_key'], ['Accept' => 'application/json']);

        $response->assertStatus(200);
    }
}
