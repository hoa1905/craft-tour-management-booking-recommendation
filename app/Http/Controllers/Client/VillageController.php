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
        // / Khởi tạo query: Eager load province và lọc status của Village
        $query = Village::where('status', 'active')->with('province');

        // Lọc theo Vùng miền
        $query->when($request->region_id, function ($q) use ($request) {
            return $q->whereHas('province.region', function ($subQ) use ($request) {
                $subQ->where('region_id', $request->region_id);
            });
        });

        // Lọc theo Tỉnh/Thành
        $query->when($request->province_id, function ($q) use ($request) {
            return $q->where('province_id', $request->province_id);
        });

        // Lấy danh sách làng nghề đã lọc
        $villages = $query->get();

        // Lấy tất cả các miền
        // phải đặt tên biến là $region_list_filter, nếu đặt là $regions sẽ lỗi vì Ngay sau khi VillageController gửi biến $regions đến view, một file khác (ViewServiceProvider.php) đã dùng View::share('regions', ...) để ghi đè lên biến $regions bằng một biến $regions khác (dùng cho menu, là 2D Collection).
        $region_list_filter = Region::all();
        // Lấy tất cả tỉnh
        $provinces = Province::all();

        return view('client.village_map', compact(
            'villages',   // Dữ liệu làng nghề (đã lọc)
            'region_list_filter',    // Cho bộ lọc vùng 
            'provinces',  // Cho bộ lọc tỉnh
        ));
    }

    public function showVillageDetail($id)
    {
        $village = Village::with([
            'province.region',
            'tours' => function ($q) {
                $q->where('status', 'active');
            }
        ])->findOrFail($id);

        return view('client.village_detail', compact('village'));
    }
}
