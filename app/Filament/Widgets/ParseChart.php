<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;
use App\Models\Product;
use App\Models\ParseError;

class ParseChart extends ChartWidget
{
    protected static ?string $heading = 'Динаміка парсингу';
    protected int | string | array $columnSpan = 'full';
    public bool $lazy = false;
    public ?string $filter = 'hour';

    protected function getPollingInterval(): ?string
    {
        return config('rozetka.dashboard_polling_interval');
    }

    protected function getFilters(): ?array
    {
        return [
            'hour' => 'Остання година',
            'day'  => 'Останні 24 г',
            'week' => 'Останні 7 дн',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getContainerAttributes(): array
    {
        return ['style' => 'width:100%; height:900px;'];
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'spanGaps'            => false,
            'elements'            => [
                'line'  => ['borderWidth' => 2, 'tension' => 0.4, 'fill' => false],
                'point' => ['radius' => 3],
            ],
            'scales' => ['y' => ['beginAtZero' => true]],
        ];
    }

    protected function getData(): array
    {
        $end = now();
        [$start, $method, $fmt] = match ($this->filter) {
            'hour' => [$end->copy()->subHour(),   'perMinute', 'H:i'],
            'day'  => [$end->copy()->subDay(),    'perHour',   'd M H:00'],
            default=> [$end->copy()->subDays(7),  'perDay',    'd M'],
        };

        $ok  = Trend::model(Product::class)->between(start: $start, end: $end)->{$method}()->count();
        $bad = Trend::model(ParseError::class)->between(start: $start, end: $end)->{$method}()->count();

        $labels  = $ok->map(fn (TrendValue $v) => Carbon::parse($v->date)->format($fmt));

        return [
            'labels'   => $labels->toArray(),
            'datasets' => [
                [
                    'label'       => 'Успішні завантаження',
                    'data'        => $ok->pluck('aggregate')->toArray(),
                    'borderColor' => 'rgb(34,197,94)',
                    'showLine'    => true,
                ],
                [
                    'label'       => 'Помилки завантаження',
                    'data'        => $bad->pluck('aggregate')->toArray(),
                    'borderColor' => 'rgb(239,68,68)',
                    'showLine'    => true,
                ],
            ],
        ];
    }
}
