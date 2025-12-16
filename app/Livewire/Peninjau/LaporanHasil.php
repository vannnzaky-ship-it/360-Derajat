<?php

namespace App\Livewire\Peninjau;

use Livewire\Component;
use App\Models\Siklus;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')] // Sesuaikan dengan layout utama Anda
class LaporanHasil extends Component
{
    public function render()
    {
        // Ambil siklus yang SUDAH memiliki data penilaian
        $sikluses = Siklus::whereHas('penilaianSession', function($q) {
            $q->whereHas('alokasis'); 
        })->orderBy('tahun_ajaran', 'desc')->get();

        return view('livewire.peninjau.laporan-hasil', [
            'sikluses' => $sikluses
        ]);
    }
}