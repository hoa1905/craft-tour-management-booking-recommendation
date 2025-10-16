<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $primaryKey = 'province_id';
    public $timestamps = false;

    protected $fillable = [
        'region_id',
        'province_name',
    ];

    // Relationships
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function villages()
    {
        return $this->hasMany(Village::class, 'province_id');
    }
}
