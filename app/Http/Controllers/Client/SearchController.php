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
        $filters = [
            'departure' => trim($request->input('departure')),
            'destination' => trim($request->input('destination')),
            'start_date' => trim($request->input('start_date')),
            'duration' => trim($request->input('duration')),
            'priceMin' => number_format((float) str_replace(',', '', $request->input('s_price_min', 0)), 3, '.', ''),
            'priceMax' => number_format((float) str_replace(',', '', $request->input('s_price_max', 50000000)), 3, '.', ''),
            'sort' => $request->input('sort', 0),
        ];

        // Nếu trống hết
        if (
            empty($filters['departure']) && empty($filters['destination']) && empty($filters['start_date']) && empty($filters['duration']) &&
            empty($request->input('s_price_min')) && empty($request->input('s_price_max'))
        ) {
            $emptyPaginator = new LengthAwarePaginator(collect([]), 0, 10, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);

            return view('client.tour_search', [
                'tours' => $emptyPaginator,
                'message' => 'Vui lòng nhập thông tin tìm kiếm.',
            ]);
        }

        $tours = Tour::searchTours($filters)->paginate(10)->appends($request->query());
        $message = $tours->isEmpty() ? 'Không tìm thấy tour phù hợp.' : '';

        return view('client.tour_search', compact('tours', 'message'));
    }
}
