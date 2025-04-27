<?php

namespace App\Filament\Resources\ParseLinkResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

use App\Tables\Columns\{CreatedAt, ErrorTitle};

class ErrorsRelationManager extends RelationManager
{
    protected static string $relationship = 'errors';
    protected static ?string $title = 'Помилки';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
					ErrorTitle::make(),
					TextColumn::make('message')->label('Помилка')->wrap()->limit(200),
					CreatedAt::make()
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('stage')
                    ->label('Етап')
                    ->options([
                        'category' => 'Категорія',
                        'product'  => 'Товар',
                    ]),
            ]);
    }
}
