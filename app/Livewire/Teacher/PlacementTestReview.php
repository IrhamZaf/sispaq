<?php

namespace App\Livewire\Teacher;

use App\Models\Course;
use App\Models\PlacementTest;
use App\Services\PlacementTestService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PlacementTestReview extends Component
{
    public $tests;
    public $courses;
    public $selectedTestId = null;
    public $readingScore = '';
    public $recommendedCourseId = '';
    public $testNotes = '';

    public function mount()
    {
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->tests = PlacementTest::with(['application.user', 'application.course', 'student'])
            ->where('examiner_id', Auth::id())
            ->orderByDesc('test_date_time')
            ->get();
        $this->courses = Course::all();
    }

    public function selectTest($testId)
    {
        $this->selectedTestId = $testId;
        $test = PlacementTest::with('application.course')->findOrFail($testId);
        $this->readingScore = $test->reading_score ?? '';
        $this->testNotes = $test->notes ?? '';
        $this->recommendedCourseId = $test->recommended_course_id
            ?? PlacementTestService::suggestRecommendedCourse(
                (int) ($this->readingScore ?: 50),
                $test->application->course->code
            );
    }

    public function recordResult()
    {
        $this->validate([
            'readingScore' => 'required|integer|min:0|max:100',
            'testNotes' => 'nullable|string|max:500',
        ]);

        $test = PlacementTest::findOrFail($this->selectedTestId);
        PlacementTestService::completeTest(
            $test,
            (int) $this->readingScore,
            $this->testNotes ?? '',
            $this->recommendedCourseId ?: null
        );

        session()->flash('success', 'Keputusan ujian penempatan direkod. Pelajar menerima status tawaran.');
        $this->selectedTestId = null;
        $this->refreshData();
    }

    public function render()
    {
        return view('livewire.teacher.placement-test-review')
            ->layout('layouts.contentNavbarLayout');
    }
}
