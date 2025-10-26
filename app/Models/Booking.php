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
}
