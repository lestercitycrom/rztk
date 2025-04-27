<?php

namespace App\Tables\Columns;

use Filament\Tables\Columns\TextColumn;

class ProductTitle extends TextColumn
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Назва')
            ->limit(60)
            ->wrap()
            ->url('url', true)  
            ->icon('heroicon-m-arrow-top-right-on-square')
            ->sortable();
    }

    public static function make(string $name = 'title'): static
    {
        return parent::make($name);
    }
}