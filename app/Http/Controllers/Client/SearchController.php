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
        // 1. Lấy và làm sạch các tham số
        $filters = [
            'departure'   => trim($request->input('departure', '')),
            'destination' => trim($request->input('destination', '')),
            'start_date'  => trim($request->input('start_date', '')),
            'duration'    => trim($request->input('duration', '')),
            'priceMin'    => (int) $request->input('s_price_min', 0),
            'priceMax'    => (int) $request->input('s_price_max', 50000000),
            'search_type' => $request->input('search_type'),
            'sort'        => $request->input('sort'),
        ];

        // 2. Kiểm tra xem có bộ lọc cụ thể nào không (Giá, Ngày, Điểm đi...)
        // Nếu có, bắt buộc phải dùng SQL DB để lọc chính xác
        $hasSpecificFilters =
            !empty($filters['departure']) ||
            !empty($filters['start_date']) ||
            !empty($filters['duration']) ||
            ($filters['priceMin'] > 0) ||
            ($filters['priceMax'] < 50000000);

        //3. Tìm kiếm bằng hệ khuyến nghị khi có từ khóa 'destination' và Không có bộ lọc khác
        $hasDestination = !empty($filters['destination']);
        $isKeywordSearch = $hasDestination && !$hasSpecificFilters;

        $tours = null;
        $message = '';

        if ($isKeywordSearch) {
            // Tìm kiếm bằng hệ khuyến nghị
            // trả về rỗng để View hiển thị Loading, sau đó JS gọi API Python.
            $tours = new LengthAwarePaginator([], 0, 10);
        } else {
            // Tìm kiếm không dùng hệ khuyến nghị (DB)
            // Chạy khi:
            // - Người dùng không nhập gì.
            // - Hoặc người dùng kết hợp Từ khóa (destination) + Giá/Ngày...

            if (!$hasSpecificFilters && !$hasDestination) {
                // Không nhập gì cả
                $emptyPaginator = new LengthAwarePaginator([], 0, 10, 1, ['path' => $request->url(), 'query' => $request->query()]);
                return view('client.tour_search', [
                    'tours' => $emptyPaginator,
                    'message' => 'Vui lòng nhập thông tin tìm kiếm.',
                    'isKeywordSearch' => false
                ]);
            }

            // Gọi vào Model để chạy câu lệnh SQL
            $tours = Tour::searchTours($filters)->paginate(10)->appends($request->query());
            $message = $tours->isEmpty() ? 'Không tìm thấy tour phù hợp với bộ lọc.' : '';
        }

        return view('client.tour_search', compact('tours', 'message', 'isKeywordSearch', 'filters'));
    }
}
