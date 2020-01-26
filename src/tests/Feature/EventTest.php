<?php


namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\WithUserTestCase;

class EventTest extends WithUserTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateEvent()
    {
        $event = factory(Event::class)->make(['user_id' => $this->user['id']]);

        $response = $this->actingAs($this->user, 'api')
            ->json('POST', '/api/events', $event->attributesToArray(), ['Accept' => 'application/json']);

        $response->assertStatus(201)
            ->assertJson(['data' => [
                'id' => true,
                'name' => true
            ]]);
    }

    public function testGetEvent()
    {
        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);

        $response = $this->actingAs($this->user, 'api')
            ->json('GET', '/api/events/' . $event['id'], ['Accept' => 'application/json']);

        $response->assertStatus(200)
            ->assertJson(['data' => [
                'id' => true,
                'name' => true
            ]]);
    }

    public function testGetEventFail()
    {
        $users = factory(User::class, 2)->create(['role_id' => $this->role['id']]);
        $event = factory(Event::class)->create(['user_id' => $users[0]['id']]);

        $response = $this->actingAs($users[1], 'api')
            ->json('GET', '/api/events/' . $event['id'], ['Accept' => 'application/json']);

        $response->assertStatus(403);
    }

    public function testGetEvents()
    {
        factory(Event::class, 5)->create(['user_id' => $this->user['id']]);

        $response = $this->actingAs($this->user, 'api')->json('GET', '/api/events', ['Accept' => 'application/json']);

        $response->assertStatus(200)
            ->assertJson(['data' => true]);
    }

    public function testUpdateEvent()
    {
        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);

        $response = $this->actingAs($this->user, 'api')->json('PUT', '/api/events/' . $event['id'], $event->attributesToArray(), ['Accept' => 'application/json']);

        $response->assertStatus(200)
            ->assertJson(['data' => [
                'id' => true,
                'name' => true
            ]]);
    }

    public function testUpdateEventFail()
    {
        $users = factory(User::class, 2)->create(['role_id' => $this->role['id']]);
        $event = factory(Event::class)->create(['user_id' => $users[0]['id']]);

        $response = $this->actingAs($users[1], 'api')
            ->json('PUT', '/api/events/' . $event['id'], $event->toArray(), ['Accept' => 'application/json']);

        $response->assertStatus(403);
    }

    public function testDeleteEvent()
    {
        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);

        $response = $this->actingAs($this->user, 'api')
            ->json('DELETE', '/api/events/' . $event['id'], ['Accept' => 'application/json']);

        $response->assertStatus(204);
    }

    public function testDeleteEventFail()
    {
        $users = factory(User::class, 2)->create(['role_id' => $this->role['id']]);
        $event = factory(Event::class)->create(['user_id' => $users[0]['id']]);

        $response = $this->actingAs($users[1], 'api')
            ->json('DELETE', '/api/events/' . $event['id'], ['Accept' => 'application/json']);

        $response->assertStatus(403);
    }
}
