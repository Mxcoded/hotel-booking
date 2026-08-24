<?php

namespace Tests\Feature;

use App\Models\RoomType;
use App\Models\User;
use App\Services\RoomTypeImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\Common\Creator\ReaderFactory;
use OpenSpout\Writer\XLSX\Writer;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoomTypeImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected string $testDir;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);

        $this->admin = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $this->admin->assignRole('admin');

        $this->testDir = sys_get_temp_dir() . '/room-type-import-tests';
        if (!is_dir($this->testDir)) {
            mkdir($this->testDir, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        foreach (glob($this->testDir . '/*.xlsx') ?: [] as $file) {
            @unlink($file);
        }
        @rmdir($this->testDir);

        parent::tearDown();
    }

    // ── Helpers ───────────────────────────────────────────────

    /**
     * Create an XLSX import file with the standard headers and given data rows.
     */
    protected function createImportFile(array $rows, ?array $headers = null): string
    {
        $path = $this->testDir . '/import-' . uniqid() . '.xlsx';

        $writer = new Writer();
        $writer->openToFile($path);
        $writer->addRow(Row::fromValues($headers ?? [
            'name', 'room_type', 'bed_type', 'price', 'description', 'features',
        ]));

        foreach ($rows as $row) {
            $writer->addRow(Row::fromValues(array_map(fn ($v) => (string) $v, $row)));
        }

        $writer->close();

        return $path;
    }

    // ── Sample Download ───────────────────────────────────────

    public function test_guest_cannot_download_sample(): void
    {
        $this->get(route('admin.room-types.sample-download'))
            ->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_download_sample(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('admin.room-types.sample-download'))
            ->assertForbidden();
    }

    public function test_admin_can_download_sample(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.room-types.sample-download'));

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // BinaryFileResponse carries content via its attached file, not getContent()
        $this->assertGreaterThan(0, filesize($response->getFile()->getRealPath()));
    }

    public function test_sample_download_is_a_valid_xlsx_with_expected_headers(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.room-types.sample-download'));

        $reader = ReaderFactory::createFromFile($response->getFile()->getRealPath());
        $reader->open($response->getFile()->getRealPath());

        $rows = array_values(iterator_to_array(
            $reader->getSheetIterator()->current()->getRowIterator()
        ));

        // Header + 3 sample rows
        $this->assertCount(4, $rows);

        $headerValues = array_map(
            fn ($cell) => $cell->getValue(),
            iterator_to_array($rows[0]->getCells())
        );

        $expectedHeaders = [
            'name', 'room_type', 'bed_type', 'room_size', 'description',
            'price', 'weekend_price', 'holiday_price', 'base_guests', 'max_guests',
            'extra_guest_fee', 'discount_percent', 'min_stay', 'max_stay',
            'check_in_time', 'check_out_time', 'is_active', 'is_featured',
            'sort_order', 'features',
        ];

        $this->assertSame($expectedHeaders, array_values(array_map('strval', $headerValues)));

        $reader->close();
    }

    public function test_sample_file_can_be_imported_directly(): void
    {
        // The downloaded sample should itself be importable — creates 3 room types.
        $response = $this->actingAs($this->admin)
            ->get(route('admin.room-types.sample-download'));

        $samplePath = $response->getFile()->getRealPath();

        RoomType::query()->delete();

        $results = (new RoomTypeImportService())->import($samplePath);

        $this->assertSame(3, $results['created']);
        $this->assertSame(0, $results['updated']);
        $this->assertSame(0, $results['skipped']);
        $this->assertEmpty($results['errors']);

        $this->assertSame(3, RoomType::count());
        $this->assertNotNull(RoomType::where('slug', 'standard-room')->first());
        $this->assertNotNull(RoomType::where('slug', 'deluxe-room')->first());
        $this->assertNotNull(RoomType::where('slug', 'executive-suite')->first());
    }

    // ── Filament Page Access ──────────────────────────────────

    public function test_import_page_loads_for_admin(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/import-room-types')
            ->assertOk();
    }

    public function test_import_page_accepts_uploaded_file_and_imports(): void
    {
        // Simulate a completed FileUpload: file stored on the local disk,
        // form property holding its relative path.
        $importFile = $this->createImportFile([
            ['Livewire Room', 'standard', '', '32000', 'Uploaded via Livewire.', 'Pool|fa-swimming-pool'],
        ]);

        $storedPath = 'temp/room-types-test.xlsx';
        \Illuminate\Support\Facades\Storage::disk('local')->put(
            $storedPath,
            file_get_contents($importFile)
        );

        Livewire::actingAs($this->admin)
            ->test(\App\Filament\Pages\ImportRoomTypes::class)
            ->set('data.import_file', [$storedPath])
            ->call('submit')
            ->assertHasNoErrors();

        \Illuminate\Support\Facades\Storage::disk('local')->delete($storedPath);

        $roomType = RoomType::where('slug', 'livewire-room')->first();
        $this->assertNotNull($roomType);
        $this->assertEquals(32000.0, (float) $roomType->price);
        $this->assertCount(1, $roomType->features);
    }

    public function test_import_page_submit_without_file_shows_warning(): void
    {
        Livewire::actingAs($this->admin)
            ->test(\App\Filament\Pages\ImportRoomTypes::class)
            ->call('submit');

        $this->assertSame(0, RoomType::count());
    }

    // ── Import Service ────────────────────────────────────────

    public function test_import_creates_new_room_types(): void
    {
        $file = $this->createImportFile([
            ['Standard Room', 'standard', 'queen', '35000', 'A standard room.', 'Free WiFi|fa-wifi,Mini Bar|fa-glass-whiskey'],
            ['Deluxe Room', 'deluxe', 'king', '55000', 'A deluxe room.', ''],
        ]);

        $results = (new RoomTypeImportService())->import($file);

        $this->assertSame(2, $results['created']);
        $this->assertSame(0, $results['updated']);
        $this->assertSame(0, $results['skipped']);
        $this->assertEmpty($results['errors']);

        $standard = RoomType::where('slug', 'standard-room')->first();
        $this->assertNotNull($standard);
        $this->assertSame('Standard Room', $standard->name);
        $this->assertSame('standard', $standard->room_type);
        $this->assertSame('queen', $standard->bed_type);
        $this->assertEquals(35000.0, (float) $standard->price);
        $this->assertTrue($standard->is_active);
        $this->assertFalse($standard->is_featured);

        // Features parsed into [{name, icon}] format
        $this->assertIsArray($standard->features);
        $this->assertCount(2, $standard->features);
        $this->assertSame('Free WiFi', $standard->features[0]['name']);
        $this->assertSame('fa-wifi', $standard->features[0]['icon']);
        $this->assertSame('Mini Bar', $standard->features[1]['name']);
        $this->assertSame('fa-glass-whiskey', $standard->features[1]['icon']);

        // Empty features column stays null
        $deluxe = RoomType::where('slug', 'deluxe-room')->first();
        $this->assertNotNull($deluxe);
        $this->assertNull($deluxe->features);
    }

    public function test_import_updates_existing_room_types_matched_by_slug(): void
    {
        RoomType::create([
            'name' => 'Standard Room',
            'slug' => 'standard-room',
            'room_type' => 'standard',
            'price' => 20000,
            'description' => 'Original description',
            'base_guests' => 2,
            'is_active' => true,
        ]);

        $file = $this->createImportFile([
            ['Standard Room', 'standard', 'queen', '45000', 'Updated description.', 'Balcony|fa-door-open'],
        ]);

        $results = (new RoomTypeImportService())->import($file);

        $this->assertSame(0, $results['created']);
        $this->assertSame(1, $results['updated']);
        $this->assertEmpty($results['errors']);

        $roomType = RoomType::where('slug', 'standard-room')->firstOrFail();
        $this->assertEquals(45000.0, (float) $roomType->price);
        $this->assertSame('Updated description.', $roomType->description);
        $this->assertCount(1, $roomType->features);

        // No duplicate created
        $this->assertSame(1, RoomType::count());
    }

    public function test_import_mixed_create_and_update(): void
    {
        RoomType::create([
            'name' => 'Existing Room',
            'slug' => 'existing-room',
            'room_type' => 'suite',
            'price' => 10000,
            'description' => 'Existing description',
            'base_guests' => 2,
        ]);

        $file = $this->createImportFile([
            ['Existing Room', 'suite', '', '15000', '', ''],
            ['Brand New Room', 'executive', '', '25000', '', ''],
        ]);

        $results = (new RoomTypeImportService())->import($file);

        $this->assertSame(1, $results['created']);
        $this->assertSame(1, $results['updated']);
        $this->assertSame(2, RoomType::count());

        $existing = RoomType::where('slug', 'existing-room')->first();
        $this->assertEquals(15000.0, (float) $existing->price);
    }

    public function test_import_skips_rows_with_missing_name(): void
    {
        $file = $this->createImportFile([
            ['', 'standard', 'queen', '35000', '', ''],
            ['   ', 'deluxe', 'king', '45000', '', ''],
        ]);

        $results = (new RoomTypeImportService())->import($file);

        $this->assertSame(2, $results['skipped']);
        $this->assertSame(0, $results['created']);
        $this->assertCount(2, $results['errors']);
        $this->assertStringContainsString('Name is required', $results['errors'][0]);
        $this->assertSame(0, RoomType::count());
    }

    public function test_import_skips_rows_with_invalid_room_type(): void
    {
        $file = $this->createImportFile([
            ['Penthouse Dream', 'penthouse', 'king', '35000', '', ''],
            ['Blank Type', '', 'queen', '25000', '', ''],
        ]);

        $results = (new RoomTypeImportService())->import($file);

        $this->assertSame(2, $results['skipped']);
        $this->assertStringContainsString('Invalid room_type', $results['errors'][0]);
        $this->assertStringContainsString('penthouse', $results['errors'][0]);
        $this->assertSame(0, RoomType::count());
    }

    public function test_import_skips_rows_with_invalid_price(): void
    {
        $file = $this->createImportFile([
            ['Not A Price', 'standard', '', 'abc', '', ''],
            ['Negative Price', 'standard', '', '-500', '', ''],
            ['Empty Price', 'standard', '', '', '', ''],
            ['Zero Price', 'standard', '', '0', '', ''],
        ]);

        $results = (new RoomTypeImportService())->import($file);

        $this->assertSame(4, $results['skipped']);
        $this->assertSame(0, $results['created']);
        $this->assertCount(4, $results['errors']);
        $this->assertStringContainsString('Price must be a positive number', $results['errors'][0]);
        $this->assertSame(0, RoomType::count());
    }

    public function test_import_parses_currency_formatted_prices(): void
    {
        $file = $this->createImportFile([
            ['Naira Room', 'standard', '', '₦35,000.50', '', ''],
        ]);

        $results = (new RoomTypeImportService())->import($file);

        $this->assertSame(1, $results['created']);
        $this->assertEquals(35000.5, (float) RoomType::where('slug', 'naira-room')->value('price'));
    }

    public function test_import_ignores_empty_rows(): void
    {
        $file = $this->createImportFile([
            ['Valid Room', 'standard', '', '30000', '', ''],
            ['', '', '', '', '', ''],
            [null, null, null, null, null, null],
            ['Another Valid Room', 'deluxe', '', '40000', '', ''],
        ]);

        $results = (new RoomTypeImportService())->import($file);

        $this->assertSame(2, $results['created']);
        $this->assertSame(0, $results['skipped']);
        $this->assertEmpty($results['errors']);
        $this->assertSame(2, RoomType::count());
    }

    public function test_import_fails_gracefully_when_headers_do_not_match(): void
    {
        $file = $this->createImportFile([
            ['Some Data', 'More Data'],
        ], ['wrong_header_one', 'wrong_header_two']);

        $results = (new RoomTypeImportService())->import($file);

        $this->assertSame(0, $results['created']);
        $this->assertNotEmpty($results['errors']);
        $this->assertStringContainsString('Could not match column headers', $results['errors'][0]);
        $this->assertSame(0, RoomType::count());
    }

    public function test_import_normalizes_case_and_whitespace(): void
    {
        $file = $this->createImportFile([
            ['  Deluxe Suite  ', ' DELUXE ', ' KING ', ' 55000 ', '', ''],
        ]);

        $results = (new RoomTypeImportService())->import($file);

        $this->assertSame(1, $results['created']);
        $this->assertEmpty($results['errors']);

        $roomType = RoomType::where('slug', 'deluxe-suite')->first();
        $this->assertNotNull($roomType);
        $this->assertSame('Deluxe Suite', $roomType->name);
        $this->assertSame('deluxe', $roomType->room_type);
        $this->assertSame('king', $roomType->bed_type);
        $this->assertEquals(55000.0, (float) $roomType->price);
    }

    public function test_import_defaults_are_applied_for_optional_columns(): void
    {
        $file = $this->createImportFile([
            ['Minimal Room', 'standard', '20000'],
        ], ['name', 'room_type', 'price']);

        $results = (new RoomTypeImportService())->import($file);

        $this->assertSame(1, $results['created']);

        $roomType = RoomType::where('slug', 'minimal-room')->first();
        $this->assertNotNull($roomType);
        $this->assertTrue($roomType->is_active);       // default true
        $this->assertFalse($roomType->is_featured);    // default false
        $this->assertSame(2, $roomType->base_guests);  // default 2
        $this->assertSame(1, $roomType->min_stay);     // default 1
        $this->assertSame(0, $roomType->sort_order);   // default 0
        $this->assertNotNull($roomType->description);  // fallback description generated
    }

    public function test_import_parses_boolean_flags(): void
    {
        $file = $this->createImportFile([
            ['Inactive Room', 'standard', '20000', 'false', 'false'],
            ['Featured Room', 'deluxe', '30000', 'true', 'yes'],
        ], ['name', 'room_type', 'price', 'is_active', 'is_featured']);

        $results = (new RoomTypeImportService())->import($file);

        $this->assertSame(2, $results['created']);

        $inactive = RoomType::where('slug', 'inactive-room')->first();
        $this->assertFalse($inactive->is_active);
        $this->assertFalse($inactive->is_featured);

        $featured = RoomType::where('slug', 'featured-room')->first();
        $this->assertTrue($featured->is_active);
        $this->assertTrue($featured->is_featured);
    }
}
