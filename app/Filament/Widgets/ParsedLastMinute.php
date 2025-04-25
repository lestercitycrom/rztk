<?php
namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Product;

class ParsedLastMinute extends StatsOverviewWidget
{
	protected ?string $heading = 'Успішно парсів за хвилину';

	protected function getCards(): array
	{
		$since = now()->subMinute();

		return [
			Card::make('Parsed', Product::where('last_detail_parsed_at', '>=', $since)->count())
				->description('товарів')
				->color('success'),
		];
	}
}
