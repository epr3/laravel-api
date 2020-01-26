<?php


namespace Tests\Unit;

use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\WithUserTestCase;

class CompanyServiceTest extends WithUserTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateCompany()
    {

        $company = factory(Company::class)->make(['user_id' => $this->user['id']]);
        $companyService = app(CompanyService::class);

        $this->be($this->user);

        $testCompany = $companyService->createCompany($company->attributesToArray());

        $this->assertDatabaseHas('companies', [
            'id' => $testCompany['id']
        ]);
    }

    public function testGetCompany()
    {

        $company = factory(Company::class)->create(['user_id' => $this->user['id']]);
        $companyService = app(CompanyService::class);

        $this->be($this->user);

        $testCompany = $companyService->getCompany($company['id']);

        $this->assertDatabaseHas('companies', [
            'id' => $testCompany['id']
        ]);
    }

    public function testGetCompanyFail()
    {

        $this->expectException(ModelNotFoundException::class);

        $this->be($this->user);

        $companyService = app(CompanyService::class);

        $companyService->getCompany('20');
    }

    public function testUpdateCompanyFail()
    {

        $this->expectException(ModelNotFoundException::class);

        $this->be($this->user);

        $companyService = app(CompanyService::class);

        $companyService->updateCompany('20', ['name' => 'randomName']);
    }

    public function testUpdateCompany()
    {

        $company = factory(Company::class)->create(['user_id' => $this->user['id']]);
        $companyService = app(CompanyService::class);

        $this->be($this->user);

        $testCompany = $companyService->updateCompany($company['id'], ['name' => 'randomName']);

        $this->assertDatabaseHas('companies', [
            'id' => $testCompany['id'],
            'name' => $testCompany['name']
        ]);
    }
}
