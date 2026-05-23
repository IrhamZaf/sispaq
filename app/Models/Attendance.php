<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'class_schedule_id',
        'talaqqi_booking_id',
        'status',
        'recorded_at',
    ];

    public function enrollment()
    {
        return $this->belongsTo(StudentEnrollment::class, 'enrollment_id');
    }

    public function classSchedule()
    {
        return $this->belongsTo(ClassSchedule::class, 'class_schedule_id');
    }

    public function talaqqiBooking()
    {
        return $this->belongsTo(TalaqqiBooking::class, 'talaqqi_booking_id');
    }
}
