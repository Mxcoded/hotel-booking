<?php

namespace Tests\Feature;

use App\Models\RoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_returns_valid_xml_with_static_pages(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/xml');

        $content = $response->getContent();

        $this->assertStringStartsWith('<?xml', $content);
        foreach (['urlset', route('home'), route('rooms'), route('gallery'), route('local-guide'), route('menu')] as $needle) {
            $this->assertStringContainsString($needle, $content);
        }
    }

    public function test_sitemap_includes_active_room_types_with_lastmod(): void
    {
        $room = RoomType::create([
            'name' => 'Sitemap Suite',
            'slug' => 'sitemap-suite',
            'room_type' => 'suite',
            'price' => 40000,
            'description' => 'Test room',
            'base_guests' => 2,
            'is_active' => true,
        ]);

        $content = $this->get('/sitemap.xml')->getContent();

        $this->assertStringContainsString(route('rooms.show', $room), $content);
        $this->assertStringContainsString('<lastmod>', $content);
    }

    public function test_sitemap_excludes_inactive_room_types(): void
    {
        $inactive = RoomType::create([
            'name' => 'Hidden Suite',
            'slug' => 'hidden-suite',
            'room_type' => 'suite',
            'price' => 40000,
            'description' => 'Hidden room',
            'base_guests' => 2,
            'is_active' => false,
        ]);

        $content = $this->get('/sitemap.xml')->getContent();

        $this->assertStringNotContainsString(route('rooms.show', $inactive), $content);
    }
}
