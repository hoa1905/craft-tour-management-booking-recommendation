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
        'image_1',
        'image_2',
        'status',
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

    // Hàm scope lọc tour được duyệt
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Lọc tour theo vùng miền (qua villages → provinces → regions)
    public function scopeByRegion($query, $regionName)
    {
        return $query->whereHas('villages.province.region', function ($q) use ($regionName) {
            $q->where('region_name', $regionName);
        });
    }

    // Lấy 8 tour hot (được duyệt và có quan hệ làng nghề)
    public static function getHotTours($limit = 8)
    {
        return self::approved()
            ->with(['villages.province.region', 'schedules'])
            ->take($limit)
            ->get();
    }

    // Lấy 8 tour đầu tiên theo vùng và tổng số tour
    public static function getToursByRegion($regionName, $limit = 2)
    {
        $query = self::approved()
            ->byRegion($regionName)
            ->with(['villages.province.region', 'schedules']);

        return [
            'tours' => (clone $query)->take($limit)->get(),
            'total' => (clone $query)->count(),
        ];
    }

    public static function loadMoreByRegion($regionName, $offset = 0, $limit = 4)
    {
        return self::where('status', 'approved')
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
     */

    // ======================
    // SEARCH FUNCTION
    // ======================
    public static function searchTours($filters)
    {
        $query = self::query()
            ->join('tour_village', 'tours.tour_id', '=', 'tour_village.tour_id')
            ->join('villages', 'tour_village.village_id', '=', 'villages.village_id')
            ->join('provinces', 'villages.province_id', '=', 'provinces.province_id')
            ->join('regions', 'provinces.region_id', '=', 'regions.region_id')
            ->leftJoin('tour_schedules', 'tours.tour_id', '=', 'tour_schedules.tour_id')
            ->where('tours.status', 'approved')
            ->select('tours.*', 'villages.village_name', 'provinces.province_name', 'regions.region_name', 'tour_schedules.start_date', 'tour_schedules.end_date');

        // Tìm theo điểm đến
        if (!empty($filters['destination'])) {
            $keywords = explode(' ', $filters['destination']);
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->orWhere('villages.village_name', 'like', "%{$word}%")
                        ->orWhere('provinces.province_name', 'like', "%{$word}%")
                        ->orWhere('regions.region_name', 'like', "%{$word}%")
                        ->orWhere('tours.destination', 'like', "%{$word}%");
                }
            });
        }

        // Tìm theo điểm khởi hành
        if (!empty($filters['departure'])) {
            $query->where('tours.departure_point', 'like', "%{$filters['departure']}%");
        }

        // Tìm theo ngày khởi hành (dựa trên bảng tour_schedules)
        if (!empty($filters['start_date'])) {
            if (strpos($filters['start_date'], ' - ') !== false) {
                [$start, $end] = explode(' - ', $filters['start_date']);
                $start = Carbon::createFromFormat('d/m/Y', trim($start))->startOfDay();
                $end = Carbon::createFromFormat('d/m/Y', trim($end))->endOfDay();
                $query->whereBetween('tour_schedules.start_date', [$start, $end]);
            } else {
                $date = Carbon::createFromFormat('d/m/Y', trim($filters['start_date']))->toDateString();
                $query->whereDate('tour_schedules.start_date', $date);
            }
        }

        // Tìm theo thời lượng
        if (!empty($filters['duration'])) {
            $query->where('tours.duration', 'like', "%{$filters['duration']}%");
        }

        // Lọc theo khoảng giá
        if (isset($filters['priceMin']) && isset($filters['priceMax'])) {
            $query->whereBetween('tours.price_adult', [$filters['priceMin'], $filters['priceMax']]);
        }

        // Sắp xếp
        switch ($filters['sort'] ?? null) {
            case 1:
                $query->orderBy('tours.price_adult', 'asc');
                break;
            case 2:
                $query->orderBy('tours.price_adult', 'desc');
                break;
            case 3:
                $query->orderBy('tour_schedules.start_date', 'asc');
                break;
            default:
                $query->orderBy('tours.created_at', 'desc');
                break;
        }

        return $query;
    }
}
