<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourRequest extends Model
{
    use HasFactory;

    protected $primaryKey = 'request_id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'full_name',
        'phone_number',
        'email',
        'departure_point',
        'desired_destination',
        'workshop',
        'village_ids',
        'start_date',
        'end_date',
        'adult_quantity',
        'child_quantity',
        'price_adult_final',
        'price_child_final',
        'final_total_price',
        'budget_min',
        'budget_max',
        'transportation',
        'requirements',
        'status',
        'admin_note'
    ];

    protected $casts = [
        'village_ids' => 'array', // Tự động chuyển JSON <-> Array
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Quan hệ với Booking (1 Request có thể tạo ra 1 Booking)
    public function booking()
    {
        return $this->hasOne(Booking::class, 'request_id', 'request_id');
    }

    // Helper để lấy tên trạng thái tiếng Việt
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'Chờ xử lý',
            'offering' => 'Đã báo giá',
            'accepted' => 'Đã chốt',
            'rejected' => 'Bị từ chối',
            'canceled' => 'Đã hủy',
            'completed' => 'Hoàn thành',
            default => $this->status,
        };
    }

    // Helper màu sắc trạng thái
    public function getStatusClassAttribute()
    {
        return match ($this->status) {
            'pending' => 'label-warning',
            'offering' => 'label-primary',
            'accepted' => 'label-success',
            'rejected', 'canceled' => 'label-danger',
            'completed' => 'label-success',
            default => 'label-default',
        };
    }
}
