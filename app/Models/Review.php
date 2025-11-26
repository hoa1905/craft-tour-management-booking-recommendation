<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $primaryKey = 'review_id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'tour_id',
        'rating',
        'comment',
        'images',
    ];

    // Tự động chuyển đổi JSON trong DB sang Mảng PHP và ngược lại
    protected $casts = [
        'images' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'tour_id');
    }
}
