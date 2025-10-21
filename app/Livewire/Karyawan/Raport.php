<?php

namespace App\Livewire\Karyawan;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.admin')] 
class Raport extends Component
{
    // Data Pengguna
    public $namaUser, $nipUser, $jabatanUser;

    // Pilihan Semester
    public $listSemester = [];
    public $selectedSemester;

    // Data Raport (Dummy)
    public $chartData = [];
    public $tableData = [];
    public $ranking = '';

    /**
     * Mount dijalankan saat komponen dimuat
     */
    public function mount()
    {
        // Ambil data user
        $user = Auth::user();
        /** @var \App\Models\User|null $user */ // <-- Tambahkan PHPDoc

        // Cek jika user ada sebelum load
        if ($user) {
            $user->load('pegawai.jabatan'); // <-- Merah akan hilang
            $this->namaUser = $user->name;
            $this->nipUser = $user->pegawai?->nip ?? 'N/A'; // Gunakan null safe operator
            $this->jabatanUser = $user->pegawai?->jabatan?->nama_jabatan ?? 'N/A';
        } else {
            // Handle jika user tidak ditemukan (meskipun middleware harusnya mencegah ini)
            // Misalnya, redirect ke login atau tampilkan error
            return redirect('/login'); 
        }

        // Isi list semester (contoh)
        $this->listSemester = [
            '20242' => '2024/2025 Genap',
            '20241' => '2024/2025 Ganjil',
            '20232' => '2023/2024 Genap',
        ];
        $this->selectedSemester = array_key_first($this->listSemester); 
        $this->loadRaportData();
    }

    /**
     * Dipanggil otomatis saat $selectedSemester berubah
     */
    public function updatedSelectedSemester()
    {
        // Muat ulang data raport berdasarkan semester baru
        $this->loadRaportData();
        
        // (Opsional) Kirim event ke JS jika chart perlu di-update
        // $this->dispatch('semesterChanged', data: $this->chartData);
    }

    /**
     * Fungsi untuk memuat data raport (masih dummy)
     */
    public function loadRaportData()
    {
        // Nanti, query ke database berdasarkan $this->selectedSemester

        // Contoh data dummy untuk chart & tabel
        // Sesuaikan label dan nilai berdasarkan semester (jika perlu)
        if ($this->selectedSemester == '20242') {
             $this->chartData = [
                'labels' => ['Kepribadian', 'Kompetensi Pedagogik', 'Kompetensi Profesional', 'Kompetensi Sosial', 'Kinerja'],
                'scores' => [85, 90, 78, 92, 88] 
            ];
            $this->tableData = [
                'Kepribadian' => 850,
                'Kompetensi Pedagogik' => 900,
                'Kompetensi Profesional' => 780,
                'Kompetensi Sosial' => 920,
                'Kinerja' => 880,
            ];
            $this->ranking = 'Posisi ke-5 dari 50 pegawai';
        } else {
             $this->chartData = [
                'labels' => ['Kepribadian', 'Kompetensi Pedagogik', 'Kompetensi Profesional', 'Kompetensi Sosial', 'Kinerja'],
                'scores' => [80, 85, 82, 88, 81] 
            ];
             $this->tableData = [
                'Kepribadian' => 800,
                'Kompetensi Pedagogik' => 850,
                'Kompetensi Profesional' => 820,
                'Kompetensi Sosial' => 880,
                'Kinerja' => 810,
            ];
            $this->ranking = 'Posisi ke-8 dari 48 pegawai';
        }
    }

    /**
     * Fungsi untuk export (placeholder)
     */
    public function export($type)
    {
        // Logika export PDF atau Excel akan ditambahkan di sini
        session()->flash('info', "Fitur Export $type belum diimplementasikan.");
    }


    public function render()
    {
        return view('livewire.karyawan.raport');
    }
}