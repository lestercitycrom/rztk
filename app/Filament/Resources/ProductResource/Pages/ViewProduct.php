<?php
namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\ViewRecord;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;


    protected function getViewData(): array
    {
        return [
            'product' => $this->record,
        ];
    }


    protected static string $view = 'filament.resources.product-resource.pages.view-product';
}
