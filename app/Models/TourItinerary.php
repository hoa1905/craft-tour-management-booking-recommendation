<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourItinerary extends Model
{
    use HasFactory;
    protected $primaryKey = 'itinerary_id';
    public $timestamps = false;

    protected $fillable = [
        'tour_id',
        'day_number',
        'activities',
        'meals',
        'accommodation',
    ];

    // Relationships
    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }
}
