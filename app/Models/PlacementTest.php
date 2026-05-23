<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlacementTest extends Model
{
    use HasFactory;

    protected $table = 'placements_tests';

    protected $fillable = [
        'application_id',
        'student_id',
        'examiner_id',
        'test_date_time',
        'reading_score',
        'notes',
        'recommended_course_id',
        'status',
    ];

    public function application()
    {
        return $this->belongsTo(StudentApplication::class, 'application_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function examiner()
    {
        return $this->belongsTo(User::class, 'examiner_id');
    }

    public function recommendedCourse()
    {
        return $this->belongsTo(Course::class, 'recommended_course_id');
    }
}
