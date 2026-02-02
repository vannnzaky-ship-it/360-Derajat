<?php

namespace App\Livewire\Karyawan;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\PenilaianAlokasi;
use App\Models\PenilaianSession; 
use Carbon\Carbon;

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
        $now = Carbon::now();

        // [PERBAIKAN] Cari sesi yang statusnya 'Open' ATAU 'Diperpanjang'
        $sesiAktif = PenilaianSession::whereIn('status', ['Open', 'Diperpanjang'])
                                     ->where('batas_waktu', '>', $now)
                                     ->latest() // Ambil yang paling baru jika ada multiple (safety)
                                     ->first();
        
        $totalTugas = 0; 
        $sudahSelesai = 0; 
        $persentase = 0; 
        $adaSesi = false; 
        $deadline = null;
        $isDiperpanjang = false; // Flag baru untuk UI

        if ($sesiAktif) {
            $totalTugas = PenilaianAlokasi::where('user_id', $userId)
                                          ->where('penilaian_session_id', $sesiAktif->id)
                                          ->count();

            if ($totalTugas > 0) {
                $adaSesi = true;
                $deadline = $sesiAktif->batas_waktu;
                $isDiperpanjang = ($sesiAktif->status === 'Diperpanjang'); // Cek status

                $sudahSelesai = PenilaianAlokasi::where('user_id', $userId)
                                                ->where('penilaian_session_id', $sesiAktif->id)
                                                ->where('status_nilai', 'Sudah')
                                                ->count();

                $persentase = round(($sudahSelesai / $totalTugas) * 100);
            }
        }

        return view('livewire.karyawan.dashboard', [
            'totalTugas'   => $totalTugas,
            'sudahSelesai' => $sudahSelesai,
            'persentase'   => $persentase,
            'adaSesi'      => $adaSesi,
            'deadline'     => $deadline,
            'isDiperpanjang' => $isDiperpanjang // Kirim ke View
        ]);
    }
}