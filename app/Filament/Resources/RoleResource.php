<?php

namespace App\Filament\Resources;

use App\Enums\NavigationGroupEnum;
use App\Filament\Resources\RoleResource\Pages;
use App\Support\PermissionGroups;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use UnitEnum;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static UnitEnum|string|null $navigationGroup = NavigationGroupEnum::Administration;

    protected static ?string $navigationLabel = 'Roles';

    protected static ?string $modelLabel = 'Role';

    protected static ?string $pluralModelLabel = 'Roles';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return static::shouldSkipAuthorization() || auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        $permissionGroups = PermissionGroups::groupedPermissionOptions();

        $fields = [
            TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
        ];

        foreach ($permissionGroups as $label => $options) {
            $fields[] = Section::make($label)
                ->collapsible()
                ->collapsed()
                ->schema([
                    CheckboxList::make('perm_' . Str::slug($label, '_'))
                        ->label('')
                        ->options($options)
                        ->columns(3)
                        ->bulkToggleable()
                        ->searchable(),
                ]);
        }

        return $schema
            ->schema($fields);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->sortable(),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->label('Permissions')
                    ->counts('permissions')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('name')
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
