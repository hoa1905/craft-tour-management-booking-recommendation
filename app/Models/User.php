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

  // Người dùng có thể đặt nhiều tour
  public function bookings()
  {
    return $this->hasMany(Booking::class, 'user_id', 'user_id');
  }

  // User có nhiều đánh giá
  public function reviews()
  {
    return $this->hasMany(Review::class, 'user_id', 'user_id');
  }
}
