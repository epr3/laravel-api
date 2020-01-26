<?php


namespace Tests\Unit;

use App\Models\User;
use App\Models\Event;
use App\Services\ImageService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\WithUserTestCase;

class ImageServiceTest extends WithUserTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testImage()
    {
        Storage::fake('event_images');

        $event = factory(Event::class)->create(['user_id' => $this->user['id']]);
        $imageService = app(ImageService::class);

        $this->be($this->user);

        $file = UploadedFile::fake()->image('image.jpg');

        $imageService->uploadImage($event['id'], $file);
        $getEvent = Event::find($event['id']);
        Storage::disk('event_images')->assertExists(explode("/", $getEvent['image_path'])[1]);
        $imageService->deleteImage($getEvent['id']);
        Storage::disk('event_images')->assertMissing(explode("/", $getEvent['image_path'])[1]);
    }
}
