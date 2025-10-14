<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $departure = trim($request->input('departure'));
        $destination = trim($request->input('destination'));
        $startDate = trim($request->input('start_date'));
        $duration = trim($request->input('duration'));
        $priceMin = number_format((float) str_replace(',', '', $request->input('s_price_min', 0)), 3, '.', '');
        $priceMax = number_format((float) str_replace(',', '', $request->input('s_price_max', 50000000)), 3, '.', '');
        $sort = $request->input('sort', 0);

        // Nếu tất cả đều trống thì trả về rỗng
        if (
            empty($departure) && empty($destination) && empty($startDate) && empty($duration) &&
            empty($request->input('s_price_min')) &&
            empty($request->input('s_price_max'))
        ) {
            $emptyPaginator = new LengthAwarePaginator(
                collect([]), // dữ liệu rỗng
                0, // tổng số phần tử
                10, // số item mỗi trang
                1, // trang hiện tại
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );
            return view('client.tour_search', [
                'tours' => $emptyPaginator,
                'message' => 'Vui lòng nhập thông tin tìm kiếm.'
            ]);
        }

        // Bắt đầu query
        $query = DB::table('tours')
            ->join('villages', 'tours.village_id', '=', 'villages.village_id')
            ->where('tours.status', 'approved')
            ->select('tours.*', 'villages.village_name', 'villages.region');

        /* FORM 1: chỉ có điểm đến (tìm gần đúng, không tách từng từ bắt buộc phải có hết) */
        if (!empty($destination) && empty($departure) && empty($startDate) && empty($duration)) {
            $keywords = explode(' ', $destination);

            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->where(function ($sub) use ($word) {
                        $sub->where('tours.destination', 'like', "%{$word}%")
                            ->orWhere('villages.village_name', 'like', "%{$word}%")
                            ->orWhere('villages.region', 'like', "%{$word}%");
                    });
                }
            });
        }

        /* FORM 2: có điểm đi, điểm đến, ngày (tìm chính xác) */ elseif (!empty($departure) || !empty($destination) || !empty($startDate)) {
            if (!empty($departure)) {
                $query->where('tours.departure_point', $departure);
            }

            if (!empty($destination)) {
                $query->where('tours.destination', $destination);
            }

            if (!empty($startDate)) {
                if (strpos($startDate, ' - ') !== false) {
                    [$start, $end] = explode(' - ', $startDate);
                    $start = Carbon::createFromFormat('d/m/Y', trim($start))->startOfDay();
                    $end = Carbon::createFromFormat('d/m/Y', trim($end))->endOfDay();
                    $query->whereBetween('tours.start_date', [$start, $end]);
                } else {
                    $date = Carbon::createFromFormat('d/m/Y', trim($startDate))->toDateString();
                    $query->whereDate('tours.start_date', $date);
                }
            }
        }

        /* FORM 3: có thời lượng tour (tìm gần giống theo duration, các input khác tìm chính xác) */
        if (!empty($duration)) {
            // Duration gần giống
            $query->where('tours.duration', 'like', "%{$duration}%");
        }

        if (!empty($startDate)) {
            if (strpos($startDate, ' - ') !== false) {
                [$start, $end] = explode(' - ', $startDate);
                $start = Carbon::createFromFormat('d/m/Y', trim($start))->startOfDay();
                $end = Carbon::createFromFormat('d/m/Y', trim($end))->endOfDay();
                $query->whereBetween('tours.start_date', [$start, $end]);
            } else {
                $date = Carbon::createFromFormat('d/m/Y', trim($startDate))->toDateString();
                $query->whereDate('tours.start_date', $date);
            }
        }

        /* Lọc giá (áp dụng cho form 3) */
        $query->whereBetween('tours.price', [$priceMin, $priceMax]);

        /* Sắp xếp */
        switch ($sort) {
            case 1:
                $query->orderBy('tours.price', 'asc');
                break;
            case 2:
                $query->orderBy('tours.price', 'desc');
                break;
            case 3:
                $query->orderBy('tours.start_date', 'asc');
                break;
            default:
                $query->orderBy('tours.created_at', 'desc');
                break;
        }

        $tours = $query->paginate(10)->appends($request->query());

        $message = $tours->isEmpty() ? 'Không tìm thấy tour phù hợp.' : '';

        return view('client.tour_search', [
            'tours' => $tours,
            'message' => $message
        ]);
    }
}
