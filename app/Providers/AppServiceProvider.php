<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Models\KritikSaran;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
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
        \URL::forceScheme('https');

        // Daftarkan file routes/api.php
        Route::prefix('api') // Prefix 'api' untuk semua route di api.php
            ->middleware('api') // Middleware default untuk API Laravel
            ->group(base_path('routes/api.php')); // Pastikan path benar

        // Periksa apakah tabel 'kritiksaran' sudah ada sebelum memuat data
        if (Schema::hasTable('kritiksaran')) {
            View::share('kritiksaran', KritikSaran::latest()->take(5)->get());
        }

        Carbon::setLocale('id'); // Mengatur lokal Carbon ke bahasa Indonesia
        setlocale(LC_TIME, 'id_ID');
    }
}
