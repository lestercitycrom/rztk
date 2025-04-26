<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\ParseError;

class RecentErrorsTable extends BaseWidget implements Tables\Contracts\HasTable
{
	use Tables\Concerns\InteractsWithTable;

	protected static ?string $heading = 'Последние ошибки';
	protected int | string | array $columnSpan = 'full';

	public function table(Table $table): Table
	{
		return $table
			->columns([
				TextColumn::make('message')
					->label('Ошибка')
					->wrap()
					->limit(120)
					->tooltip(fn ($record) => $record->message),
				TextColumn::make('created_at')
					->label('Время')
					->since(),
			])
			->defaultSort('created_at', 'desc')
			->paginated([10]) // 10 записей на страницу
			->poll(config('rozetka.dashboard_polling_interval', '10s'))
			->query(ParseError::query()->latest());
	}
}
