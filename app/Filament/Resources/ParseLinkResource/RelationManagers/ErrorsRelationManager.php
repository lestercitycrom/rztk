<?php
/*
namespace App\Filament\Resources\ParseLinkResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class ErrorsRelationManager extends RelationManager
{
	protected static string $relationship = 'errors';
	protected static ?string $title = 'Помилки';

	public static function table(Table $table): Table
	{
		return $table
			->columns([
				Tables\Columns\TextColumn::make('stage')
					->label('Етап')
					->sortable(),
				Tables\Columns\TextColumn::make('message')
					->label('Повідомлення')
					->wrap()
					->searchable(),
				Tables\Columns\TextColumn::make('created_at')
					->label('Коли')
					->dateTime()
					->sortable(),
			])
			->defaultSort('created_at', 'desc')
			->filters([
				Tables\Filters\SelectFilter::make('stage')
					->options(['category'=>'category','product'=>'product']),
			]);
	}
}
*/


namespace App\Filament\Resources\ParseLinkResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class ErrorsRelationManager extends RelationManager
{
    protected static string $relationship = 'errors';
    protected static ?string $title = 'Помилки';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('stage')
                    ->label('Етап')
                    ->sortable(),
                TextColumn::make('message')
                    ->label('Повідомлення')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Коли')
                    ->dateTime()
                    ->sortable(),
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
