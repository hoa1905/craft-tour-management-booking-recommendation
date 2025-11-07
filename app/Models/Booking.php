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
     *
     * @return bool
     */
    public function getIsCancelableAttribute()
    {
        // 1. Lấy trạng thái từ payment (ưu tiên) hoặc booking
        $paymentStatus = $this->payment?->status; // 'success', 'waiting_payment', 'failed', 'pending_refund', 'refunded'
        $bookingStatus = $this->status; // 'waiting_payment', 'paid', 'active', 'completed', 'canceled'

        // 2. Xác định trạng thái hiện tại
        $isWaitingPayment = ($bookingStatus === 'waiting_payment' || $paymentStatus === 'waiting_payment');
        $isPaid = ($paymentStatus === 'success'); // 'paid' là khi payment đã 'success'

        // 3. Chỉ cho phép hủy nếu là CHỜ THANH TOÁN hoặc ĐÃ THANH TOÁN
        if (!$isWaitingPayment && !$isPaid) {
            return false; // Không thể hủy nếu đã 'active', 'completed', 'canceled', 'refunded'
        }

        // 4. Kiểm tra logic ngày (TRƯỚC 7 NGÀY)
        if (!$this->schedule || !$this->schedule->start_date) {
            return false; // Không có lịch trình
        }

        // Lấy ngày bắt đầu
        $startDate = $this->schedule->start_date;

        // Cho phép hủy nếu ngày hiện tại còn TRƯỚC 7 ngày so với ngày khởi hành
        return now()->lt($startDate->subDays(7));
    }
}
