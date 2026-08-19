<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomUnitResource\Pages;
use App\Filament\Resources\RoomUnitResource\RelationManagers;
use App\Models\RoomUnit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RoomUnitResource extends Resource
{
    protected static ?string $model = RoomUnit::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationGroup = 'Hotel Management';

    protected static ?string $navigationLabel = 'Room Units';

    protected static ?string $modelLabel = 'Room Unit';

    protected static ?string $pluralModelLabel = 'Room Units';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Unit Details')
                    ->schema([
                        Forms\Components\Select::make('room_type_id')
                            ->label('Room Type')
                            ->relationship('roomType', 'name', fn ($query) => $query->where('is_active', true))
                            ->required()
                            ->searchable()
                            ->preload(),
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
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('roomType.name')
                    ->label('Room Type')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('unit_number')
                    ->label('Unit')
                    ->sortable()
                    ->weight('bold'),
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
            ->defaultSort(fn ($query) => $query->join('room_types', 'room_units.room_type_id', '=', 'room_types.id')->orderBy('room_types.sort_order'))
            ->filters([
                Tables\Filters\SelectFilter::make('room_type_id')
                    ->label('Room Type')
                    ->relationship('roomType', 'name')
                    ->preload(),
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
                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AvailabilitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoomUnits::route('/'),
            'create' => Pages\CreateRoomUnit::route('/create'),
            'edit' => Pages\EditRoomUnit::route('/{record}/edit'),
        ];
    }
}