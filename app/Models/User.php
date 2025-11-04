<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
  use HasFactory, Notifiable;

  protected $primaryKey = 'user_id';
  public $timestamps = true;

  protected $fillable = [
    'user_name',
    'email',
    'password',
    'phone_number',
    'avatar',
    'address',
    'role',
    'status',
  ];

  // Quan hệ với bảng Tours (1 partner có nhiều tours)
  public function tours()
  {
    return $this->hasMany(Tour::class, 'partner_id', 'user_id');
  }

  // Người dùng có thể đặt nhiều tour
  public function bookings()
  {
    return $this->hasMany(Booking::class, 'user_id', 'user_id');
  }
}
