<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class AuditLog extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Audit Log';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.audit-log';

    protected static ?string $title = 'Audit Log';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Activity::query()
                    ->whereIn('log_name', ['reservations', 'room_types'])
                    ->with(['causer', 'subject'])
            )
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100])
            ->columns([
                TextColumn::make('created_at')
                    ->label('When')
                    ->since()
                    ->dateTimeTooltip()
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable(),
                TextColumn::make('event')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->color(fn (string $state) => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        default => 'danger',
                    }),
                TextColumn::make('subject')
                    ->label('On')
                    ->getStateUsing(function (Activity $record) {
                        $short = class_basename($record->subject_type);

                        return $short && $record->subject_id ? "#{$record->subject_id} · {$short}" : '—';
                    }),
                TextColumn::make('causer.name')
                    ->label('By')
                    ->default('System / Guest')
                    ->getStateUsing(fn (Activity $record) => $record->causer instanceof User
                        ? $record->causer->name
                        : ($record->causer ? class_basename($record->causer) . ' #' . $record->causer->getKey() : 'Guest')),
                TextColumn::make('changes')
                    ->label('Changes')
                    ->limit(60)
                    ->tooltip(function (Activity $record) {
                        $attrs = $record->attribute_changes['attributes'] ?? [];
                        $old = $record->attribute_changes['old'] ?? [];

                        if (empty($attrs)) {
                            return '';
                        }

                        return collect($attrs)
                            ->map(fn ($new, $field) => sprintf(
                                '%s: %s%s',
                                $field,
                                isset($old[$field]) ? $this->stringify($old[$field]) . ' → ' : '',
                                $this->stringify($new),
                            ))
                            ->implode("\n");
                    })
                    ->formatStateUsing(function (Activity $record) {
                        $attrs = $record->attribute_changes['attributes'] ?? [];

                        if (empty($attrs)) {
                            return '—';
                        }

                        $fields = array_keys($attrs);
                        $summary = implode(', ', array_slice($fields, 0, 4));

                        return count($fields) > 4 ? $summary . ', …' : $summary;
                    }),
                TextColumn::make('log_name')
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),
                SelectFilter::make('log_name')
                    ->label('Area')
                    ->options([
                        'reservations' => 'Reservations',
                        'room_types' => 'Room Types',
                    ]),
            ]);
    }

    protected function stringify(mixed $value): string
    {
        if (is_array($value)) {
            return json_encode($value) ?: '';
        }

        return is_scalar($value) || $value === null ? (string) $value : '';
    }
}
