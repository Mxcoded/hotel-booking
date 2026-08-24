<?php

namespace App\Filament\Widgets;

use App\Models\Visitor;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class VisitorChartWidget extends ChartWidget
{
    protected ?string $heading = 'Unique Visitors (Last 7 Days)';

    protected static ?int $sort = 2;

    protected ?string $description = 'Daily unique visitors tracked by IP address';

    protected int | string | array $columnSpan = 'full';

    protected ?array $options = [
        'scales' => [
            'y' => [
                'beginAtZero' => true,
                'ticks' => [
                    'stepSize' => 1,
                ],
            ],
        ],
        'plugins' => [
            'legend' => [
                'display' => false,
            ],
        ],
        'responsive' => true,
        'maintainAspectRatio' => false,
    ];

    protected function getData(): array
    {
        $visitors = Visitor::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(DISTINCT ip_address) as unique_visits')
        )
            ->where('created_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->format('D, M j');
            $visit = $visitors->firstWhere('date', $date);
            $data[] = $visit ? $visit->unique_visits : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Unique Visitors',
                    'data' => $data,
                    'borderColor' => 'rgba(245, 158, 11, 1)',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'borderWidth' => 3,
                    'tension' => 0.4,
                    'fill' => true,
                    'pointBackgroundColor' => 'rgba(245, 158, 11, 1)',
                    'pointRadius' => 5,
                    'pointHoverRadius' => 7,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}