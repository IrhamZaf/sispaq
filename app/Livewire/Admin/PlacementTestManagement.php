<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Models\PlacementTest;
use App\Models\StudentApplication;
use App\Models\User;
use Livewire\Component;

class PlacementTestManagement extends Component
{
    public $tests;
    public $pendingApps;
    public $teachers;
    public $selectedAppId = null;
    public $examinerId = '';
    public $testDateTime = '';

    public function mount()
    {
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->tests = PlacementTest::with(['application.user', 'application.course', 'student', 'examiner'])
            ->orderByDesc('test_date_time')
            ->get();
        $this->pendingApps = StudentApplication::with('user', 'course')
            ->whereIn('status', ['pending', 'under_review'])
            ->whereHas('course', fn ($q) => $q->whereIn('code', ['Pra KuTAB', 'KuTAB']))
            ->whereDoesntHave('placementTest')
            ->get();
        $this->teachers = User::where('role', 'teacher')->get();
    }

    public function selectAppForTest($appId)
    {
        $this->selectedAppId = $appId;
        $this->examinerId = $this->teachers->first()?->id ?? '';
        $this->testDateTime = date('Y-m-d\TH:i', strtotime('+1 day'));
    }

    public function scheduleTest()
    {
        $this->validate([
            'examinerId' => 'required|exists:users,id',
            'testDateTime' => 'required|date|after:now',
        ]);

        $app = StudentApplication::findOrFail($this->selectedAppId);

        PlacementTest::updateOrCreate(
            ['application_id' => $app->id],
            [
                'student_id' => $app->user_id,
                'examiner_id' => $this->examinerId,
                'test_date_time' => $this->testDateTime,
                'status' => 'scheduled',
            ]
        );

        $app->update(['status' => 'test_scheduled']);
        $this->selectedAppId = null;
        $this->refreshData();
        session()->flash('success', 'Ujian penempatan dijadualkan.');
    }

    public function render()
    {
        return view('livewire.admin.placement-test-management')
            ->layout('layouts.contentNavbarLayout');
    }
}
