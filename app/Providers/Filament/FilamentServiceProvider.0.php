<?php

namespace App\Providers\Filament;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Css;
use Illuminate\Support\HtmlString;

class FilamentServiceProvider extends ServiceProvider
{
	public function boot(): void
	{
		
		/*
		// Подключаем сгенерированный Tailwind CSS
		FilamentAsset::register([
			Css::make('tailwind', asset('css/tailwind.css')),
		]);
*/
		// Стили для горизонтального меню
		Filament::serving(function (): void {
			Filament::registerRenderHook(
				'head.end',
				fn (): HtmlString => new HtmlString(<<<'HTML'
					<style>
						/* wrap layout to allow horizontal nav */
						.filament-app-layout {
							display: flex;
							flex-direction: column;
						}
						/* turn sidebar into top nav */
						.filament-sidebar {
							display: flex !important;
							flex-direction: row !important;
							width: 100% !important;
							position: relative !important;
							height: auto !important;
						}
						/* remove left margin from main content */
						.filament-main-content {
							margin-left: 0 !important;
						}
						/* align items, center text */
						.filament-sidebar .filament-sidebar-item {
							flex: 1 1 auto;
							text-align: center;
						}
					</style>
				HTML)
			);
		});
	}
}
