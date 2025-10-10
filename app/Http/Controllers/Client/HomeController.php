<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $hotTours = DB::table('tours')
            ->join('villages', 'tours.village_id', '=', 'villages.village_id')
            ->where('tours.status', 'approved')
            ->select('tours.*', 'villages.region', 'villages.province')
            ->take(8)
            ->get();

        // Hàm lấy 8 tour đầu tiên + tổng số tour
        $getTours = function ($region) {
            $query = DB::table('tours')
                ->join('villages', 'tours.village_id', '=', 'villages.village_id')
                ->where('villages.region', $region)
                ->where('tours.status', 'approved')
                ->select('tours.*', 'villages.region', 'villages.province');

            return [
                'tours' => (clone $query)->take(8)->get(),
                'total' => (clone $query)->count()
            ];
        };

        $north = $getTours('Tour Miền Bắc');
        $central = $getTours('Tour Miền Trung');
        $south = $getTours('Tour Miền Nam');

        return view('client.home', [
            'hotTours' => $hotTours,
            'northTours' => $north['tours'],
            'centralTours' => $central['tours'],
            'southTours' => $south['tours'],
            'totalNorthTours' => $north['total'],
            'totalCentralTours' => $central['total'],
            'totalSouthTours' => $south['total'],
        ]);
    }

    public function loadMoreTours(Request $request)
    {
        $offset = (int)$request->get('offset', 0);
        $limit = 4;
        $region = $request->get('region');

        $tours = DB::table('tours')
            ->join('villages', 'tours.village_id', '=', 'villages.village_id')
            ->where('villages.region', $region)
            ->where('tours.status', 'approved')
            ->select('tours.*', 'villages.region', 'villages.province')
            ->skip($offset) // Bỏ qua số bản ghi đã tải trước đó
            ->take($limit)
            ->get();

        return response()->json($tours);
    }

    public function showTourDetail($id)
    {
        // Lấy thông tin tour theo id
        $tour = DB::table('tours')
            ->join('villages', 'tours.village_id', '=', 'villages.village_id')
            ->join('users', 'tours.partner_id', '=', 'users.user_id')
            ->select(
                'tours.*',
                'villages.village_name',
                'villages.region',
                'villages.province',
                'users.full_name as partner_name',
                'users.phone as partner_phone'
            )
            ->where('tours.tour_id', $id)
            ->first();

        if (!$tour) {
            abort(404, 'Tour không tồn tại');
        }

        return view('client.tour_detail', compact('tour'));
    }
}
