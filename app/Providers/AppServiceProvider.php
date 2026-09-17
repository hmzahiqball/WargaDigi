<?php

namespace App\Providers;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // The project UI is built on Bootstrap 5, so render pagination with the matching theme.
        Paginator::useBootstrapFive();

        $this->registerActivityLogListeners();
    }

    /**
     * Record authentication events into the activity log.
     */
    protected function registerActivityLogListeners(): void
    {
        if (! function_exists('activity')) {
            return;
        }

        Event::listen(Login::class, function (Login $event) {
            activity('auth')
                ->performedOn($event->user)
                ->causedBy($event->user)
                ->withProperties(['status' => 'Sukses', 'category' => 'login'])
                ->event('login')
                ->log('Berhasil masuk ke sistem.');
        });

        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                activity('auth')
                    ->performedOn($event->user)
                    ->causedBy($event->user)
                    ->withProperties(['status' => 'Sukses', 'category' => 'login'])
                    ->event('logout')
                    ->log('Keluar dari sistem.');
            }
        });

        Event::listen(Failed::class, function (Failed $event) {
            activity('auth')
                ->withProperties([
                    'status' => 'Gagal',
                    'category' => 'login',
                    'nik' => $event->credentials['nik'] ?? null,
                ])
                ->event('failed')
                ->log('Percobaan login gagal.');
        });
    }
}
