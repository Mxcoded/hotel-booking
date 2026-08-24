<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class Thumbnails
{
    public const PRESETS = [
        'thumb' => 480,
        'card' => 960,
    ];

    protected static array $memo = [];

    public static function url(?string $path, string $preset = 'card'): string
    {
        if (!$path || !isset(static::PRESETS[$preset])) {
            return $path ? self::originalUrl($path) : '';
        }

        $key = $preset . '|' . $path;

        if (array_key_exists($key, static::$memo)) {
            return static::$memo[$key];
        }

        $url = self::resolve($path, $preset);

        return static::$memo[$key] = $url;
    }

    public static function generate(string $path, string $preset, bool $force = false): bool
    {
        if (!isset(static::PRESETS[$preset])) {
            throw new InvalidArgumentException("Unknown thumbnail preset [{$preset}]");
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($path)) {
            return false;
        }

        $target = self::targetPath($path, $preset);

        if (!$force && $disk->exists($target)) {
            return true;
        }

        try {
            $source = self::loadImage($disk->path($path));

            if ($source === false) {
                return false;
            }

            [$image, $type] = $source;
            $width = imagesx($image);
            $maxWidth = static::PRESETS[$preset];

            if ($width <= $maxWidth) {
                imagedestroy($image);
                return false;
            }

            $scaled = imagescale($image, $maxWidth, -1, IMG_BICUBIC);
            imagedestroy($image);

            if ($scaled === false) {
                return false;
            }

            $disk->makeDirectory(dirname($target));

            ob_start();
            self::writeImage($scaled, $type);
            $bytes = ob_get_clean();
            imagedestroy($scaled);

            if ($bytes === false || $bytes === '') {
                return false;
            }

            $disk->put($target, $bytes);

            return true;
        } catch (\Throwable $e) {
            Log::warning('Thumbnail generation failed', [
                'path' => $path,
                'preset' => $preset,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public static function flushMemo(): void
    {
        static::$memo = [];
    }

    protected static function resolve(string $path, string $preset): string
    {
        try {
            self::generate($path, $preset);
        } catch (\Throwable $e) {
            Log::warning('Thumbnail resolution failed', ['path' => $path, 'error' => $e->getMessage()]);
        }

        $target = self::targetPath($path, $preset);

        if (Storage::disk('public')->exists($target)) {
            return Storage::disk('public')->url($target);
        }

        return self::originalUrl($path);
    }

    public static function targetPath(string $path, string $preset): string
    {
        $dir = str_replace('\\', '/', dirname($path));

        return ($dir === '.' ? '' : $dir . '/') . 'thumbs/' . $preset . '/' . basename($path);
    }

    /**
     * @return array{0: \GdImage, 1: int}|false
     */
    protected static function loadImage(string $absolute): array|false
    {
        if (!is_file($absolute)) {
            return false;
        }

        $info = @getimagesize($absolute);

        if ($info === false) {
            return false;
        }

        $image = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($absolute),
            IMAGETYPE_PNG => @imagecreatefrompng($absolute),
            IMAGETYPE_WEBP => @imagecreatefromwebp($absolute),
            IMAGETYPE_GIF => @imagecreatefromgif($absolute),
            default => false,
        };

        if ($image === false) {
            return false;
        }

        return [$image, $info[2]];
    }

    protected static function writeImage(\GdImage $image, int $type): void
    {
        match ($type) {
            IMAGETYPE_JPEG => imagejpeg($image, null, 82),
            IMAGETYPE_PNG => imagepng($image, null, 6),
            IMAGETYPE_WEBP => imagewebp($image, null, 82),
            IMAGETYPE_GIF => imagegif($image),
            default => imagejpeg($image, null, 82),
        };
    }

    protected static function originalUrl(string $path): string
    {
        return Storage::disk('public')->url($path);
    }
}
