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
        // UPDATE: Status boleh 'Open' ATAU 'Diperpanjang'
        $activeSession = PenilaianSession::whereHas('alokasis', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })
        ->whereIn('status', ['Open', 'Diperpanjang']) // <-- Izinkan status Diperpanjang
        ->latest()
        ->first();

        // 2. Ambil Data Penilaian (Hanya jika Sesi Masih Buka & Belum Expired)
        $activeAssignments = PenilaianAlokasi::with(['targetUser', 'targetJabatan', 'penilaiJabatan'])
            ->where('user_id', $userId)
            ->whereHas('penilaianSession', function($q) {
                // UPDATE: Cek status 'Open' ATAU 'Diperpanjang' DAN Waktu belum habis
                $q->whereIn('status', ['Open', 'Diperpanjang'])
                  ->where('batas_waktu', '>', now()); 
            })
            ->get();

        return view('livewire.karyawan.penilaian', [
            // LOGIKA DITUKAR (Sesuai request Anda sebelumnya agar Label Tab sesuai Persepsi User)
            
            // Tab "Penilaian Atasan" -> Menampilkan orang yang menilai saya sebagai 'Bawahan' (Artinya Target adalah Atasan saya)
            'atasan' => $activeAssignments->where('sebagai', 'Bawahan'),

            // Tab "Penilaian Bawahan" -> Menampilkan orang yang menilai saya sebagai 'Atasan' (Artinya Target adalah Bawahan saya)
            'bawahan' => $activeAssignments->where('sebagai', 'Atasan'),

            // Rekan & Diri Sendiri (Logika Lurus)
            'rekan' => $activeAssignments->where('sebagai', 'Rekan'),
            'diri' => $activeAssignments->where('sebagai', 'Diri Sendiri'),
            
            'sessionInfo' => $activeSession 
        ])->layout('layouts.admin'); 
    }
}