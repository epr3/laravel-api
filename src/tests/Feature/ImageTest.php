<?php


namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;
use Tests\WithUserTestCase;

class ImageTest extends WithUserTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateImage()
    {
        Storage::fake('event_images');

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);

        $file = UploadedFile::fake()->image('image.jpg');

        $response = $this->actingAs($this->user, 'api')
            ->json('POST', '/api/events/' . $event['id'] . '/image', ['image' => $file], ['Accept' => 'application/json']);

        $response->assertStatus(204);
    }

    public function testDeleteImage()
    {
        Storage::fake('event_images');

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);

        $file = UploadedFile::fake()->image('image.jpg');

        $this->actingAs($this->user, 'api')
            ->json('POST', '/api/events/' . $event['id'] . '/image', ['image' => $file], ['Accept' => 'application/json']);

        $response =  $this->actingAs($this->user, 'api')
            ->json('DELETE', '/api/events/' . $event['id'] . '/image', ['Accept' => 'application/json']);

        $response->assertStatus(204);
    }
}
