<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'current_module',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'enrollment_id');
    }

    public function talaqqiBookings()
    {
        return $this->hasMany(TalaqqiBooking::class, 'enrollment_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'enrollment_id');
    }

    public function academicRecords()
    {
        return $this->hasMany(AcademicRecord::class, 'enrollment_id');
    }
}
