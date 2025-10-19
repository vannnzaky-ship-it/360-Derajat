<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

#[Layout('layouts.app')] // Menggunakan layout login
#[Title('Pilih Peran')]
class PilihRole extends Component
{
    public $roles;

    public function mount()
    {
        // Ambil semua role dari user yang sedang login
        $this->roles = Auth::user()->roles;
    }

    /**
     * Dipanggil saat user memilih salah satu role
     */
    public function selectRole(string $roleName)
    {
        // 1. Validasi apakah user benar-benar punya role tsb
        if (!$this->roles->contains('name', $roleName)) {
            return session()->flash('error', 'Peran tidak valid.');
        }

        // 2. Simpan peran yang dipilih ke session
        Session::put('selected_role', $roleName);

        // 3. Arahkan ke dashboard yang sesuai
        return $this->redirect($this->getRedirectPath($roleName), navigate: true);
    }

    protected function getRedirectPath(string $roleName): string
    {
        return match ($roleName) {
            'superadmin' => '/superadmin/dashboard',
            'peninjau' => '/peninjau/dashboard',
            'karyawan' => '/karyawan/dashboard',
            default => '/',
        };
    }

    public function render()
    {
        return view('livewire.pilih-role');
    }
}