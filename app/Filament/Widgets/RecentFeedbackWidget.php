<?php

namespace App\Filament\Widgets;

use App\Models\Feedback;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentFeedbackWidget extends TableWidget
{
    protected static ?string $heading = 'Pending Feedback Approval';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $description = 'Guest feedback awaiting your review';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Feedback::query()
                    ->where('is_approved', false)
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->default('Anonymous')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('rating')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('message')
                    ->limit(60)
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Action::make('view')
                    ->url(fn (Feedback $record): string => route('filament.admin.resources.feedback.view', $record))
                    ->icon('heroicon-o-eye')
                    ->color('primary'),
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Feedback $record) => $record->update(['is_approved' => true])),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Feedback $record) => $record->delete()),
            ])
            ->emptyStateHeading('No pending feedback')
            ->emptyStateDescription('All guest feedback has been reviewed.')
            ->emptyStateIcon('heroicon-o-star');
    }
}