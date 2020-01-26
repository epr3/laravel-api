<?php


namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testVerify()
    {
        $role = factory(Role::class)->create();
        $user = factory(User::class)->create(['password' => bcrypt('12345678'), 'role_id' => $role['id'], 'email_verified_at' => null]);

        Event::fake();

        $response = $this->actingAs($user, 'api')
            ->json('GET', '/api/auth/email/verify/' . $user->getKey() . '/' . sha1($user->getEmailForVerification()), ['Accept' => 'application/json']);

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);

        Event::assertDispatched(Verified::class);
    }

    public function testResend()
    {
        Notification::fake();

        Notification::assertNothingSent();

        $role = factory(Role::class)->create();
        $user = factory(User::class)->create(['password' => bcrypt('12345678'), 'role_id' => $role['id'], 'email_verified_at' => null]);

        $response = $this->actingAs($user, 'api')
            ->json('GET', '/api/auth/email/resend', ['Accept' => 'application/json']);

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);

        Notification::assertSentTo(
            [User::latest()->first()],
            VerifyEmail::class
        );
    }
}
