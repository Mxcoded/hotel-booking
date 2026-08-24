<?php

namespace App\Filament\Widgets;

use App\Models\Reservation;
use App\Models\Visitor;
use App\Models\WhatsappLead;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class BookingFunnelWidget extends Widget
{
    protected string $view = 'filament.widgets.booking-funnel';

    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public int $days = 30;

    /**
     * @return array<int, array{label: string, count: int, conversion: float|null, color: string}>
     */
    public function getStepsProperty(): array
    {
        $since = now()->subDays($this->days)->startOfDay();

        $visitors = Visitor::where('visited_date', '>=', $since->toDateString())
            ->distinct('ip_address')
            ->count('ip_address');

        $leads = WhatsappLead::where('created_at', '>=', $since)->count();

        $requests = Reservation::where('created_at', '>=', $since)->count();

        $confirmed = Reservation::where('created_at', '>=', $since)
            ->confirmed()
            ->count();

        $steps = [
            ['label' => 'Unique Visitors', 'count' => $visitors, 'color' => 'bg-indigo-500'],
            ['label' => 'WhatsApp Leads', 'count' => $leads, 'color' => 'bg-green-500'],
            ['label' => 'Booking Requests', 'count' => $requests, 'color' => 'bg-amber-500'],
            ['label' => 'Confirmed Stays', 'count' => $confirmed, 'color' => 'bg-emerald-600'],
        ];

        $max = max(1, ...array_column($steps, 'count'));

        return array_map(function ($step, $i) use ($steps, $max) {
            $previous = $i > 0 ? max(1, $steps[$i - 1]['count']) : null;

            return [
                'label' => $step['label'],
                'count' => $step['count'],
                'color' => $step['color'],
                'width' => round(($step['count'] / $max) * 100),
                'conversion' => $previous !== null ? round(($step['count'] / $previous) * 100, 1) : null,
            ];
        }, $steps, array_keys($steps));
    }

    public function getOverallConversionProperty(): float
    {
        $steps = $this->steps;

        return ($steps[0]['count'] ?? 0) > 0
            ? round((end($steps)['count'] / $steps[0]['count']) * 100, 1)
            : 0.0;
    }

    public function getPeriodLabelProperty(): string
    {
        return Carbon::now()->subDays($this->days)->format('d M') . ' – ' . now()->format('d M Y');
    }
}
