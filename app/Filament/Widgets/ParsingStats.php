<?php
namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\{Product, ParseError};
use Carbon\Carbon;

class ParsingStats extends StatsOverviewWidget
{
	protected ?string $heading = 'Статистика за хвилину';

	protected function getCards(): array
	{
		$since = Carbon::now()->subMinute();

		return [
			Card::make(
				'Успішно оброблено',
				Product::where('last_detail_parsed_at', '>=', $since)->count()
			)->description('товарів'),

			Card::make(
				'Помилки парсингу',
				ParseError::where('created_at', '>=', $since)->count()
			)->description('за хвилину')
			 ->color('danger'),
		];
	}
}
