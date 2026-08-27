<?php

namespace App\Filament\Pages;

use App\Enums\NavigationGroupEnum;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use UnitEnum;

class AuditLog extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Audit Log';

    protected static UnitEnum|string|null $navigationGroup = NavigationGroupEnum::Settings;

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.audit-log';

    protected static ?string $title = 'Audit Log';

    public function table(Table $table): Table
    {
        return $table
            ->query(Activity::query()->with(['causer', 'subject']))
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
                        'deleted' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('subject')
                    ->label('On')
                    ->getStateUsing(function (Activity $record) {
                        if (!$record->subject_type) {
                            return '—';
                        }

                        $short = str_replace('App\\Models\\', '', $record->subject_type);

                        return $short . ' #' . $record->subject_id;
                    }),
                TextColumn::make('causer.name')
                    ->label('By')
                    ->getStateUsing(fn (Activity $record) => $record->causer instanceof User
                        ? $record->causer->name
                        : ($record->causer ? class_basename($record->causer) . ' #' . $record->causer->getKey() : 'System')),
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
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => $this->logLabel($state)),
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
                    ->options($this->logAreas()),
            ]);
    }

    /**
     * Human-readable labels for each audit area.
     */
    protected function logLabel(?string $logName): string
    {
        $label = config('activitylog_labels.' . $logName);

        return $label ?? Str::headline($logName ?? '');
    }

    /**
     * All log areas currently present, labelled for the filter dropdown.
     */
    protected function logAreas(): array
    {
        return Activity::query()
            ->distinct()
            ->whereNotNull('log_name')
            ->pluck('log_name')
            ->mapWithKeys(fn ($name) => [$name => $this->logLabel($name)])
            ->all();
    }

    protected function stringify(mixed $value): string
    {
        if (is_array($value)) {
            return json_encode($value) ?: '';
        }

        return is_scalar($value) || $value === null ? (string) $value : '';
    }
}
