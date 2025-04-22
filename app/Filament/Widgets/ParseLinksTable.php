<?php

namespace App\Filament\Widgets;

use App\Models\ParseLink;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;

class ParseLinksTable extends BaseWidget
{
    protected static ?string $heading = 'Активні посилання';
    protected int|string|array $columnSpan = 'full';
	protected function getTableQuery(): Builder
	{
		return ParseLink::query()->latest();
	}


    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('title')->searchable()->label('Назва'),
            TextColumn::make('last_parsed_page')->label('Остання'),
            BadgeColumn::make('status')
                ->colors(['success' => 'success', 'danger' => 'danger']),
            ToggleColumn::make('is_active')->label('Активний'),
        ];
    }

    /** Автообновление каждые 10 сек */
    protected function getPollingInterval(): ?string
    {
        return '10s';
    }
}
