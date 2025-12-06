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
use Illuminate\Support\Collection; 

#[Layout('layouts.admin')]
class DataPegawai extends Component
{
    use WithPagination;

    // Properti Form
    public $name, $email, $password, $nip;
    public $selectedRoles = []; 
    public $selectedJabatans = []; 

    // Properti Edit
    public $pegawaiId = null; 
    public $userId = null; 
    public $isEditMode = false;

    // Properti UI
    public $search = '';
    public $showModal = false;

    // Properti Singleton
    public array $takenSingletonJabatans = []; 

    public function mount()
    {
        $this->updateTakenSingletons(); 
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

    public function render()
    {
        // 1. AMBIL DATA JABATAN (Raw Data)
        // Urutkan berdasarkan Bidang -> Level -> Urutan
        $allJabatans = Jabatan::orderBy('bidang', 'asc')
            ->orderBy('level', 'asc') 
            ->orderBy('urutan', 'asc')
            ->get();

        // 2. GROUPING & SORTING HIERARKI
        // Data dikelompokkan per bidang, lalu diurutkan Parent -> Child di dalamnya
        $groupedJabatans = $allJabatans->groupBy('bidang')->map(function ($list) {
            return $this->sortListHierarchically($list);
        });

        // Query utama Pegawai
        $pegawaiQuery = Pegawai::with(['user.roles', 'jabatans']) 
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
            
        if ($this->search) {
             $pegawaiQuery->where(function ($query) { 
                 $query->orWhere('nip', 'like', '%'.$this->search.'%')
                       ->orWhereHas('jabatans', function ($qJab) { 
                           $qJab->where('nama_jabatan', 'like', '%'.$this->search.'%');
                       });
             });
        }

        $pegawai = $pegawaiQuery->paginate(10);

        // Data Role untuk Form
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

    // --- FUNGSI HELPER UNTUK SORTING POHON (HIERARKI) ---

    private function sortListHierarchically(Collection $list)
    {
        $sorted = new Collection();
        $idsInList = $list->pluck('id')->toArray();

        // 1. Cari "Root Lokal" (Jabatan paling atas di bidang ini)
        // Yaitu jabatan yang parent_id-nya NULL atau parent_id-nya TIDAK ADA di list bidang ini
        $roots = $list->filter(function ($item) use ($idsInList) {
            return is_null($item->parent_id) || !in_array($item->parent_id, $idsInList);
        })->sortBy('level')->sortBy('urutan');

        // 2. Loop setiap root dan cari anak-anaknya secara rekursif
        foreach ($roots as $root) {
            $this->traverseChildren($root, $list, $sorted, 0);
        }

        return $sorted;
    }

    private function traverseChildren($node, $allList, &$result, $depth)
    {
        // Set kedalaman untuk indentasi di View nanti
        $node->indent_level = $depth; 
        $result->push($node);

        // Cari anak dari node ini yang ada di list yang sama
        $children = $allList->where('parent_id', $node->id)->sortBy('urutan');

        foreach ($children as $child) {
            $this->traverseChildren($child, $allList, $result, $depth + 1);
        }
    }

    // ----------------------------------------------------

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
        $this->selectedJabatans = $pegawai->jabatans->pluck('id')->toArray(); 
        $this->password = ''; 
        
        $this->isEditMode = true;
        $this->resetValidation(); 
        $this->updateTakenSingletons(); 
        $this->showModal = true;
    }

     public function showTambahModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->updateTakenSingletons(); 
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

        $this->updateTakenSingletons(); 
        foreach ($this->selectedJabatans as $jabatanId) {
             if (in_array($jabatanId, $this->takenSingletonJabatans)) {
                  $jabatan = Jabatan::find($jabatanId); 
                  $this->addError('selectedJabatans', "Jabatan '{$jabatan?->nama_jabatan}' sudah terisi.");
                  return; 
             }
        }

        $this->validate($rules); 

        $userData = ['name' => $this->name, 'email' => $this->email];
        if (!empty($this->password)) { $userData['password'] = Hash::make($this->password); }
        $user = User::updateOrCreate(['id' => $this->userId], $userData);

        $pegawai = Pegawai::updateOrCreate(
            ['user_id' => $user->id], 
            ['nip' => $this->nip] 
        );
        $this->pegawaiId = $pegawai->id; 

        $pegawai->jabatans()->sync($this->selectedJabatans);
        $user->roles()->sync($this->selectedRoles);

        session()->flash('message', $this->isEditMode ? 'Data Pegawai Berhasil Diperbarui' : 'Data Pegawai Berhasil Ditambahkan');
        $this->closeModal();
        $this->updateTakenSingletons(); 
    }
    
    #[On('deleteConfirmed')] 
    public function destroy($userId) { 
        $user = User::findOrFail($userId);
        if($user->pegawai) {
            $user->pegawai()->delete();
        }
        $user->delete();
        session()->flash('message', 'Pegawai berhasil dihapus');
    }

    public function closeModal() { $this->showModal = false; $this->resetForm(); }
    
    public function resetForm()
    {
        $this->reset(['name', 'email', 'password', 'nip', 'selectedRoles', 'selectedJabatans', 'pegawaiId', 'userId', 'isEditMode']);
        $this->resetErrorBag(); 
        $this->resetValidation(); 
    }
}