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
use Filament\Schemas\Components\Section;
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
                Section::make('Guest')
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
                Section::make('Stay')
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
                            ->options([
                                Reservation::STATUS_PENDING => 'Pending',
                                Reservation::STATUS_CONFIRMED => 'Confirmed',
                                Reservation::STATUS_CANCELLED => 'Cancelled',
                            ])
                            ->required(),
                    ])->columns(3),
                Section::make('Details')
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
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->color(fn (string $state) => match ($state) {
                        Reservation::STATUS_PENDING => 'warning',
                        Reservation::STATUS_CONFIRMED => 'success',
                        default => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        Reservation::STATUS_PENDING => 'Pending',
                        Reservation::STATUS_CONFIRMED => 'Confirmed',
                        Reservation::STATUS_CANCELLED => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('room_type')
                    ->relationship('roomType', 'name')
                    ->label('Room type'),
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
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Infolists\Components\Section::make('Guest')
                    ->schema([
                        Infolists\Components\TextEntry::make('guest_name'),
                        Infolists\Components\TextEntry::make('guest_email'),
                        Infolists\Components\TextEntry::make('guest_phone'),
                    ])->columns(3),
                Infolists\Components\Section::make('Stay')
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
                    ])->columns(4),
                Infolists\Components\Section::make('Details')
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