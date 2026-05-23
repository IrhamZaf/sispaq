<?php

namespace App\Livewire\Admin;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Payment;
use App\Models\StudentApplication;
use App\Models\StudentEnrollment;
use App\Models\User;
use Livewire\Component;

class Reports extends Component
{
    public $totalStudents = 0;
    public $totalApplications = 0;
    public $totalActiveEnrollments = 0;
    public $totalRevenue = 0;
    public $totalCertificates = 0;

    public $courseStats = [];
    public $recentPayments = [];

    public function mount()
    {
        $this->refreshStats();
    }

    public function refreshStats()
    {
        $this->totalStudents = User::where('role', 'student')->count();
        $this->totalApplications = StudentApplication::count();
        $this->totalActiveEnrollments = StudentEnrollment::where('status', 'active')->count();
        $this->totalRevenue = Payment::where('payment_status', 'paid')->sum('amount');
        $this->totalCertificates = Certificate::count();

        // Fetch course specific stats
        $courses = Course::all();
        $this->courseStats = [];
        
        foreach ($courses as $c) {
            $enrollments = StudentEnrollment::where('course_id', $c->id)->get();
            $activeCount = $enrollments->where('status', 'active')->count();
            $completedCount = $enrollments->where('status', 'completed')->count();
            $revenue = Payment::where('course_id', $c->id)->where('payment_status', 'paid')->sum('amount');
            
            $this->courseStats[] = [
                'code' => $c->code,
                'name' => $c->name,
                'active' => $activeCount,
                'completed' => $completedCount,
                'revenue' => $revenue,
            ];
        }

        // Fetch recent payments
        $this->recentPayments = Payment::with(['student', 'course'])
            ->where('payment_status', 'paid')
            ->orderBy('paid_at', 'desc')
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.reports')
            ->layout('layouts.contentNavbarLayout');
    }
}
