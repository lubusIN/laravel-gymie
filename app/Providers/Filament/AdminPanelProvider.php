<?php

namespace App\Providers\Filament;

use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use App\Filament\Pages\Settings;
use App\Filament\Resources\Enquiries\EnquiryResource;
use App\Filament\Resources\FollowUps\FollowUpResource;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Filament\Resources\Members\MemberResource;
use App\Filament\Resources\Plans\PlanResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Filament\Resources\Subscriptions\SubscriptionResource;
use App\Filament\Resources\Users\UserResource;
use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
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
        $panel = $this->basePanel($panel);
        $panel->navigation(fn(NavigationBuilder $builder) => $this->buildNavigation($builder));
        return $panel;
    }

    public function basePanel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('/')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->passwordReset()
            ->brandName('Gymie')
            ->unsavedChangesAlerts()
            ->colors($this->colors())
            ->darkMode(false)
            ->sidebarWidth('12rem')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
                Settings::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->plugins([FilamentShieldPlugin::make()
                ->navigationIcon(fn(): null => null)
                ->activeNavigationIcon(fn(): null => null)])
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
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->databaseNotifications()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k']);
    }

    protected function buildNavigation(NavigationBuilder $builder): NavigationBuilder
    {
        $administration = [
            ...Settings::getNavigationItems(),
            ...UserResource::getNavigationItems(),
            ...RoleResource::getNavigationItems(),
        ];

        $sales = [
            ...EnquiryResource::getNavigationItems(),
            ...FollowUpResource::getNavigationItems(),
        ];

        $billing = [
            ...InvoiceResource::getNavigationItems(),
        ];

        $memberships = [
            ...MemberResource::getNavigationItems(),
            ...PlanResource::getNavigationItems(),
            ...ServiceResource::getNavigationItems(),
            ...SubscriptionResource::getNavigationItems(),
        ];

        return $builder
            ->groups([
                NavigationGroup::make('Sales')
                    ->icon('heroicon-o-shopping-cart')
                    ->items($sales)
                    ->collapsed(false),

                NavigationGroup::make('Memberships')
                    ->icon('heroicon-o-user-group')
                    ->items($memberships)
                    ->collapsed(false),

                NavigationGroup::make('Billing')
                    ->icon('heroicon-o-document-text')
                    ->items($billing)
                    ->collapsed(false),

                NavigationGroup::make('Administration')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->items($administration)
                    ->collapsed(false),
            ])
            ->item(
                NavigationItem::make('Dashboard')
                    ->icon('heroicon-o-chart-bar')
                    ->url(fn() => Dashboard::getUrl())
                    ->isActiveWhen(fn() => request()->routeIs('filament.admin.pages.dashboard'))
            );
    }

    protected function colors(): array
    {
        return [
            'primary' => [
                50 => '#b3fefc',
                100 => '#37f2ee',
                200 => '#2dcdc9',
                300 => '#24adaa',
                400 => '#1c908d',
                500 => '#157573',
                600 => '#0e5c5a',
                700 => '#084543',
                800 => '#042f2e',
                900 => '#021f1e',
                950 => '#011413',
            ],
            'danger' => Color::Rose,
            'gray' => Color::Gray,
            'info' => Color::Blue,
            'success' => Color::Emerald,
            'warning' => Color::Orange,
        ];
    }
}
