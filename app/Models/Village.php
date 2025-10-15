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
        'village_name',
        'region',
        'province',
    ];

    // Quan hệ với bảng Tours (1 village có nhiều tours)
    public function tours()
    {
        return $this->hasMany(Tour::class, 'village_id', 'village_id');
    }
}
