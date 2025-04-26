<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class ParserSettings extends Page implements HasForms
{
	use InteractsWithForms;

	// sidebar group
	//protected static ?string $navigationGroup = 'Парсер Rozetka';
	// sidebar label
	protected static ?string $navigationLabel = 'Налаштування';
	// sidebar icon
	protected static ?string $navigationIcon = 'heroicon-o-cog';
	// page title
	protected static ?string $title           = 'Налаштування';
	// blade view path
	protected static string  $view            = 'filament.pages.parser-settings';

	public array $data = [];

	public function mount(): void
	{
		$settings = Setting::firstOrFail();
		$this->data = $settings->toArray();
		$this->form->fill($this->data);
	}

	public function form(Form $form): Form
	{
		return $form
			->schema([
				TextInput::make('request_delay')
					->label('Затримка (мс)')
					->numeric()
					->required()
					->minValue(100),
				TextInput::make('details_per_category')
					->label('Детальних запитів')
					->numeric()
					->required()
					->minValue(1),
			])
			->statePath('data');
	}

	public function submit(): void
	{
		Setting::first()->update($this->data);

		Notification::make()
			->success()
			->title('Налаштування збережено')
			->send();

		$this->form->fill($this->data);
	}
}
