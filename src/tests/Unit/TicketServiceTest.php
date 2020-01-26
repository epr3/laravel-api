<?php


namespace Tests\Unit;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Event;
use App\Models\Voucher;
use App\Services\TicketService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\WithUserTestCase;

class TicketServiceTest extends WithUserTestCase
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

        $this->be($this->user);

        $ticketService = app(TicketService::class);

        $testTicket = $ticketService->createTicket($ticket->attributesToArray());

        $this->assertDatabaseHas('tickets', [
            'id' => $testTicket['id']
        ]);
    }

    public function testGetTicket()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);

        $this->be($this->user);

        $ticketService = app(TicketService::class);

        $testTicket = $ticketService->getTicket($ticket['id']);

        $this->assertDatabaseHas('tickets', [
            'id' => $testTicket['id']
        ]);
    }

    public function testGetTickets()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        factory(Ticket::class, 5)->create(['event_id' => $event['id']]);

        $this->be($this->user);

        $ticketService = app(TicketService::class);

        $testTickets = $ticketService->getTickets(['event_id' => $event['id']]);

        $this->assertCount(5, $testTickets);
    }

    public function testGetTicketFail()
    {
        $this->expectException(ModelNotFoundException::class);

        $ticketService = app(TicketService::class);

        $ticketService->getTicket('20');
    }

    public function testDeleteTicketFail()
    {
        $this->expectException(ModelNotFoundException::class);

        $this->be($this->user);

        $ticketService = app(TicketService::class);

        $ticketService->deleteTicket('20');
    }

    public function testUpdateTicketFail()
    {
        $this->expectException(ModelNotFoundException::class);

        $this->be($this->user);

        $ticketService = app(TicketService::class);

        $ticketService->updateTicket('20', ['type' => 'randomName']);
    }

    public function testDeleteTicket()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);

        $this->be($this->user);

        $ticketService = app(TicketService::class);

        $testTicket = $ticketService->deleteTicket($ticket['id']);

        $this->assertDatabaseMissing('tickets', [
            'id' => $testTicket['id']
        ]);
    }

    public function testUpdateTicket()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);

        $this->be($this->user);

        $ticketService = app(TicketService::class);

        $testTicket = $ticketService->updateTicket($ticket['id'], ['type' => 'randomName']);

        $this->assertDatabaseHas('tickets', [
            'id' => $testTicket['id'],
            'type' => $testTicket['type']
        ]);
    }

    public function testAttachVoucherToTicket()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);
        $voucher = factory(Voucher::class)->create(['event_id' => $event['id']]);

        $this->be($this->user);

        $ticketService = app(TicketService::class);

        $ticketService->addVoucherToTicket($ticket['id'], $voucher['id']);

        $this->assertDatabaseHas('vouchers_tickets', [
            'voucher_id' => $voucher['id'],
            'ticket_id' => $ticket['id']
        ]);
    }

    public function testDetachVoucherFromTicket()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $ticket = factory(Ticket::class)->create(['event_id' => $event['id']]);
        $voucher = factory(Voucher::class)->create(['event_id' => $event['id']]);

        $this->be($this->user);

        $ticket->vouchers()->attach($voucher['id']);

        $ticketService = app(TicketService::class);

        $ticketService->removeVoucherFromTicket($ticket['id'], $voucher['id']);

        $this->assertDatabaseMissing('vouchers_tickets', [
            'voucher_id' => $voucher['id'],
            'ticket_id' => $ticket['id']
        ]);
    }
}
