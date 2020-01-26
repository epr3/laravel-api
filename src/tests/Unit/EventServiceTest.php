<?php


namespace Tests\Unit;

use App\Models\User;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\WithUserTestCase;

class EventServiceTest extends WithUserTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateEvent()
    {

        $event = factory(Event::class)->make(['user_id' => $this->user['id']]);
        $eventService = app(EventService::class);

        $this->be($this->user);

        $testEvent = $eventService->createEvent($event->attributesToArray());

        $this->assertDatabaseHas('events', [
            'id' => $testEvent['id']
        ]);
    }

    public function testGetEvent()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $eventService = app(EventService::class);

        $this->be($this->user);

        $testEvent = $eventService->getEvent($event['id']);

        $this->assertDatabaseHas('events', [
            'id' => $testEvent['id']
        ]);
    }

    public function testGetEvents()
    {

        factory(Event::class, 5)->create(['user_id' => $this->user['id']]);
        $eventService = app(EventService::class);

        $this->be($this->user);

        $testEvents = $eventService->getEvents(['user_id' => $this->user['id']]);

        $this->assertCount(5, $testEvents);
    }

    public function testGetEventFail()
    {

        $this->expectException(ModelNotFoundException::class);

        $this->be($this->user);

        $eventService = app(EventService::class);

        $eventService->getEvent('20');
    }

    public function testDeleteEventFail()
    {

        $this->expectException(ModelNotFoundException::class);

        $this->be($this->user);

        $eventService = app(EventService::class);

        $eventService->deleteEvent('20');
    }

    public function testUpdateEventFail()
    {

        $this->expectException(ModelNotFoundException::class);

        $this->be($this->user);

        $eventService = app(EventService::class);

        $eventService->updateEvent('20', ['name' => 'randomName']);
    }

    public function testDeleteEvent()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $eventService = app(EventService::class);

        $this->be($this->user);

        $testEvent = $eventService->deleteEvent($event['id']);

        $this->assertDatabaseMissing('events', [
            'id' => $testEvent['id']
        ]);
    }

    public function testUpdateEvent()
    {

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $eventService = app(EventService::class);

        $this->be($this->user);

        $testEvent = $eventService->updateEvent($event['id'], ['name' => 'randomName']);

        $this->assertDatabaseHas('events', [
            'id' => $testEvent['id'],
            'name' => $testEvent['name']
        ]);
    }
}
