<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('/')
            ->login()
            ->brandName('Gymie')
            ->colors([
                'primary' => [
                    50 => '248, 11, 252', // Closest to #F8FAFC (very light gray)
                    100 => '248, 11, 252', // Closest to #F8FAFC (very light gray)
                    200 => '203, 11, 225', // Lighter shade of #CBD5E1 (light blue-gray)
                    300 => '180, 11, 210', // Slightly darker shade of #CBD5E1
                    400 => '150, 11, 190', // Darker shade of #CBD5E1
                    500 => '10, 10, 10',    // Exactly #CBD5E1 (light blue-gray)
                    600 => '50, 50, 50',    // Lighter shade of #0A0A0A (very dark gray)
                    700 => '10, 10, 10',    // Exactly #0A0A0A (very dark gray)
                    800 => '10, 10, 10',    // Exactly #0A0A0A (very dark gray)
                    900 => '10, 10, 10',    // Exactly #0A0A0A (very dark gray)
                    950 => '248, 250, 252', // Closest to #F8FAFC (very light gray)
                ],
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->sidebarWidth('12rem')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
