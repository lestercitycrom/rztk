<?php
namespace App\Filament\Resources\ParseLinkResource\Pages;

use App\Filament\Resources\ParseLinkResource;
use Filament\Resources\Pages\CreateRecord;

class CreateParseLink extends CreateRecord
{
    protected static string $resource = ParseLinkResource::class;
	protected static ?string $title = 'Створити посилання на парсинг';
	
	protected function getRedirectUrl(): string
	{
		return static::getResource()::getUrl('index');
	}	
}
