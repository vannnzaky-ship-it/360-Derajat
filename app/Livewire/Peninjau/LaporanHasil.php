<?php

namespace App\Livewire\Peninjau;

use Livewire\Component;
use App\Models\Siklus;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin', ['title' => 'Laporan Hasil'])]
class LaporanHasil extends Component
{
    public function render()
    {
        // AMBIL SEMUA SIKLUS YANG PUNYA SESI PENILAIAN
        // (Tidak perlu filter status 'Closed' lagi, karena kita mau tampilkan semua)
        $sikluses = Siklus::whereHas('penilaianSession')
            ->orderBy('tahun_ajaran', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        return view('livewire.peninjau.laporan-hasil', [
            'sikluses' => $sikluses
        ]);
    }
}