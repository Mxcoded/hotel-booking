<?php

namespace App\Filament\Pages;

use App\Enums\NavigationGroupEnum;
use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use UnitEnum;

class PaymentSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Payment Settings';

    protected static UnitEnum|string|null $navigationGroup = NavigationGroupEnum::Settings;

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.payment-settings';

    protected static ?string $title = 'Payment Settings';

    public ?array $data = [];

    public function mount(): void
    {
        $keys = [
            'paystack_enabled',
            'paystack_mode',
            'paystack_public_key',
            'paystack_secret_key',
            'paystack_currency',
            'paystack_callback_url',
        ];

        $values = Setting::whereIn('key', $keys)->pluck('value', 'key')->all();

        $this->form->fill([
            'paystack_enabled' => filter_var($values['paystack_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'paystack_mode' => $values['paystack_mode'] ?? 'test',
            'paystack_public_key' => $values['paystack_public_key'] ?? null,
            'paystack_secret_key' => $values['paystack_secret_key'] ?? null,
            'paystack_currency' => $values['paystack_currency'] ?? 'NGN',
            'paystack_callback_url' => $values['paystack_callback_url'] ?? null,
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Paystack Gateway')
                    ->description('Configure the Paystack online payment gateway. Changes take effect immediately.')
                    ->schema([
                        Toggle::make('paystack_enabled')
                            ->label('Enable Paystack Payments')
                            ->helperText('Master switch for online card payments via Paystack.'),
                        Select::make('paystack_mode')
                            ->label('Mode')
                            ->options(['test' => 'Test', 'live' => 'Live'])
                            ->default('test')
                            ->helperText('Test mode uses sandbox keys; Live mode processes real payments.'),
                        TextInput::make('paystack_public_key')
                            ->label('Public Key')
                            ->placeholder('pk_live_... or pk_test_...')
                            ->helperText('Public key used on the payment page.'),
                        TextInput::make('paystack_secret_key')
                            ->label('Secret Key')
                            ->placeholder('sk_live_... or sk_test_...')
                            ->password()
                            ->revealable()
                            ->helperText('Secret key used for server-side verification. Store securely.'),
                        Select::make('paystack_currency')
                            ->label('Currency')
                            ->options([
                                'NGN' => 'NGN (Nigerian Naira)',
                                'GHS' => 'GHS (Ghanaian Cedi)',
                                'ZAR' => 'ZAR (South African Rand)',
                                'USD' => 'USD (US Dollar)',
                            ])
                            ->default('NGN'),
                        TextInput::make('paystack_callback_url')
                            ->label('Callback URL')
                            ->url()
                            ->placeholder('https://yourhotel.com/paystack/callback')
                            ->helperText('URL Paystack redirects to after a payment attempt.'),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $settings = [
            'paystack_enabled' => ($data['paystack_enabled'] ?? false) ? '1' : '0',
            'paystack_mode' => $data['paystack_mode'] ?? 'test',
            'paystack_public_key' => $data['paystack_public_key'] ?? null,
            'paystack_secret_key' => $data['paystack_secret_key'] ?? null,
            'paystack_currency' => $data['paystack_currency'] ?? 'NGN',
            'paystack_callback_url' => $data['paystack_callback_url'] ?? null,
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'group' => 'payment',
                    'label' => $this->labelFor($key),
                    'value' => $value,
                ]
            );
        }

        Cache::forget('settings');

        Notification::make()
            ->title('Payment settings saved')
            ->success()
            ->send();
    }

    protected function labelFor(string $key): string
    {
        return match ($key) {
            'paystack_enabled' => 'Enable Paystack Payments',
            'paystack_mode' => 'Paystack Mode',
            'paystack_public_key' => 'Paystack Public Key',
            'paystack_secret_key' => 'Paystack Secret Key',
            'paystack_currency' => 'Paystack Currency',
            'paystack_callback_url' => 'Paystack Callback URL',
            default => ucwords(str_replace('_', ' ', $key)),
        };
    }
}
