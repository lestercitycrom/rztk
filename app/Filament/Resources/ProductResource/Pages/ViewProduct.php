<?php
namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\Page;
use Filament\Support\Colors\Color;
use Filament\Forms as Forms;
use Filament\Tables as Tables;

class ViewProduct extends Page
{
    protected static string $resource = ProductResource::class;
    protected static string $view = 'filament.resources.product-resource.pages.view-product';
}
