<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use HasFactory;

    protected $primaryKey = 'tour_id';
    public $timestamps = true;

    protected $fillable = [
        'partner_id',
        'tour_name',
        'price_adult',
        'price_child',
        'duration',
        'description',
        'departure_point',
        'destination',
        'transport',
        'image_main',
        'gallery',
        'status',
    ];

    // Tự động chuyển đổi JSON trong DB sang Mảng PHP và ngược lại
    protected $casts = [
        'gallery' => 'array',
    ];

    // Relationships
    // Mỗi tour do một đối tác (partner) tạo
    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    // Một tour có thể có nhiều lịch khởi hành
    public function schedules()
    {
        return $this->hasMany(TourSchedule::class, 'tour_id');
    }

    // Mỗi tour có nhiều làng nghề thông qua bảng trung gian tour_village
    public function villages()
    {
        return $this->belongsToMany(Village::class, 'tour_village', 'tour_id', 'village_id');
    }

    // Một tour có nhiều lịch trình (itineraries)
    public function itineraries()
    {
        return $this->hasMany(TourItinerary::class, 'tour_id', 'tour_id')->orderBy('day_number', 'asc');
    }

    // Một tour có thể có nhiều lượt đặt
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'tour_id', 'tour_id');
    }

    // Hàm scope lọc tour có trạng thái active
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Lọc tour theo vùng miền (qua villages → provinces → regions)
    public function scopeByRegion($query, $regionName)
    {
        return $query->whereHas('villages.province.region', function ($q) use ($regionName) {
            $q->where('region_name', $regionName);
        });
    }

    // Lấy 8 tour được đặt nhiều nhất
    public static function getHotTours($limit = 8)
    {
        return self::active()
            ->with(['villages.province.region', 'schedules'])

            // 1. LỌC: Chỉ lấy tour có ít nhất 1 booking completed
            ->whereHas('bookings', function ($query) {
                $query->where('status', 'completed');
            })

            // 2. ĐẾM: Để lấy số liệu sắp xếp
            ->withCount(['bookings' => function ($query) {
                $query->where('status', 'completed');
            }])

            // Sắp xếp giảm dần theo số lượng đơn completed (bookings_count là cột ảo Laravel tự tạo)
            ->orderByDesc('bookings_count')
            ->take($limit)
            ->get();
    }

    // Lấy 8 tour đầu tiên theo vùng và tổng số tour
    public static function getToursByRegion($regionName, $limit = 8)
    {
        $query = self::active()
            ->byRegion($regionName)
            ->with(['villages.province.region', 'schedules']);

        return [
            'tours' => (clone $query)->take($limit)->get(),
            'total' => (clone $query)->count(),
        ];
    }

    public static function loadMoreByRegion($regionName, $offset = 0, $limit = 4)
    {
        return self::where('status', 'active')
            ->whereHas('villages.province.region', function ($query) use ($regionName) {
                $query->where('region_name', $regionName);
            })
            ->with(['villages.province.region', 'schedules'])
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    public static function getTourDetail($id)
    {
        return self::with([
            'villages.province.region',
            'partner',
            'schedules',
            'itineraries',
        ])
            ->where('tour_id', $id)
            ->firstOrFail();
    }


    /**
     * Hàm xử lý tìm kiếm Tour theo các điều kiện
     * Hỗ trợ 3 loại form:
     * - Form 1: Chỉ tìm theo destination (từ khóa tự do)
     * - Form 2: Tìm theo departure + destination + start_date (tùy chọn)
     * - Form 3: Tìm theo tất cả bộ lọc (AND logic)
     */
    public static function searchTours($filters)
    {
        // Xác định loại tìm kiếm
        $searchType = $filters['search_type']; // 'fuzzy' hoặc 'exact'

        // Lấy danh sách tour_id thỏa mãn điều kiện
        $tourIdsQuery = self::query()
            ->join('tour_village', 'tours.tour_id', '=', 'tour_village.tour_id')
            ->join('villages', 'tour_village.village_id', '=', 'villages.village_id')
            ->join('provinces', 'villages.province_id', '=', 'provinces.province_id')
            ->join('regions', 'provinces.region_id', '=', 'regions.region_id')
            ->leftJoin('tour_schedules', 'tours.tour_id', '=', 'tour_schedules.tour_id')
            ->where('tours.status', 'active');

        // Tìm theo điểm đến
        if (!empty($filters['destination'])) {
            $destination = trim($filters['destination']);

            if ($searchType === 'exact') {
                // Tìm kiếm chính xác từ khóa (Form 2 và Form 3)
                $tourIdsQuery->where(function ($q) use ($destination) {
                    $q->where('provinces.province_name', '=', $destination)
                        ->orWhere('regions.region_name', '=', $destination)
                        ->orWhere('villages.village_name', '=', $destination)
                        ->orWhere('tours.destination', '=', $destination);
                });
            } else {
                // Tìm kiếm linh hoạt (Form 1: tìm theo tên làng nghề, tỉnh, vùng miền, tour)
                $keywords = array_filter(explode(' ', $destination));

                $tourIdsQuery->where(function ($q) use ($keywords, $destination) {
                    // Tìm chính xác toàn bộ cụm từ trước
                    $q->where('villages.village_name', 'like', "%{$destination}%")
                        ->orWhere('provinces.province_name', 'like', "%{$destination}%")
                        ->orWhere('regions.region_name', 'like', "%{$destination}%")
                        ->orWhere('tours.destination', 'like', "%{$destination}%")
                        ->orWhere('tours.tour_name', 'like', "%{$destination}%");

                    // Sau đó tìm từng từ khóa riêng lẻ
                    foreach ($keywords as $word) {
                        $q->orWhere('villages.village_name', 'like', "%{$word}%")
                            ->orWhere('provinces.province_name', 'like', "%{$word}%")
                            ->orWhere('regions.region_name', 'like', "%{$word}%")
                            ->orWhere('tours.destination', 'like', "%{$word}%")
                            ->orWhere('tours.tour_name', 'like', "%{$word}%");
                    }
                });
            }
        }

        // Tìm theo điểm khởi hành (departure) - Tìm với từ khóa chính xác
        if (!empty($filters['departure'])) {
            $departure = trim($filters['departure']);

            if ($searchType === 'exact') {
                // Chính xác tuyệt đối
                $tourIdsQuery->where('tours.departure_point', '=', $departure);
            } else {
                // Linh hoạt hơn
                $tourIdsQuery->where(function ($q) use ($departure) {
                    $q->where('tours.departure_point', '=', $departure)
                        ->orWhere('tours.departure_point', 'like', "%{$departure}%");
                });
            }
        }

        // Tìm theo ngày khởi hành
        if (!empty($filters['start_date'])) {
            $startDate = trim($filters['start_date']);

            if (strpos($startDate, ' - ') !== false) {
                $dates = explode(' - ', $startDate);
                if (count($dates) === 2) {
                    try {
                        $start = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                        $end = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();

                        $tourIdsQuery->where(function ($q) use ($start, $end) {
                            $q->whereBetween('tour_schedules.start_date', [$start, $end])
                                ->orWhereBetween('tour_schedules.end_date', [$start, $end])
                                ->orWhere(function ($q2) use ($start, $end) {
                                    $q2->where('tour_schedules.start_date', '<=', $start)
                                        ->where('tour_schedules.end_date', '>=', $end);
                                });
                        });
                    } catch (\Exception $e) {
                        // Ignore error
                    }
                }
            } else {
                try {
                    $date = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->toDateString();
                    $tourIdsQuery->where(function ($q) use ($date) {
                        $q->whereDate('tour_schedules.start_date', '=', $date)
                            ->orWhere(function ($q2) use ($date) {
                                $q2->whereDate('tour_schedules.start_date', '<=', $date)
                                    ->whereDate('tour_schedules.end_date', '>=', $date);
                            });
                    });
                } catch (\Exception $e) {
                    // Ignore error
                }
            }
        }

        // Tìm theo thời lượng tour (duration)
        if (!empty($filters['duration'])) {
            $duration = trim($filters['duration']);

            if ($searchType === 'exact') {
                // Chính xác
                $tourIdsQuery->where('tours.duration', '=', $duration);
            } else {
                // Linh hoạt
                $tourIdsQuery->where(function ($q) use ($duration) {
                    $q->where('tours.duration', '=', $duration)
                        ->orWhere('tours.duration', 'like', "%{$duration}%");
                });
            }
        }

        // Lọc theo giá
        if (isset($filters['priceMin']) && isset($filters['priceMax'])) {
            $priceMin = (int) $filters['priceMin'];
            $priceMax = (int) $filters['priceMax'];

            if ($priceMin > 0 || $priceMax < 50000000) {
                $tourIdsQuery->whereBetween('tours.price_adult', [$priceMin, $priceMax]);
            }
        }

        // Lấy danh sách tour_id duy nhất
        $tourIds = $tourIdsQuery->distinct()
            ->pluck('tours.tour_id')
            ->unique()
            ->toArray();

        // Query lại chỉ với tour_id đã lọc
        $query = self::query()
            ->whereIn('tour_id', $tourIds)
            ->with(['villages.province.region', 'schedules']);

        // Sắp xếp kết quả
        switch ($filters['sort'] ?? null) {
            case 1:
                $query->orderBy('price_adult', 'asc');
                break;
            case 2:
                $query->orderBy('price_adult', 'desc');
                break;
            case 3:
                $query->orderByRaw('(SELECT MIN(start_date) FROM tour_schedules WHERE tour_schedules.tour_id = tours.tour_id) ASC');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query;
    }
}
