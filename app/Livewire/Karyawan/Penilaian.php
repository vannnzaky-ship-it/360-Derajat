<?php

namespace App\Livewire\Karyawan;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\PenilaianAlokasi;
use App\Models\Siklus;

class Penilaian extends Component
{
    public function render()
    {
        $userId = Auth::id();
        
        // Cek Sesi Aktif untuk menampilkan Alert Batas Waktu
        // Kita ambil sesi terakhir yang melibatkan user ini
        $activeSession = \App\Models\PenilaianSession::whereHas('alokasis', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('status', 'Open')->latest()->first();

        // FILTER UTAMA: Hanya ambil data jika Sesi Masih OPEN dan Waktu BELUM HABIS
        // where('batas_waktu', '>', now()) adalah kuncinya!
        $activeAssignments = PenilaianAlokasi::with(['target.pegawai.jabatans', 'jabatan', 'penilaiJabatan'])
            ->where('user_id', $userId)
            ->whereHas('penilaianSession', function($q) {
                $q->where('status', 'Open')
                  ->where('batas_waktu', '>', now()); // <--- INI FILTER WAKTUNYA
            })
            ->get();

        return view('livewire.karyawan.penilaian', [
            'atasan' => $activeAssignments->where('sebagai', 'Atasan'),
            'rekan' => $activeAssignments->where('sebagai', 'Rekan'),
            'bawahan' => $activeAssignments->where('sebagai', 'Bawahan'),
            'diri' => $activeAssignments->where('sebagai', 'Diri Sendiri'),
            
            // Kirim data sesi ke view untuk Alert
            'sessionInfo' => $activeSession 
        ])->layout('layouts.admin');
    }
}