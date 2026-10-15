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

        $this->ensureStorageSynced();

        $this->registerActivityLogListeners();
    }

    /**
     * Ensure storage files from storage/app/public are available in public/storage
     * for direct static serving on Windows environments without symlink privilege.
     */
    protected function ensureStorageSynced(): void
    {
        try {
            $folders = ['bukti_pembayaran', 'rekening', 'bukti_transaksi'];
            foreach ($folders as $folder) {
                $srcDir = storage_path('app/public/' . $folder);
                $dstDir = public_path('storage/' . $folder);
                if (is_dir($srcDir)) {
                    if (!is_dir($dstDir)) {
                        @mkdir($dstDir, 0755, true);
                    }
                    $files = @scandir($srcDir);
                    if ($files) {
                        foreach ($files as $file) {
                            if ($file === '.' || $file === '..' || $file === '.gitkeep') continue;
                            $srcFile = $srcDir . DIRECTORY_SEPARATOR . $file;
                            $dstFile = $dstDir . DIRECTORY_SEPARATOR . $file;
                            if (is_file($srcFile) && !file_exists($dstFile)) {
                                @copy($srcFile, $dstFile);
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {}
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
            $user = $event->user;
            $nik = $event->credentials['nik'] ?? null;
            if (! $user && $nik) {
                $user = \App\Models\User::where('nik', $nik)->first();
            }

            $activity = activity('auth');
            if ($user) {
                $activity->performedOn($user)->causedBy($user);
            }

            $userName = $user?->name ?? ($nik ? "NIK: {$nik}" : 'Pengguna Tidak Dikenal');
            $userRole = $user?->role ?? 'Pengguna';

            $activity->withProperties([
                'status' => 'Gagal',
                'category' => 'login',
                'nik' => $nik,
                'name' => $userName,
                'role' => $userRole,
            ])
            ->event('failed')
            ->log($user 
                ? "Percobaan login gagal untuk NIK {$user->nik} ({$user->name})."
                : ($nik ? "Percobaan login gagal: NIK {$nik} tidak terdaftar." : 'Percobaan login gagal.')
            );
        });
    }
}
