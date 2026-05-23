<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class LecturerManagement extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 7;

    // Form fields
    public $userId = null;
    public $name = '';
    public $email = '';
    public $password = '';
    public $phone = '';
    public $ic_number = '';
    public $address = '';

    protected function rules()
    {
        $emailRule = $this->userId ? 'required|email|unique:users,email,' . $this->userId : 'required|email|unique:users,email';
        $passwordRule = $this->userId ? 'nullable|min:6' : 'required|min:6';

        return [
            'name' => 'required|string|max:255',
            'email' => $emailRule,
            'password' => $passwordRule,
            'phone' => 'required|string',
            'ic_number' => 'required|string',
            'address' => 'nullable|string',
        ];
    }

    protected $messages = [
        'name.required' => 'Nama wajib dimasukkan.',
        'email.required' => 'E-mel wajib dimasukkan.',
        'email.email' => 'Format e-mel tidak sah.',
        'email.unique' => 'E-mel ini telah digunakan.',
        'password.required' => 'Kata laluan wajib dimasukkan.',
        'password.min' => 'Kata laluan mestilah sekurang-kurangnya 6 aksara.',
        'phone.required' => 'Nombor telefon wajib dimasukkan.',
        'ic_number.required' => 'Nombor IC wajib dimasukkan.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->phone = '';
        $this->ic_number = '';
        $this->address = '';
    }

    public function openEditModal($id)
    {
        $this->resetValidation();
        $this->userId = $id;
        $user = User::findOrFail($id);
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->phone = $user->phone;
        $this->ic_number = $user->ic_number;
        $this->address = $user->address;
    }

    public function saveLecturer()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'ic_number' => $this->ic_number,
            'address' => $this->address,
            'role' => 'teacher',
        ];

        if (!empty($this->password)) {
            $data['password'] = bcrypt($this->password);
        }

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $user->update($data);
            session()->flash('success', 'Akaun pensyarah berjaya dikemas kini.');
        } else {
            User::create($data);
            session()->flash('success', 'Akaun pensyarah baharu berjaya didaftarkan.');
        }

        $this->dispatch('close-modal');
    }

    public function deleteLecturer($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        session()->flash('success', 'Akaun pensyarah berjaya dipadam.');
    }

    public function render()
    {
        $query = User::where('role', 'teacher')->orderBy('name');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                   ->orWhere('email', 'like', '%' . $this->search . '%')
                   ->orWhere('ic_number', 'like', '%' . $this->search . '%')
                   ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        $lecturers = $query->paginate($this->perPage);

        return view('livewire.admin.lecturer-management', [
            'lecturers' => $lecturers,
        ])->layout('layouts.contentNavbarLayout');
    }
}
