<?php

namespace App\Filament\Resources;

use App\Enums\NavigationGroupEnum;
use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static UnitEnum|string|null $navigationGroup = NavigationGroupEnum::Settings;

    protected static ?string $navigationLabel = 'Site Settings';

    protected static ?string $modelLabel = 'Setting';

    protected static ?string $pluralModelLabel = 'Settings';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Setting Details')
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->dehydrated()
                            ->helperText('Unique key used in code via setting(\'key\')'),
                        Forms\Components\TextInput::make('label')
                            ->maxLength(255)
                            ->helperText('Human-readable name shown in admin panel'),
                        Forms\Components\Textarea::make('description')
                            ->rows(2)
                            ->helperText('Explains what this setting does for other admins'),
                        Forms\Components\Select::make('group')
                            ->options([
                                'brand'    => 'Brand Identity',
                                'hero'     => 'Hero Section',
                                'contact'  => 'Contact Information',
                                'social'   => 'Social Media',
                                'seo'      => 'SEO',
                                'business' => 'Business',
                                'footer'   => 'Footer',
                                'general'  => 'General',
                            ])
                            ->required()
                            ->default('general')
                            ->helperText('Organizational group for filtering'),
                        Forms\Components\Select::make('type')
                            ->options([
                                'text'   => 'Text',
                                'number' => 'Number',
                                'color'  => 'Color',
                                'image'  => 'Image',
                                'video'  => 'Video',
                                'file'   => 'File',
                            ])
                            ->required()
                            ->default('text')
                            ->reactive(),
                        Forms\Components\TextInput::make('sort')
                            ->label('Sort Order')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->helperText('Display order within group (lower = first)'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Disable to hide without deleting'),
                        Forms\Components\KeyValue::make('options')
                            ->visible(fn (Forms\Get $get) => in_array($get('type'), ['text', 'number']))
                            ->keyLabel('Key')
                            ->valueLabel('Value')
                            ->addActionLabel('Add Option')
                            ->helperText('Options for select/radio inputs (JSON)')
                            ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->filter(fn ($value, $key) => is_string($key) && filled($key))->map(fn ($value) => filled($value) ? (string) $value : null)->all() : $state),
                    ])->columns(2),

                Forms\Components\Section::make('Value')
                    ->schema([
                        Forms\Components\ColorPicker::make('value')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'color'),
                        Forms\Components\Textarea::make('value')
                            ->rows(4)
                            ->visible(fn (Forms\Get $get) => in_array($get('type'), ['text', 'number'])),
                        Forms\Components\FileUpload::make('file_upload')
                            ->label('File')
                            ->directory('settings')
                            ->maxSize(10240)
                            ->visible(fn (Forms\Get $get) => in_array($get('type'), ['image', 'video', 'file']))
                            ->afterStateHydrated(function (Forms\Components\FileUpload $component, $state, $record): void {
                                if ($record && in_array($record->type, ['image', 'video', 'file']) && filled($record->value)) {
                                    $component->state($record->value);
                                } else {
                                    $component->state(null);
                                }
                            })
                            ->dehydrated(false),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'brand'    => 'warning',
                        'hero'     => 'info',
                        'contact'  => 'success',
                        'social'   => 'primary',
                        'seo'      => 'gray',
                        'business' => 'danger',
                        'footer'   => 'gray',
                        default    => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('label')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Setting $record): string => $record->description ?? ''),
                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'text'   => 'gray',
                        'number' => 'info',
                        'color'  => 'warning',
                        'image'  => 'success',
                        'video'  => 'warning',
                        'file'   => 'danger',
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort')
                    ->label('Order')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('value')
                    ->limit(50)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('group')
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->options([
                        'brand'    => 'Brand Identity',
                        'hero'     => 'Hero Section',
                        'contact'  => 'Contact Information',
                        'social'   => 'Social Media',
                        'seo'      => 'SEO',
                        'business' => 'Business',
                        'footer'   => 'Footer',
                        'general'  => 'General',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'text'   => 'Text',
                        'number' => 'Number',
                        'color'  => 'Color',
                        'image'  => 'Image',
                        'video'  => 'Video',
                        'file'   => 'File',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive'),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}