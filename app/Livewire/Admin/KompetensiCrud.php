<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use App\Models\Kompetensi;
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

    public function saveKompetensi()
    {
        $validatedData = $this->validate();

        $currentTotalBobot = Kompetensi::where('status', 'Aktif')
            ->where('id', '!=', $this->kompetensiId)
            ->sum('bobot');
        $newTotalBobot = $currentTotalBobot + ($this->status == 'Aktif' ? $this->bobot : 0);

        if ($this->status == 'Aktif' && $newTotalBobot > 100) {
            $this->addError('bobot_total', 'Total bobot semua kompetensi aktif tidak boleh melebihi 100%. Sisa bobot: ' . (100 - $currentTotalBobot) . '%');
            return;
        }

        Kompetensi::updateOrCreate(['id' => $this->kompetensiId], $validatedData);

        $this->closeModal();
        $this->dispatch('close-kompetensi-modal');
        session()->flash('message', $this->isEditMode ? 'Kompetensi berhasil diperbarui!' : 'Kompetensi berhasil ditambahkan!');
        $this->resetPage();
    }

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
        $this->dispatch('open-kompetensi-modal');
    }

    public function showTambahModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->status = 'Aktif';
        $this->dispatch('open-kompetensi-modal');
    }

    public function confirmDelete($id)
    {
        $this->kompetensiId = $id;
        $this->dispatch('show-delete-confirmation-kompetensi');
    }

    #[On('deleteConfirmedKompetensi')]
    public function delete()
    {
        $kompetensi = Kompetensi::find($this->kompetensiId);
        if ($kompetensi) {
            // [LOGIKA BARU] Cek server-side juga untuk keamanan
            if ($kompetensi->pertanyaans()->exists()) {
                session()->flash('error', 'GAGAL: Kompetensi tidak bisa dihapus karena masih memiliki pertanyaan.');
                $this->kompetensiId = null;
                return;
            }

            // Validasi bobot (logika lama Anda)
            if ($kompetensi->status == 'Aktif') {
                $currentTotalBobot = Kompetensi::where('status', 'Aktif')
                     ->where('id', '!=', $this->kompetensiId)
                     ->sum('bobot');
                if ($currentTotalBobot == 0 && Kompetensi::where('status', 'Aktif')->count() > 1) {
                    session()->flash('error', 'Tidak dapat menghapus. Harus ada minimal satu kompetensi aktif.');
                    $this->kompetensiId = null;
                    return;
                }
            }

            $kompetensi->delete();
            session()->flash('message', 'Kompetensi berhasil dihapus.');
        } else {
            session()->flash('error', 'Kompetensi tidak ditemukan.');
        }
        $this->kompetensiId = null;
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->dispatch('close-kompetensi-modal');
    }

    public function resetForm()
    {
        $this->reset(['kompetensiId', 'isEditMode', 'nama_kompetensi', 'deskripsi', 'bobot', 'status']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        // [UPDATE] Tambahkan withCount('pertanyaans')
        $daftarKompetensi = Kompetensi::withCount('pertanyaans')
            ->where('nama_kompetensi', 'like', '%'.$this->search.'%')
            ->orderBy('nama_kompetensi', 'asc')
            ->paginate(10);

        $totalBobotAktif = Kompetensi::where('status', 'Aktif')->sum('bobot');

        return view('livewire.admin.kompetensi-crud', [
            'daftarKompetensi' => $daftarKompetensi,
            'totalBobotAktif' => $totalBobotAktif,
        ]);
    }
}