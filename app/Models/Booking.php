<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $primaryKey = 'booking_id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'tour_id',
        'schedule_id',
        'request_id',
        'full_name',
        'phone_number',
        'email',
        'address',
        'adult_quantity',
        'child_quantity',
        'price_adult_at_booking',
        'price_child_at_booking',
        'total_price',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }

    public function schedule()
    {
        return $this->belongsTo(TourSchedule::class, 'schedule_id');
    }

    public function tourRequest()
    {
        // Booking thuộc về một TourRequest (nếu có)
        return $this->belongsTo(TourRequest::class, 'request_id', 'request_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'booking_id', 'booking_id');
    }


    // Lọc theo user
    public function scopeOfUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Lọc theo trạng thái kết hợp Booking + Payment
    public function scopeFilterByStatus($query, $status)
    {
        if ($status === 'all') {
            return $query;
        }

        return $query->where(function ($q) use ($status) {
            switch ($status) {
                case 'waiting_payment':
                    $q->where('bookings.status', 'waiting_payment')
                        ->orWhereHas('payment', fn($p) => $p->where('status', 'waiting_payment'));
                    break;

                case 'paid':
                    $q->whereHas('payment', fn($p) => $p->where('status', 'success'));
                    break;

                case 'active':
                    $q->where('bookings.status', 'active');
                    break;

                case 'completed':
                    $q->where('bookings.status', 'completed');
                    break;

                case 'canceled':
                    $q->where('bookings.status', 'canceled');
                    break;

                case 'pending_refund':
                    $q->whereHas('payment', fn($p) => $p->where('status', 'pending_refund'));
                    break;

                case 'refunded':
                    $q->whereHas('payment', fn($p) => $p->where('status', 'refunded'));
                    break;
            }
        });
    }

    /**
     * Accessor: Kiểm tra xem đơn hàng có được phép hủy hay không.
     *Logic: 
     * 1. Trạng thái không phải là (Hoàn thành, Đang đi, Đã hủy, Đã hoàn tiền).
     * 2. Ngày hiện tại phải trước ngày đi 7 ngày.
     * 
     * @return bool
     */
    public function getIsCancelableAttribute()
    {
        // 1. Danh sách các trạng thái không được phép hủy
        $blockedStatuses = ['active', 'completed', 'canceled', 'refunded', 'pending_refund'];
        
        if (in_array($this->status, $blockedStatuses)) {
            return false;
        }

        // 2. Lấy ngày đi của tour từ lịch trình hoặc yêu cầu tour
        $startDate = null;

        // Trường hợp 1: Tour thường (có lịch trình)
        if ($this->schedule) {
            $startDate = $this->schedule->start_date;
        } 
        // Trường hợp 2: Tour theo yêu cầu (lấy ngày từ request)
        elseif ($this->tourRequest) {
            $startDate = $this->tourRequest->start_date;
        }

        // Nếu không xác định được ngày đi, Không cho hủy để an toàn
        if (!$startDate) {
            return false;
        }

        // 3. Kiểm tra thời gian (Luật 7 ngày)
        // Dùng copy() để không làm thay đổi biến gốc $startDate khi tính toán
        $deadline = $startDate->copy()->subDays(7); 

        // Cho phép hủy nếu: Hiện tại < Hạn chót (tức là còn sớm hơn 7 ngày)
        return now()->lt($deadline);
    }

    /**
     * Accessor: Tự động tạo tên tour hiển thị
     * Gọi trên view: {{ $booking->tour_name }}
     */
    public function getTourNameAttribute()
    {
        // 1. Nếu là Tour có sẵn (Tour thường)
        if ($this->tour) {
            return $this->tour->tour_name;
        }

        // 2. Nếu là Tour theo yêu cầu
        if ($this->tourRequest) {
            $name = "Tour theo yêu cầu: " . $this->tourRequest->desired_destination;

            // Logic lấy tên các làng nghề
            if (!empty($this->tourRequest->village_ids)) {
                // Lấy danh sách tên làng dựa trên mảng ID
                $villageNames = Village::whereIn('village_id', $this->tourRequest->village_ids)
                    ->pluck('village_name')
                    ->toArray();

                if (!empty($villageNames)) {
                    // Nối chuỗi lại
                    $name .= ' - Tham quan: ' . implode(', ', $villageNames);
                }
            }

            return $name;
        }

        return "Tour không xác định";
    }
}
