<?php

namespace App\Livewire\Karyawan;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\PenilaianAlokasi;
use App\Models\PenilaianSession;

class Penilaian extends Component
{
    public function render()
    {
        $userId = Auth::id();
        
        // 1. Cek Sesi Aktif (Untuk Alert & Validasi Waktu)
        $activeSession = PenilaianSession::whereHas('alokasis', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('status', 'Open')->latest()->first();

        // 2. Ambil Data Penilaian (Hanya jika Sesi Masih Buka & Belum Expired)
        $activeAssignments = PenilaianAlokasi::with(['target.pegawai.jabatans', 'jabatan', 'penilaiJabatan'])
            ->where('user_id', $userId)
            ->whereHas('penilaianSession', function($q) {
                $q->where('status', 'Open')
                  ->where('batas_waktu', '>', now()); 
            })
            ->get();

        return view('livewire.karyawan.penilaian', [
            // [LOGIKA DITUKAR DISINI AGAR TAMPILAN BENAR]
            
            // Tab "Penilaian Atasan" -> Isinya orang yang saya nilai sebagai 'Bawahan' (Bos Saya)
            'atasan' => $activeAssignments->where('sebagai', 'Bawahan'),

            // Tab "Penilaian Bawahan" -> Isinya orang yang saya nilai sebagai 'Atasan' (Anak Buah Saya)
            'bawahan' => $activeAssignments->where('sebagai', 'Atasan'),

            // Rekan & Diri Sendiri (Tidak berubah)
            'rekan' => $activeAssignments->where('sebagai', 'Rekan'),
            'diri' => $activeAssignments->where('sebagai', 'Diri Sendiri'),
            
            'sessionInfo' => $activeSession 
        ])->layout('layouts.admin'); // Sesuaikan jika Anda punya layouts.karyawan
    }
}