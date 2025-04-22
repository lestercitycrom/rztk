<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Product;
use App\Models\Category;

class Counters extends StatsOverviewWidget
{
    protected ?string $heading = 'Статистика';

    protected function getCards(): array
    {
        return [
            Card::make('Товарів', Product::count()),
            Card::make('Категорій', Category::count()),
        ];
    }
}
