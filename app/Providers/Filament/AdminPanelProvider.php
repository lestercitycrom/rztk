<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\{
	Authenticate,
	AuthenticateSession,
	DisableBladeIconComponents,
	DispatchServingFilamentEvent
};
use Illuminate\Cookie\Middleware\{
	EncryptCookies,
	AddQueuedCookiesToResponse
};
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;

class AdminPanelProvider extends PanelProvider
{
	public function panel(Panel $panel): Panel
	{
		return $panel
			->default()
			->id('admin')
			->path('/')                                   // dashboard on "/"
			->brandName('Rozetka Parser')
			->brandLogo(asset('assets/filament/rozetka_parser_logo_cool.svg'))
			->darkModeBrandLogo(asset('assets/filament/rozetka_parser_logo_cool_dark_transparent.svg'))
			->brandLogoHeight('36px')
			->favicon(asset('assets/filament/favicon.ico'))
			->colors(['primary' => Color::Amber])
			->login()
			->profile()
			->discoverResources(
				app_path('Filament/Resources'),
				'App\\Filament\\Resources'
			)
			->discoverPages(
				app_path('Filament/Pages'),
				'App\\Filament\\Pages'
			)
			->discoverWidgets(                             // register widgets
				app_path('Filament/Widgets'),
				'App\\Filament\\Widgets'
			)
			->pages([
				\App\Filament\Pages\Dashboard::class,    // our dashboard page
			])
			->middleware([
				EncryptCookies::class,
				AddQueuedCookiesToResponse::class,
				StartSession::class,
				AuthenticateSession::class,
				ShareErrorsFromSession::class,
				VerifyCsrfToken::class,
				SubstituteBindings::class,
				DisableBladeIconComponents::class,
				DispatchServingFilamentEvent::class,
			])
			->authMiddleware([
				Authenticate::class,
			])
			->topNavigation();                            // use top nav
	}
}
