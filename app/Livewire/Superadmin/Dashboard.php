<?php

namespace App\Livewire\Superadmin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;

// Pastikan nama layout sesuai dengan file kamu (misal: layouts.admin)
#[Layout('layouts.admin')]
#[Title('Dashboard')] // <-- PENTING: Judul Halaman 
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.superadmin.dashboard', [
            'user' => Auth::user(),
            
            // Hitung total semua user
            'total_pegawai' => User::count(),
            
            // PERBAIKAN: Gunakan whereHas untuk mengecek relasi roles
            'total_admin' => User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->count(),
        ]);
    }
}