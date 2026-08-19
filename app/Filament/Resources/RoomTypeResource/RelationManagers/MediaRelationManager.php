<?php

namespace App\Filament\Resources\RoomTypeResource\RelationManagers;

use App\Models\RoomMedia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MediaRelationManager extends RelationManager
{
    protected static string $relationship = 'media';

    protected static ?string $recordTitleAttribute = 'file_path';

    protected static ?string $title = 'Media Gallery';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('file_path')
                    ->label('File')
                    ->image()
                    ->imageEditor()
                    ->directory('room-types')
                    ->maxSize(5120)
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('type')
                    ->options([
                        'image' => 'Image',
                        'video' => 'Video',
                    ])
                    ->required()
                    ->default('image'),
                Forms\Components\TextInput::make('caption')
                    ->maxLength(255)
                    ->placeholder('Optional caption'),
                Forms\Components\TextInput::make('alt_text')
                    ->maxLength(255)
                    ->placeholder('Alt text for accessibility/SEO'),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
            ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file_path')
            ->columns([
                Tables\Columns\ImageColumn::make('file_path')
                    ->disk('public')
                    ->circular()
                    ->label('Preview'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'image' ? 'success' : 'info'),
                Tables\Columns\TextColumn::make('caption')
                    ->limit(30)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
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
}