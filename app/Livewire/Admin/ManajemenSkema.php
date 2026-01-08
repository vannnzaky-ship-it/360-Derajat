<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Siklus;
use App\Models\SkemaPenilaian;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('layouts.admin', ['title' => 'Skema Penilaian'])]
class ManajemenSkema extends Component
{
    // Data List
    public $siklus_list;
    public $siklus_id_aktif; 
    public $daftar_skema = [];

    // State UI
    public $isFull = false; 
    public $isEditMode = false; // Penanda mode Edit
    public $skemaIdEditing = null; // ID skema yang sedang diedit
    public $usedLevels = []; // [BARU] Menyimpan level yang sudah dipakai oleh skema lain

    // Form Input
    #[Rule('required|min:3', as: 'Nama Skema')]
    public $nama_skema;

    #[Rule('required|array|min:1', as: 'Target Level')]
    public $selected_levels = []; 

    #[Rule('required|numeric|min:0|max:100')]
    public $p_diri = 0;

    #[Rule('required|numeric|min:0|max:100')]
    public $p_atasan = 0;

    #[Rule('required|numeric|min:0|max:100')]
    public $p_rekan = 0;

    #[Rule('required|numeric|min:0|max:100')]
    public $p_bawahan = 0;

    // Hardcode Level Jabatan
    public $masterLevel = [
        1 => 'Level 1 - Pimpinan Puncak (Direktur)',
        2 => 'Level 2 - Wakil Pimpinan (Wadir)',
        3 => 'Level 3 - Kepala Bagian / Unit',
        4 => 'Level 4 - Kepala Seksi / Sub / SPV',
        5 => 'Level 5 - Staff / Pelaksana',
    ];

    public function mount()
    {
        $this->siklus_list = Siklus::orderBy('tahun_ajaran', 'desc')->get();
        
        // Default pilih siklus Aktif atau yang terbaru
        $siklusAktif = Siklus::where('status', 'Aktif')->first();
        $this->siklus_id_aktif = $siklusAktif ? $siklusAktif->id : ($this->siklus_list->first()->id ?? null);

        $this->loadSkema();
    }

    public function updatedSiklusIdAktif()
    {
        $this->loadSkema();
    }

    public function loadSkema()
    {
        // Reset daftar level terpakai setiap kali load
        $this->usedLevels = []; 

        if ($this->siklus_id_aktif) {
            $this->daftar_skema = SkemaPenilaian::where('siklus_id', $this->siklus_id_aktif)->get();

            // Kumpulkan level yang sudah dipakai oleh skema LAIN
            foreach($this->daftar_skema as $skema) {
                // Jika sedang mode edit, JANGAN masukkan level milik skema yang sedang diedit ini
                // agar checkboxnya tidak disabled untuk dirinya sendiri.
                if ($this->isEditMode && $this->skemaIdEditing == $skema->id) {
                    continue; 
                }

                if(is_array($skema->level_target)) {
                    // Konversi ke string agar in_array aman
                    $levels = array_map('strval', $skema->level_target);
                    $this->usedLevels = array_merge($this->usedLevels, $levels);
                }
            }
            $this->usedLevels = array_unique($this->usedLevels); // Hapus duplikat
            
            // Cek apakah semua level master sudah terpakai semua?
            $allMasterLevels = array_map('strval', array_keys($this->masterLevel));
            $remainingLevels = array_diff($allMasterLevels, $this->usedLevels);
            
            // isFull true jika tidak ada sisa level DAN kita tidak sedang dalam mode edit
            // (kalo mode edit, kita anggap tidak full karena bisa ngedit punya sendiri)
            $this->isFull = empty($remainingLevels) && !$this->isEditMode;

        } else {
            $this->daftar_skema = [];
            $this->isFull = false;
        }
    }

    public function showTambahModal()
    {
        $this->resetForm(); // Pastikan form bersih & mode edit mati
        $this->loadSkema(); // Reload untuk update $usedLevels (semua terpakai masuk)
        $this->dispatch('open-modal'); 
    }

    public function edit($id)
    {
        $skema = SkemaPenilaian::findOrFail($id);
        
        // [LOGIKA LOCK] Cek apakah Siklus ini sudah punya sesi penilaian
        if ($skema->siklus && $skema->siklus->penilaianSession()->exists()) {
             session()->flash('error', 'MAAF: Skema tidak bisa diedit karena penilaian periode ini sudah dimulai/selesai.');
             return;
        }

        $this->skemaIdEditing = $skema->id;
        $this->nama_skema = $skema->nama_skema;
        $this->selected_levels = $skema->level_target; // Array
        $this->p_diri = $skema->persen_diri;
        $this->p_atasan = $skema->persen_atasan;
        $this->p_rekan = $skema->persen_rekan;
        $this->p_bawahan = $skema->persen_bawahan;
        
        $this->isEditMode = true; // Nyalakan mode edit
        
        $this->loadSkema(); // PENTING: Reload skema agar $usedLevels di-refresh dengan exclude ID ini
        
        $this->dispatch('open-modal');
    }

    public function simpan()
    {
        $this->validate();

        $total = $this->p_diri + $this->p_atasan + $this->p_rekan + $this->p_bawahan;
        if ($total != 100) {
            $this->addError('total_persen', "Total persentase harus pas 100%. Total saat ini: {$total}%");
            return;
        }

        if (!$this->siklus_id_aktif) {
            session()->flash('error', 'Silakan pilih Siklus Semester terlebih dahulu.');
            return;
        }

        // [LOGIKA LOCK SAAT SAVE] (Double Check keamanan server-side)
        if ($this->isEditMode && $this->skemaIdEditing) {
             $skemaCek = SkemaPenilaian::find($this->skemaIdEditing);
             if ($skemaCek && $skemaCek->siklus && $skemaCek->siklus->penilaianSession()->exists()) {
                 session()->flash('error', 'GAGAL SIMPAN: Penilaian sedang berjalan, skema terkunci.');
                 return;
             }
        }

        // VALIDASI ANTI-NABRAK (Overlap)
        // Ambil skema lain KECUALI diri sendiri (jika sedang edit)
        $query = SkemaPenilaian::where('siklus_id', $this->siklus_id_aktif);
        
        if ($this->isEditMode && $this->skemaIdEditing) {
            $query->where('id', '!=', $this->skemaIdEditing);
        }
        
        $existingSchemes = $query->get();

        foreach ($existingSchemes as $scheme) {
            // Pastikan casting array bekerja atau decode manual jika perlu
            $schemeLevels = is_array($scheme->level_target) ? $scheme->level_target : json_decode($scheme->level_target, true);
            
            if(is_array($schemeLevels)){
                $intersect = array_intersect($this->selected_levels, $schemeLevels);
                if (!empty($intersect)) {
                    $duplicateLevels = implode(', ', $intersect);
                    $this->addError('selected_levels', "Level ($duplicateLevels) sudah dipakai di skema: '{$scheme->nama_skema}'.");
                    return; 
                }
            }
        }

        // SIMPAN ATAU UPDATE
        SkemaPenilaian::updateOrCreate(
            ['id' => $this->skemaIdEditing], // Kunci pencarian (jika null berarti create baru)
            [
                'siklus_id' => $this->siklus_id_aktif,
                'nama_skema' => $this->nama_skema,
                'level_target' => $this->selected_levels, 
                'persen_diri' => $this->p_diri,
                'persen_atasan' => $this->p_atasan,
                'persen_rekan' => $this->p_rekan,
                'persen_bawahan' => $this->p_bawahan,
            ]
        );

        session()->flash('message', $this->isEditMode ? 'Skema berhasil diperbarui!' : 'Skema berhasil ditambahkan!');
        
        $this->resetForm();
        $this->dispatch('close-modal'); 
        $this->loadSkema();
    }

    public function hapus($id)
    {
        $skema = SkemaPenilaian::find($id);
        if ($skema) {
            // [LOGIKA LOCK] Cek Siklus
            if ($skema->siklus && $skema->siklus->penilaianSession()->exists()) {
                 session()->flash('error', 'GAGAL HAPUS: Skema ini sedang digunakan dalam penilaian aktif.');
                 return;
            }

            $skema->delete();
            session()->flash('message', 'Skema berhasil dihapus.');
            $this->loadSkema();
        }
    }

    public function resetForm()
    {
        // Reset semua properti termasuk ID editing dan mode edit
        $this->reset(['nama_skema', 'selected_levels', 'p_diri', 'p_atasan', 'p_rekan', 'p_bawahan', 'isEditMode', 'skemaIdEditing']);
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.admin.manajemen-skema');
    }
}