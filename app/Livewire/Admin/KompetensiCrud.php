<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use App\Models\Kompetensi; // Import Model
use Livewire\WithPagination;
use Livewire\Attributes\On; 

#[Layout('layouts.admin', ['title' => 'Manajemen Kompetensi'])] 
class KompetensiCrud extends Component
{
    use WithPagination; 

    public $search = '';

    // Properti Form
    public $kompetensiId = null; 
    public $isEditMode = false;
    public $showModal = false;

    #[Rule('required|string|max:255', message:'Nama kompetensi wajib diisi.')]
    public $nama_kompetensi = '';
    
    #[Rule('nullable|string')]
    public $deskripsi = '';

    #[Rule('required|integer|min:0|max:100', message:'Bobot wajib angka 0-100.')]
    public $bobot = 0;

    #[Rule('required|in:Aktif,Tidak Aktif')]
    public $status = 'Aktif'; 

    /**
     * Fungsi utama untuk menyimpan (Create atau Update)
     */
    public function saveKompetensi()
    {
        $validatedData = $this->validate();

        // Validasi custom: Total Bobot Kompetensi Aktif harus 100%
        $currentTotalBobot = Kompetensi::where('status', 'Aktif')
                                      ->where('id', '!=', $this->kompetensiId) // Abaikan diri sendiri saat edit
                                      ->sum('bobot');
        $newTotalBobot = $currentTotalBobot + ($this->status == 'Aktif' ? $this->bobot : 0);

        if ($this->status == 'Aktif' && $newTotalBobot > 100) {
            $this->addError('bobot_total', 'Total bobot semua kompetensi aktif tidak boleh melebihi 100%. Sisa bobot: ' . (100 - $currentTotalBobot) . '%');
            return;
        }
         // Opsional: Beri warning jika total < 100? Atau biarkan saja?

        // Simpan atau Update ke Database
        Kompetensi::updateOrCreate(['id' => $this->kompetensiId], $validatedData);

        $this->closeModal();
        $this->dispatch('close-kompetensi-modal'); 
        session()->flash('message', $this->isEditMode ? 'Kompetensi berhasil diperbarui!' : 'Kompetensi berhasil ditambahkan!');
        $this->resetPage(); 
    }

    /**
     * Memuat data untuk mode Edit & membuka modal
     */
    public function edit($id)
    {
        $kompetensi = Kompetensi::findOrFail($id);
        $this->kompetensiId = $kompetensi->id;
        $this->nama_kompetensi = $kompetensi->nama_kompetensi;
        $this->deskripsi = $kompetensi->deskripsi;
        $this->bobot = $kompetensi->bobot;
        $this->status = $kompetensi->status;
        $this->isEditMode = true;
        $this->resetValidation(); 
        $this->dispatch('open-kompetensi-modal'); // Kirim event BUKA modal ke JS
    }

     /**
     * Membuka modal Tambah
     */
    public function showTambahModal()
    {
        $this->resetForm(); 
        $this->isEditMode = false;
        $this->status = 'Aktif'; // Default aktif
        $this->dispatch('open-kompetensi-modal'); // Kirim event BUKA modal ke JS
    }

    /**
     * Konfirmasi sebelum hapus
     */
    public function confirmDelete($id)
    {
        $this->kompetensiId = $id; 
        $this->dispatch('show-delete-confirmation-kompetensi'); // Nama event unik
    }

    /**
     * Hapus data setelah dikonfirmasi
     */
    #[On('deleteConfirmedKompetensi')] // Nama listener unik
    public function delete()
    {
        $kompetensi = Kompetensi::find($this->kompetensiId);
        if ($kompetensi) {
            // Logika tambahan: Cek apakah kompetensi ini digunakan di Pertanyaan? Jika iya, jangan hapus?
            // if ($kompetensi->pertanyaan()->exists()) { ... error ... return; } 

            // Cek ulang total bobot jika menghapus kompetensi aktif
             if ($kompetensi->status == 'Aktif') {
                 $currentTotalBobot = Kompetensi::where('status', 'Aktif')
                                               ->where('id', '!=', $this->kompetensiId) 
                                               ->sum('bobot');
                 if ($currentTotalBobot == 0 && Kompetensi::where('status', 'Aktif')->count() > 1) {
                      session()->flash('error', 'Tidak dapat menghapus. Harus ada minimal satu kompetensi aktif atau total bobot aktif menjadi 0.');
                      $this->kompetensiId = null;
                      return;
                 }
                 // Warning jika total bobot tidak 100% setelah dihapus?
            }

            $kompetensi->delete();
            session()->flash('message', 'Kompetensi berhasil dihapus.');
        } else {
            session()->flash('error', 'Kompetensi tidak ditemukan.');
        }
        $this->kompetensiId = null;
    }

    /**
     * Menutup modal dan reset state (hanya kirim event)
     */
    public function closeModal()
    {
        $this->resetForm();
        $this->dispatch('close-kompetensi-modal'); // Kirim event close
    }

    /**
     * Reset properti form
     */
    public function resetForm()
    {
         $this->reset([
            'kompetensiId', 'isEditMode', 'nama_kompetensi', 'deskripsi', 
            'bobot', 'status'
        ]);
        $this->resetErrorBag(); 
        $this->resetValidation();
    }

    public function render()
    {
        // Ambil data dari database dengan pagination dan search
        $daftarKompetensi = Kompetensi::where('nama_kompetensi', 'like', '%'.$this->search.'%')
                            ->orderBy('nama_kompetensi', 'asc')
                            ->paginate(10); 

        // Hitung total bobot aktif untuk ditampilkan
        $totalBobotAktif = Kompetensi::where('status', 'Aktif')->sum('bobot');

        return view('livewire.admin.kompetensi-crud', [
            'daftarKompetensi' => $daftarKompetensi,
            'totalBobotAktif' => $totalBobotAktif,
        ]);
    }
}