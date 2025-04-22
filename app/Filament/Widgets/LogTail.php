<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\File;

class LogTail extends Widget
{
    protected static string $view = 'filament.widgets.log-tail';
    protected int|string|array $columnSpan = 'full';

    public function getLines(): array
    {
        $log = storage_path('logs/laravel.log');
        if (! File::exists($log)) return [];

        $lines = explode("\n", trim(File::get($log)));
        return array_slice($lines, -20);   // последние 20
    }

    protected function getPollingInterval(): ?string
    {
        return '10s';  // автоподгрузка каждые 10 секунд
    }
}
