<?php

namespace App\Livewire\Student;

use App\Models\Course;
use App\Models\StudentApplication;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class ApplicationForm extends Component
{
    use WithFileUploads;

    public $course;
    public $kiblat_category = '';
    public $reading_level = '';
    public $ic_document;
    public $supporting_documents;
    
    public $step = 1;
    public $successMessage = '';

    protected $rules = [
        'ic_document' => 'required|file|mimes:pdf,jpg,png,jpeg|max:2048',
        'supporting_documents' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:2048',
    ];

    public function mount($course_id)
    {
        $this->course = Course::findOrFail($course_id);

        // Check if student has already applied for this course
        $existing = StudentApplication::where('user_id', Auth::id())
            ->where('course_id', $this->course->id)
            ->whereIn('status', ['pending', 'under_review', 'test_scheduled', 'offered', 'approved'])
            ->first();

        if ($existing) {
            session()->flash('error', 'Anda telah memohon untuk program ini dan permohonan sedang diproses.');
            return redirect()->route('student.dashboard');
        }
    }

    public function nextStep()
    {
        if ($this->step === 1) {
            if ($this->course->code === 'KIBLAT') {
                $this->validate([
                    'kiblat_category' => 'required|in:A,B',
                ], [
                    'kiblat_category.required' => 'Sila pilih kategori kelayakan anda.',
                ]);
            }
            if (in_array($this->course->code, ['Pra KuTAB', 'KuTAB'])) {
                $this->validate([
                    'reading_level' => 'required',
                ], [
                    'reading_level.required' => 'Sila pilih tahap pembacaan Al-Quran anda.',
                ]);
            }
            $this->step = 2;
        }
    }

    public function prevStep()
    {
        $this->step = 1;
    }

    public function submitApplication()
    {
        $this->validate();

        // Store IC document
        $icPath = null;
        if ($this->ic_document) {
            $icName = 'ic_' . Auth::id() . '_' . time() . '.' . $this->ic_document->getClientOriginalExtension();
            $this->ic_document->storeAs('uploads/applications', $icName, 'public');
            $icPath = 'uploads/applications/' . $icName;
        }

        // Store supporting documents
        $supportPath = null;
        if ($this->supporting_documents) {
            $supportName = 'support_' . Auth::id() . '_' . time() . '.' . $this->supporting_documents->getClientOriginalExtension();
            $this->supporting_documents->storeAs('uploads/applications', $supportName, 'public');
            $supportPath = 'uploads/applications/' . $supportName;
        }

        // Check auto eligibility
        // Let's say courses like KPAQ, KPI, KPAH are auto eligible if IC is uploaded. 
        // For KuTAB and Pra KuTAB, they must go through placement test, status = 'pending'.
        $autoEligible = false;
        $status = 'pending';

        if (in_array($this->course->code, ['KPAQ', 'KPAH', 'KBA', 'KPI', 'KTKT'])) {
            $autoEligible = true;
            $status = 'offered'; // Immediately offer seat to student
        }

        StudentApplication::create([
            'user_id' => Auth::id(),
            'course_id' => $this->course->id,
            'kiblat_category' => $this->course->code === 'KIBLAT' ? $this->kiblat_category : null,
            'reading_level' => in_array($this->course->code, ['Pra KuTAB', 'KuTAB']) ? $this->reading_level : null,
            'ic_document' => $icPath,
            'supporting_documents' => $supportPath,
            'status' => $status,
            'auto_eligible' => $autoEligible,
        ]);

        if ($status === 'offered') {
            session()->flash('success', 'Permohonan anda telah berjaya! Anda ditawarkan tempat serta-merta. Sila buat pembayaran yuran untuk pendaftaran.');
        } else {
            session()->flash('success', 'Permohonan anda telah berjaya dihantar dan kini sedang disemak.');
        }

        return redirect()->route('student.dashboard');
    }

    public function render()
    {
        return view('livewire.student.application-form')
            ->layout('layouts.contentNavbarLayout');
    }
}
