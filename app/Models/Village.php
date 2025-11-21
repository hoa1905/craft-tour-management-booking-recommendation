<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    use HasFactory;

    protected $primaryKey = 'village_id';
    public $timestamps = true;

    protected $fillable = [
        'province_id',
        'village_name',
        'address',
        'description',
        'latitude',
        'longitude',
        'image_main',
        'gallery',
        'status',
    ];

    // Tự động chuyển đổi JSON trong DB sang Mảng PHP và ngược lại
    protected $casts = [
        'gallery' => 'array',
    ];

    // Relationships
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function tours()
    {
        return $this->belongsToMany(Tour::class, 'tour_village', 'village_id', 'tour_id');
    }
}
