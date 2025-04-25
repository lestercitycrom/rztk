<?php
namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\ParseError;

class ErrorsLastMinute extends StatsOverviewWidget
{
	protected ?string $heading = 'Помилок за хвилину';

	protected function getCards(): array
	{
		$since = now()->subMinute();

		return [
			Card::make('Errors', ParseError::where('created_at', '>=', $since)->count())
				->description('повідомлень')
				->color('danger'),
		];
	}
}
