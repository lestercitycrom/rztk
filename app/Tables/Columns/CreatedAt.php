<?php

namespace App\Tables\Columns;

use Filament\Tables\Columns\TextColumn;

class CreatedAt extends TextColumn 
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Коли')
            ->since() 
            ->sortable();
    }
	
    public static function make(string $name = 'created_at'): static 
    {
        return parent::make($name);
    }	

}