<?php

// Pastikan namespace-nya adalah "Admin"
namespace App\Livewire\Admin; // <-- PERBAIKAN DI SINI (menggunakan \)

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

// Pastikan nama class-nya "Dashboard"
class Dashboard extends Component
{
    public $userName;

    public function mount()
    {
        $this->userName = Auth::user()->name ?? 'Your Name';
    }

    public function render()
    {
        // Ini akan menggunakan layout admin dan mengirim judul
        return view('livewire.admin.dashboard')
            ->layout('layouts.admin', ['title' => 'Dashboard Admin']);
    }
}