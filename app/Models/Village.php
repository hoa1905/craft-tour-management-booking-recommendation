<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    use HasFactory;

    protected $primaryKey = 'village_id';
    public $timestamps = false;

    protected $fillable = [
        'province_id',
        'village_name',
    ];

    // Relationships
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function tourVillages()
    {
        return $this->hasMany(TourVillage::class, 'village_id');
    }

    public function tours()
    {
        return $this->belongsToMany(Tour::class, 'tour_villages', 'village_id', 'tour_id');
    }
}
