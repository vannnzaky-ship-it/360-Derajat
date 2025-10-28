<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use App\Models\Siklus; // Import Model
use Livewire\WithPagination;
use Livewire\Attributes\On; // Import On

#[Layout('layouts.admin', ['title' => 'Siklus Semester'])]
class SiklusSemester extends Component
{
    use WithPagination;

    public $search = '';

    // Properti Form
    public $siklusId = null;
    public $isEditMode = false;
    public $showModal = false; // Tambah properti untuk kontrol modal

    #[Rule('required|digits:4|integer|min:2020|max:2099', message: 'Tahun ajaran wajib format 4 digit.')]
    public $tahun_ajaran = '';

    #[Rule('required|in:Ganjil,Genap', message: 'Semester wajib dipilih.')]
    public $semester = '';

    #[Rule('required|integer|min:0|max:100', message: 'Presentase wajib angka 0-100.')]
    public $persen_diri = 0;

    #[Rule('required|integer|min:0|max:100', message: 'Presentase wajib angka 0-100.')]
    public $persen_atasan = 0;

    #[Rule('required|integer|min:0|max:100', message: 'Presentase wajib angka 0-100.')]
    public $persen_rekan = 0;

    #[Rule('required|integer|min:0|max:100', message: 'Presentase wajib angka 0-100.')]
    public $persen_bawahan = 0;

    #[Rule('required|in:Aktif,Tidak Aktif', message: 'Status wajib dipilih.')]
    public $status = 'Tidak Aktif'; // Default Tidak Aktif

    /**
     * Fungsi utama untuk menyimpan (Create atau Update)
     */
    public function saveSiklus()
    {
        // 1. Validasi dasar
        $validatedData = $this->validate();

        // 2. Validasi custom: Total Persentase harus 100
        $totalPersen = $this->persen_diri + $this->persen_atasan + $this->persen_rekan + $this->persen_bawahan;
        if ($totalPersen !== 100) {
            $this->addError('persen_total', 'Total persentase Diri+Atasan+Rekan+Bawahan harus 100%. Total saat ini: ' . $totalPersen . '%');
            return;
        }

        // 3. Validasi custom: Hanya boleh ada 1 Siklus Aktif
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
        
        // 4. Validasi unique kombinasi tahun & semester
        $queryUnique = Siklus::where('tahun_ajaran', $this->tahun_ajaran)
                             ->where('semester', $this->semester);
        if ($this->isEditMode && $this->siklusId) {
            $queryUnique->where('id', '!=', $this->siklusId);
        }
        if ($queryUnique->exists()) {
            $this->addError('tahun_ajaran', 'Kombinasi Tahun Ajaran dan Semester ini sudah ada.');
            $this->addError('semester', ''); // Error kedua agar jelas
            return;
        }

        // 5. Simpan atau Update ke Database
        Siklus::updateOrCreate(['id' => $this->siklusId], $validatedData);

        // 6. Tutup modal & kirim notifikasi
        $this->closeModal();
        $this->dispatch('close-modal', '#siklusModal'); 
        session()->flash('message', $this->isEditMode ? 'Data siklus berhasil diperbarui!' : 'Data siklus berhasil ditambahkan!');
        $this->resetPage(); // Kembali ke halaman 1 pagination
    }

    /**
     * Memuat data untuk mode Edit & membuka modal
     */
    public function edit($id)
    {
        $siklus = Siklus::findOrFail($id);
        $this->siklusId = $siklus->id;
        $this->tahun_ajaran = $siklus->tahun_ajaran;
        $this->semester = $siklus->semester;
        $this->persen_diri = $siklus->persen_diri;
        $this->persen_atasan = $siklus->persen_atasan;
        $this->persen_rekan = $siklus->persen_rekan;
        $this->persen_bawahan = $siklus->persen_bawahan;
        $this->status = $siklus->status;
        $this->isEditMode = true;
        $this->showModal = true; // Buka modal
        $this->resetValidation(); 

        $this->dispatch('open-modal');
    }

     /**
     * Membuka modal Tambah
     */
    public function showTambahModal()
    {
        $this->resetForm(); // Reset form dulu
        $this->tahun_ajaran = date('Y'); // Default tahun sekarang
        $this->semester = 'Ganjil'; // Default Ganjil
        $this->status = 'Tidak Aktif'; // Default Tidak Aktif
        $this->isEditMode = false;
        $this->showModal = true; // Buka modal
        $this->dispatch('open-modal');
    }

    /**
     * Konfirmasi sebelum hapus
     */
    public function confirmDelete($id)
    {
        $this->siklusId = $id; 
        $this->dispatch('show-delete-confirmation');
    }

    /**
     * Hapus data setelah dikonfirmasi
     */
    #[On('deleteConfirmed')] 
    public function delete()
    {
        $siklus = Siklus::find($this->siklusId);
        if ($siklus) {
            // Logika tambahan: Jika siklus aktif, jangan biarkan dihapus?
            if ($siklus->status == 'Aktif') {
                 session()->flash('error', 'Tidak dapat menghapus Siklus Semester yang sedang Aktif.');
                 $this->siklusId = null;
                 return;
            }
            $siklus->delete();
            session()->flash('message', 'Data siklus berhasil dihapus.');
        } else {
            session()->flash('error', 'Data siklus tidak ditemukan.');
        }
        $this->siklusId = null;
    }

    /**
     * Menutup modal dan reset state
     */
    public function closeModal()
    {
        $this->showModal = false; // Tutup modal
        $this->resetForm();

        $this->dispatch('close-modal');
    }

    /**
     * Reset properti form
     */
    public function resetForm()
    {
         $this->reset([
            'siklusId', 'isEditMode', 'tahun_ajaran', 'semester', 
            'persen_diri', 'persen_atasan', 'persen_rekan', 'persen_bawahan', 
            'status'
        ]);
        $this->resetErrorBag(); // Bersihkan error validasi
        $this->resetValidation();
    }

    public function render()
    {
        // Ambil data dari database dengan pagination dan search
        $daftarSiklus = Siklus::where(function($query) {
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
