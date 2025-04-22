<?php
namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Pages\Page;

class ParserSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationGroup = 'Парсер Rozetka';
    protected static ?string $navigationLabel = 'Налаштування';
    protected static string $view = 'filament.pages.parser-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Setting::firstOrFail()->toArray());
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('request_delay')->label('Затримка (мс)')
                ->numeric()->required()->minValue(100),
            Forms\Components\TextInput::make('details_per_category')->label('Детальних запитів')
                ->numeric()->required()->minValue(1),
        ];
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        Setting::first()->update($data);
        $this->notify('success','Налаштування збережено');
    }
}
