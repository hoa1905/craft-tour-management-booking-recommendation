<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourSchedule extends Model
{
    use HasFactory;

    protected $primaryKey = 'schedule_id';
    public $timestamps = false;

    protected $fillable = [
        'tour_id',
        'start_date',
        'end_date',
        'seats_available',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }

    // Tự động tính end_date khi tạo schedule
    public static function boot()
    {
        parent::boot();

        static::creating(function ($schedule) {
            if (!$schedule->end_date && $schedule->tour) {
                $schedule->end_date = \Carbon\Carbon::parse($schedule->start_date)
                    ->addDays($schedule->tour->duration_days - 1);
            }
        });
    }
}
