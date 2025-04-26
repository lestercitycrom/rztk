<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;


class Dashboard extends BaseDashboard
{

	protected ?string $heading = "";
	
	public function getColumns(): int | string | array
	{
		return [
			'sm' => 2,
			'md' => 3,
			'lg' => 5,
		];
	}

	public function getWidgets(): array
	{
		return [
			\App\Filament\Widgets\DashboardStats::class,
			\App\Filament\Widgets\ParseChart::class,
			\App\Filament\Widgets\ActiveLinksTable::class,
			\App\Filament\Widgets\RecentErrorsTable::class,
		];
	}
}
