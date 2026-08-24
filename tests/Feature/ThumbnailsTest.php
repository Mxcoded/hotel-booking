<?php

namespace Tests\Feature;

use App\Models\Attraction;
use App\Models\Gallery;
use App\Models\RoomMedia;
use App\Models\RoomType;
use App\Support\Thumbnails;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ThumbnailsTest extends TestCase
{
    use RefreshDatabase;

    protected string $fakeRoot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fakeRoot = sys_get_temp_dir() . '/thumbs-test-' . uniqid();
        mkdir($this->fakeRoot, 0777, true);

        config(['filesystems.disks.public.root' => $this->fakeRoot]);
        Storage::disk('public')->makeDirectory('');

        Thumbnails::flushMemo();
    }

    protected function tearDown(): void
    {
        $this->rrmdir($this->fakeRoot);

        parent::tearDown();
    }

    protected function rrmdir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        foreach (array_diff(scandir($dir), ['.', '..']) as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            is_dir($path) ? $this->rrmdir($path) : @unlink($path);
        }

        @rmdir($dir);
    }

    /**
     * Create a real JPEG on the public disk and return its storage-relative path.
     */
    protected function makeImage(string $storagePath, int $width = 1600, int $height = 900): string
    {
        $image = imagecreatetruecolor($width, $height);
        imagefill($image, 0, 0, imagecolorallocate($image, 200, 120, 40));

        ob_start();
        imagejpeg($image, null, 90);
        $bytes = ob_get_clean();
        imagedestroy($image);

        Storage::disk('public')->put($storagePath, $bytes);

        return $storagePath;
    }

    public function test_generates_scaled_thumbnail_and_returns_its_url(): void
    {
        $path = $this->makeImage('gallery/beach.jpg');

        $url = thumbnail_url($path, 'thumb');

        $target = 'gallery/thumbs/thumb/beach.jpg';

        Storage::disk('public')->assertExists($target);

        [$w] = getimagesize(Storage::disk('public')->path($target));
        $this->assertEquals(Thumbnails::PRESETS['thumb'], $w);

        $this->assertStringContainsString('gallery/thumbs/thumb/beach.jpg', $url);
    }

    public function test_does_not_upscale_small_images(): void
    {
        $path = $this->makeImage('gallery/small.jpg', 300, 200);

        $url = thumbnail_url($path, 'card');

        $this->assertEquals(Storage::disk('public')->url($path), $url);
        Storage::disk('public')->assertMissing('gallery/thumbs/card/small.jpg');
    }

    public function test_falls_back_to_original_when_source_missing(): void
    {
        $url = thumbnail_url('gallery/ghost.jpg', 'card');

        $this->assertEquals(Storage::disk('public')->url('gallery/ghost.jpg'), $url);
    }

    public function test_empty_path_returns_empty_string(): void
    {
        $this->assertSame('', thumbnail_url(null));
        $this->assertSame('', thumbnail_url(''));
    }

    public function test_corrupt_image_falls_back_to_original_url(): void
    {
        Storage::disk('public')->put('gallery/broken.jpg', 'not-an-image');

        $url = thumbnail_url('gallery/broken.jpg', 'thumb');

        $this->assertEquals(Storage::disk('public')->url('gallery/broken.jpg'), $url);
    }

    public function test_command_generates_thumbs_for_all_image_sources(): void
    {
        $roomType = RoomType::create([
            'name' => 'Thumb Suite',
            'slug' => 'thumb-suite',
            'room_type' => 'suite',
            'price' => 50000,
            'description' => 'Test.',
            'base_guests' => 2,
            'min_stay' => 1,
            'advance_booking_days' => 365,
            'is_active' => true,
            'image' => $this->makeImage('room-types/suite.jpg'),
        ]);

        RoomMedia::create([
            'room_type_id' => $roomType->id,
            'file_path' => $this->makeImage('room-media/detail.jpg'),
            'type' => 'video',
        ]);
        RoomMedia::create([
            'room_type_id' => $roomType->id,
            'file_path' => $this->makeImage('room-media/shot.jpg'),
            'type' => 'image',
        ]);

        Gallery::create(['path' => $this->makeImage('gallery/pool.jpg'), 'alt_text' => 'Pool']);
        Attraction::create([
            'name' => 'Jabi Lake',
            'category' => 'nature',
            'description' => 'Nearby lake.',
            'image' => $this->makeImage('attractions/jabi.jpg'),
        ]);

        $this->artisan('images:generate-thumbs')->assertSuccessful();

        foreach ([
            'room-types/thumbs/card/suite.jpg',
            'room-types/thumbs/thumb/suite.jpg',
            'room-media/thumbs/card/shot.jpg',
            'gallery/thumbs/thumb/pool.jpg',
            'attractions/thumbs/card/jabi.jpg',
        ] as $expected) {
            Storage::disk('public')->assertExists($expected);
        }

        Storage::disk('public')->assertMissing('room-media/thumbs/card/detail.jpg');
    }
}
