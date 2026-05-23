<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AcademicRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'module_number',
        'marks',
        'status',
        'recorded_by',
    ];

    public function enrollment()
    {
        return $this->belongsTo(StudentEnrollment::class, 'enrollment_id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
