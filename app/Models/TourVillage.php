<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourVillage extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'tour_id',
        'village_id',
    ];

    // Relationships
    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }

    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id');
    }
}
