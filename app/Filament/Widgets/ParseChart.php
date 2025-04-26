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
	// widget heading
	protected static ?string $heading = 'Успешные и неуспешные парсинги';

	// default filter
	public ?string $filter = 'hour';

	// polling interval from config/rozetka.php
	protected function getPollingInterval(): ?string
	{
		return config('rozetka.dashboard_polling_interval', '10s');
	}

	// filter options
	protected function getFilters(): ?array
	{
		return [
			'hour' => 'Последний час',
			'day'  => 'Последние 24 ч',
		];
	}

	// chart type
	protected function getType(): string
	{
		return 'line';
	}

	// build dataset
	protected function getData(): array
	{
		$end = Carbon::now();

		if ($this->filter === 'hour') {
			$start = Carbon::now()->subHour();

			$successTrend = Trend::model(Product::class)
				->between(start: $start, end: $end)
				->perMinute()
				->count();

			$errorTrend = Trend::model(ParseError::class)
				->between(start: $start, end: $end)
				->perMinute()
				->count();

			$labels = $successTrend->map(fn (TrendValue $v) => Carbon::parse($v->date)->format('H:i'));
			$success = $successTrend->map(fn (TrendValue $v) => $v->aggregate);
			$errors  = $errorTrend->map(fn (TrendValue $v) => $v->aggregate);
		} else {
			$start = Carbon::now()->subDay();

			$successTrend = Trend::model(Product::class)
				->between(start: $start, end: $end)
				->perHour()
				->count();

			$errorTrend = Trend::model(ParseError::class)
				->between(start: $start, end: $end)
				->perHour()
				->count();

			$labels = $successTrend->map(fn (TrendValue $v) => Carbon::parse($v->date)->format('d M H:00'));
			$success = $successTrend->map(fn (TrendValue $v) => $v->aggregate);
			$errors  = $errorTrend->map(fn (TrendValue $v) => $v->aggregate);
		}

		return [
			'labels'   => $labels->toArray(),
			'datasets' => [
				[
					'label' => 'Успешно',
					'data'  => $success->toArray(),
				],
				[
					'label' => 'Ошибки',
					'data'  => $errors->toArray(),
				],
			],
		];
	}
}
