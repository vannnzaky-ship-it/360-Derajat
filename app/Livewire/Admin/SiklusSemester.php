<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use App\Models\Siklus;
use Livewire\WithPagination;
use Livewire\Attributes\On;

#[Layout('layouts.admin', ['title' => 'Siklus Semester'])]
class SiklusSemester extends Component
{
    use WithPagination;

    public $search = '';

    // Properti Form
    public $siklusId = null;
    public $isEditMode = false;
    public $showModal = false;

    #[Rule('required|digits:4|integer|min:2020|max:2099', message: 'Tahun ajaran wajib format 4 digit.')]
    public $tahun_ajaran = '';

    #[Rule('required|in:Ganjil,Genap', message: 'Semester wajib dipilih.')]
    public $semester = '';

    #[Rule('required|in:Aktif,Tidak Aktif', message: 'Status wajib dipilih.')]
    public $status = 'Tidak Aktif';

    // --- (HAPUS SEMUA VARIABEL REKAP DATA & MODAL DISINI) ---

    public function saveSiklus()
    {
        $validatedData = $this->validate();

        // Validasi Siklus Aktif
        if ($this->status == 'Aktif') {
             $query = Siklus::where('status', 'Aktif');
             if ($this->isEditMode && $this->siklusId) {
                 $query->where('id', '!=', $this->siklusId);
             }
             if ($query->exists()) {
                 $this->addError('status', 'Hanya boleh ada satu Siklus Semester yang berstatus Aktif.');
                 return;
             }
        }
        
        // Validasi Unik Tahun+Semester
        $queryUnique = Siklus::where('tahun_ajaran', $this->tahun_ajaran)
                             ->where('semester', $this->semester);
        if ($this->isEditMode && $this->siklusId) {
            $queryUnique->where('id', '!=', $this->siklusId);
        }
        if ($queryUnique->exists()) {
            $this->addError('tahun_ajaran', 'Kombinasi Tahun Ajaran dan Semester ini sudah ada.');
            $this->addError('semester', ''); 
            return;
        }

        Siklus::updateOrCreate(['id' => $this->siklusId], $validatedData);

        $this->closeModal();
        $this->dispatch('close-modal'); 
        session()->flash('message', $this->isEditMode ? 'Data siklus berhasil diperbarui!' : 'Data siklus berhasil ditambahkan!');
        $this->resetPage();
    }

    public function edit($id)
    {
        $siklus = Siklus::findOrFail($id);
        $this->siklusId = $siklus->id;
        $this->tahun_ajaran = $siklus->tahun_ajaran;
        $this->semester = $siklus->semester;
        $this->status = $siklus->status;
        
        $this->isEditMode = true;
        $this->showModal = true;
        $this->resetValidation(); 

        $this->dispatch('open-modal');
    }

    public function showTambahModal()
    {
        $this->resetForm();
        $this->tahun_ajaran = date('Y');
        $this->semester = 'Ganjil';
        $this->status = 'Tidak Aktif';
        $this->isEditMode = false;
        $this->showModal = true;
        $this->dispatch('open-modal');
    }

    public function confirmDelete($id)
    {
        $this->siklusId = $id; 
        $this->dispatch('show-delete-confirmation');
    }

    #[On('deleteConfirmed')] 
    public function delete()
    {
        $siklus = Siklus::find($this->siklusId);
        if ($siklus) {
            // Validasi: Tidak boleh hapus jika sudah dipakai Random Penilai
            if ($siklus->penilaianSession()->exists()) {
                session()->flash('error', 'Gagal hapus! Siklus ini sudah digunakan untuk penilaian.');
                return;
            }
            // Validasi: Tidak boleh hapus yang Aktif
            if ($siklus->status == 'Aktif') {
                 session()->flash('error', 'Tidak dapat menghapus Siklus Semester yang sedang Aktif.');
                 return;
            }
            $siklus->delete();
            session()->flash('message', 'Data siklus berhasil dihapus.');
        } else {
            session()->flash('error', 'Data siklus tidak ditemukan.');
        }
        $this->siklusId = null;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->dispatch('close-modal');
    }

    public function resetForm()
    {
         $this->reset(['siklusId', 'isEditMode', 'tahun_ajaran', 'semester', 'status']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    // --- (HAPUS SEMUA FUNGSI EXPORT/REKAP DISINI) ---

    public function render()
    {
        // Tetap pakai with('penilaianSession') untuk cek tombol mata/hapus
        $daftarSiklus = Siklus::with('penilaianSession')
                            ->withCount('skemaPenilaians')
                            ->where(function($query) {
                                $query->where('tahun_ajaran', 'like', '%'.$this->search.'%')
                                      ->orWhere('semester', 'like', '%'.$this->search.'%');
                            })
                            ->orderBy('tahun_ajaran', 'desc')
                            ->orderBy('semester', 'desc')
                            ->paginate(10); 

        return view('livewire.admin.siklus-semester', [
            'daftarSiklus' => $daftarSiklus
        ]);
    }
}