<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $primaryKey = 'region_id';
    public $timestamps = false;

    protected $fillable = [
        'region_name',
    ];

    // Relationships
    public function provinces()
    {
        return $this->hasMany(Province::class, 'region_id');
    }

    public function villages()
    {
        return $this->hasManyThrough(Village::class, Province::class);
    }
}
