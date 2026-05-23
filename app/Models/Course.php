<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'total_modules',
        'fee_per_module',
        'duration_per_module',
        'schedule_day_time',
        'description',
        'requirements',
        'kiblat_cat_a_fee',
        'kiblat_cat_b_fee',
        'teacher_id',
    ];

    public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function applications()
    {
        return $this->hasMany(StudentApplication::class);
    }

    public function isTalaqqi(): bool
    {
        return in_array($this->code, ['Pra KuTAB', 'KuTAB', 'KTKT'], true);
    }

    public function programCategory(): string
    {
        return match (true) {
            in_array($this->code, ['KPAQ', 'KPAH'], true) => 'quran_hadith',
            $this->code === 'KBA' => 'bahasa',
            $this->isTalaqqi() => 'talaqqi',
            $this->code === 'KPI' => 'perubatan',
            $this->code === 'KIBLAT' => 'falak',
            default => 'other',
        };
    }

    public function displayFee(): ?float
    {
        if ($this->fee_per_module !== null) {
            return (float) $this->fee_per_module;
        }

        if ($this->code === 'KIBLAT') {
            $fees = array_filter([
                $this->kiblat_cat_a_fee ? (float) $this->kiblat_cat_a_fee : null,
                $this->kiblat_cat_b_fee ? (float) $this->kiblat_cat_b_fee : null,
            ]);

            return $fees ? (float) min($fees) : null;
        }

        return null;
    }

    public function requiresPlacementTest(): bool
    {
        return in_array($this->code, ['Pra KuTAB', 'KuTAB'], true);
    }

    public function attendanceThreshold(): int
    {
        return match ($this->code) {
            'KPAH' => 70,
            'KPAQ', 'KPI' => 80,
            default => 80,
        };
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}

