<?php

namespace App\Filament\Resources;

use App\Enums\NavigationGroupEnum;
use App\Filament\Resources\RoomTypeResource\Pages;
use App\Filament\Resources\RoomTypeResource\RelationManagers;
use App\Models\RoomType;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class RoomTypeResource extends Resource
{
    protected static ?string $model = RoomType::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static UnitEnum|string|null $navigationGroup = NavigationGroupEnum::HotelManagement;

    protected static ?string $navigationLabel = 'Room Types';

    protected static ?string $modelLabel = 'Room Type';

    protected static ?string $pluralModelLabel = 'Room Types';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->dehydrated()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('room_type')
                            ->options([
                                'standard' => 'Standard',
                                'deluxe' => 'Deluxe',
                                'suite' => 'Suite',
                                'executive' => 'Executive',
                                'presidential' => 'Presidential',
                            ])
                            ->default('standard')
                            ->required(),
                        Forms\Components\Select::make('bed_type')
                            ->options([
                                'king' => 'King',
                                'queen' => 'Queen',
                                'twin' => 'Twin (2 Singles)',
                                'double' => 'Double',
                                'sofa_bed' => 'Sofa Bed',
                            ])
                            ->searchable(),
                        Forms\Components\TextInput::make('room_size')
                            ->label('Room Size (sqm)')
                            ->numeric()
                            ->minValue(1)
                            ->suffix('m²'),
                    ])->columns(3),

                Section::make('Description')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Capacity & Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('base_guests')
                            ->label('Base Guests Included')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(2),
                        Forms\Components\TextInput::make('max_guests')
                            ->label('Maximum Guests')
                            ->numeric()
                            ->minValue(1),
                        Forms\Components\TextInput::make('extra_guest_fee')
                            ->label('Extra Guest Fee (₦)')
                            ->numeric()
                            ->prefix('₦')
                            ->minValue(0)
                            ->default(0)
                            ->step(100),

                        Forms\Components\TextInput::make('price')
                            ->label('Base Price/Night (₦)')
                            ->required()
                            ->numeric()
                            ->prefix('₦')
                            ->minValue(0)
                            ->step(100),
                        Forms\Components\TextInput::make('weekend_price')
                            ->label('Weekend Price (Fri/Sat) (₦)')
                            ->numeric()
                            ->prefix('₦')
                            ->minValue(0)
                            ->step(100),
                        Forms\Components\TextInput::make('holiday_price')
                            ->label('Holiday Price (₦)')
                            ->numeric()
                            ->prefix('₦')
                            ->minValue(0)
                            ->step(100),
                        Forms\Components\TextInput::make('discount_percent')
                            ->label('Discount %')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->default(0)
                            ->suffix('%'),
                        Forms\Components\DatePicker::make('discount_start')
                            ->label('Discount Start'),
                        Forms\Components\DatePicker::make('discount_end')
                            ->label('Discount End'),

                        Forms\Components\KeyValue::make('seasonal_pricing')
                            ->label('Seasonal Pricing')
                            ->keyLabel('Period Name|Start to End (e.g., "Harmattan|2024-12-01 to 2025-02-28")')
                            ->valueLabel('Price (₦)')
                            ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->filter(fn ($value, $key) => is_string($key) && filled($key))->map(fn ($value) => filled($value) ? (string) $value : null)->all() : $state)
                            ->columnSpanFull(),
                    ])->columns(4),

                Section::make('Booking Rules')
                    ->schema([
                        Forms\Components\TextInput::make('min_stay')
                            ->label('Min Stay (nights)')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(1),
                        Forms\Components\TextInput::make('max_stay')
                            ->label('Max Stay (nights)')
                            ->numeric()
                            ->minValue(1),
                        Forms\Components\TextInput::make('advance_booking_days')
                            ->label('Advance Booking (days)')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(365),
                        Forms\Components\TimePicker::make('check_in_time')
                            ->label('Check-in Time')
                            ->default('14:00')
                            ->seconds(false),
                        Forms\Components\TimePicker::make('check_out_time')
                            ->label('Check-out Time')
                            ->default('11:00')
                            ->seconds(false),
                    ])->columns(5),

                Section::make('Status & Display')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured on Homepage')
                            ->default(false),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                    ])->columns(3),

                Section::make('Main Image')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->imageEditor()
                            ->directory('room-types')
                            ->maxSize(5120)
                            ->columnSpanFull(),
                    ]),

                Section::make('Features')
                    ->schema([
                        Forms\Components\KeyValue::make('features')
                            ->reorderable()
                            ->addActionLabel('Add Feature')
                            ->keyLabel('Feature')
                            ->valueLabel('Icon (FontAwesome class)')
                            ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->filter(fn ($value, $key) => is_string($key) && filled($key))->map(fn ($value, $key) => ['name' => (string) $key, 'icon' => filled($value) ? (string) $value : ''])->values()->all() : $state)
                            ->columnSpanFull(),
                    ])->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('room_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'standard' => 'gray',
                        'deluxe' => 'info',
                        'suite' => 'warning',
                        'executive' => 'primary',
                        'presidential' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('bed_type')
                    ->label('Bed')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('price')
                    ->money('NGN')
                    ->sortable()
                    ->label('Base Price'),
                Tables\Columns\TextColumn::make('base_guests')
                    ->label('Base Guests')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_guests')
                    ->label('Max Guests')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('units_count')
                    ->label('Units')
                    ->counts('units')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('room_type')
                    ->options([
                        'standard' => 'Standard',
                        'deluxe' => 'Deluxe',
                        'suite' => 'Suite',
                        'executive' => 'Executive',
                        'presidential' => 'Presidential',
                    ]),
                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\UnitsRelationManager::class,
            RelationManagers\MediaRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoomTypes::route('/'),
            'create' => Pages\CreateRoomType::route('/create'),
            'edit' => Pages\EditRoomType::route('/{record}/edit'),
        ];
    }
}