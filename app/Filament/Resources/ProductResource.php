<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\{TextColumn, IconColumn};
use App\Tables\Columns\{CreatedAt, ProductTitle};
use Filament\Tables\Actions\Action;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationLabel = 'Товари';
	protected static ?string $modelLabel = "Товар";
	protected static ?string $pluralModelLabel = "Товари";	
	protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
					ProductTitle::make(),

					TextColumn::make('category_id')
						->label('Категорія')
						->formatStateUsing(fn (mixed $state, Product $record): string => 
							"c{$record->category_id} – {$record->category?->title}"
						)->sortable(),

					TextColumn::make('price')
						->label('Ціна')
						->money('UAH', true)->sortable(),

					TextColumn::make('old_price')
						->label('Стара ціна')
						->money('UAH', true)->sortable(),

					CreatedAt::make('created_at'),

					CreatedAt::make('updated_at')->label('Оновлено')
            ])
            ->actions([
                Action::make('view')
                    ->label('Детальніше')
                    ->icon('heroicon-o-chevron-down')
                    ->url(fn (Product $record): string => static::getUrl('view', ['record' => $record])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'view'  => Pages\ViewProduct::route('/{record}'),
        ];
    }
}
