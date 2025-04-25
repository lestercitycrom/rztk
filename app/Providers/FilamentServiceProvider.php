<?php

namespace App\Providers;

use Filament\Events\ServingFilament;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
	public function boot(): void
	{
		Filament::serving(function (ServingFilament $event): void {
			// Inject Tailwind Play CDN + config into EVERY Filament page <head>
			FilamentView::registerRenderHook(
				PanelsRenderHook::HEAD_START,
				fn (): HtmlString => new HtmlString(<<<'HTML'
					<script src="https://cdn.tailwindcss.com"></script>
					<script>
						tailwind.config = {
							darkMode: 'media',
							theme: {
								extend: {
									colors: {
										primary: '#06b6d4',
										secondary: '#0891b2'
									}
								}
							}
						}
					</script>
				HTML)
			);
		});
	}
}
