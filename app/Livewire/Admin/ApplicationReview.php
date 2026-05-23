<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Models\PlacementTest;
use App\Models\StudentApplication;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ApplicationReview extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filters & Search
    public $statusFilter = 'all';
    public $courseFilter = 'all';
    public $search = '';
    public $perPage = 7;

    // Collections loaded once
    public $teachers;
    public $courses;

    // Schedule Placement Test form fields
    public $selectedAppId = null;
    public $examinerId = '';
    public $testDateTime = '';

    // Record Test Result form fields
    public $selectedTestId = null;
    public $readingScore = '';
    public $recommendedCourseId = '';
    public $testNotes = '';

    // Edit Application form fields
    public $editingAppId = null;
    public $editCourseId = '';
    public $editKiblatCategory = '';
    public $editReadingLevel = '';
    public $editStatus = '';

    protected $rules = [
        'examinerId' => 'required|exists:users,id',
        'testDateTime' => 'required|date|after:now',
    ];

    public function mount()
    {
        $this->teachers = User::where('role', 'teacher')->get();
        $this->courses = Course::all();
    }

    // Reset pagination when filter or search changes
    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingCourseFilter()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function changeStatus($appId, $status)
    {
        $app = StudentApplication::findOrFail($appId);
        $app->update(['status' => $status]);
        
        session()->flash('success', 'Status permohonan berjaya dikemas kini kepada ' . strtoupper($status) . '.');
    }

    public function selectAppForTest($appId)
    {
        $this->selectedAppId = $appId;
        $app = StudentApplication::findOrFail($appId);
        $this->examinerId = $this->teachers->first()?->id ?? '';
        $this->testDateTime = date('Y-m-d\TH:i', strtotime('+1 day'));
    }

    public function scheduleTest()
    {
        $this->validate();

        $app = StudentApplication::findOrFail($this->selectedAppId);

        // Create or update Placement Test
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
        session()->flash('success', 'Ujian penempatan telah dijadualkan dengan jayanya.');
    }

    public function selectTestForResult($testId)
    {
        $this->selectedTestId = $testId;
        $test = PlacementTest::findOrFail($testId);
        $this->readingScore = $test->reading_score ?? '';
        $this->recommendedCourseId = $test->recommended_course_id ?? $test->application->course_id;
        $this->testNotes = $test->notes ?? '';
    }

    public function recordTestResult()
    {
        $this->validate([
            'readingScore' => 'required',
            'recommendedCourseId' => 'required|exists:courses,id',
        ], [
            'readingScore.required' => 'Sila masukkan markah/tahap pembacaan.',
            'recommendedCourseId.required' => 'Sila pilih program pengajian disyorkan.',
        ]);

        $test = PlacementTest::findOrFail($this->selectedTestId);
        $test->update([
            'reading_score' => $this->readingScore,
            'recommended_course_id' => $this->recommendedCourseId,
            'notes' => $this->testNotes,
            'status' => 'completed',
        ]);

        // Offer recommended course instead of original course if different
        $app = $test->application;
        $app->update([
            'course_id' => $this->recommendedCourseId,
            'status' => 'offered', // Offer seat after test completed
        ]);

        $this->selectedTestId = null;
        session()->flash('success', 'Keputusan ujian penempatan direkodkan. Tawaran telah dihantar kepada pelajar.');
    }

    // Edit Application
    public function editApplication($appId)
    {
        $this->editingAppId = $appId;
        $app = StudentApplication::findOrFail($appId);
        $this->editCourseId = $app->course_id;
        $this->editKiblatCategory = $app->kiblat_category ?? '';
        $this->editReadingLevel = $app->reading_level ?? '';
        $this->editStatus = $app->status;
    }

    public function updateApplication()
    {
        $this->validate([
            'editCourseId' => 'required|exists:courses,id',
            'editStatus' => 'required|in:pending,under_review,test_scheduled,offered,approved,rejected',
        ]);

        $app = StudentApplication::findOrFail($this->editingAppId);
        $course = Course::findOrFail($this->editCourseId);

        $kiblatCategory = ($course->code === 'KIBLAT') ? $this->editKiblatCategory : null;
        $readingLevel = in_array($course->code, ['Pra KuTAB', 'KuTAB']) ? $this->editReadingLevel : null;

        $app->update([
            'course_id' => $this->editCourseId,
            'kiblat_category' => $kiblatCategory,
            'reading_level' => $readingLevel,
            'status' => $this->editStatus,
        ]);

        $this->editingAppId = null;
        session()->flash('success', 'Permohonan pelajar berjaya dikemas kini.');
    }

    // Delete Application
    public function deleteApplication($appId)
    {
        $app = StudentApplication::findOrFail($appId);
        $app->delete();
        session()->flash('success', 'Permohonan berjaya dipadam.');
    }

    public function render()
    {
        $query = StudentApplication::with(['user', 'course', 'placementTest.examiner', 'placementTest.recommendedCourse'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->courseFilter !== 'all') {
            $query->where('course_id', $this->courseFilter);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->whereHas('user', function ($qu) {
                    $qu->where('name', 'like', '%' . $this->search . '%')
                       ->orWhere('ic_number', 'like', '%' . $this->search . '%')
                       ->orWhere('phone', 'like', '%' . $this->search . '%');
                })->orWhereHas('course', function ($qc) {
                    $qc->where('name', 'like', '%' . $this->search . '%')
                       ->orWhere('code', 'like', '%' . $this->search . '%');
                });
            });
        }

        $applications = $query->paginate($this->perPage);

        return view('livewire.admin.application-review', [
            'applications' => $applications,
        ])->layout('layouts.contentNavbarLayout');
    }
}
