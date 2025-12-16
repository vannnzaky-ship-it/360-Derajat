<?php

namespace App\Livewire\Karyawan;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

// Import Model yang Benar
use App\Models\PenilaianAlokasi;
use App\Models\PenilaianSession; 

#[Layout('layouts.admin')] 
class Dashboard extends Component
{
    public $namaUser;

    public function mount()
    {
        $this->namaUser = Auth::user()->name;
    }

    public function render()
    {
        $userId = Auth::id();

        // 1. CEK SESI AKTIF
        // Kita cari sesi yang statusnya 'Aktif'. 
        // (Pastikan di database tabel 'penilaian_sessions' ada kolom 'status')
        $sesiAktif = PenilaianSession::where('status', 'Aktif')->first();
        
        // Default Variable (Jika tidak ada sesi)
        $totalTugas = 0;
        $sudahSelesai = 0;
        $persentase = 0;
        $adaSesi = false;

        // 2. JIKA ADA SESI AKTIF, BARU HITUNG DATA
        if ($sesiAktif) {
            $adaSesi = true;

            // Hitung Total Tugas pada sesi INI SAJA (filter by penilaian_session_id)
            $totalTugas = PenilaianAlokasi::where('user_id', $userId)
                                          ->where('penilaian_session_id', $sesiAktif->id)
                                          ->count();

            // Hitung yang statusnya 'Sudah'
            $sudahSelesai = PenilaianAlokasi::where('user_id', $userId)
                                            ->where('penilaian_session_id', $sesiAktif->id)
                                            ->where('status_nilai', 'Sudah')
                                            ->count();

            // Hitung Persentase
            if ($totalTugas > 0) {
                $persentase = round(($sudahSelesai / $totalTugas) * 100);
            }
        }

        return view('livewire.karyawan.dashboard', [
            'totalTugas'   => $totalTugas,
            'sudahSelesai' => $sudahSelesai,
            'persentase'   => $persentase,
            'adaSesi'      => $adaSesi // Variable penentu tampilan
        ]);
    }
}