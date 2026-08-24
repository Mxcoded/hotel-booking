<?php

namespace App\Filament\Resources\RoomTypeResource\RelationManagers;

use App\Models\RoomUnit;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class UnitsRelationManager extends RelationManager
{
    protected static string $relationship = 'units';

    protected static ?string $recordTitleAttribute = 'unit_number';

    protected static ?string $title = 'Room Units';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('unit_number')
                    ->label('Unit Number')
                    ->required()
                    ->maxLength(20)
                    ->placeholder('e.g., 101, 202'),
                Forms\Components\TextInput::make('floor')
                    ->label('Floor')
                    ->numeric()
                    ->minValue(0),
                Forms\Components\Select::make('status')
                    ->options([
                        'available' => 'Available',
                        'occupied' => 'Occupied',
                        'maintenance' => 'Maintenance',
                        'out_of_order' => 'Out of Order',
                        'housekeeping' => 'Housekeeping',
                    ])
                    ->required()
                    ->default('available'),
                Forms\Components\Textarea::make('notes')
                    ->rows(2)
                    ->placeholder('Unit-specific notes'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('unit_number')
            ->columns([
                Tables\Columns\TextColumn::make('unit_number')
                    ->label('Unit')
                    ->weight('bold')
                    ->sortable(),
                Tables\Columns\TextColumn::make('floor')
                    ->label('Floor')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'available',
                        'danger' => 'occupied',
                        'warning' => 'maintenance',
                        'gray' => 'out_of_order',
                        'info' => 'housekeeping',
                    ])
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),
                Tables\Columns\TextColumn::make('notes')
                    ->limit(30)
                    ->toggleable(),
            ])
            ->defaultSort('unit_number')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'occupied' => 'Occupied',
                        'maintenance' => 'Maintenance',
                        'out_of_order' => 'Out of Order',
                        'housekeeping' => 'Housekeeping',
                    ]),
                Tables\Filters\SelectFilter::make('floor')
                    ->options(fn () => RoomUnit::query()->where('floor', '!=', null)->distinct()->pluck('floor', 'floor')->toArray())
                    ->label('Floor'),
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