<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;
use App\Models\Category;
use App\Models\ParseLink;
use App\Models\ParseError;

class DashboardStats extends StatsOverviewWidget
{
	// dynamic polling from config
	protected function getPollingInterval(): ?string
	{
		return config('rozetka.dashboard_polling_interval', '10s');
	}

	protected function getStats(): array
	{
		$totalProducts   = Product::count();
		$activeLinks     = ParseLink::where('is_active', true)->count();
		$recentErrors    = ParseError::where('created_at', '>=', now()->subMinutes(10))->count();
		$recentParsed    = Product::where('created_at', '>=', now()->subMinutes(10))->count();
		$totalCategories = Category::count();

		return [
			Stat::make('Все товары', $totalProducts),
			Stat::make('Активные ссылки', $activeLinks),
			Stat::make('Ошибки (10 мин)', $recentErrors)->color('danger'),
			Stat::make('Спарсено (10 мин)', $recentParsed)->color('success'),
			Stat::make('Категории', $totalCategories),
		];
	}
}
