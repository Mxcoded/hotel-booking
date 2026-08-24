<?php

namespace App\Filament\Widgets;

use App\Models\Contact;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentContactsWidget extends TableWidget
{
    protected static ?string $heading = 'Recent Contact Messages';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $description = 'Latest unread messages from guests';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Contact::query()
                    ->where('is_read', false)
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('message')
                    ->limit(60)
                    ->wrap(),
                Tables\Columns\IconColumn::make('is_read')
                    ->boolean()
                    ->label('Read'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Action::make('view')
                    ->url(fn (Contact $record): string => route('filament.admin.resources.contacts.view', $record))
                    ->icon('heroicon-o-eye')
                    ->color('primary'),
                Action::make('markRead')
                    ->label('Mark as Read')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Contact $record) => $record->update(['is_read' => true])),
            ])
            ->emptyStateHeading('No unread messages')
            ->emptyStateDescription('All caught up! No new contact messages to review.')
            ->emptyStateIcon('heroicon-o-envelope');
    }
}