<?php

namespace App\Providers;

use Filament\Events\ServingFilament;
use Filament\Facades\Filament;
use Illuminate\Support\HtmlString;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
	public function boot(): void
	{
		Filament::serving(function (ServingFilament $event): void {
			// Inject external CSS into the HEAD of every Filament page
			dd("Hello");
			Filament::registerRenderHook(
				'head.end',
				fn (): HtmlString => new HtmlString(<<<'HTML'
					<!-- Tailwind CSS core -->
					<link
						rel="stylesheet"
						href="https://cdn.jsdelivr.net/npm/tailwindcss@3/dist/tailwind.min.css"
					/>
					<!-- Your demo styles from TailwindFlex -->
					<link
						rel="stylesheet"
						href="https://tailwindflex.com/@abhirajk/shopping-cart-page-product.css"
					/>
				HTML)
			);
		});
	}
}
