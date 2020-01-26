<?php


namespace Tests\Feature;

use App\Models\Role;
use App\Models\Token;
use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Http\Middleware\EncryptCookies;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testLogin()
    {
        $role = factory(Role::class)->create();
        $user = factory(User::class)->create(['password' => bcrypt('12345678'), 'role_id' => $role['id']]);

        $body = [
            'email' => $user['email'],
            'password' =>  '12345678'
        ];

        $response = $this->json('POST', '/api/auth/login', $body);

        $response->assertStatus(200)
            ->assertCookie('refresh_token')
            ->assertJsonStructure(['access_token']);
    }

    public function testLoginFail()
    {
        $role = factory(Role::class)->create();
        $user = factory(User::class)->create(['password' => bcrypt('12345678'), 'role_id' => $role['id']]);

        $body = [
            'email' => $user['email'],
            'password' =>  '1234567'
        ];

        $response = $this->json('POST', '/api/auth/login', $body);

        $response->assertStatus(403)
            ->assertJsonStructure(['code', 'type', 'message']);
    }

    public function testRegister()
    {
        factory(Role::class)->create();
        $user = factory(User::class)->make(['password' => bcrypt('12345678')]);

        $body = [
            'email' => $user['email'],
            'name' => $user['name'],
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'surname' => $user['surname']
        ];

        Notification::fake();

        Notification::assertNothingSent();

        $response = $this->json('POST', '/api/auth/register', $body);

        $response->assertStatus(201)
            ->assertCookie('refresh_token')
            ->assertJsonStructure(['access_token']);

        Notification::assertSentTo(
            [User::latest()->first()],
            VerifyEmail::class
        );
    }

    public function testRegisterValidationFail()
    {
        $user = factory(User::class)->make(['password' => bcrypt('12345678')]);

        $body = [
            'email' => $user['email'],
            'name' => $user['name'],
            'password' => '12345678',
            'password_confirmation' => '1234567',
            'surname' => $user['surname']
        ];

        $response = $this->json('POST', '/api/auth/register', $body);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'code', 'type', 'errors']);
    }

    public function testRefresh()
    {
        $this->disableCookiesEncryption('refresh_token');

        $role = factory(Role::class)->create();
        $user = factory(User::class)->create(['password' => bcrypt('12345678'), 'role_id' => $role['id']]);
        $token = factory(Token::class)->create(['user_id' => $user['id']]);

        $response = $this
            ->call('GET', '/api/auth/refresh', [], ['refresh_token' => $token['token']]);

        $response->assertStatus(200)
            ->assertCookie('refresh_token')
            ->assertJsonStructure(['access_token']);
    }

    public function testRefreshFail()
    {
        $role = factory(Role::class)->create();
        $user = factory(User::class)->create(['password' => bcrypt('12345678'), 'role_id' => $role['id']]);

        $response = $this->actingAs($user, 'api')
            ->json('GET', '/api/auth/refresh');

        $response->assertStatus(403)
            ->assertJsonStructure(['message', 'code', 'type']);
    }

    public function testLogout()
    {
        $role = factory(Role::class)->create();
        $user = factory(User::class)->create(['password' => bcrypt('12345678'), 'role_id' => $role['id']]);
        $token = factory(Token::class)->create(['user_id' => $user['id']]);

        $this->disableCookiesEncryption('refresh_token');

        $response = $this
            ->call('DELETE', '/api/auth/logout', [], ['refresh_token' => $token['token']]);

        $response->assertStatus(204);
    }

    public function testLogoutFail()
    {
        $response = $this->json('DELETE', '/api/auth/logout');

        $response->assertStatus(403);
    }

    private function disableCookiesEncryption($cookies)
    {
        $this->app->resolving(
            EncryptCookies::class,
            function ($object) use ($cookies) {
                $object->disableFor($cookies);
            }
        );

        return $this;
    }
}
