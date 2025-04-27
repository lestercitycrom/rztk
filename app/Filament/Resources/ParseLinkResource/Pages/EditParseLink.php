<?php

namespace App\Filament\Resources\ParseLinkResource\Pages;

use App\Filament\Resources\ParseLinkResource;
use Filament\Resources\Pages\EditRecord;

class EditParseLink extends EditRecord
{
    protected static string $resource = ParseLinkResource::class;
	protected static ?string $title = 'Створити посилання на парсинг';
	
	protected function getRedirectUrl(): string
	{
		return static::getResource()::getUrl('index');
	}	
}
