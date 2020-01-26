<?php

namespace Tests;

use App\Models\User;
use App\Models\Role;

class WithUserTestCase extends TestCase
{
    public $user;
    public $role;

    public function setUp(): void
    {
        parent::setUp();

        $this->role = factory(Role::class)->create();
        $this->user = factory(User::class)->create(['password' => bcrypt('12345678'), 'role_id' => $this->role['id']]);
    }
}
