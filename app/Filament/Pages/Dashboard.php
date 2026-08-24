<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    public function getHeading(): string|Htmlable
    {
        return 'Dashboard';
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\HotelStatsWidget::class,
            \App\Filament\Widgets\BookingFunnelWidget::class,
            \App\Filament\Widgets\VisitorChartWidget::class,
            \App\Filament\Widgets\RecentContactsWidget::class,
            \App\Filament\Widgets\RecentFeedbackWidget::class,
        ];
    }
}
