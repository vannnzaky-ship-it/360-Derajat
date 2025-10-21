<?php

namespace App\Livewire\Karyawan;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

// Gunakan layout 'admin' baru kita
#[Layout('layouts.admin')] 
class Dashboard extends Component
{
    public $namaUser;

    public function mount()
    {
        // Ambil nama user yang sedang login
        $this->namaUser = Auth::user()->name;
    }

    public function render()
    {
        return view('livewire.karyawan.dashboard');
    }
}