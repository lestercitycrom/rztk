<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationGroup = 'Каталог';
    protected static ?string $navigationLabel = 'Товари';
    protected static ?string $pluralLabel = 'Товари';

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('rozetka_id')->label('ID')->sortable(),
                TextColumn::make('title')
                    ->label('Назва')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('category.title')->label('Категорія'),
                TextColumn::make('price')
                    ->label('Ціна')
                    ->money('UAH', true)
                    ->sortable(),
                IconColumn::make('in_stock')
                    ->label('Наявність')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Оновлено')
                    ->since(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Категорія')
                    ->relationship('category', 'title'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Детально')
                    ->icon('heroicon-o-eye')
                    ->url(fn(Product $record) => ProductResource::getUrl('view', ['record' => $record])),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make(),
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
