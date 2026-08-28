<?php

namespace App\Filament\Resources;

use App\Enums\NavigationGroupEnum;
use App\Filament\Resources\ReservationResource\Pages;
use App\Models\Reservation;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section as FormSection;
use Filament\Schemas\Components\Section as InfolistSection;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static UnitEnum|string|null $navigationGroup = NavigationGroupEnum::Reservations;

    protected static ?string $navigationLabel = 'Booking Requests';

    protected static ?string $modelLabel = 'Booking Request';

    protected static ?string $pluralModelLabel = 'Booking Requests';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FormSection::make('Guest')
                    ->schema([
                        Forms\Components\TextInput::make('guest_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('guest_email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('guest_phone')
                            ->required()
                            ->maxLength(32),
                    ])->columns(3),
                FormSection::make('Stay')
                    ->schema([
                        Forms\Components\Select::make('room_type_id')
                            ->relationship('roomType', 'name')
                            ->required()
                            ->searchable(),
                        Forms\Components\DatePicker::make('check_in')
                            ->required(),
                        Forms\Components\DatePicker::make('check_out')
                            ->required()
                            ->afterOrEqual('check_in'),
                        Forms\Components\TextInput::make('guests')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(20)
                            ->required(),
                        Forms\Components\TextInput::make('quoted_total')
                            ->numeric()
                            ->prefix('₦'),
                        Forms\Components\Select::make('status')
                            ->options(Reservation::STATUS_OPTIONS)
                            ->required(),
                    ])->columns(3),
                FormSection::make('Details')
                    ->schema([
                        Forms\Components\Textarea::make('special_requests')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('source')
                            ->disabled()
                            ->dehydrated(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('guest_name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Reservation $record) => $record->guest_phone),
                Tables\Columns\TextColumn::make('roomType.name')
                    ->label('Room type')
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_in')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_out')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nights')
                    ->state(fn (Reservation $record) => (int) $record->check_in->diffInDays($record->check_out))
                    ->suffix(' night(s)'),
                Tables\Columns\TextColumn::make('guests')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quoted_total')
                    ->money('NGN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => \Illuminate\Support\Str::headline(str_replace('_', ' ', $state)))
                    ->color(fn (string $state) => match ($state) {
                        Reservation::STATUS_PENDING => 'warning',
                        Reservation::STATUS_CONFIRMED => 'success',
                        Reservation::STATUS_CHECKED_IN => 'info',
                        Reservation::STATUS_CHECKED_OUT => 'gray',
                        Reservation::STATUS_NO_SHOW => 'danger',
                        Reservation::STATUS_CANCELLED => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state ? ucfirst($state) : '—')
                    ->color(fn (?string $state) => match ($state) {
                        Reservation::PAYMENT_PAID => 'success',
                        Reservation::PAYMENT_PARTIAL => 'warning',
                        Reservation::PAYMENT_REFUNDED => 'gray',
                        default => 'danger',
                    })
                    ->toggleable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Reservation::STATUS_OPTIONS),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        Reservation::PAYMENT_UNPAID => 'Unpaid',
                        Reservation::PAYMENT_PARTIAL => 'Partially paid',
                        Reservation::PAYMENT_PAID => 'Paid',
                        Reservation::PAYMENT_REFUNDED => 'Refunded',
                    ]),
                Tables\Filters\SelectFilter::make('room_type')
                    ->relationship('roomType', 'name')
                    ->label('Room type'),
                Tables\Filters\TernaryFilter::make('arriving_today')
                    ->label('Arriving today')
                    ->queries(
                        true: fn ($query) => $query->arriving(),
                        false: fn ($query) => $query->where(function ($q) {
                            $q->where('check_in', '!=', now()->toDateString())->orWhereNotIn('status', [Reservation::STATUS_CONFIRMED, Reservation::STATUS_PENDING]);
                        }),
                    )
                    ->placeholder('All bookings'),
            ])
            ->actions([
                Action::make('confirm')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Reservation $record) => $record->isPending())
                    ->action(fn (Reservation $record) => $record->markConfirmed()),
                Action::make('cancel')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Reservation $record) => $record->status !== Reservation::STATUS_CANCELLED)
                    ->action(fn (Reservation $record) => $record->markCancelled()),
                Action::make('check_in')
                    ->icon('heroicon-m-arrow-right-circle')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (Reservation $record) => in_array($record->status, [Reservation::STATUS_CONFIRMED, Reservation::STATUS_PENDING], true))
                    ->action(fn (Reservation $record) => $record->markCheckedIn()),
                Action::make('check_out')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Reservation $record) => $record->status === Reservation::STATUS_CHECKED_IN)
                    ->action(fn (Reservation $record) => $record->markCheckedOut()),
                Action::make('no_show')
                    ->icon('heroicon-m-user-minus')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Reservation $record) => in_array($record->status, [Reservation::STATUS_CONFIRMED, Reservation::STATUS_PENDING], true))
                    ->action(fn (Reservation $record) => $record->markNoShow()),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('bulk_confirm')
                        ->label('Confirm selected')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->isPending()) {
                                    $record->markConfirmed();
                                }
                            }
                        }),
                    \Filament\Actions\BulkAction::make('bulk_cancel')
                        ->label('Cancel selected')
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->status !== Reservation::STATUS_CANCELLED) {
                                    $record->markCancelled();
                                }
                            }
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                InfolistSection::make('Guest')
                    ->schema([
                        Infolists\Components\TextEntry::make('guest_name'),
                        Infolists\Components\TextEntry::make('guest_email'),
                        Infolists\Components\TextEntry::make('guest_phone'),
                    ])->columns(3),
                InfolistSection::make('Stay')
                    ->schema([
                        Infolists\Components\TextEntry::make('roomType.name'),
                        Infolists\Components\TextEntry::make('check_in')->date('D, d M Y'),
                        Infolists\Components\TextEntry::make('check_out')->date('D, d M Y'),
                        Infolists\Components\TextEntry::make('nights')
                            ->state(fn (Reservation $record) => (int) $record->check_in->diffInDays($record->check_out)),
                        Infolists\Components\TextEntry::make('guests')->numeric(),
                        Infolists\Components\TextEntry::make('quoted_total')->money('NGN'),
                        Infolists\Components\TextEntry::make('status')->badge(),
                        Infolists\Components\TextEntry::make('confirmed_at')->dateTime()->placeholder('—'),
                        Infolists\Components\TextEntry::make('checked_in_at')->dateTime()->placeholder('—'),
                        Infolists\Components\TextEntry::make('checked_out_at')->dateTime()->placeholder('—'),
                    ])->columns(4),
                InfolistSection::make('Payment')
                    ->schema([
                        Infolists\Components\TextEntry::make('payment_status')->badge(),
                        Infolists\Components\TextEntry::make('amount_paid')->money('NGN'),
                        Infolists\Components\TextEntry::make('balance_due')
                            ->state(fn (Reservation $record) => $record->balanceDue())
                            ->money('NGN'),
                        Infolists\Components\TextEntry::make('payment_method')->placeholder('—'),
                        Infolists\Components\TextEntry::make('payment_reference')->placeholder('—'),
                        Infolists\Components\TextEntry::make('coupon_code')->badge()->placeholder('—'),
                    ])->columns(3),
                InfolistSection::make('Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('special_requests')
                            ->prose()
                            ->placeholder('None')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('source')->badge(),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'view' => Pages\ViewReservation::route('/{record}'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}