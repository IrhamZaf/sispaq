<?php

namespace App\Livewire\Public;

use App\Models\Certificate;
use Livewire\Component;

class CertificateVerify extends Component
{
    public $hash = null;
    public $searchQuery = '';
    public $certificate = null;
    public $searched = false;

    public function mount($hash = null)
    {
        if ($hash && $hash !== 'semak') {
            $this->hash = $hash;
            $this->certificate = Certificate::with(['student', 'course'])
                ->where('qr_code_hash', $hash)
                ->first();
            $this->searched = true;
        }
    }

    public function verifyCertificate()
    {
        $this->validate([
            'searchQuery' => 'required',
        ], [
            'searchQuery.required' => 'Sila masukkan nombor sijil untuk carian.',
        ]);

        $this->certificate = Certificate::with(['student', 'course'])
            ->where('certificate_number', trim($this->searchQuery))
            ->first();

        $this->searched = true;
    }

    public function render()
    {
        return view('livewire.public.certificate-verify')
            ->layout('layouts.blankLayout'); // Public page, no auth or sidebars
    }
}
