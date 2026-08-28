<?php

namespace App\Filament\Resources;

use App\Enums\NavigationGroupEnum;
use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-ticket';

    protected static UnitEnum|string|null $navigationGroup = NavigationGroupEnum::Reservations;

    protected static ?string $navigationLabel = 'Promo Codes';

    protected static ?string $modelLabel = 'Promo Code';

    protected static ?string $pluralModelLabel = 'Promo Codes';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('Coupon')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->helperText('Guests enter this code at booking. e.g. SUMMER20'),
                        Forms\Components\TextInput::make('description')
                            ->maxLength(255),
                    ])->columns(2),
                Forms\Components\Section::make('Discount')
                    ->schema([
                        Forms\Components\Select::make('discount_type')
                            ->options([
                                Coupon::TYPE_PERCENT => 'Percentage (%)',
                                Coupon::TYPE_FIXED => 'Fixed amount (₦)',
                            ])
                            ->default(Coupon::TYPE_PERCENT)
                            ->required(),
                        Forms\Components\TextInput::make('discount_value')
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                        Forms\Components\TextInput::make('min_booking_amount')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->label('Minimum booking value (₦)'),
                    ])->columns(3),
                Forms\Components\Section::make('Validity & Usage')
                    ->schema([
                        DatePicker::make('valid_from'),
                        DatePicker::make('valid_until'),
                        Forms\Components\TextInput::make('max_uses')
                            ->numeric()
                            ->minValue(1)
                            ->nullable()
                            ->placeholder('Unlimited'),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(40)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('discount_type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === Coupon::TYPE_PERCENT ? '%' : '₦'),
                Tables\Columns\TextColumn::make('discount_value')
                    ->numeric(),
                Tables\Columns\TextColumn::make('used_count')
                    ->label('Used')
                    ->suffix(fn (Coupon $record) => $record->max_uses ? " / {$record->max_uses}" : '')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                Tables\Columns\TextColumn::make('valid_until')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
