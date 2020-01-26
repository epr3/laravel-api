<?php


namespace Tests\Unit;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Event;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\WithUserTestCase;

class BookingServiceTest extends WithUserTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateBooking()
    {
        Storage::fake('qr_codes');

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);
        $booking = factory(Booking::class)->make(['event_id' => $event['id'], 'ticket_id' => $ticket['id']]);

        $bookingService = app(BookingService::class);

        $testBooking = $bookingService->createBooking($booking->attributesToArray());

        $this->assertDatabaseHas('bookings', [
            'id' => $testBooking['id']
        ]);

        Storage::disk('qr_codes')->assertExists(explode("/", $testBooking['qr_code_path'])[1]);
    }

    public function testGetBooking()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);
        $booking = factory(Booking::class)->create(['event_id' => $event['id'], 'ticket_id' => $ticket['id']]);

        $this->be($this->user);

        $bookingService = app(BookingService::class);

        $testBooking = $bookingService->getBooking($booking['id']);

        $this->assertDatabaseHas('bookings', [
            'id' => $testBooking['id']
        ]);
    }

    public function testGetBookings()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);
        factory(Booking::class, 5)->create(['event_id' => $event['id'], 'ticket_id' => $ticket['id']]);

        $this->be($this->user);

        $bookingService = app(BookingService::class);

        $testBookings = $bookingService->getBookings(['event_id' => $event['id']]);

        $this->assertCount(5, $testBookings);
    }

    public function testGetBookingFail()
    {

        $this->expectException(ModelNotFoundException::class);

        $this->be($this->user);

        $bookingService = app(BookingService::class);

        $bookingService->getBooking('20');
    }

    public function testDeleteBookingFail()
    {

        $this->expectException(ModelNotFoundException::class);

        $this->be($this->user);

        $bookingService = app(BookingService::class);

        $bookingService->deleteBooking('20');
    }

    public function testUpdateBookingFail()
    {

        $this->expectException(ModelNotFoundException::class);

        $this->be($this->user);

        $bookingService = app(BookingService::class);

        $bookingService->updateBooking('20', ['name' => 'randomName']);
    }

    public function testDeleteBooking()
    {
        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);
        $booking = factory(Booking::class)->create(['event_id' => $event['id'], 'ticket_id' => $ticket['id']]);

        $this->be($this->user);

        $bookingService = app(BookingService::class);

        $testBooking = $bookingService->deleteBooking($booking['id']);

        $this->assertDatabaseMissing('bookings', [
            'id' => $testBooking['id']
        ]);
    }

    public function testUpdateTicket()
    {
        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);
        $booking = factory(Booking::class)->create(['event_id' => $event['id'], 'ticket_id' => $ticket['id']]);

        $this->be($this->user);

        $bookingService = app(BookingService::class);

        $testBooking = $bookingService->updateBooking($booking['id'], ['name' => 'randomName']);

        $this->assertDatabaseHas('bookings', [
            'id' => $testBooking['id'],
            'name' => $testBooking['name']
        ]);
    }
}
