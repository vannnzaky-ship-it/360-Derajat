<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use App\Models\Siklus;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Carbon\Carbon;

#[Layout('layouts.admin', ['title' => 'Siklus Semester'])]
class SiklusSemester extends Component
{
    use WithPagination;

    public $search = '';

    public $siklusId = null;
    public $isEditMode = false;
    public $showModal = false;

    #[Rule(['required', 'string', 'regex:/^\d{4}\/\d{4}$/'], message: [
        'required' => 'Tahun ajaran wajib diisi.',
        'regex' => 'Format wajib TAHUN/TAHUN (Contoh: 2025/2026).'
    ])]
    public $tahun_ajaran = '';

    #[Rule('required|in:Ganjil,Genap', message: 'Semester wajib dipilih.')]
    public $semester = '';

    #[Rule('required|in:Aktif,Tidak Aktif', message: 'Status wajib dipilih.')]
    public $status = 'Tidak Aktif';

    public function saveSiklus()
    {
        $validatedData = $this->validate();

        // 1. CEK KEAMANAN BACKEND: Jangan izinkan edit jika siklus sedang berjalan
        if ($this->isEditMode && $this->siklusId) {
            $siklus = Siklus::with('penilaianSession')->find($this->siklusId);
            if ($siklus && $siklus->penilaianSession) {
                $batasWaktu = Carbon::parse($siklus->penilaianSession->batas_waktu);
                if (now()->lessThan($batasWaktu)) {
                    $this->dispatch('show-error-alert', message: 'Gagal! Siklus sedang berjalan dalam penilaian. Tidak bisa diubah.');
                    return;
                }
            }
        }

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
        $siklus = Siklus::with('penilaianSession')->findOrFail($id);

        // --- LOGIKA PENGUNCIAN EDIT ---
        if ($siklus->penilaianSession) {
            $batasWaktu = Carbon::parse($siklus->penilaianSession->batas_waktu);
            // Jika waktu sekarang kurang dari batas waktu (sedang berjalan) -> TOLAK EDIT
            if (now()->lessThan($batasWaktu)) {
                $this->dispatch('show-error-alert', message: 'Siklus ini sedang digunakan dalam penilaian aktif. Tidak bisa diedit hingga selesai.');
                return; 
            }
        }

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
        
        $now = date('Y');
        $next = $now + 1;
        $this->tahun_ajaran = "$now/$next"; 
        
        $this->semester = 'Ganjil';
        $this->status = 'Tidak Aktif';
        $this->isEditMode = false;
        $this->showModal = true;
        $this->dispatch('open-modal');
    }

    public function confirmDelete($id)
    {
        $this->siklusId = $id; 
        
        $siklus = Siklus::with('penilaianSession')->find($id);
        if ($siklus && $siklus->penilaianSession) {
             $this->dispatch('show-error-alert', message: 'Siklus ini memiliki data penilaian. Tidak dapat dihapus.');
             return;
        }

        $this->dispatch('show-delete-confirmation');
    }

    #[On('deleteConfirmed')] 
    public function delete()
    {
        $siklus = Siklus::find($this->siklusId);
        if ($siklus) {
            if ($siklus->penilaianSession()->exists()) {
                session()->flash('error', 'Gagal hapus! Siklus ini sudah digunakan untuk penilaian.');
                return;
            }
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

    public function render()
    {
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