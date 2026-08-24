<?php

namespace App\Console\Commands;

use App\Models\Attraction;
use App\Models\Gallery;
use App\Models\RoomMedia;
use App\Models\RoomType;
use App\Support\Thumbnails;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateThumbs extends Command
{
    protected $signature = 'images:generate-thumbs {--force : Regenerate thumbnails that already exist}';

    protected $description = 'Generate optimized thumbnails for all room, media, gallery and attraction images';

    public function handle(): int
    {
        $force = (bool) $this->option('force');

        $paths = collect()
            ->merge(RoomType::query()->whereNotNull('image')->pluck('image'))
            ->merge(RoomMedia::query()->where('type', 'image')->pluck('file_path'))
            ->merge(Gallery::query()->pluck('path'))
            ->merge(Attraction::query()->whereNotNull('image')->pluck('image'))
            ->filter()
            ->unique()
            ->values();

        if ($paths->isEmpty()) {
            $this->info('No source images found.');

            return self::SUCCESS;
        }

        $created = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($paths as $path) {
            foreach (Thumbnails::PRESETS as $preset => $width) {
                try {
                    if (!$force && Storage::disk('public')->exists(Thumbnails::targetPath($path, $preset))) {
                        $skipped++;
                        continue;
                    }

                    if (Thumbnails::generate($path, $preset, $force)) {
                        $created++;
                    } else {
                        $skipped++;
                    }
                } catch (\Throwable $e) {
                    $failed++;
                    $this->warn("Failed [{$preset}] {$path}: {$e->getMessage()}");
                }
            }
        }

        $this->info("Done. Sources: {$paths->count()} — created: {$created}, skipped: {$skipped}, failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
