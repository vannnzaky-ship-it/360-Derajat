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
use Livewire\Attributes\Title;
use Livewire\Attributes\On; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection; 

#[Layout('layouts.admin')]
#[Title('Data Pegawai')] // <-- PENTING: Judul Halaman
class DataPegawai extends Component
{
    use WithPagination;

    // --- PROPERTI FORM ---
    protected $paginationTheme = 'bootstrap';
    public $name, $email, $password, $nip, $no_hp;
    public $selectedRoles = []; 
    public $selectedJabatans = []; 

    // --- PROPERTI EDIT & UI ---
    public $pegawaiId = null; 
    public $userId = null; 
    public $isEditMode = false;
    public $search = '';
    public $showModal = false;

    // --- PROPERTI VALIDASI ---
    public array $takenSingletonJabatans = []; 
    public $peninjauTakenBy = null; 

    public function mount()
    {
        $this->updateTakenSingletons(); 
        $this->checkPeninjauStatus(); 
    }
    
    public function updateTakenSingletons()
    {
         $this->takenSingletonJabatans = Jabatan::where('is_singleton', true)
             ->whereHas('pegawais', function ($query) {
                 if ($this->isEditMode && $this->pegawaiId) {
                     $query->where('pegawai.id', '!=', $this->pegawaiId);
                 }
             })
             ->pluck('id') 
             ->toArray();
    }

    public function checkPeninjauStatus()
    {
        $existingPeninjau = User::whereHas('roles', function($q) {
            $q->where('name', 'peninjau');
        })->first();

        if ($existingPeninjau) {
            if ($this->isEditMode && $this->userId == $existingPeninjau->id) {
                $this->peninjauTakenBy = null;
            } else {
                $this->peninjauTakenBy = $existingPeninjau->name;
            }
        } else {
            $this->peninjauTakenBy = null; 
        }
    }

    public function render()
    {
        $this->checkPeninjauStatus();

        $allJabatans = Jabatan::orderBy('bidang', 'asc')
            ->orderBy('level', 'asc') 
            ->orderBy('urutan', 'asc')
            ->get();

        $groupedJabatans = $allJabatans->groupBy('bidang')->map(function ($list) {
            return $this->sortListHierarchically($list);
        });

        $pegawaiQuery = Pegawai::with(['user.roles', 'jabatans']) 
            ->whereHas('user', function ($query) {
                $query->whereDoesntHave('roles', fn($q) => $q->where('name', 'superadmin'));
                $query->where('id', '!=', Auth::id()); 
            });

        if ($this->search) {
             $pegawaiQuery->where(function ($query) { 
                 $query->whereHas('user', function ($qUser) {
                     $qUser->where('name', 'like', '%'.$this->search.'%')
                           ->orWhere('email', 'like', '%'.$this->search.'%');
                 })
                 ->orWhere('nip', 'like', '%'.$this->search.'%')
                 ->orWhereHas('jabatans', function ($qJab) { 
                     $qJab->where('nama_jabatan', 'like', '%'.$this->search.'%');
                 });
             });
        }

        $pegawai = $pegawaiQuery->distinct()->orderBy('created_at', 'desc')->paginate(10);

        $currentUser = Auth::user(); 
        $isAdminAccessing = false; 
        if ($currentUser && !$currentUser->hasRole('superadmin')) { $isAdminAccessing = true; }
        
        $availableRolesQuery = Role::where('name', '!=', 'superadmin');
        if ($isAdminAccessing) { $availableRolesQuery->where('name', '!=', 'admin'); }
        
        $roleList = $availableRolesQuery->get();
        
        return view('livewire.superadmin.data-pegawai', [
            'pegawaiList' => $pegawai,
            'roleList' => $roleList,
            'groupedJabatans' => $groupedJabatans, 
        ]); 
    }

    private function sortListHierarchically(Collection $list)
    {
        $sorted = new Collection();
        $idsInList = $list->pluck('id')->toArray();

        $roots = $list->filter(function ($item) use ($idsInList) {
            return is_null($item->parent_id) || !in_array($item->parent_id, $idsInList);
        })->sortBy('level')->sortBy('urutan');

        foreach ($roots as $root) {
            $this->traverseChildren($root, $list, $sorted, 0);
        }
        return $sorted;
    }

    private function traverseChildren($node, $allList, &$result, $depth)
    {
        $node->indent_level = $depth; 
        $result->push($node);
        $children = $allList->where('parent_id', $node->id)->sortBy('urutan');
        foreach ($children as $child) {
            $this->traverseChildren($child, $allList, $result, $depth + 1);
        }
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
        $this->no_hp = $pegawai->no_hp;
        $this->selectedRoles = $pegawai->user->roles->pluck('id')->toArray();
        $this->selectedJabatans = $pegawai->jabatans->pluck('id')->toArray(); 
        $this->password = ''; 
        
        $this->isEditMode = true;
        $this->resetValidation(); 
        
        $this->updateTakenSingletons(); 
        $this->checkPeninjauStatus();
        
        $this->showModal = true;
    }

     public function showTambahModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        
        $this->updateTakenSingletons(); 
        $this->checkPeninjauStatus();
        
        $this->showModal = true;
    }

    public function store()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->userId)],
            'nip' => ['required', 'string', Rule::unique('pegawai', 'nip')->ignore($this->pegawaiId)], 
            'no_hp' => 'nullable|numeric|digits_between:10,15',
            'selectedRoles' => 'required|array|min:1',
            'selectedJabatans' => 'required|array|min:1', 
            'selectedJabatans.*' => 'exists:jabatan,id', 
        ];
        if (!$this->isEditMode) { $rules['password'] = 'required|string|min:8'; } 
        else { $rules['password'] = 'nullable|string|min:8'; }

        $this->updateTakenSingletons(); 
        foreach ($this->selectedJabatans as $jabatanId) {
             if (in_array($jabatanId, $this->takenSingletonJabatans)) {
                  $jabatan = Jabatan::find($jabatanId); 
                  $this->addError('selectedJabatans', "Jabatan '{$jabatan?->nama_jabatan}' sudah terisi.");
                  return; 
             }
        }

        $this->checkPeninjauStatus();
        $peninjauRole = Role::where('name', 'peninjau')->first();
        if ($peninjauRole && in_array($peninjauRole->id, $this->selectedRoles) && $this->peninjauTakenBy) {
             $this->addError('selectedRoles', "Role Peninjau sudah digunakan oleh: {$this->peninjauTakenBy}.");
             return;
        }

        $this->validate($rules); 

        $userData = ['name' => $this->name, 'email' => $this->email];
        if (!empty($this->password)) { $userData['password'] = Hash::make($this->password); }
        $user = User::updateOrCreate(['id' => $this->userId], $userData);

        $pegawai = Pegawai::updateOrCreate(
            ['user_id' => $user->id], 
            ['nip' => $this->nip, 'no_hp' => $this->no_hp]
        );
        $this->pegawaiId = $pegawai->id; 

        $pegawai->jabatans()->sync($this->selectedJabatans);
        $user->roles()->sync($this->selectedRoles);

        session()->flash('message', $this->isEditMode ? 'Data Pegawai Berhasil Diperbarui' : 'Data Pegawai Berhasil Ditambahkan');
        $this->closeModal();
        $this->updateTakenSingletons(); 
    }
    
    public function confirmDelete($userId)
    {
        $this->dispatch('show-delete-confirmation', $userId);
    }

    #[On('deleteConfirmed')] 
    public function destroy($userId) 
    { 
        if (is_array($userId)) { $userId = reset($userId); }

        $user = User::find($userId); 

        if ($user) {
            if($user->pegawai) {
                $user->pegawai()->delete();
            }
            $user->delete();
            session()->flash('message', 'Pegawai berhasil dihapus');
        } else {
            session()->flash('error', 'Data pegawai tidak ditemukan.');
        }
    }

    public function closeModal() { $this->showModal = false; $this->resetForm(); }
    
    public function resetForm()
    {
        $this->reset(['name', 'email', 'password', 'nip','no_hp', 'selectedRoles', 'selectedJabatans', 'pegawaiId', 'userId', 'isEditMode', 'peninjauTakenBy']);
        $this->resetErrorBag(); 
        $this->resetValidation(); 
    }
}