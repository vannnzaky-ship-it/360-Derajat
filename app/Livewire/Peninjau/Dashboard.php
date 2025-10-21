<?php

namespace App\Livewire\Peninjau;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')] // Menggunakan layout admin
class Dashboard extends Component
{
    public function render()
    {
        // Nanti Anda bisa tambahkan logika untuk mengambil data
        // ringkasan hasil penilaian di sini.
        return view('livewire.peninjau.dashboard');
    }
}