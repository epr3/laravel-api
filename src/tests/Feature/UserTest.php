<?php


namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\WithUserTestCase;

class UserTest extends WithUserTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testProfile()
    {
        $response = $this->actingAs($this->user, 'api')->json('GET', '/api/profile');

        $response->assertStatus(200)
            ->assertJson(['data' => true]);
    }
}
