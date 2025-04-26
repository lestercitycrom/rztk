<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use App\Models\ParseLink;

class ActiveLinksTable extends BaseWidget implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $heading = 'Активні посилання';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('url')
                    ->label('URL')
                    ->limit(60)->wrap()
                    ->url('url', true)->icon('heroicon-m-arrow-top-right-on-square')
                    ->sortable(),

BadgeColumn::make('type')
	->label('Тип')
	->colors([
		'success' => 'vendor',
		'warning' => 'category',
	])
	->formatStateUsing(fn (string $state) => $state === 'vendor' ? 'продавець' : 'категорія')
	->sortable(),

                TextColumn::make('total_pages')->label('Сторінок')->sortable(),
                TextColumn::make('last_parsed_page')->label('Остання сторінка')->sortable(),

                TextColumn::make('last_parsed_at')
                    ->label('Оновлено')
                    ->since()
                    ->sortable(),

BadgeColumn::make('status')
	->label('Статус')
	->colors([
		'success' => 'active',
		'warning' => 'pending',
		'danger'  => 'error',
	])
	->formatStateUsing(fn (string $state) => match ($state) {
		'active'  => 'активне',
		'pending' => 'очікує',
		'error'   => 'помилка',
		default   => $state,
	})
	->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->poll(config('rozetka.dashboard_polling_interval'))
            ->paginated([10, 25, 50])
            ->query(ParseLink::where('is_active', true));
    }
}
