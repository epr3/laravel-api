<?php


namespace Tests\Feature;

use App\Mail\BookingCreated;
use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\WithUserTestCase;

class BookingTest extends WithUserTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateBooking()
    {

        Storage::fake('qr_codes');

        Mail::fake();

        Mail::assertNothingSent();

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);
        $booking = factory(Booking::class)->make([
            'ticket_id' => $ticket['id'],
            'event_id' => $event['id']
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->json('POST', '/api/events/' . $event['id'] . '/bookings', $booking->attributesToArray(), ['Accept' => 'application/json']);

        $response->assertStatus(201)
            ->assertJson(['data' => [
                'id' => true,
                'name' => true
            ]]);

        Mail::assertQueued(BookingCreated::class, function ($mail) use ($booking) {
            return $mail->booking->email === $booking->email;
        });
    }

    public function testGetBooking()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);
        $booking = factory(Booking::class)->create([
            'ticket_id' => $ticket['id'],
            'event_id' => $event['id']
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->json('GET', '/api/events/' . $event['id'] . '/bookings/' . $booking['id'], ['Accept' => 'application/json']);

        $response->assertStatus(200)
            ->assertJson(['data' => [
                'id' => true,
                'name' => true
            ]]);
    }

    public function testGetBookingFail()
    {
        $users = factory(User::class, 2)->create(['role_id' => $this->role['id']]);
        $event = factory(Event::class)->create(['user_id' => $users[0]['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);
        $booking = factory(Booking::class)->create([
            'ticket_id' => $ticket['id'],
            'event_id' => $event['id']
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->json('GET', '/api/events/' . $event['id'] . '/bookings/' . $booking['id'], ['Accept' => 'application/json']);

        $response->assertStatus(403);
    }

    public function testGetBookings()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);
        factory(Booking::class, 5)->create([
            'ticket_id' => $ticket['id'],
            'event_id' => $event['id']
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->json('GET', '/api/events/' . $event['id'] . '/bookings', ['Accept' => 'application/json']);

        $response->assertStatus(200)
            ->assertJson(['data' => true]);
    }

    public function testUpdateBooking()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);
        $booking = factory(Booking::class)->create([
            'ticket_id' => $ticket['id'],
            'event_id' => $event['id']
        ]);

        $response = $this->actingAs($this->user, 'api')->json('PUT', '/api/events/' . $event['id'] . '/bookings/' . $booking['id'], $booking->attributesToArray(), ['Accept' => 'application/json']);

        $response->assertStatus(200)
            ->assertJson(['data' => [
                'id' => true,
                'name' => true
            ]]);
    }

    public function testUpdateBookingFail()
    {
        $users = factory(User::class, 2)->create(['role_id' => $this->role['id']]);
        $event = factory(Event::class)->create(['user_id' => $users[0]['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);
        $booking = factory(Booking::class)->create([
            'ticket_id' => $ticket['id'],
            'event_id' => $event['id']
        ]);

        $response = $this->actingAs($users[1], 'api')
            ->json('PUT', '/api/events/' . $event['id'] . '/bookings/' . $booking['id'], $booking->toArray(), ['Accept' => 'application/json']);

        $response->assertStatus(403);
    }

    public function testDeleteBooking()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);
        $booking = factory(Booking::class)->create([
            'ticket_id' => $ticket['id'],
            'event_id' => $event['id']
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->json('DELETE', '/api/events/' . $event['id'] . '/bookings/' . $booking['id'], ['Accept' => 'application/json']);

        $response->assertStatus(204);
    }

    public function testDeleteBookingFail()
    {
        $users = factory(User::class, 2)->create(['role_id' => $this->role['id']]);
        $event = factory(Event::class)->create(['user_id' => $users[0]['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);
        $booking = factory(Booking::class)->create([
            'ticket_id' => $ticket['id'],
            'event_id' => $event['id']
        ]);

        $response = $this->actingAs($users[1], 'api')
            ->json('DELETE', '/api/events/' . $event['id'] . '/bookings/' . $booking['id'], ['Accept' => 'application/json']);

        $response->assertStatus(403);
    }
}
