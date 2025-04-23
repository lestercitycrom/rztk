<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationGroup = 'Каталог';
    protected static ?string $navigationLabel = 'Товари';

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Назва')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('url')
                    ->label('URL')
                    ->url(fn (Product $record): string => $record->url)
                    ->icon('heroicon-o-link')
                    ->openUrlInNewTab(),

                TextColumn::make('category_id')
                    ->label('Категорія')
                    ->formatStateUsing(fn (mixed $state, Product $record): string => 
                        "c{$record->category_id} – {$record->category?->title}"
                    ),

                TextColumn::make('price')
                    ->label('Ціна')
                    ->money('UAH', true),

                TextColumn::make('old_price')
                    ->label('Стара ціна')
                    ->money('UAH', true),

                TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime(),

                TextColumn::make('updated_at')
                    ->label('Оновлено')
                    ->dateTime(),
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
