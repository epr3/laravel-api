<?php


namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\WithUserTestCase;

class TicketTest extends WithUserTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateTicket()
    {
        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $ticket = factory(Ticket::class)->make(['event_id' => $event['id']]);

        $response = $this->actingAs($this->user, 'api')
            ->json('POST', '/api/events/' . $event['id'] . '/tickets', $ticket->attributesToArray(), ['Accept' => 'application/json']);

        $response->assertStatus(201)
            ->assertJson(['data' => [
                'id' => true,
                'type' => true
            ]]);
    }

    public function testGetTicket()
    {
        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);

        $response = $this->actingAs($this->user, 'api')
            ->json('GET', '/api/events/' . $event['id'] . '/tickets/' . $ticket['id'], ['Accept' => 'application/json']);

        $response->assertStatus(200)
            ->assertJson(['data' => [
                'id' => true,
                'type' => true
            ]]);
    }

    public function testGetTicketFail()
    {
        $users = factory(User::class, 2)->create(['role_id' => $this->role['id']]);
        $event = factory(Event::class)->create(['user_id' => $users[0]['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);

        $response = $this->actingAs($users[1], 'api')
            ->json('GET', '/api/events/' . $event['id'] . '/tickets/' . $ticket['id'], ['Accept' => 'application/json']);

        $response->assertStatus(403);
    }

    public function testGetTickets()
    {
        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        factory(Ticket::class, 5)->create(['event_id' => $event['id']]);

        $response = $this->actingAs($this->user, 'api')
            ->json('GET', '/api/events/' . $event['id'] . '/tickets', ['Accept' => 'application/json']);

        $response->assertStatus(200)
            ->assertJson(['data' => true]);
    }

    public function testUpdateTicket()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);

        $response = $this->actingAs($this->user, 'api')
            ->json('PUT', '/api/events/' . $event['id'] . '/tickets/' . $ticket['id'], $ticket->attributesToArray(), ['Accept' => 'application/json']);

        $response->assertStatus(200)
            ->assertJson(['data' => [
                'id' => true,
                'type' => true
            ]]);
    }

    public function testUpdateTicketFail()
    {
        $users = factory(User::class, 2)->create(['role_id' => $this->role['id']]);
        $event = factory(Event::class)->create(['user_id' => $users[0]['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);

        $response = $this->actingAs($users[1], 'api')
            ->json('PUT', '/api/events/' . $event['id'] . '/tickets/' . $ticket['id'], $ticket->toArray(), ['Accept' => 'application/json']);

        $response->assertStatus(403);
    }

    public function testDeleteTicket()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);

        $response = $this->actingAs($this->user, 'api')
            ->json('DELETE', '/api/events/' . $event['id'] . '/tickets/' . $ticket['id'], ['Accept' => 'application/json']);

        $response->assertStatus(204);
    }

    public function testDeleteTicketFail()
    {
        $users = factory(User::class, 2)->create(['role_id' => $this->role['id']]);
        $event = factory(Event::class)->create(['user_id' => $users[0]['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);

        $response = $this->actingAs($users[1], 'api')
            ->json('DELETE', '/api/events/' . $event['id'] . '/tickets/' . $ticket['id'], ['Accept' => 'application/json']);

        $response->assertStatus(403);
    }
}
