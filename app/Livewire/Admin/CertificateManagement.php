<?php

namespace App\Livewire\Admin;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Component;

class CertificateManagement extends Component
{
    public $certificates;
    public $previewCertificate = null;

    public function mount()
    {
        $this->refreshList();
    }

    public function refreshList()
    {
        $this->certificates = Certificate::with(['student', 'course'])
            ->orderByDesc('issue_date')
            ->get();
    }

    public function preview($id)
    {
        $this->previewCertificate = Certificate::with(['student', 'course'])->findOrFail($id);
    }

    public function issueManual($enrollmentId, $type = 'lulus')
    {
        $enrollment = StudentEnrollment::with(['student', 'course'])->findOrFail($enrollmentId);
        $number = 'SISPAQ-' . $enrollment->course->code . '-' . date('Y') . '-' . str_pad((string) $enrollment->id, 5, '0', STR_PAD_LEFT);

        Certificate::create([
            'student_id' => $enrollment->student_id,
            'course_id' => $enrollment->course_id,
            'certificate_type' => $type,
            'certificate_number' => $number,
            'issue_date' => now()->toDateString(),
            'qr_code_hash' => Str::random(32),
        ]);

        session()->flash('success', 'Sijil dijana (pratonton HTML, PDF simulasi).');
        $this->refreshList();
    }

    public function render()
    {
        return view('livewire.admin.certificate-management')
            ->layout('layouts.contentNavbarLayout');
    }
}
