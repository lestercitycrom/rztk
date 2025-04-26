<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;
use App\Models\Category;
use App\Models\ParseLink;
use App\Models\ParseError;
use Illuminate\Support\Carbon;

class DashboardStats extends StatsOverviewWidget
{
	// один-единственный источник интервала
	protected function getPollingInterval(): ?string
	{
		return config('rozetka.dashboard_polling_interval');
	}

	protected function getStats(): array
	{
		$windowStart = Carbon::now()->subMinutes(10);   // ровно 10 минут
		$newProd  = Product::where('created_at', '>=', $windowStart)->count();
		$newErrs  = ParseError::where('created_at', '>=', $windowStart)->count();
		$newReq   = ParseLink::where('last_parsed_at', '>=', $windowStart)->count();
		$newReq  += Product::where('updated_at', '>=', $windowStart)->count();

		return [
			Stat::make('Товари', Product::count())
				->icon('heroicon-m-archive-box')
				->description('Усього товарів')
				->color('primary'),

			Stat::make('Категорії', Category::count())
				->icon('heroicon-m-rectangle-group')
				->description('Усього категорій')
				->color('info'),

			Stat::make('Активні парсера', ParseLink::where('is_active', true)->count())
				->icon('heroicon-m-link')
				->description('Парсер працює')
				->color('success'),

			Stat::make('Нові товари (10 хв)', $newProd)
				->icon('heroicon-m-arrow-trending-up')
				->description('Додано за 10 хв')
				->color('emerald'),

			Stat::make('Оновлено (10 хв)', $newReq)
				->icon('heroicon-m-server-stack')
				->description('Кількість оновлень за за 10 хв')
				->color('amber'),

			Stat::make('Помилки (10 хв)', $newErrs)
				->icon('heroicon-m-exclamation-triangle')
				->description('Критичні за 10 хв')
				->color('danger'),
		];
	}
}
