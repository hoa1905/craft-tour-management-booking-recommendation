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
        'tour_name',
        'village_id',
        'partner_id',
        'price',
        'duration',
        'description',
        'start_date',
        'end_date',
        'departure_point',
        'destination',
        'transport',
        'image_url',
        'status',
    ];

    // Quan hệ với bảng Villages (1 tour thuộc về 1 village)
    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id', 'village_id');
    }

    // Quan hệ với Users (partner) (1 tour thuộc về 1 partner)
    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id', 'user_id');
    }

    // Hàm scope lọc tour được duyệt
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Hàm scope lọc tour theo vùng
    public function scopeByRegion($query, $region)
    {
        return $query->whereHas('village', function ($q) use ($region) {
            $q->where('region', $region);
        });
    }

    // Lấy 8 tour hot
    public static function getHotTours($limit = 8)
    {
        return self::approved()
            ->with('village')
            ->take($limit)
            ->get();
    }

    // Lấy 8 tour đầu tiên theo vùng và tổng số tour
    public static function getToursByRegion($region, $limit = 8)
    {
        $query = self::approved()->byRegion($region)->with('village');

        return [
            'tours' => (clone $query)->take($limit)->get(),
            'total' => (clone $query)->count(),
        ];
    }

    /**
     * Hàm xử lý tìm kiếm Tour theo các điều kiện
     */
    // Hàm tìm kiếm logic tách riêng trong model
    public static function searchTours($filters)
    {
        $query = self::join('villages', 'tours.village_id', '=', 'villages.village_id')
            ->where('tours.status', 'approved')
            ->select('tours.*', 'villages.village_name', 'villages.region');

        // FORM 1: chỉ có điểm đến (tìm gần đúng)
        if (!empty($filters['destination']) && empty($filters['departure']) && empty($filters['start_date']) && empty($filters['duration'])) {
            $keywords = explode(' ', $filters['destination']);
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

        // FORM 2: có điểm đi, đến, ngày (chính xác)
        elseif (!empty($filters['departure']) || !empty($filters['destination']) || !empty($filters['start_date'])) {
            if (!empty($filters['departure'])) {
                $query->where('tours.departure_point', $filters['departure']);
            }

            if (!empty($filters['destination'])) {
                $query->where('tours.destination', $filters['destination']);
            }

            if (!empty($filters['start_date'])) {
                if (strpos($filters['start_date'], ' - ') !== false) {
                    [$start, $end] = explode(' - ', $filters['start_date']);
                    $start = \Carbon\Carbon::createFromFormat('d/m/Y', trim($start))->startOfDay();
                    $end = \Carbon\Carbon::createFromFormat('d/m/Y', trim($end))->endOfDay();
                    $query->whereBetween('tours.start_date', [$start, $end]);
                } else {
                    $date = \Carbon\Carbon::createFromFormat('d/m/Y', trim($filters['start_date']))->toDateString();
                    $query->whereDate('tours.start_date', $date);
                }
            }
        }

        // FORM 3: có duration
        if (!empty($filters['duration'])) {
            $query->where('tours.duration', 'like', "%{$filters['duration']}%");
        }

        // Lọc giá
        $query->whereBetween('tours.price', [$filters['priceMin'], $filters['priceMax']]);

        // Sắp xếp
        switch ($filters['sort']) {
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

        return $query;
    }
}
