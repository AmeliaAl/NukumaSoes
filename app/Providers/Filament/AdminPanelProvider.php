<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Support\Enums\Width;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('Nukuma Soes')
            ->maxContentWidth(Width::Full)
            ->login()
            ->colors([
                'primary' => Color::Yellow,
            ])
            ->renderHook(
                'panels::head.end',
                fn () => '<style>
                    /* Sidebar: putih dengan teks ungu */
                    .fi-sidebar {
                        background-color: #ffffff !important;
                        border-right: 1px solid #e9d5ff !important;
                    }
                    .fi-sidebar .fi-sidebar-item-label { color: #6d28d9 !important; }
                    .fi-sidebar .fi-sidebar-group-label { color: #7c3aed !important; font-weight: 600 !important; }
                    .fi-sidebar .fi-sidebar-item-icon { color: #7c3aed !important; }
                    .fi-sidebar .fi-active .fi-sidebar-item-label { color: #5b21b6 !important; font-weight: 600 !important; }

                    /* Topbar: putih */
                    .fi-topbar { background-color: #ffffff !important; border-bottom: 1px solid #e9d5ff !important; }

                    /* Background konten utama: ungu muda */
                    .fi-main, .fi-body, .fi-page, .fi-simple-main { background-color: #ede9fe !important; }

                    /* Tabel Filament: putih bersih (hanya tabel Filament, bukan blade custom) */
                    .fi-ta-ctn, .fi-ta-table, .fi-ta-header-cell, .fi-ta-cell { background-color: #ffffff !important; }

                    /* Teks tabel */
                    .fi-ta-header-cell-label { color: #000000ff !important; font-weight: 600 !important; }
                    .fi-ta-cell { color: #374151 !important; }

                    /* Card & section: putih */
                    .fi-section, .fi-card, .fi-wi-stats-overview-stat { background-color: #ffffff !important; }

                    /* Input field: override warna primary-50 (biru muda) jadi putih */
                    .fi-input-wrp { background-color: #ffffff !important; }
                    .fi-input-wrp input,
                    .fi-input-wrp textarea,
                    .fi-input-wrp select {
                        background-color: #ffffff !important;
                        --tw-ring-color: transparent !important;
                    }
                    /* Override CSS variable Tailwind yang dipakai Filament untuk bg input */
                    :root {
                        --color-primary-50: 255 255 255 !important;
                    }
                </style>'
            )
            ->resources([
                \App\Filament\Resources\CoaResource::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->pages([
                Dashboard::class,
            ])
            // Widget didaftarkan manual — tidak pakai discoverWidgets
            // agar Livewire tidak mencoba resolve class sebelum autoload siap
            ->widgets([
                AccountWidget::class,
                \App\Filament\Admin\Widgets\StatsOverview::class,
                \App\Filament\Admin\Widgets\GrafikPenjualan::class,
                \App\Filament\Admin\Widgets\AgingPiutang::class,
                \App\Filament\Admin\Widgets\KontribusiPenjualan::class,
                \App\Filament\Admin\Widgets\TopMenunggak::class,
            ]);
    }
}
