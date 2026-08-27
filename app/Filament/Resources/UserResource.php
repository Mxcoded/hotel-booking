<?php

namespace App\Filament\Resources;

use App\Enums\NavigationGroupEnum;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Support\PermissionGroups;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Section as FormSection;
use Filament\Schemas\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    protected static UnitEnum|string|null $navigationGroup = NavigationGroupEnum::Administration;

    protected static ?string $navigationLabel = 'Users';

    protected static ?string $modelLabel = 'User';

    protected static ?string $pluralModelLabel = 'Users';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return static::shouldSkipAuthorization() || parent::canAccess();
    }

    public static function form(Schema $schema): Schema
    {
        $permissionGroups = PermissionGroups::groupedPermissionOptions();

        $fields = [
            FormSection::make('Account Details')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn (string $state): string => bcrypt($state))
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->maxLength(255),
                    Forms\Components\Toggle::make('email_verified_at')
                        ->label('Email Verified')
                        ->dehydrated(fn (bool $state): ?\Illuminate\Support\Carbon => $state ? now() : null),
                ]),
        ];

        $fields[] = FormSection::make('Roles')
            ->schema([
                CheckboxList::make('roles')
                    ->relationship('roles', 'name')
                    ->label('Assign Role(s)')
                    ->options(fn (): array => \Spatie\Permission\Models\Role::orderBy('name')->pluck('name', 'id')->all())
                    ->columns(3)
                    ->bulkToggleable()
                    ->searchable(),
            ]);

        // One labelled group per module
        foreach ($permissionGroups as $label => $options) {
            $fields[] = FormSection::make($label)
                ->collapsible()
                ->collapsed()
                ->schema([
                    CheckboxList::make('perm_' . Str::slug($label))
                        ->label(fn (): string => '')
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
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'admin' => 'warning',
                        'manager' => 'info',
                        'receptionist' => 'success',
                        'housekeeping' => 'gray',
                        default => 'gray',
                    })
                    ->label('Roles'),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->boolean()
                    ->label('Verified'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->label('Role'),
            ])
            ->actions([
                EditAction::make(),
                ViewAction::make(),
                DeleteAction::make()
                    ->visible(fn (User $record): bool => $record->id !== auth()->id()),
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
            InfolistSection::make('User Details')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email'),
                        TextEntry::make('email_verified_at')
                            ->label('Email Verified')
                            ->formatStateUsing(fn ($state): string => $state ? 'Yes' : 'No'),
                        TextEntry::make('created_at')->dateTime(),
                    ])->columns(2),
                InfolistSection::make('Roles & Permissions')
                    ->schema([
                        TextEntry::make('roles.name')
                            ->badge()
                            ->label('Roles'),
                        TextEntry::make('permissions.name')
                            ->badge()
                            ->label('Direct Permissions'),
                    ])->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
