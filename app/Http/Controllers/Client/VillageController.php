<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Province;
use App\Models\Region;
use App\Models\Village;
use Illuminate\Http\Request;

class VillageController extends Controller
{
    /**
     * Hiển thị bản đồ làng nghề với bộ lọc
     */
    public function map(Request $request)
    {
        // Bắt đầu query với quan hệ 'province' để lấy tên tỉnh
        $query = Village::with('province');

        // Lọc theo Vùng miền
        $query->when($request->region, function ($q) use ($request) {
            return $q->whereHas('province.region', function ($subQ) use ($request) {
                $subQ->where('region_name', $request->region);
            });
        });

        // Lọc theo Tỉnh/Thành
        $query->when($request->province_id, function ($q) use ($request) {
            return $q->where('province_id', $request->province_id);
        });

        // Lấy danh sách làng nghề đã lọc
        $villages = $query->get();

        // Lấy tất cả dữ liệu cho bộ lọc
        $regions = Region::all();
        $provinces = Province::all(); // Lấy tất cả tỉnh

        return view('client.map', compact(
            'villages',   // Dữ liệu làng nghề (đã lọc)
            'regions',    // Cho bộ lọc vùng
            'provinces',  // Cho bộ lọc tỉnh
        ));
    }

    /**
     * Display village details
     */
    public function showVillageDetail($id)
    {
        $village = Village::with([
            'province.region',
            'tours' => function ($q) {
                $q->where('status', 'active'); // Giả định tour có status 'active'
            }
        ])->findOrFail($id);

        return view('client.village_detail', compact('village'));
    }
}
