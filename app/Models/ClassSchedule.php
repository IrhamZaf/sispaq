<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClassSchedule extends Model
{
    use HasFactory;

    protected $table = 'classes_and_schedules';

    protected $fillable = [
        'course_id',
        'module_number',
        'schedule_datetime',
        'location',
        'is_hybrid',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'class_schedule_id');
    }
}
