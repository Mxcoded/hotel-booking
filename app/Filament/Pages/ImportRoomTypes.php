<?php

namespace App\Filament\Pages;

use App\Enums\NavigationGroupEnum;
use App\Services\RoomTypeImportService;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

class ImportRoomTypes extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static ?string $navigationLabel = 'Import Rooms';

    protected static UnitEnum|string|null $navigationGroup = NavigationGroupEnum::HotelManagement;

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.import-room-types';

    protected static ?string $title = 'Import Room Types';

    public ?array $importResults = null;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                FileUpload::make('import_file')
                    ->label('Excel File')
                    ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                    ->maxSize(10240)
                    ->required()
                    ->disk('local')
                    ->directory('temp')
                    ->visibility('private'),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $file = $data['import_file'] ?? null;
        if (is_array($file)) {
            $file = $file[0] ?? null;
        }

        if (empty($file)) {
            Notification::make()
                ->title('No file selected')
                ->warning()
                ->send();
            return;
        }

        $fullPath = Storage::disk('local')->path($file);

        if (!file_exists($fullPath)) {
            Notification::make()
                ->title('File not found')
                ->danger()
                ->send();
            return;
        }

        $service = new RoomTypeImportService();
        $results = $service->import($fullPath);

        Storage::disk('local')->delete($file);

        $totalProcessed = $results['created'] + $results['updated'] + $results['skipped'];

        $this->importResults = $results;
        $this->form->fill([]);

        if ($totalProcessed === 0 && !empty($results['errors'])) {
            Notification::make()
                ->title('Import failed')
                ->body($results['errors'][0])
                ->danger()
                ->send();
            return;
        }

        $parts = [];
        if ($results['created'] > 0) $parts[] = "{$results['created']} created";
        if ($results['updated'] > 0) $parts[] = "{$results['updated']} updated";
        if ($results['skipped'] > 0) $parts[] = "{$results['skipped']} skipped";

        $notification = Notification::make()
            ->title('Import complete')
            ->body(implode(', ', $parts) . '.')
            ->success();

        if (!empty($results['errors'])) {
            $notification->body($notification->getBody() . ' ' . count($results['errors']) . ' warning(s).');
        }

        $notification->send();
    }

    public function getSampleDownloadUrl(): string
    {
        return route('admin.room-types.sample-download');
    }
}
