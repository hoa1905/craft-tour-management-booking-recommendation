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
        // Chia sẻ biến $regions cho tất cả view trong thư mục client
        // Chia sẻ danh sách tỉnh theo từng vùng miền cho tất cả view trong client
        View::composer('client.*', function ($view) {
            $regions = DB::table('regions')
                ->select('region_name', 'province_name')
                ->join('provinces', 'regions.region_id', '=', 'provinces.region_id')
                ->orderBy('regions.region_name')
                ->orderBy('provinces.province_name')
                ->get()
                ->groupBy('region_name')
                ->map(function ($group) {
                    return $group->pluck('province_name')->unique();
                });

            $view->with('regions', $regions);
        });
    }
}
