<?php

use App\Support\Thumbnails;

if (!function_exists('thumbnail_url')) {
    function thumbnail_url(?string $path, string $preset = 'card'): string
    {
        return Thumbnails::url($path, $preset);
    }
}
