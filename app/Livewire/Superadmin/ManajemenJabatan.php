<?php

namespace App\Livewire\Superadmin;

use Livewire\Component;
use App\Models\Jabatan;
use Livewire\Attributes\Layout;
// use Livewire\WithPagination; // HAPUS INI: Kita tidak pakai halaman lagi

#[Layout('layouts.admin')]
class ManajemenJabatan extends Component
{
    // use WithPagination; // HAPUS INI

    // Variable Form
    public $nama_jabatan, $parent_id, $bidang, $urutan, $level;
    public $status = true; 
    public $is_singleton = false; 

    public $jabatan_id; 
    public $isEdit = false;
    public $search = '';

    // Opsi Bidang
    public $opsiBidang = [
        'Direktorat' => 'Direktorat (Petinggi)',
        'Bidang 1' => 'Bidang 1 (Akademik)',
        'Bidang 2' => 'Bidang 2 (Keuangan/SDM)',
        'Bidang 3' => 'Bidang 3 (Humas)',
        'Lainnya' => 'Lainnya',
    ];

    // Opsi Level
    public $opsiLevel = [
        1 => 'Level 1 - Pimpinan Puncak (Direktur)',
        2 => 'Level 2 - Wakil Pimpinan (Wadir)',
        3 => 'Level 3 - Kepala Bagian / Unit (Ka ...)',
        4 => 'Level 4 - Kepala Seksi / Sub / Supervisor',
        5 => 'Level 5 - Staff / Pelaksana',
    ];

    public function render()
    {
        // 1. AMBIL SEMUA DATA (TANPA PAGINATION)
        $allJabatans = Jabatan::query()
            ->with('parent')
            ->when($this->search, function($q) {
                $q->where('nama_jabatan', 'like', '%'.$this->search.'%');
            })
            // Urutkan agar rapi: Bidang -> Level -> Urutan
            ->orderBy('bidang', 'asc') 
            ->orderBy('level', 'asc') 
            ->orderBy('urutan', 'asc')
            ->get();

        // 2. KELOMPOKKAN DATA BERDASARKAN BIDANG
        // Hasilnya: ['Direktorat' => [data...], 'Bidang 1' => [data...]]
        $groupedJabatans = $allJabatans->groupBy('bidang');


        // 3. LOGIKA DROPDOWN ATASAN (Dependent Dropdown)
        $queryParent = Jabatan::query()
            ->where('is_singleton', true)
            ->orderBy('level', 'asc')
            ->orderBy('urutan', 'asc');

        // Filter Atasan Berdasarkan Level yang Dipilih User
        if ($this->level) {
            if ($this->level == 2) {
                $queryParent->where('level', 1); // Wadir atasan Direktur
            } elseif ($this->level == 3) {
                $queryParent->where('level', 2); // Ka Unit atasan Wadir
            } elseif ($this->level == 4) {
                $queryParent->where('level', 3); // Kasi atasan Ka Unit
            } elseif ($this->level == 5) {
                $queryParent->whereIn('level', [3, 4]); // Staff atasan Ka Unit/Kasi
            } elseif ($this->level == 1) {
                $queryParent->where('id', 0); // Direktur tidak punya atasan
            }
        } else {
            // Default jika level belum dipilih
            $queryParent->where('level', '<', 5);
        }

        // Filter Atasan Berdasarkan Bidang
        if (!empty($this->bidang)) {
            $queryParent->where(function($q) {
                $q->where('bidang', $this->bidang)
                  ->orWhere('bidang', 'Direktorat'); 
            });
        }

        // Jangan pilih diri sendiri
        if ($this->jabatan_id) {
            $queryParent->where('id', '!=', $this->jabatan_id);
        }

        $parentOptions = $queryParent->get()->groupBy('bidang');

        return view('livewire.superadmin.manajemen-jabatan', [
            'groupedJabatans' => $groupedJabatans, // Kirim data yang sudah di-group
            'parentOptions' => $parentOptions
        ]);
    }

    public function resetInput()
    {
        $this->nama_jabatan = '';
        $this->parent_id = null;
        $this->bidang = '';
        $this->level = ''; 
        $this->urutan = 0;
        $this->status = true;
        $this->is_singleton = false;
        $this->jabatan_id = null;
        $this->isEdit = false;
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate([
            'nama_jabatan' => 'required|string|max:255',
            'bidang' => 'required',
            'level' => 'required|integer|in:1,2,3,4,5',
            'urutan' => 'nullable|integer',
            'parent_id' => 'nullable|exists:jabatan,id',
            'status' => 'boolean',
            'is_singleton' => 'boolean'
        ]);

        Jabatan::create([
            'nama_jabatan' => $this->nama_jabatan,
            'bidang' => $this->bidang,
            'parent_id' => $this->parent_id ?: null,
            'urutan' => $this->urutan ?? 0,
            'level' => $this->level,
            'status' => $this->status,
            'is_singleton' => $this->is_singleton
        ]);

        session()->flash('message', 'Jabatan berhasil ditambahkan.');
        $this->resetInput();
        $this->dispatch('close-modal');
    }

    public function edit($targetId)
    {
        $jabatan = Jabatan::findOrFail($targetId);

        $this->jabatan_id = $targetId;
        $this->nama_jabatan = $jabatan->nama_jabatan;
        $this->bidang = $jabatan->bidang;
        $this->parent_id = $jabatan->parent_id;
        $this->urutan = $jabatan->urutan;
        $this->level = $jabatan->level;
        
        // Konversi status dari DB ke Boolean untuk Toggle Switch
        $this->status = ($jabatan->status == 'Aktif' || $jabatan->status == 1) ? true : false;
        $this->is_singleton = (bool) $jabatan->is_singleton;
        
        $this->isEdit = true;
        $this->resetValidation();
        $this->dispatch('open-modal'); 
    }

    public function update()
    {
        $this->validate([
            'nama_jabatan' => 'required|string|max:255',
            'bidang' => 'required',
            'level' => 'required|integer|in:1,2,3,4,5',
            'urutan' => 'nullable|integer',
            'parent_id' => 'nullable|exists:jabatan,id',
            'status' => 'boolean',
            'is_singleton' => 'boolean'
        ]);

        if ($this->parent_id == $this->jabatan_id) {
            $this->addError('parent_id', 'Jabatan tidak bisa menjadi atasan bagi dirinya sendiri.');
            return;
        }

        $jabatan = Jabatan::findOrFail($this->jabatan_id);
        
        $jabatan->update([
            'nama_jabatan' => $this->nama_jabatan,
            'bidang' => $this->bidang,
            'parent_id' => $this->parent_id ?: null,
            'urutan' => $this->urutan,
            'level' => $this->level,
            'status' => $this->status, 
            'is_singleton' => $this->is_singleton 
        ]);

        session()->flash('message', 'Jabatan berhasil diperbarui.');
        $this->resetInput();
        $this->dispatch('close-modal');
    }

    public function delete($targetId)
    {
        $jabatan = Jabatan::findOrFail($targetId);
        if($jabatan->children()->count() > 0) {
            session()->flash('error', 'Gagal hapus! Jabatan ini masih memiliki bawahan.');
            return;
        }
        $jabatan->delete();
        session()->flash('message', 'Jabatan berhasil dihapus.');
    }
}