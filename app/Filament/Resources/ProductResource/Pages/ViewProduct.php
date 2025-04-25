<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\ProductResource;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    // Показываем заголовок страницы = название товара
    public function getTitle(): string
    {
        return $this->record->title;
    }

    protected static string $view = 'filament.resources.product-resource.pages.view-product';
}
