<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use App\Models\Pertanyaan; // Import Pertanyaan
use App\Models\Kompetensi; // Import Kompetensi
use Livewire\WithPagination;
use Livewire\Attributes\On; 

#[Layout('layouts.admin', ['title' => 'Manajemen Pertanyaan'])] 
class PertanyaanCrud extends Component
{
    use WithPagination; 

    public $search = '';
    public $perPage = 10;

    // Properti Form
    public $pertanyaanId = null; 
    public $isEditMode = false;
    public $showModal = false;

    #[Rule('required|exists:kompetensi,id', message:'Kompetensi wajib dipilih.')]
    public $kompetensi_id = '';
    
    #[Rule('required|string|min:10', message:'Teks pertanyaan wajib diisi (minimal 10 karakter).')]
    public $teks_pertanyaan = '';

    // Properti untuk checkbox Tipe Penilai
    #[Rule('boolean')]
    public $untuk_diri = false;
    #[Rule('boolean')]
    public $untuk_atasan = false;
    #[Rule('boolean')]
    public $untuk_rekan = false;
    #[Rule('boolean')]
    public $untuk_bawahan = false;
    // Validasi custom: minimal 1 checkbox terpilih
    public $penilaiError = ''; 

    #[Rule('required|in:Aktif,Tidak Aktif')]
    public $status = 'Aktif'; 

    /**
     * Fungsi utama untuk menyimpan (HANYA CREATE) atau UPDATE STATUS
     */
    public function savePertanyaan()
    {
        // 1. Validasi dasar
        $validatedData = $this->validate();

        // 2. Validasi custom: minimal 1 Tipe Penilai dipilih
        if (!$this->untuk_diri && !$this->untuk_atasan && !$this->untuk_rekan && !$this->untuk_bawahan) {
            $this->addError('penilai_checkbox', 'Pilih minimal satu Tipe Penilai.');
            return;
        }

        // 3. Logika Simpan atau Update Status
        if ($this->isEditMode && $this->pertanyaanId) {
            // MODE EDIT: Hanya update status
            $pertanyaan = Pertanyaan::find($this->pertanyaanId);
            if ($pertanyaan) {
                $pertanyaan->status = $this->status;
                $pertanyaan->save();
                session()->flash('message', 'Status pertanyaan berhasil diperbarui!');
            } else {
                 session()->flash('error', 'Pertanyaan tidak ditemukan.');
            }
        } else {
            // MODE CREATE: Simpan data baru
            Pertanyaan::create($validatedData);
            session()->flash('message', 'Pertanyaan berhasil ditambahkan!');
        }

        // 4. Tutup modal & kirim notifikasi
        $this->closeModal();
        $this->dispatch('close-pertanyaan-modal'); 
        $this->resetPage(); 
    }

    /**
     * Memuat data untuk mode Edit & membuka modal
     * Hanya status yang bisa diedit.
     */
    public function edit($id)
    {
        $pertanyaan = Pertanyaan::findOrFail($id);
        $this->pertanyaanId = $pertanyaan->id;
        $this->kompetensi_id = $pertanyaan->kompetensi_id;
        $this->teks_pertanyaan = $pertanyaan->teks_pertanyaan;
        $this->untuk_diri = (bool)$pertanyaan->untuk_diri;
        $this->untuk_atasan = (bool)$pertanyaan->untuk_atasan;
        $this->untuk_rekan = (bool)$pertanyaan->untuk_rekan;
        $this->untuk_bawahan = (bool)$pertanyaan->untuk_bawahan;
        $this->status = $pertanyaan->status;
        
        $this->isEditMode = true; // Tandai mode edit
        $this->resetValidation(); 
        $this->dispatch('open-pertanyaan-modal'); 
    }

     /**
     * Membuka modal Tambah
     */
    public function showTambahModal()
    {
        $this->resetForm(); 
        $this->isEditMode = false; // Pastikan mode tambah
        $this->status = 'Aktif'; // Default aktif
        $this->dispatch('open-pertanyaan-modal'); 
    }

    /**
     * Konfirmasi sebelum hapus
     */
    public function confirmDelete($id)
    {
        $this->pertanyaanId = $id; 
        $this->dispatch('show-delete-confirmation-pertanyaan'); // Nama event unik
    }

    /**
     * Hapus data setelah dikonfirmasi
     */
    #[On('deleteConfirmedPertanyaan')] // Nama listener unik
    public function delete()
    {
        $pertanyaan = Pertanyaan::find($this->pertanyaanId);
        if ($pertanyaan) {
            // Logika tambahan: Cek apakah pertanyaan ini sudah pernah dijawab? Jika iya, jangan hapus?
            // if ($pertanyaan->jawaban()->exists()) { session()->flash('error','...'); return; } 

            $pertanyaan->delete();
            session()->flash('message', 'Pertanyaan berhasil dihapus.');
        } else {
            session()->flash('error', 'Pertanyaan tidak ditemukan.');
        }
        $this->pertanyaanId = null;
    }

    /**
     * Menutup modal dan reset state (hanya kirim event)
     */
    public function closeModal()
    {
        $this->resetForm();
        $this->dispatch('close-pertanyaan-modal'); 
    }

    /**
     * Reset properti form
     */
    public function resetForm()
    {
         $this->reset([
            'pertanyaanId', 'isEditMode', 'kompetensi_id', 'teks_pertanyaan', 
            'untuk_diri', 'untuk_atasan', 'untuk_rekan', 'untuk_bawahan', 
            'status'
        ]);
        $this->resetErrorBag(); 
        $this->resetValidation();
    }

    public function render()
    {
        // Ambil data dari database dengan pagination dan search
        $daftarPertanyaan = Pertanyaan::with('kompetensi') // Eager load relasi kompetensi
                            ->where(function($query) {
                                $query->where('teks_pertanyaan', 'like', '%'.$this->search.'%')
                                      ->orWhereHas('kompetensi', function ($qKompetensi) {
                                          $qKompetensi->where('nama_kompetensi', 'like', '%'.$this->search.'%');
                                      });
                            })
                            ->orderBy('kompetensi_id', 'asc') // Urutkan berdasarkan kompetensi dulu
                            ->orderBy('created_at', 'asc') // Lalu berdasarkan waktu dibuat
                            ->paginate($this->perPage); 

        // Ambil daftar kompetensi aktif untuk dropdown di modal
        $kompetensiList = Kompetensi::where('status', 'Aktif')->orderBy('nama_kompetensi')->get();

        return view('livewire.admin.pertanyaan-crud', [
            'daftarPertanyaan' => $daftarPertanyaan,
            'kompetensiList' => $kompetensiList, // Kirim daftar kompetensi ke view
        ]);
    }
}
