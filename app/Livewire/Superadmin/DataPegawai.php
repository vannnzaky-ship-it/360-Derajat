<?php

namespace App\Livewire\Superadmin;

use Livewire\Component;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

#[Layout('layouts.admin')]
class DataPegawai extends Component
{
    use WithPagination;

    // Properti untuk form
    public $name, $email, $password, $jabatan_id, $nip;
    public $selectedRoles = []; 

    // Properti untuk edit
    public $pegawaiId, $userId;
    public $isEditMode = false;

    // Properti untuk UI
    public $search = '';
    public $showModal = false;

    public function render()
    {
        $pegawaiQuery = Pegawai::with(['user.roles', 'jabatan'])
            ->whereHas('user', function ($query) {
                $query->whereDoesntHave('roles', function ($roleQuery) {
                    $roleQuery->where('name', 'superadmin');
                });
                $query->where('id', '!=', Auth::id()); 
                if ($this->search) {
                    $query->where(function($subQuery) {
                         $subQuery->where('name', 'like', '%'.$this->search.'%')
                                  ->orWhere('email', 'like', '%'.$this->search.'%');
                    });
                }
            });
            
        if ($this->search) {
             $pegawaiQuery->orWhere('nip', 'like', '%'.$this->search.'%');
        }

        $pegawai = $pegawaiQuery->paginate(10);

        $currentUser = Auth::user(); 
        $isAdminAccessing = false; 
        if ($currentUser) { 
            /** @var \App\Models\User $currentUser */ 
            if (!$currentUser->hasRole('superadmin')) { 
                $isAdminAccessing = true; 
            }
        }
        
        $availableRolesQuery = Role::where('name', '!=', 'superadmin');
        if ($isAdminAccessing) {
            $availableRolesQuery->where('name', '!=', 'admin');
        }
        $roleList = $availableRolesQuery->get();

        return view('livewire.superadmin.data-pegawai', [
            'pegawaiList' => $pegawai,
            'jabatanList' => Jabatan::all(),
            'roleList' => $roleList 
        ]); 
    }

    // Buka modal (kosong) - PASTIKAN INI ADA DAN PUBLIC
    public function create()
    {
        $this->resetForm();
        $this->isEditMode = false; // Pastikan mode edit false saat create
        $this->showModal = true;
    }

    // Buka modal (isi data)
    public function edit($id)
    {
        $pegawai = Pegawai::with('user.roles')->findOrFail($id);

        if ($pegawai->user->hasRole('superadmin')) {
            session()->flash('error', 'Tidak diizinkan mengedit akun Superadmin.'); 
            return; 
        }
        
        $this->pegawaiId = $pegawai->id;
        $this->userId = $pegawai->user_id;
        $this->name = $pegawai->user->name;
        $this->email = $pegawai->user->email;
        $this->nip = $pegawai->nip;
        $this->jabatan_id = $pegawai->jabatan_id;
        $this->selectedRoles = $pegawai->user->roles->pluck('id')->toArray();
        $this->password = ''; 
        
        $this->isEditMode = true;
        $this->showModal = true;
    }

    // Simpan (Create atau Update)
    public function store()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->userId), 
            ],
            'nip' => [
                'required',
                'string',
                'unique:pegawai,nip,' . ($this->pegawaiId ?? 'NULL') . ',id' 
            ],
            'jabatan_id' => 'required|exists:jabatan,id',
            'selectedRoles' => 'required|array|min:1',
        ];

        if (!$this->isEditMode) {
            $rules['password'] = 'required|string|min:8';
        } else {
            $rules['password'] = 'nullable|string|min:8'; 
        }

        $this->validate($rules); // Perbaiki typo validate()

        $userData = [
            'name' => $this->name,
            'email' => $this->email,
        ];
        if (!empty($this->password)) {
            $userData['password'] = Hash::make($this->password);
        }
        
        $user = User::updateOrCreate(['id' => $this->userId], $userData);
        
        Pegawai::updateOrCreate(
            ['id' => $this->pegawaiId],
            [
                'user_id' => $user->id,
                'jabatan_id' => $this->jabatan_id,
                'nip' => $this->nip
            ]
        );

        $user->roles()->sync($this->selectedRoles);

        session()->flash('message', $this->isEditMode ? 'Data Pegawai Berhasil Diperbarui' : 'Data Pegawai Berhasil Ditambahkan');
        $this->closeModal();
    }
    
    // Hapus
    #[On('deleteConfirmed')] 
    public function destroy($id) 
    {
        $user = User::find($id); 

        if ($user && $user->hasRole('superadmin')) {
            session()->flash('error', 'Tidak diizinkan menghapus akun Superadmin.'); 
            return; 
        }
       
        if ($user) {
            $user->delete(); 
            session()->flash('message', 'Data Pegawai Berhasil Dihapus');
        } else {
             session()->flash('error', 'Data Pegawai tidak ditemukan.');
        }
    }

    // Helper
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['name', 'email', 'password', 'jabatan_id', 'nip', 'selectedRoles', 'pegawaiId', 'userId', 'isEditMode']);
        $this->resetErrorBag(); // Bersihkan error validasi juga
        $this->resetValidation(); // Bersihkan status validasi
    }
}