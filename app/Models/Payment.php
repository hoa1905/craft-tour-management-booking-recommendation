<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $primaryKey = 'payment_id';
    public $timestamps = true;

    protected $fillable = [
        'booking_id',
        'transaction_id',
        'payment_method',
        'amount',
        'payper_name',
        'payper_email',
        'status'
    ];

    // Quan hệ
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
}
