<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class RoomCategory extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public const COLORS = [
        'gray' => 'Gray',
        'primary' => 'Primary',
        'info' => 'Info',
        'success' => 'Success',
        'warning' => 'Warning',
        'danger' => 'Danger',
    ];

    /**
     * Fallback categories used when the table is empty (e.g. before seeding).
     */
    public const DEFAULT_OPTIONS = [
        'standard' => 'Standard',
        'deluxe' => 'Deluxe',
        'suite' => 'Suite',
        'executive' => 'Executive',
        'presidential' => 'Presidential',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function roomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class, 'room_type', 'slug');
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ── Helpers ───────────────────────────────────────────────

    /**
     * Slug => label map for select inputs. Falls back to the default list.
     */
    public static function options(): array
    {
        $options = static::query()->active()->ordered()->pluck('name', 'slug')->all();

        return $options ?: self::DEFAULT_OPTIONS;
    }

    /**
     * All known category slugs (active or not), used for validation.
     */
    public static function slugs(): array
    {
        $slugs = static::query()->pluck('slug')->all();

        return $slugs ?: array_keys(self::DEFAULT_OPTIONS);
    }

    public static function colorFor(?string $slug): string
    {
        $color = static::query()->where('slug', $slug)->value('color');

        return $color ?: 'gray';
    }

    // ── Audit Logging ─────────────────────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logExcept(['updated_at', 'created_at'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('room_categories')
            ->setDescriptionForEvent(fn (string $eventName) => "Room category {$eventName}");
    }
}
