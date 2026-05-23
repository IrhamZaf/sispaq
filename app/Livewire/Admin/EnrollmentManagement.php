<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Models\StudentApplication;
use App\Models\StudentEnrollment;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class EnrollmentManagement extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filters & Search
    public $statusFilter = 'all';
    public $courseFilter = 'all';
    public $search = '';
    public $perPage = 7;

    // Collections loaded once
    public $courses;

    // Edit fields (Enrollment)
    public $editingEnrollId = null;
    public $editCurrentModule = 1;
    public $editStatus = 'active';

    // Edit fields (Student Profile)
    public $editingStudentId = null;
    public $editStudentName = '';
    public $editStudentIC = '';
    public $editStudentPhone = '';
    public $editStudentIdNum = '';
    public $editStudentCourse = '';
    public $editStudentFaculty = '';

    public function mount()
    {
        $this->courses = Course::all();
    }

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

    public function editEnrollment($enrollId)
    {
        $this->editingEnrollId = $enrollId;
        $enroll = StudentEnrollment::findOrFail($enrollId);
        $this->editCurrentModule = $enroll->current_module;
        $this->editStatus = $enroll->status;
    }

    public function updateEnrollment()
    {
        $this->validate([
            'editCurrentModule' => 'required|integer|min:1',
            'editStatus' => 'required|in:active,completed,dropped',
        ]);

        $enroll = StudentEnrollment::findOrFail($this->editingEnrollId);
        $enroll->update([
            'current_module' => $this->editCurrentModule,
            'status' => $this->editStatus,
        ]);

        $this->editingEnrollId = null;
        session()->flash('success', 'Pendaftaran pelajar berjaya dikemas kini.');
    }

    public function openEditStudentModal($studentId)
    {
        $this->resetValidation();
        $this->editingStudentId = $studentId;
        $student = User::findOrFail($studentId);
        $this->editStudentName = $student->name;
        $this->editStudentIC = $student->ic_number;
        $this->editStudentPhone = $student->phone;
        $this->editStudentIdNum = $student->student_id;
        $this->editStudentCourse = $student->uni_course;
        $this->editStudentFaculty = $student->uni_faculty;
    }

    public function updateStudent()
    {
        $this->validate([
            'editStudentName' => 'required|string|max:255',
            'editStudentIC' => 'required|string|max:20',
            'editStudentPhone' => 'required|string|max:20',
            'editStudentIdNum' => 'nullable|string|max:255',
            'editStudentCourse' => 'nullable|string|max:255',
            'editStudentFaculty' => 'nullable|string|max:255',
        ]);

        $student = User::findOrFail($this->editingStudentId);
        $student->update([
            'name' => $this->editStudentName,
            'ic_number' => $this->editStudentIC,
            'phone' => $this->editStudentPhone,
            'student_id' => $this->editStudentIdNum,
            'uni_course' => $this->editStudentCourse,
            'uni_faculty' => $this->editStudentFaculty,
        ]);

        $this->editingStudentId = null;
        session()->flash('success', 'Profil peribadi dan maklumat universiti pelajar berjaya dikemas kini.');
        $this->dispatch('close-modal');
    }

    public function deleteEnrollment($enrollId)
    {
        $enroll = StudentEnrollment::findOrFail($enrollId);
        $enroll->delete();
        session()->flash('success', 'Pendaftaran pelajar berjaya dipadam.');
    }

    public function render()
    {
        $query = User::where('role', 'student')
            ->with(['enrollments' => function ($q) {
                $q->with('course')->orderByDesc('created_at');
                
                // Apply filters directly to the nested relation
                if ($this->statusFilter !== 'all') {
                    $q->where('status', $this->statusFilter);
                }
                if ($this->courseFilter !== 'all') {
                    $q->where('course_id', $this->courseFilter);
                }
            }])
            ->whereHas('enrollments', function ($q) {
                // Only show students who have enrollments matching filters
                if ($this->statusFilter !== 'all') {
                    $q->where('status', $this->statusFilter);
                }
                if ($this->courseFilter !== 'all') {
                    $q->where('course_id', $this->courseFilter);
                }
            })
            ->orderBy('name');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                   ->orWhere('ic_number', 'like', '%' . $this->search . '%')
                   ->orWhere('phone', 'like', '%' . $this->search . '%')
                   ->orWhere('student_id', 'like', '%' . $this->search . '%')
                   ->orWhere('uni_course', 'like', '%' . $this->search . '%')
                   ->orWhere('uni_faculty', 'like', '%' . $this->search . '%')
                   ->orWhereHas('enrollments.course', function ($qu) {
                       $qu->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('code', 'like', '%' . $this->search . '%');
                   });
            });
        }

        $students = $query->paginate($this->perPage);

        $offeredApplications = StudentApplication::with(['user', 'course'])
            ->where('status', 'offered')
            ->orderByDesc('updated_at')
            ->get();

        return view('livewire.admin.enrollment-management', [
            'students' => $students,
            'offeredApplications' => $offeredApplications,
        ])->layout('layouts.contentNavbarLayout');
    }
}
