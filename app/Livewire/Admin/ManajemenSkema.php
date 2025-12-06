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
        
        // Default pilih siklus Aktif
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
        if ($this->siklus_id_aktif) {
            $this->daftar_skema = SkemaPenilaian::where('siklus_id', $this->siklus_id_aktif)->get();

            // LOGIKA CEK PENUH
            $usedLevels = [];
            foreach($this->daftar_skema as $skema) {
                if(is_array($skema->level_target)) {
                    $usedLevels = array_merge($usedLevels, $skema->level_target);
                }
            }
            $usedLevels = array_unique($usedLevels);
            
            $allMasterLevels = array_keys($this->masterLevel);
            $remainingLevels = array_diff($allMasterLevels, $usedLevels);
            
            $this->isFull = empty($remainingLevels);

        } else {
            $this->daftar_skema = [];
            $this->isFull = false;
        }
    }

    public function showTambahModal()
    {
        $this->resetForm(); // Pastikan form bersih & mode edit mati
        $this->dispatch('open-modal'); 
    }

    // FUNGSI EDIT BARU
    public function edit($id)
    {
        $skema = SkemaPenilaian::findOrFail($id);
        
        $this->skemaIdEditing = $skema->id;
        $this->nama_skema = $skema->nama_skema;
        $this->selected_levels = $skema->level_target; // Array
        $this->p_diri = $skema->persen_diri;
        $this->p_atasan = $skema->persen_atasan;
        $this->p_rekan = $skema->persen_rekan;
        $this->p_bawahan = $skema->persen_bawahan;
        
        $this->isEditMode = true; // Nyalakan mode edit
        
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

        // VALIDASI ANTI-NABRAK (Overlap)
        // Ambil skema lain KECUALI diri sendiri (jika sedang edit)
        $query = SkemaPenilaian::where('siklus_id', $this->siklus_id_aktif);
        
        if ($this->isEditMode && $this->skemaIdEditing) {
            $query->where('id', '!=', $this->skemaIdEditing);
        }
        
        $existingSchemes = $query->get();

        foreach ($existingSchemes as $scheme) {
            $intersect = array_intersect($this->selected_levels, $scheme->level_target);
            if (!empty($intersect)) {
                $duplicateLevels = implode(', ', $intersect);
                $this->addError('selected_levels', "Level ($duplicateLevels) sudah dipakai di skema: '{$scheme->nama_skema}'.");
                return; 
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