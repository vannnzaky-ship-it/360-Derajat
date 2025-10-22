<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Rule; // Import Atribut Rule

class SiklusSemester extends Component
{
    public $search = '';

    // --- Properti untuk Form Modal ---
    
    #[Rule('required|digits:4|integer|min:2020|max:2099', message: 'Tahun ajaran wajib diisi format 4 digit.')]
    public $tahun_ajaran = '';

    #[Rule('required|in:Ganjil,Genap', message: 'Semester wajib dipilih.')]
    public $semester = '';

    #[Rule('required|integer|min:0|max:100', message: 'Presentase wajib diisi angka 0-100.')]
    public $persen_atasan = '';
    
    #[Rule('required|integer|min:0|max:100', message: 'Presentase wajib diisi angka 0-100.')]
    public $persen_rekan = '';

    #[Rule('required|integer|min:0|max:100', message: 'Presentase wajib diisi angka 0-100.')]
    public $persen_bawahan = '';

    #[Rule('required|in:Aktif,Tidak Aktif', message: 'Status wajib dipilih.')]
    public $status = 'Aktif'; // Set nilai default

    // --- Method untuk Modal ---
    
    /**
     * Reset form & validasi saat modal ditutup
     */
    public function closeModal()
    {
        $this->reset([
            'tahun_ajaran', 'semester', 'persen_atasan', 
            'persen_rekan', 'persen_bawahan', 'status'
        ]);
        $this->resetValidation();
    }

    /**
     * Reset form saat modal akan dibuka
     */
    public function showTambahModal()
    {
        // Kita reset datanya di sini agar form selalu kosong
        $this->closeModal();
        $this->status = 'Aktif'; // Set default status
    }

    /**
     * Simpan data baru
     */
    public function saveSiklus()
    {
        // 1. Validasi input
        $validatedData = $this->validate();

        // 2. (Simulasi) Logika penyimpanan data
        // Ganti bagian ini dengan logika database Anda
        // ...
        
        // 3. Reset form
        $this->closeModal();

        // 4. Kirim event untuk memberitahu JS agar menutup modal
        $this->dispatch('close-modal', '#tambahSiklusModal');
        
        // 5. (Opsional) Kirim notifikasi sukses
        // $this->dispatch('swal:success', 'Data siklus berhasil ditambahkan!');

        // 6. Refresh data di tabel
        // (Jika data dari DB, panggil method render ulang atau refresh komponen)
    }


    /**
     * Render komponen.
     */
    public function render()
    {
        // === INI DATA TABEL YANG SUDAH ADA (TETAP ADA) ===
        $data = collect([
            (object)[
                'id' => 1,
                'tahun' => '2022',
                'semester' => 'Ganjil',
                'penilaian' => 'Atasan : 50%<br>Rekan Sejawat : 30%<br>Bawahan : 20%',
                'status' => 'Tidak Aktif'
            ],
            (object)[
                'id' => 2,
                'tahun' => '2022',
                'semester' => 'Genap',
                'penilaian' => 'Atasan : 50%<br>Rekan Sejawat : 30%<br>Bawahan : 20%',
                'status' => 'Tidak Aktif'
            ],
            (object)[
                'id' => 3,
                'tahun' => '2023',
                'semester' => 'Ganjil',
                'penilaian' => 'Atasan : 50%<br>Rekan Sejawat : 30%<br>Bawahan : 20%',
                'status' => 'Tidak Aktif'
            ],
            (object)[
                'id' => 4,
                'tahun' => '2023',
                'semester' => 'Genap',
                'penilaian' => 'Atasan : 50%<br>Rekan Sejawat : 30%<br>Bawahan : 20%',
                'status' => 'Aktif'
            ],
            (object)[
                'id' => 5,
                'tahun' => '2024',
                'semester' => 'Ganjil',
                'penilaian' => 'Atasan : 50%<br>Rekan Sejawat : 30%<br>Bawahan : 20%',
                'status' => 'Aktif'
            ],
            (object)[
                'id' => 6,
                'tahun' => '2024',
                'semester' => 'Genap',
                'penilaian' => 'Atasan : 50%<br>Rekan Sejawat : 30%<br>Bawahan : 20%',
                'status' => 'Tidak Aktif'
            ],
        ]);
        // ===============================================

        // Filter data berdasarkan pencarian (TETAP ADA)
        $filteredData = $data->filter(function ($item) {
            return str_contains(strtolower($item->tahun), strtolower($this->search)) ||
                   str_contains(strtolower($item->semester), strtolower($this->search));
        });

        return view('livewire.admin.siklus-semester', [
            'daftarSiklus' => $filteredData
        ])
        ->layout('layouts.admin', ['title' => 'Siklus Semester']);
    }
}