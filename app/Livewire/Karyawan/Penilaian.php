<?php

namespace App\Livewire\Karyawan;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth; // <-- Pastikan ini ada

#[Layout('layouts.admin')] 
class Penilaian extends Component
{
    // Properti untuk menyimpan daftar orang yang akan dinilai
    public $diriSendiri = []; // <-- Tambahkan ini
    public $atasan = [];
    public $rekanSejawat = [];
    public $bawahan = [];

    /**
     * Method 'mount' dijalankan saat komponen pertama kali dimuat.
     */
    public function mount()
    {
        // Ambil data user yang sedang login
        $user = Auth::user();
        /** @var \App\Models\User|null $user */ // <-- Tambahkan PHPDoc

        // Cek jika user ada sebelum load
        if ($user) {
             $user->load('pegawai.jabatan'); // <-- Merah akan hilang

            // 1. Data Diri Sendiri
            $this->diriSendiri = [
                [
                    'nama' => $user->name,
                    'nip' => $user->pegawai?->nip ?? 'N/A',
                    'jabatan' => $user->pegawai?->jabatan?->nama_jabatan ?? 'Jabatan Tidak Ditemukan',
                    'foto' => '/avatar.png' 
                ]
            ];
        } else {
             // Handle jika user tidak ditemukan
             return redirect('/login');
        }

        // 2. Contoh data atasan
        $this->atasan = [
            [
                'nama' => 'Sri Wahyuni, S.Kom., M.Kom',
                'nip' => '198501012015042001',
                'jabatan' => 'Wakil Direktur I',
                'foto' => '/avatar.png'
            ]
        ];

        // 3. Contoh data rekan sejawat
        $this->rekanSejawat = [
            [
                'nama' => 'Fina Nasari, S.Kom., M.Kom',
                'nip' => '198602022016052002',
                'jabatan' => 'Dosen Teknik Informatika',
                'foto' => '/avatar.png'
            ],
            [
                'nama' => 'Ferdi Febrian, S.T., M.T',
                'nip' => '198703032017061003',
                'jabatan' => 'Dosen Teknik Informatika',
                'foto' => '/avatar.png'
            ]
        ];

        // 4. Contoh data bawahan
        $this->bawahan = [
             [
                'nama' => 'Khairul Hasybi, S.Kom',
                'nip' => '200104042023071004',
                'jabatan' => 'Staff Laboratorium',
                'foto' => '/avatar.png'
            ]
        ];
    }
    
    public function render()
    {
        return view('livewire.karyawan.penilaian');
    }
}