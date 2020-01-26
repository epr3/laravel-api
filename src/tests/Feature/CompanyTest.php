<?php


namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\WithUserTestCase;

class CompanyTest extends WithUserTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateCompany()
    {
        $company = factory(Company::class)->make(['user_id' => $this->user['id']]);

        $response = $this->actingAs($this->user, 'api')
            ->json('POST', '/api/companies', $company->attributesToArray(), ['Accept' => 'application/json']);

        $response->assertStatus(201)
            ->assertJson(['data' => [
                'id' => true,
                'name' => true
            ]]);
    }

    public function testUpdateCompany()
    {
        $company = factory(Company::class)->create(['user_id' => $this->user['id']]);

        $response = $this->actingAs($this->user, 'api')
            ->json('PUT', '/api/companies/' . $company['id'], $company->attributesToArray(), ['Accept' => 'application/json']);

        $response->assertStatus(200)
            ->assertJson(['data' => [
                'id' => true,
                'name' => true
            ]]);
    }

    public function testUpdateCompanyFail()
    {
        $users = factory(User::class, 2)->create(['role_id' => $this->role['id']]);
        $company = factory(Company::class)->create(['user_id' => $users[0]['id']]);

        $response = $this->actingAs($users[1], 'api')
            ->json('PUT', '/api/companies/' . $company['id'], $company->toArray(), ['Accept' => 'application/json']);

        $response->assertStatus(403);
    }
}
