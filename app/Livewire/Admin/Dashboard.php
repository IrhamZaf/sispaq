<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Models\Payment;
use App\Models\PlacementTest;
use App\Models\StudentApplication;
use App\Models\StudentEnrollment;
use App\Models\User;
use Livewire\Component;

class Dashboard extends Component
{
    public $totalStudents = 0;
    public $pendingApplicationsCount = 0;
    public $scheduledTestsCount = 0;
    public $pendingPaymentsCount = 0;

    public $pendingApplications = [];
    public $upcomingPlacementTests = [];

    public function mount()
    {
        $this->refreshStats();
    }

    public function refreshStats()
    {
        $this->totalStudents = User::where('role', 'student')->count();
        
        $this->pendingApplicationsCount = StudentApplication::whereIn('status', ['pending', 'under_review'])->count();
        
        $this->scheduledTestsCount = PlacementTest::where('status', 'scheduled')->count();
        
        $this->pendingPaymentsCount = Payment::where('payment_status', 'pending')->count();

        // Get top 5 pending applications for quick review
        $this->pendingApplications = StudentApplication::with(['user', 'course'])
            ->whereIn('status', ['pending', 'under_review'])
            ->orderBy('created_at', 'asc')
            ->take(5)
            ->get();

        // Get top 5 upcoming scheduled placement tests
        $this->upcomingPlacementTests = PlacementTest::with(['student', 'application.course', 'examiner'])
            ->where('status', 'scheduled')
            ->orderBy('test_date_time', 'asc')
            ->take(5)
            ->get();
    }

    public function approveDirect($appId)
    {
        $app = StudentApplication::findOrFail($appId);
        $app->update(['status' => 'offered']);

        session()->flash('success', 'Permohonan terpilih telah diluluskan dan tawaran telah dihantar.');
        $this->refreshStats();
    }

    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('layouts.contentNavbarLayout');
    }
}
