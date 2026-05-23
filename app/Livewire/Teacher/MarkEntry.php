<?php

namespace App\Livewire\Teacher;

use App\Models\AcademicRecord;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Payment;
use App\Models\StudentEnrollment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MarkEntry extends Component
{
    public $courses;
    public $selectedCourseId = '';
    public $selectedModule = 1;
    
    public $students = [];
    public $marks = []; // enrollment_id => mark

    public function mount()
    {
        $this->courses = Course::all();
    }

    public function loadStudents()
    {
        if (!$this->selectedCourseId) {
            $this->students = [];
            return;
        }

        // Fetch students enrolled in selected course with current module equal to selected module
        $this->students = StudentEnrollment::with(['student', 'academicRecords'])
            ->where('course_id', $this->selectedCourseId)
            ->where('current_module', $this->selectedModule)
            ->where('status', 'active')
            ->get();

        // Pre-fill marks
        foreach ($this->students as $enroll) {
            $record = AcademicRecord::where('enrollment_id', $enroll->id)
                ->where('module_number', $this->selectedModule)
                ->first();
            $this->marks[$enroll->id] = $record ? $record->marks : '';
        }
    }

    public function saveGrades()
    {
        $this->validate([
            'selectedCourseId' => 'required|exists:courses,id',
            'selectedModule' => 'required|integer|min:1',
            'marks.*' => 'required|integer|between:0,100',
        ], [
            'marks.*.required' => 'Markah wajib diisi.',
            'marks.*.integer' => 'Markah mestilah nombor bulat.',
            'marks.*.between' => 'Markah mestilah antara 0 hingga 100.',
        ]);

        $course = Course::findOrFail($this->selectedCourseId);

        foreach ($this->marks as $enrollId => $mark) {
            $enrollment = StudentEnrollment::findOrFail($enrollId);
            $status = $mark >= 40 ? 'lulus' : 'gagal';

            // Create or update Academic Record
            AcademicRecord::updateOrCreate(
                [
                    'enrollment_id' => $enrollment->id,
                    'module_number' => $this->selectedModule,
                ],
                [
                    'marks' => $mark,
                    'status' => $status,
                    'recorded_by' => Auth::id(),
                ]
            );

            if ($status === 'lulus') {
                $totalModules = $course->total_modules ?? 1;

                if ($this->selectedModule < $totalModules) {
                    // 1. Increment current module
                    $nextModule = $this->selectedModule + 1;
                    $enrollment->update([
                        'current_module' => $nextModule,
                    ]);

                    // 2. Generate payment invoice for next module
                    $fee = $course->fee_per_module;
                    if ($course->code === 'KIBLAT') {
                        // Check original application for category or default to Cat B
                        $app = $enrollment->student->applications()
                            ->where('course_id', $course->id)
                            ->first();
                        $fee = ($app && $app->kiblat_category === 'A') 
                            ? $course->kiblat_cat_a_fee 
                            : $course->kiblat_cat_b_fee;
                    }

                    Payment::updateOrCreate(
                        [
                            'enrollment_id' => $enrollment->id,
                            'module_number' => $nextModule,
                        ],
                        [
                            'student_id' => $enrollment->student_id,
                            'course_id' => $course->id,
                            'amount' => $fee ?? 600.00,
                            'payment_status' => 'pending',
                        ]
                    );

                } else {
                    // Passed final module! Set status to completed and generate certificate
                    $enrollment->update(['status' => 'completed']);

                    // Generate Unique Certificate
                    $certType = 'lulus';
                    if ($course->code === 'KIBLAT') {
                        $certType = 'kemahiran';
                    } elseif (in_array($course->code, ['Pra KuTAB', 'KuTAB'])) {
                        $certType = 'sanad';
                    }

                    $certNo = 'CERT-' . strtoupper($course->code) . '-' . date('Y') . '-' . rand(10000, 99999);
                    $qrHash = md5($enrollment->student_id . '-' . $course->id . '-' . time() . '-' . rand());

                    Certificate::updateOrCreate(
                        [
                            'student_id' => $enrollment->student_id,
                            'course_id' => $course->id,
                        ],
                        [
                            'certificate_type' => $certType,
                            'certificate_number' => $certNo,
                            'issue_date' => now()->toDateString(),
                            'qr_code_hash' => $qrHash,
                        ]
                    );
                }
            }
        }

        session()->flash('success', 'Markah penilaian bagi Modul ' . $this->selectedModule . ' berjaya disimpan.');
        $this->loadStudents();
    }

    public function render()
    {
        return view('livewire.teacher.mark-entry')
            ->layout('layouts.contentNavbarLayout');
    }
}
