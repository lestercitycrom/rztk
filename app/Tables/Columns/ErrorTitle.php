<?php

namespace App\Tables\Columns;

use Filament\Tables\Columns\TextColumn;

class ErrorTitle extends TextColumn 
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Парсер')
            ->limit(60)
            ->wrap()
            ->url(fn ($record) => $record->link->url, true) 
            ->icon('heroicon-m-arrow-top-right-on-square')
            ->sortable()
            ->searchable();
    }

    public static function make(string $name = 'link.title'): static 
    {
        return parent::make($name);
    }
}