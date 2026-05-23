<?php

namespace App\Livewire\Concerns;

use App\Models\Attendance;

trait CalculatesAttendance
{
    public function getAttendanceRate($enrollmentId): int
    {
        $records = Attendance::where('enrollment_id', $enrollmentId)->get();
        if ($records->isEmpty()) {
            return 100;
        }
        $present = $records->whereIn('status', ['hadir', 'lewat', 'bersebab'])->count();

        return (int) round(($present / $records->count()) * 100);
    }
}
