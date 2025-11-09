<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        // Lấy và làm sạch các tham số
        $filters = [
            'departure' => trim($request->input('departure', '')),
            'destination' => trim($request->input('destination', '')),
            'start_date' => trim($request->input('start_date', '')),
            'duration' => trim($request->input('duration', '')),
            'priceMin' => $request->input('s_price_min'),
            'priceMax' => $request->input('s_price_max'),
            'search_type' => $request->input('search_type'),
            'sort' => $request->input('sort'),
        ];

        // Kiểm tra nếu không có tham số nào
        $hasFilters = !empty($filters['departure']) ||
            !empty($filters['destination']) ||
            !empty($filters['start_date']) ||
            !empty($filters['duration']) ||
            ($filters['priceMin'] !== null && $filters['priceMax'] !== null);

        if (!$hasFilters) {
            $emptyPaginator = new LengthAwarePaginator(collect([]), 0, 10, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);

            return view('client.tour_search', [
                'tours' => $emptyPaginator,
                'message' => 'Vui lòng nhập thông tin tìm kiếm.',
            ]);
        }

        // Thực hiện tìm kiếm
        $tours = Tour::searchTours($filters)->paginate(10)->appends($request->query());

        $message = $tours->isEmpty() ? 'Không tìm thấy tour phù hợp.' : '';

        return view('client.tour_search', compact('tours', 'message'));
    }
}
