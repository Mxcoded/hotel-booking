<?php

namespace App\Filament\Resources\RoomUnitResource\RelationManagers;

use App\Models\RoomAvailability;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AvailabilitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'availabilities';

    protected static ?string $recordTitleAttribute = 'date';

    protected static ?string $title = 'Availability Calendar';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->required()
                    ->displayFormat('Y-m-d'),
                Forms\Components\Select::make('status')
                    ->options([
                        'available' => 'Available',
                        'booked' => 'Booked',
                        'blocked' => 'Blocked',
                        'maintenance' => 'Maintenance',
                    ])
                    ->required()
                    ->default('available'),
                Forms\Components\TextInput::make('price_override')
                    ->label('Price Override (₦)')
                    ->numeric()
                    ->prefix('₦')
                    ->step(100)
                    ->minValue(0)
                    ->placeholder('Leave blank to use dynamic pricing'),
                Forms\Components\Textarea::make('note')
                    ->rows(2)
                    ->placeholder('Reason for block/maintenance, special event, etc.'),
            ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('date')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'available',
                        'danger' => 'booked',
                        'warning' => 'blocked',
                        'info' => 'maintenance',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_override')
                    ->money('NGN')
                    ->label('Override Price')
                    ->placeholder('Dynamic'),
                Tables\Columns\TextColumn::make('note')
                    ->limit(40)
                    ->toggleable(),
            ])
            ->defaultSort('date')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'booked' => 'Booked',
                        'blocked' => 'Blocked',
                        'maintenance' => 'Maintenance',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}