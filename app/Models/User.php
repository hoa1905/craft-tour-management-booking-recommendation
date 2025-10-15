<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
  use HasFactory;

  protected $primaryKey = 'user_id';
  public $timestamps = true;

  protected $fillable = [
    'full_name',
    'email',
    'password',
    'phone',
    'role',
    'status',
  ];

  // Quan hệ với bảng Tours (1 partner có nhiều tours)
  public function tours()
  {
    return $this->hasMany(Tour::class, 'partner_id', 'user_id');
  }
}
