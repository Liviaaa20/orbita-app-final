<?php

namespace App\Providers;

use App\Models\Perbaikan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
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
        /*
        |-----------------------------------------------------------------
        | NOTIFIKASI LONCENG - Permintaan Perbaikan
        |-----------------------------------------------------------------
        | Dipasang sebagai View Composer pada layouts.master supaya data
        | notifikasi otomatis tersedia di SEMUA halaman (bukan hanya
        | halaman /perbaikan), karena lonceng tampil di navbar global.
        |
        | - Teknisi : notif tiket yang belum diterima (tgl_diterima null)
        | - Koordinator : notif tiket yang menunggu verifikasi
        |                 (status='selesai' & validasi_koordinator null)
        | - Role lain : tidak mendapat data notifikasi (array kosong)
        */
        View::composer('layouts.master', function ($view) {

            $notifBell = [
                'count' => 0,
                'type'  => null, // 'teknisi' | 'koordinator' | null
                'items' => collect(),
            ];

            if (Auth::check()) {

                $role = strtolower(trim(Auth::user()->role->nama_role ?? ''));

                if ($role === 'teknisi') {

                    $items = Perbaikan::with('alat')
                        ->whereNull('tgl_diterima')
                        ->latest()
                        ->take(8)
                        ->get();

                    $notifBell = [
                        'count' => $items->count(),
                        'type'  => 'teknisi',
                        'items' => $items,
                    ];

                } elseif ($role === 'koordinator') {

                    $items = Perbaikan::with('alat')
                        ->where('status', 'selesai')
                        ->whereNull('validasi_koordinator')
                        ->latest()
                        ->take(8)
                        ->get();

                    $notifBell = [
                        'count' => $items->count(),
                        'type'  => 'koordinator',
                        'items' => $items,
                    ];
                }
            }

            $view->with('notifBell', $notifBell);
        });
    }
}