<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitorResource\Pages;
use App\Models\Visitor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VisitorResource extends Resource
{
    protected static ?string $model = Visitor::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Visitors';

    protected static ?string $modelLabel = 'Visitor';

    protected static ?string $pluralModelLabel = 'Visitors';

    protected static ?int $navigationSort = 2;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ip_address')
                    ->maxLength(45),
                Forms\Components\DatePicker::make('visited_date')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ip_address')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('visited_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('visited_date', 'desc')
            ->filters([
                Tables\Filters\Filter::make('visited_date')
                    ->form([
                        Forms\Components\DatePicker::make('visited_from'),
                        Forms\Components\DatePicker::make('visited_until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['visited_from'], fn ($q, $date) => $q->whereDate('visited_date', '>=', $date))
                            ->when($data['visited_until'], fn ($q, $date) => $q->whereDate('visited_date', '<=', $date));
                    }),
            ])
            ->actions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVisitors::route('/'),
        ];
    }
}
