<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Site settings organized by group with full metadata.
     */
    protected function getSettings(): array
    {
        return [
            // ── Brand Identity ───────────────────────────────
            [
                'key'         => 'site_name',
                'type'        => 'text',
                'group'       => 'brand',
                'label'       => 'Site Name',
                'description' => 'The official name of the hotel displayed in headers, SEO, and communications.',
                'sort'        => 1,
                'is_active'   => true,
                'value'       => 'Brickspoint Hotel',
            ],
            [
                'key'         => 'site_tagline',
                'type'        => 'text',
                'group'       => 'brand',
                'label'       => 'Site Tagline',
                'description' => 'Short tagline shown alongside the site name (e.g., location or slogan).',
                'sort'        => 2,
                'is_active'   => true,
                'value'       => 'Wuse, Abuja, Nigeria',
            ],
            [
                'key'         => 'logo',
                'type'        => 'image',
                'group'       => 'brand',
                'label'       => 'Logo',
                'description' => 'Main logo image (SVG/PNG recommended). Displayed in header and footer.',
                'sort'        => 3,
                'is_active'   => true,
                'value'       => null,
            ],
            [
                'key'         => 'favicon',
                'type'        => 'image',
                'group'       => 'brand',
                'label'       => 'Favicon',
                'description' => 'Browser tab icon (ICO/PNG 32x32). Used across all pages and error pages.',
                'sort'        => 4,
                'is_active'   => true,
                'value'       => null,
            ],
            [
                'key'         => 'seo_logo',
                'type'        => 'image',
                'group'       => 'brand',
                'label'       => 'SEO / Social Share Image',
                'description' => 'Image used for Open Graph (og:image) and Twitter Card when sharing links.',
                'sort'        => 5,
                'is_active'   => true,
                'value'       => null,
            ],
            [
                'key'         => 'primary_color',
                'type'        => 'color',
                'group'       => 'brand',
                'label'       => 'Primary Brand Color',
                'description' => 'Main accent color (amber #f59e0b). Used for buttons, links, and highlights.',
                'sort'        => 6,
                'is_active'   => true,
                'value'       => '#f59e0b',
            ],
            [
                'key'         => 'secondary_color',
                'type'        => 'color',
                'group'       => 'brand',
                'label'       => 'Secondary Color',
                'description' => 'Secondary color for backgrounds, cards, and subtle accents (slate #1e293b).',
                'sort'        => 7,
                'is_active'   => true,
                'value'       => '#1e293b',
            ],
            [
                'key'         => 'accent_color',
                'type'        => 'color',
                'group'       => 'brand',
                'label'       => 'Accent Color',
                'description' => 'Tertiary color for success states, CTAs, and highlights (emerald #10b981).',
                'sort'        => 8,
                'is_active'   => true,
                'value'       => '#10b981',
            ],

            // ── Hero Section ─────────────────────────────────
            [
                'key'         => 'hero_title',
                'type'        => 'text',
                'group'       => 'hero',
                'label'       => 'Hero Title',
                'description' => 'Large headline on the homepage hero section (e.g., "Welcome To").',
                'sort'        => 1,
                'is_active'   => true,
                'value'       => 'Welcome To',
            ],
            [
                'key'         => 'hero_subtitle',
                'type'        => 'text',
                'group'       => 'hero',
                'label'       => 'Hero Subtitle',
                'description' => 'Supporting text below the hero title explaining the booking process.',
                'sort'        => 2,
                'is_active'   => true,
                'value'       => 'No forms. No hassle. Just message us on WhatsApp to reserve your stay.',
            ],
            [
                'key'         => 'slogan',
                'type'        => 'text',
                'group'       => 'hero',
                'label'       => 'Slogan',
                'description' => 'Catchy slogan displayed prominently on the homepage (e.g., "Your Home Away From Home...").',
                'sort'        => 3,
                'is_active'   => true,
                'value'       => 'Your Home Away From Home...',
            ],
            [
                'key'         => 'hero_media',
                'type'        => 'image',
                'group'       => 'hero',
                'label'       => 'Hero Background Media',
                'description' => 'Background image or video for the hero section. Overlays the hero content.',
                'sort'        => 4,
                'is_active'   => true,
                'value'       => null,
            ],

            // ── Contact Information ──────────────────────────
            [
                'key'         => 'phone_number',
                'type'        => 'text',
                'group'       => 'contact',
                'label'       => 'Phone Number',
                'description' => 'Primary contact phone displayed in header, footer, and schema.org markup.',
                'sort'        => 1,
                'is_active'   => true,
                'value'       => '+2348099999620',
            ],
            [
                'key'         => 'whatsapp_number',
                'type'        => 'text',
                'group'       => 'contact',
                'label'       => 'WhatsApp Number',
                'description' => 'WhatsApp number for direct booking inquiries (international format, no +).',
                'sort'        => 2,
                'is_active'   => true,
                'value'       => '+2348099999620',
            ],
            [
                'key'         => 'email',
                'type'        => 'text',
                'group'       => 'contact',
                'label'       => 'Email Address',
                'description' => 'General contact email for inquiries and notifications.',
                'sort'        => 3,
                'is_active'   => true,
                'value'       => null,
            ],
            [
                'key'         => 'address',
                'type'        => 'text',
                'group'       => 'contact',
                'label'       => 'Physical Address',
                'description' => 'Full street address for maps, schema.org, and contact page.',
                'sort'        => 4,
                'is_active'   => true,
                'value'       => 'Wuse, Abuja, Nigeria',
            ],

            // ── Social Media ─────────────────────────────────
            [
                'key'         => 'facebook_url',
                'type'        => 'text',
                'group'       => 'social',
                'label'       => 'Facebook Page URL',
                'description' => 'Full URL to the hotel\'s Facebook page. Used in footer social links.',
                'sort'        => 1,
                'is_active'   => true,
                'value'       => null,
            ],
            [
                'key'         => 'instagram_url',
                'type'        => 'text',
                'group'       => 'social',
                'label'       => 'Instagram Profile URL',
                'description' => 'Full URL to the hotel\'s Instagram profile.',
                'sort'        => 2,
                'is_active'   => true,
                'value'       => null,
            ],
            [
                'key'         => 'twitter_url',
                'type'        => 'text',
                'group'       => 'social',
                'label'       => 'Twitter / X Profile URL',
                'description' => 'Full URL to the hotel\'s Twitter/X profile.',
                'sort'        => 3,
                'is_active'   => true,
                'value'       => null,
            ],
            [
                'key'         => 'linkedin_url',
                'type'        => 'text',
                'group'       => 'social',
                'label'       => 'LinkedIn Page URL',
                'description' => 'Full URL to the hotel\'s LinkedIn page.',
                'sort'        => 4,
                'is_active'   => true,
                'value'       => null,
            ],

            // ── SEO ──────────────────────────────────────────
            [
                'key'         => 'meta_title',
                'type'        => 'text',
                'group'       => 'seo',
                'label'       => 'Default Meta Title',
                'description' => 'Fallback <title> tag for pages without specific SEO titles.',
                'sort'        => 1,
                'is_active'   => true,
                'value'       => null,
            ],
            [
                'key'         => 'meta_description',
                'type'        => 'text',
                'group'       => 'seo',
                'label'       => 'Default Meta Description',
                'description' => 'Fallback meta description for SEO. Max ~160 chars recommended.',
                'sort'        => 2,
                'is_active'   => true,
                'value'       => null,
            ],

            // ── Business ─────────────────────────────────────
            [
                'key'         => 'usd_exchange_rate',
                'type'        => 'number',
                'group'       => 'business',
                'label'       => 'USD to NGN Exchange Rate',
                'description' => 'Exchange rate for displaying USD prices alongside NGN. 0 = disabled.',
                'sort'        => 1,
                'is_active'   => true,
                'value'       => '0',
            ],
            [
                'key'         => 'food_menu_pdf',
                'type'        => 'file',
                'group'       => 'business',
                'label'       => 'Food Menu PDF',
                'description' => 'Downloadable food & beverage menu (PDF). Linked from menu page and rooms.',
                'sort'        => 2,
                'is_active'   => true,
                'value'       => null,
            ],

            // ── Footer ───────────────────────────────────────
            [
                'key'         => 'footer_text',
                'type'        => 'text',
                'group'       => 'footer',
                'label'       => 'Footer Description',
                'description' => 'About text displayed in the footer column (supports basic HTML).',
                'sort'        => 1,
                'is_active'   => true,
                'value'       => null,
            ],
            [
                'key'         => 'copyright_text',
                'type'        => 'text',
                'group'       => 'footer',
                'label'       => 'Copyright Notice',
                'description' => 'Copyright text for footer (e.g., "© 2024 Brickspoint Hotel. All rights reserved.").',
                'sort'        => 2,
                'is_active'   => true,
                'value'       => null,
            ],
        ];
    }

    public function run(): void
    {
        foreach ($this->getSettings() as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting,
            );
        }
    }
}