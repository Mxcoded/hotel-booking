<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class FoodMenuManager extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Food Menu';

    protected static ?string $navigationGroup = 'Hotel Management';

    protected static ?int $navigationSort = 6;

    protected static string $view = 'filament.pages.food-menu-manager';

    protected static ?string $title = 'Manage Food Menu';

    private const MENU_KEY = 'food_menu_pdf';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('menu_pdf')
                    ->label('Food Menu PDF')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(10240)
                    ->required()
                    ->disk('public')
                    ->directory('menus')
                    ->visibility('public'),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $file = $data['menu_pdf'] ?? null;
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

        $menuSetting = Setting::where('key', self::MENU_KEY)->first();

        if ($menuSetting && $menuSetting->value && Storage::disk('public')->exists($menuSetting->value)) {
            Storage::disk('public')->delete($menuSetting->value);
        }

        Setting::updateOrCreate(
            ['key' => self::MENU_KEY],
            ['value' => $file, 'type' => 'file']
        );

        Cache::forget('settings');

        $this->form->fill([]);

        Notification::make()
            ->title('Food menu PDF uploaded successfully.')
            ->success()
            ->send();
    }

    public function removeMenu(): void
    {
        $menuSetting = Setting::where('key', self::MENU_KEY)->first();

        if ($menuSetting) {
            if ($menuSetting->value && Storage::disk('public')->exists($menuSetting->value)) {
                Storage::disk('public')->delete($menuSetting->value);
            }
            $menuSetting->update(['value' => null]);
            Cache::forget('settings');
        }

        Notification::make()
            ->title('Food menu PDF removed successfully.')
            ->success()
            ->send();
    }

    public function hasMenu(): bool
    {
        return filled(setting(self::MENU_KEY));
    }

    public function getMenuUrl(): string
    {
        return asset('storage/' . setting(self::MENU_KEY));
    }
}
