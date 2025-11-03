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
use Illuminate\Support\Collection; // Untuk grouping


#[Layout('layouts.admin')]
class DataPegawai extends Component
{
    use WithPagination;

    // Properti Form
    public $name, $email, $password, $nip;
    public $selectedRoles = []; 
    public $selectedJabatans = []; // Array untuk multiple jabatan

    // Properti Edit
    public $pegawaiId = null; 
    public $userId = null; 
    public $isEditMode = false;

    // Properti UI
    public $search = '';
    public $showModal = false;

    // Properti untuk daftar jabatan di view
    public Collection $allJabatans; // Koleksi semua jabatan
    public array $takenSingletonJabatans = []; // ID jabatan singleton yg terisi

    /**
     * Mount dijalankan sekali saat komponen dimuat
     */
    public function mount()
    {
        // Ambil semua jabatan sekali saja
        $this->allJabatans = Jabatan::orderBy('parent_id')->orderBy('nama_jabatan')->get();
        // Cek jabatan singleton awal (tanpa mengecualikan siapapun)
        $this->updateTakenSingletons(); 
    }
    
    /**
     * Memperbarui daftar ID jabatan singleton yang sudah terisi
     */
     public function updateTakenSingletons()
     {
         $this->takenSingletonJabatans = Jabatan::where('is_singleton', true)
             ->whereHas('pegawais', function ($query) {
                 // Kecualikan diri sendiri JIKA sedang mode edit
                 if ($this->isEditMode && $this->pegawaiId) {
                     $query->where('pegawai.id', '!=', $this->pegawaiId);
                 }
             })
             ->pluck('id') 
             ->toArray();
     }


    public function render()
    {
        // Query utama
        $pegawaiQuery = Pegawai::with(['user.roles', 'jabatans']) // Load jabatans (plural)
            ->whereHas('user', function ($query) {
                $query->whereDoesntHave('roles', fn($q) => $q->where('name', 'superadmin'));
                $query->where('id', '!=', Auth::id()); 
                if ($this->search) { 
                     $query->where(function($subQuery) {
                         $subQuery->where('name', 'like', '%'.$this->search.'%')
                                  ->orWhere('email', 'like', '%'.$this->search.'%');
                    });
                }
            });
            
        // Filter NIP atau Nama Jabatan
        if ($this->search) {
             $pegawaiQuery->where(function ($query) { // Bungkus orWhere agar tidak bentrok
                 $query->orWhere('nip', 'like', '%'.$this->search.'%')
                       ->orWhereHas('jabatans', function ($qJab) { // Cari berdasarkan nama jabatan
                           $qJab->where('nama_jabatan', 'like', '%'.$this->search.'%');
                       });
             });
        }

        $pegawai = $pegawaiQuery->paginate(10);

        // Logika role list
        $currentUser = Auth::user(); 
        $isAdminAccessing = false; 
        if ($currentUser && !$currentUser->hasRole('superadmin')) { $isAdminAccessing = true; }
        $availableRolesQuery = Role::where('name', '!=', 'superadmin');
        if ($isAdminAccessing) { $availableRolesQuery->where('name', '!=', 'admin'); }
        $roleList = $availableRolesQuery->get();
        
        // Kelompokkan jabatan untuk view (setiap render agar update)
        $groupedJabatans = $this->allJabatans->groupBy('parent_id');

        return view('livewire.superadmin.data-pegawai', [
            'pegawaiList' => $pegawai,
            'roleList' => $roleList,
            'groupedJabatans' => $groupedJabatans, // Kirim data terkelompok
        ]); 
    }

    public function edit($pegawaiId) 
    {
        $pegawai = Pegawai::with('user.roles', 'jabatans')->findOrFail($pegawaiId);
        if ($pegawai->user->hasRole('superadmin')) { return; }
        
        $this->pegawaiId = $pegawai->id; 
        $this->userId = $pegawai->user_id; 
        $this->name = $pegawai->user->name;
        $this->email = $pegawai->user->email;
        $this->nip = $pegawai->nip;
        $this->selectedRoles = $pegawai->user->roles->pluck('id')->toArray();
        $this->selectedJabatans = $pegawai->jabatans->pluck('id')->toArray(); // Load multiple jabatans
        $this->password = ''; 
        
        $this->isEditMode = true;
        $this->resetValidation(); 
        $this->updateTakenSingletons(); // Update daftar singleton (kecualikan diri sendiri)
        $this->showModal = true;
    }

     public function showTambahModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->updateTakenSingletons(); // Update daftar singleton (tanpa pengecualian)
        $this->showModal = true;
    }

    public function store()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->userId)],
            'nip' => ['required', 'string', Rule::unique('pegawai', 'nip')->ignore($this->pegawaiId)], 
            'selectedRoles' => 'required|array|min:1',
            'selectedJabatans' => 'required|array|min:1', 
            'selectedJabatans.*' => 'exists:jabatan,id', 
        ];
        if (!$this->isEditMode) { $rules['password'] = 'required|string|min:8'; } 
        else { $rules['password'] = 'nullable|string|min:8'; }

        // Validasi tambahan: Cek singleton
        // Panggil updateTakenSingletons lagi SEBELUM validasi ini untuk data terbaru
        $this->updateTakenSingletons(); 
        foreach ($this->selectedJabatans as $jabatanId) {
             if (in_array($jabatanId, $this->takenSingletonJabatans)) {
                  $jabatan = $this->allJabatans->firstWhere('id', $jabatanId); // Ambil dari koleksi
                  $this->addError('selectedJabatans', "Jabatan '{$jabatan?->nama_jabatan}' sudah terisi.");
                  return; 
             }
        }

        $this->validate($rules); 

        // 1. Update/Create User
        $userData = ['name' => $this->name, 'email' => $this->email];
        if (!empty($this->password)) { $userData['password'] = Hash::make($this->password); }
        $user = User::updateOrCreate(['id' => $this->userId], $userData);

        // 2. Update/Create Pegawai 
        $pegawai = Pegawai::updateOrCreate(
            ['user_id' => $user->id], 
            ['nip' => $this->nip] 
        );
        $this->pegawaiId = $pegawai->id; // Pastikan pegawaiId terisi

        // 3. Sync Jabatan
        $pegawai->jabatans()->sync($this->selectedJabatans);

        // 4. Sync Roles
        $user->roles()->sync($this->selectedRoles);

        session()->flash('message', $this->isEditMode ? 'Data Pegawai Berhasil Diperbarui' : 'Data Pegawai Berhasil Ditambahkan');
        $this->closeModal();
        $this->updateTakenSingletons(); // Update lagi setelah simpan
    }
    
    #[On('deleteConfirmed')] 
    public function destroy($userId) { /* ... logika hapus user ... */ }
    public function closeModal() { $this->showModal = false; $this->resetForm(); }
    public function resetForm()
    {
        $this->reset(['name', 'email', 'password', 'nip', 'selectedRoles', 'selectedJabatans', 'pegawaiId', 'userId', 'isEditMode']);
        $this->resetErrorBag(); 
        $this->resetValidation(); 
    }
}