<?php

namespace App\Models;

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

    // Quan hệ với bảng Villages
    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id', 'village_id');
    }

    // Quan hệ với Users (partner)
    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id', 'user_id');
    }
}
