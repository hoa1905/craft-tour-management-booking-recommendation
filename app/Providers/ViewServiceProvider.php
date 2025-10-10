<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Chia sẻ biến $regions chỉ cho tất cả các view trong view client
        View::composer('client.*', function ($view) {
            $regions = [
                'Tour Miền Bắc' => DB::table('villages')->where('region', 'Tour Miền Bắc')->pluck('province')->unique(),
                'Tour Miền Trung' => DB::table('villages')->where('region', 'Tour Miền Trung')->pluck('province')->unique(),
                'Tour Miền Nam' => DB::table('villages')->where('region', 'Tour Miền Nam')->pluck('province')->unique()
            ];
            $view->with('regions', $regions);
        });
    }
}
