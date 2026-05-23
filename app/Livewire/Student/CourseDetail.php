<?php

namespace App\Livewire\Student;

use App\Livewire\Concerns\CalculatesAttendance;
use App\Models\AcademicRecord;
use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\ClassSchedule;
use App\Models\StudentEnrollment;
use App\Models\TalaqqiBooking;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;

class CourseDetail extends Component
{
    use CalculatesAttendance;

    public StudentEnrollment $enrollment;

    public $academicRecords;

    public $talaqqiBookings;

    public $schedules;

    public $attendances;

    public ?Certificate $certificate = null;

    #[Url]
    public $section = 'jadual';

    public function mount($enrollment_id)
    {
        $this->enrollment = StudentEnrollment::with('course')
            ->where('student_id', Auth::id())
            ->findOrFail($enrollment_id);

        $allowedSections = ['jadual', 'kehadiran', 'akademik'];
        if ($this->isCourseCompleted()) {
            $allowedSections[] = 'sijil';
            $this->certificate = Certificate::where('student_id', Auth::id())
                ->where('course_id', $this->enrollment->course_id)
                ->first();
        }

        if (! in_array($this->section, $allowedSections, true)) {
            $this->section = 'jadual';
        }

        $this->academicRecords = AcademicRecord::where('enrollment_id', $this->enrollment->id)
            ->orderBy('module_number')
            ->get();

        $this->talaqqiBookings = TalaqqiBooking::with('teacher')
            ->where('enrollment_id', $this->enrollment->id)
            ->orderByDesc('booking_datetime')
            ->get();

        $this->schedules = ClassSchedule::where('course_id', $this->enrollment->course_id)
            ->orderBy('schedule_datetime')
            ->get();

        $this->attendances = Attendance::with(['classSchedule', 'talaqqiBooking.teacher'])
            ->where('enrollment_id', $this->enrollment->id)
            ->orderByDesc('recorded_at')
            ->get();
    }

    public function attendanceSessionLabel(Attendance $attendance): string
    {
        if ($attendance->classSchedule) {
            return 'Kelas Modul ' . $attendance->classSchedule->module_number;
        }

        if ($attendance->talaqqiBooking) {
            return 'Talaqqi m/s ' . $attendance->talaqqiBooking->current_page;
        }

        return 'Sesi';
    }

    public function attendanceStatusClass(string $status): string
    {
        return match ($status) {
            'hadir' => 'success',
            'lewat', 'bersebab' => 'warning',
            default => 'danger',
        };
    }

    public function attendanceStatusLabel(string $status): string
    {
        $key = "sispaq.status.attendance.{$status}";
        $label = __($key);

        return $label !== $key ? $label : ucfirst(str_replace('_', ' ', $status));
    }

    public function isCourseCompleted(): bool
    {
        return $this->enrollment->status === 'completed';
    }

    public function certificateTypeLabel(?string $type): string
    {
        if (! $type) {
            return '';
        }

        $key = "sispaq.course_detail.cert_type_{$type}";
        $label = __($key);

        return $label !== $key ? $label : ucfirst($type);
    }

    public function moduleProgress(): int
    {
        $total = (int) ($this->enrollment->course->total_modules ?? 0);
        if ($total < 1) {
            return 0;
        }

        return (int) min(100, round(($this->enrollment->current_module / $total) * 100));
    }

    public function passedModulesCount(): int
    {
        return $this->academicRecords->where('status', 'lulus')->count();
    }

    public function attendanceSummary(): array
    {
        $present = $this->attendances->whereIn('status', ['hadir', 'lewat', 'bersebab'])->count();
        $total = $this->attendances->count();

        return [
            'rate' => $this->getAttendanceRate($this->enrollment->id),
            'threshold' => $this->enrollment->course->attendanceThreshold(),
            'present' => $present,
            'total' => $total,
            'absent' => $total - $present,
        ];
    }

    public function getUpcomingSessionsProperty(): Collection
    {
        $now = now()->timestamp;

        if ($this->enrollment->course->isTalaqqi()) {
            return $this->talaqqiBookings
                ->filter(fn ($b) => strtotime($b->booking_datetime) >= $now)
                ->sortBy('booking_datetime')
                ->values();
        }

        return $this->schedules
            ->filter(fn ($s) => strtotime($s->schedule_datetime) >= $now)
            ->sortBy('schedule_datetime')
            ->values();
    }

    public function getNextSessionProperty(): mixed
    {
        return $this->upcomingSessions->first();
    }

    public function render()
    {
        return view('livewire.student.course-detail')
            ->layout('layouts.contentNavbarLayout');
    }
}
