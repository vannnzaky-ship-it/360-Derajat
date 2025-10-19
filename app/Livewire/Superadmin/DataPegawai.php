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
use Livewire\Attributes\On; // <-- Pastikan ini di-import

#[Layout('layouts.admin')]
class DataPegawai extends Component
{
    use WithPagination;

    // Properti untuk form
    public $name, $email, $password, $jabatan_id, $nip;
    public $selectedRoles = []; // Untuk checkbox role

    // Properti untuk edit
    public $pegawaiId, $userId;
    public $isEditMode = false;

    // Properti untuk UI
    public $search = '';
    public $showModal = false;

    // Listener '$listeners' sudah tidak digunakan di Livewire 3
    // protected $listeners = ['deleteConfirmed' => 'destroy'];

    public function render()
    {
        $pegawai = Pegawai::with(['user', 'jabatan'])
            ->whereHas('user', function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                      ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->orWhere('nip', 'like', '%'.$this->search.'%')
            ->paginate(10);

        return view('livewire.superadmin.data-pegawai', [
            'pegawaiList' => $pegawai,
            'jabatanList' => Jabatan::all(),
            // Ambil role selain superadmin
            'roleList' => Role::where('name', '!=', 'superadmin')->get() 
        ]);
    }

    // Buka modal (kosong)
    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    // Buka modal (isi data)
    public function edit($id)
    {
        $pegawai = Pegawai::with('user.roles')->findOrFail($id);
        
        $this->pegawaiId = $pegawai->id;
        $this->userId = $pegawai->user_id;
        $this->name = $pegawai->user->name;
        $this->email = $pegawai->user->email;
        $this->nip = $pegawai->nip;
        $this->jabatan_id = $pegawai->jabatan_id;
        $this->selectedRoles = $pegawai->user->roles->pluck('id')->toArray();
        $this->password = ''; // Kosongkan password saat edit
        
        $this->isEditMode = true;
        $this->showModal = true;
    }

    // Simpan (Create atau Update)
    public function store()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'nip' => 'required|string|unique:pegawai,nip,' . $this->pegawaiId,
            'jabatan_id' => 'required|exists:jabatan,id',
            'selectedRoles' => 'required|array|min:1',
        ];

        // Tambahkan validasi password HANYA saat create
        if (!$this->isEditMode) {
            $rules['password'] = 'required|string|min:8';
        }

        $this->validate($rules);

        // Update atau Create User
        $user = User::updateOrCreate(
            ['id' => $this->userId],
            [
                'name' => $this->name,
                'email' => $this->email,
                // Hanya update password jika diisi
                'password' => $this->password ? Hash::make($this->password) : User::find($this->userId)?->password
            ]
        );
        
        // Update atau Create Pegawai
        Pegawai::updateOrCreate(
            ['id' => $this->pegawaiId],
            [
                'user_id' => $user->id,
                'jabatan_id' => $this->jabatan_id,
                'nip' => $this->nip
            ]
        );

        // Sync roles
        $user->roles()->sync($this->selectedRoles);

        session()->flash('message', $this->isEditMode ? 'Data Pegawai Berhasil Diperbarui' : 'Data Pegawai Berhasil Ditambahkan');
        $this->closeModal();
    }

    /**
     * Ini adalah cara baru untuk menangani event di Livewire 3.
     * Atribut #[On] akan "mendengarkan" event 'deleteConfirmed' dari frontend.
     */
    #[On('deleteConfirmed')]
    public function destroy($id)
    {
        // Temukan user berdasarkan ID dan hapus.
        // Relasi onDelete('cascade') di migrasi akan otomatis menghapus data pegawai terkait.
        $user = User::find($id);
        if ($user) {
            $user->delete();
            session()->flash('message', 'Data Pegawai Berhasil Dihapus');
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
    }
}
