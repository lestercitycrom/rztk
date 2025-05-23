<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\ParseError;

use App\Tables\Columns\{CreatedAt, ErrorTitle};

class RecentErrorsTable extends BaseWidget implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $heading = 'Останні помилки';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->columns([

				ErrorTitle::make(),

                TextColumn::make('message')->label('Помилка')->wrap()->limit(200),

				CreatedAt::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll(config('rozetka.dashboard_polling_interval'))
            ->paginated([10, 25])
            ->query(ParseError::with('link')->latest());
    }
}
