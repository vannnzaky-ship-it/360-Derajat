<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use App\Models\Pertanyaan;
use App\Models\Kompetensi;
use Livewire\WithPagination;
use Livewire\Attributes\On;

#[Layout('layouts.admin', ['title' => 'Manajemen Pertanyaan'])]
class PertanyaanCrud extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;

    public $pertanyaanId = null;
    public $isEditMode = false;
    public $showModal = false;

    #[Rule('required|exists:kompetensi,id', message:'Kompetensi wajib dipilih.')]
    public $kompetensi_id = '';

    #[Rule('required|string|min:10', message:'Teks pertanyaan wajib diisi (minimal 10 karakter).')]
    public $teks_pertanyaan = '';

    #[Rule('boolean')] public $untuk_diri = false;
    #[Rule('boolean')] public $untuk_atasan = false;
    #[Rule('boolean')] public $untuk_rekan = false;
    #[Rule('boolean')] public $untuk_bawahan = false;

    public $penilaiError = '';

    #[Rule('required|in:Aktif,Tidak Aktif')]
    public $status = 'Aktif';

    public function savePertanyaan()
    {
        $validatedData = $this->validate();

        if (!$this->untuk_diri && !$this->untuk_atasan && !$this->untuk_rekan && !$this->untuk_bawahan) {
            $this->addError('penilai_checkbox', 'Pilih minimal satu Tipe Penilai.');
            return;
        }

        if ($this->isEditMode && $this->pertanyaanId) {
            // MODE EDIT
            $pertanyaan = Pertanyaan::find($this->pertanyaanId);
            if ($pertanyaan) {
                // Jika sudah ada nilai, hanya boleh edit status (dan teks jika perlu, tapi hati-hati)
                // Disini kita izinkan edit status.
                $pertanyaan->status = $this->status;
                
                // Opsional: Jika ingin mengizinkan edit teks meski sudah ada nilai, biarkan baris ini.
                // Jika ingin melarang edit teks saat sudah ada nilai, bungkus dengan if.
                $pertanyaan->teks_pertanyaan = $this->teks_pertanyaan;
                // Update tipe penilai dll...
                $pertanyaan->untuk_diri = $this->untuk_diri;
                $pertanyaan->untuk_atasan = $this->untuk_atasan;
                $pertanyaan->untuk_rekan = $this->untuk_rekan;
                $pertanyaan->untuk_bawahan = $this->untuk_bawahan;
                $pertanyaan->kompetensi_id = $this->kompetensi_id;

                $pertanyaan->save();
                session()->flash('message', 'Pertanyaan berhasil diperbarui!');
            } else {
                session()->flash('error', 'Pertanyaan tidak ditemukan.');
            }
        } else {
            // MODE CREATE
            Pertanyaan::create($validatedData);
            session()->flash('message', 'Pertanyaan berhasil ditambahkan!');
        }

        $this->closeModal();
        $this->dispatch('close-pertanyaan-modal');
        $this->resetPage();
    }

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

        $this->isEditMode = true;
        $this->resetValidation();
        $this->dispatch('open-pertanyaan-modal');
    }

    public function showTambahModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->status = 'Aktif';
        $this->dispatch('open-pertanyaan-modal');
    }

    public function confirmDelete($id)
    {
        $this->pertanyaanId = $id;
        $this->dispatch('show-delete-confirmation-pertanyaan');
    }

    #[On('deleteConfirmedPertanyaan')]
    public function delete()
    {
        $pertanyaan = Pertanyaan::find($this->pertanyaanId);
        if ($pertanyaan) {
            // [LOGIKA BARU] Cek apakah pertanyaan sudah ada di PenilaianSkor
            if ($pertanyaan->penilaianSkors()->exists()) {
                session()->flash('error', 'DILARANG: Pertanyaan ini sudah digunakan dalam penilaian (data nilai sudah ada). Menghapus ini akan merusak hasil penilaian.');
                $this->pertanyaanId = null;
                return;
            }

            $pertanyaan->delete();
            session()->flash('message', 'Pertanyaan berhasil dihapus.');
        } else {
            session()->flash('error', 'Pertanyaan tidak ditemukan.');
        }
        $this->pertanyaanId = null;
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->dispatch('close-pertanyaan-modal');
    }

    public function resetForm()
    {
        $this->reset(['pertanyaanId', 'isEditMode', 'kompetensi_id', 'teks_pertanyaan', 'untuk_diri', 'untuk_atasan', 'untuk_rekan', 'untuk_bawahan', 'status']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        // [UPDATE] Tambahkan withCount('penilaianSkors')
        $daftarPertanyaan = Pertanyaan::with('kompetensi')
            ->withCount('penilaianSkors') // <-- Menghitung jumlah penggunaan di tabel skor
            ->where(function($query) {
                $query->where('teks_pertanyaan', 'like', '%'.$this->search.'%')
                      ->orWhereHas('kompetensi', function ($qKompetensi) {
                          $qKompetensi->where('nama_kompetensi', 'like', '%'.$this->search.'%');
                      });
            })
            ->orderBy('kompetensi_id', 'asc')
            ->orderBy('created_at', 'asc')
            ->paginate($this->perPage);

        $kompetensiList = Kompetensi::where('status', 'Aktif')->orderBy('nama_kompetensi')->get();

        return view('livewire.admin.pertanyaan-crud', [
            'daftarPertanyaan' => $daftarPertanyaan,
            'kompetensiList' => $kompetensiList,
        ]);
    }
}