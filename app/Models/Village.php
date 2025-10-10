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
}
