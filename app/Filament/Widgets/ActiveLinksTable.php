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

	protected static ?string $heading = 'Активные ссылки';
	protected int | string | array $columnSpan = 'full';

	public function table(Table $table): Table
	{
		return $table
			->columns([
				TextColumn::make('url')
					->label('Ссылка')
					->searchable()
					->limit(60)
					->wrap(),
				BadgeColumn::make('status')
					->label('Статус')
					->colors([
						'warning' => 'pending',
						'success' => 'active',
						'danger'  => 'error',
					])
					->icons([
						'heroicon-o-minus-circle' => 'pending',
						'heroicon-o-check-circle' => 'active',
						'heroicon-o-x-circle'     => 'error',
					]),
			])
			->defaultSort('updated_at', 'desc')
			->poll(config('rozetka.dashboard_polling_interval', '10s'))
			->query(ParseLink::where('is_active', true));
	}
}
