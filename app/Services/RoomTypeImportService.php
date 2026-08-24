<?php

namespace App\Services;

use App\Models\RoomType;
use Illuminate\Support\Str;
use OpenSpout\Reader\Common\Creator\ReaderFactory;
use OpenSpout\Common\Entity\Row;

class RoomTypeImportService
{
    protected array $errors = [];
    protected int $created = 0;
    protected int $updated = 0;
    protected int $skipped = 0;

    protected static array $headers = [
        'name',
        'room_type',
        'bed_type',
        'room_size',
        'description',
        'price',
        'weekend_price',
        'holiday_price',
        'base_guests',
        'max_guests',
        'extra_guest_fee',
        'discount_percent',
        'min_stay',
        'max_stay',
        'check_in_time',
        'check_out_time',
        'is_active',
        'is_featured',
        'sort_order',
        'features',
    ];

    protected static array $roomTypes = [
        'standard', 'deluxe', 'suite', 'executive', 'presidential',
    ];

    protected static array $bedTypes = [
        'king', 'queen', 'twin', 'double', 'sofa_bed',
    ];

    public function getHeaders(): array
    {
        return self::$headers;
    }

    public function import(string $filePath): array
    {
        $reader = ReaderFactory::createFromFile($filePath);
        $reader->open($filePath);

        $sheets = $reader->getSheetIterator();
        $sheets->current(); // first sheet

        $rowIterator = $sheets->current()->getRowIterator();
        $headerMap = [];

        foreach ($rowIterator as $index => $row) {
            $cells = $this->getCellsFromRow($row);

            if ($index === 1) {
                $headerMap = $this->mapHeaders($cells);
                if (empty($headerMap)) {
                    $this->errors[] = 'Row 1: Could not match column headers. Please use the sample file.';
                    break;
                }
                continue;
            }

            if ($this->isEmptyRow($cells)) {
                continue;
            }

            $this->processRow($cells, $headerMap, $index);
        }

        $reader->close();

        return [
            'created' => $this->created,
            'updated' => $this->updated,
            'skipped' => $this->skipped,
            'errors' => $this->errors,
        ];
    }

    protected function getCellsFromRow(Row $row): array
    {
        $cells = [];
        foreach ($row->getCells() as $cell) {
            $cells[] = $cell->getValue();
        }
        return $cells;
    }

    protected function mapHeaders(array $cells): array
    {
        $map = [];
        foreach ($cells as $colIndex => $header) {
            $normalized = Str::slug(trim((string) $header), '_');
            if (in_array($normalized, self::$headers)) {
                $map[$colIndex] = $normalized;
            }
        }
        return $map;
    }

    protected function isEmptyRow(array $cells): bool
    {
        return empty(array_filter($cells, fn ($c) => $c !== null && trim((string) $c) !== ''));
    }

    protected function processRow(array $cells, array $headerMap, int $rowIndex): void
    {
        $data = [];
        foreach ($headerMap as $colIndex => $field) {
            $data[$field] = $cells[$colIndex] ?? null;
        }

        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            $this->errors[] = "Row {$rowIndex}: Name is required. Skipped.";
            $this->skipped++;
            return;
        }

        $roomTypeInput = $data['room_type'] ?? '';
        $roomType = $this->normalizeRoomType($roomTypeInput ?: null);
        if (!$roomType) {
            $this->errors[] = "Row {$rowIndex}: Invalid room_type \"{$roomTypeInput}\". Skipped.";
            $this->skipped++;
            return;
        }

        $price = $this->parseFloat($data['price'] ?? null);
        if ($price === null || $price <= 0) {
            $this->errors[] = "Row {$rowIndex}: Price must be a positive number. Skipped.";
            $this->skipped++;
            return;
        }

        $slug = Str::slug($name);
        $existing = RoomType::where('slug', $slug)->first();

        $payload = $this->buildPayload($data, $name, $slug, $roomType, $price);

        if ($existing) {
            $existing->update($payload);
            $this->updated++;
        } else {
            RoomType::create($payload);
            $this->created++;
        }
    }

    protected function buildPayload(array $data, string $name, string $slug, string $roomType, float $price): array
    {
        $bedType = $this->normalizeBedType($data['bed_type'] ?? null);

        $payload = [
            'name' => $name,
            'slug' => $slug,
            'room_type' => $roomType,
            'description' => trim((string) ($data['description'] ?? '')) ?: "{$name} at Brickspoint Hotel",
            'price' => $price,
            'base_guests' => max(1, (int) $this->parseInt($data['base_guests'] ?? 2)),
            'is_active' => $this->parseBool($data['is_active'] ?? true),
            'is_featured' => $this->parseBool($data['is_featured'] ?? false),
            'sort_order' => max(0, (int) $this->parseInt($data['sort_order'] ?? 0)),
            'min_stay' => max(1, (int) $this->parseInt($data['min_stay'] ?? 1)),
            'check_in_time' => $this->normalizeTime($data['check_in_time'] ?? '14:00'),
            'check_out_time' => $this->normalizeTime($data['check_out_time'] ?? '11:00'),
        ];

        if ($bedType) {
            $payload['bed_type'] = $bedType;
        }

        $roomSize = $this->parseInt($data['room_size'] ?? null);
        if ($roomSize && $roomSize > 0) {
            $payload['room_size'] = $roomSize;
        }

        $weekendPrice = $this->parseFloat($data['weekend_price'] ?? null);
        if ($weekendPrice !== null && $weekendPrice > 0) {
            $payload['weekend_price'] = $weekendPrice;
        }

        $holidayPrice = $this->parseFloat($data['holiday_price'] ?? null);
        if ($holidayPrice !== null && $holidayPrice > 0) {
            $payload['holiday_price'] = $holidayPrice;
        }

        $maxGuests = $this->parseInt($data['max_guests'] ?? null);
        if ($maxGuests !== null && $maxGuests > 0) {
            $payload['max_guests'] = $maxGuests;
        }

        $extraGuestFee = $this->parseFloat($data['extra_guest_fee'] ?? null);
        if ($extraGuestFee !== null && $extraGuestFee >= 0) {
            $payload['extra_guest_fee'] = $extraGuestFee;
        }

        $discountPercent = $this->parseFloat($data['discount_percent'] ?? null);
        if ($discountPercent !== null && $discountPercent > 0 && $discountPercent <= 100) {
            $payload['discount_percent'] = $discountPercent;
        }

        $maxStay = $this->parseInt($data['max_stay'] ?? null);
        if ($maxStay !== null && $maxStay > 0) {
            $payload['max_stay'] = $maxStay;
        }

        $features = $this->parseFeatures($data['features'] ?? null);
        if ($features) {
            $payload['features'] = $features;
        }

        return $payload;
    }

    protected function normalizeRoomType(?string $value): ?string
    {
        $value = strtolower(trim((string) $value));
        return in_array($value, self::$roomTypes) ? $value : null;
    }

    protected function normalizeBedType(?string $value): ?string
    {
        $value = strtolower(trim((string) $value));
        return in_array($value, self::$bedTypes) ? $value : null;
    }

    protected function normalizeTime(?string $value): string
    {
        $value = trim((string) $value);
        if (preg_match('/^(\d{1,2}):(\d{2})$/', $value, $m)) {
            return str_pad($m[1], 2, '0', STR_PAD_LEFT) . ':' . $m[2];
        }
        return '14:00';
    }

    protected function parseFloat($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        $cleaned = preg_replace('/[^0-9.\-]/', '', (string) $value);
        return is_numeric($cleaned) ? (float) $cleaned : null;
    }

    protected function parseInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }
        $cleaned = preg_replace('/[^0-9\-]/', '', (string) $value);
        return is_numeric($cleaned) ? (int) $cleaned : null;
    }

    protected function parseBool($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        $value = strtolower(trim((string) $value));
        return in_array($value, ['1', 'true', 'yes', 'on', 'active', 'enabled']);
    }

    protected function parseFeatures(?string $value): ?array
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $features = [];
        $pairs = array_map('trim', explode(',', (string) $value));

        foreach ($pairs as $pair) {
            $parts = array_map('trim', explode('|', $pair));
            $featureName = $parts[0] ?? '';
            $featureIcon = $parts[1] ?? 'fa-check';

            if ($featureName !== '') {
                $features[] = [
                    'name' => $featureName,
                    'icon' => $featureIcon,
                ];
            }
        }

        return $features ?: null;
    }
}
