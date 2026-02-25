<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

#[Layout('layouts.app')]
#[Title('Pilih Peran')]
class PilihRole extends Component
{
    public $roles;

    public function mount()
    {
        $user = Auth::user();
        
        // --- LOGIKA BARU: PROTEKSI SUPERADMIN ---
        // Jika dia superadmin mencoba akses URL ini, kembalikan ke dashboardnya
        if ($user->hasRole('superadmin')) {
            Session::put('selected_role', 'superadmin');
            return redirect('/superadmin/dashboard');
        }

        // Ambil role selain superadmin
        $this->roles = $user->roles;

        // Logika Otomatis (Jika user cuma punya 1 role)
        if ($this->roles->count() === 1) {
            $singleRole = $this->roles->first()->name;
            Session::put('selected_role', $singleRole);
            return $this->redirect($this->getRedirectPath($singleRole));
        }

        // Jika tidak punya role sama sekali
        if ($this->roles->isEmpty()) {
            Auth::logout();
            return redirect()->route('login')->withErrors('Akun tidak punya peran.');
        }
    }

    public function selectRole(string $roleName)
    {
        // Validasi apakah role yang diklik benar-benar dimiliki oleh user tersebut
        if (!$this->roles->contains('name', $roleName)) {
            return session()->flash('error', 'Peran tidak valid.');
        }

        // Timpa session 'selected_role' dengan role yang baru dipilih
        Session::put('selected_role', $roleName);
        return $this->redirect($this->getRedirectPath($roleName));
    }

    protected function getRedirectPath(string $roleName): string
    {
        return match ($roleName) {
            'superadmin' => '/superadmin/dashboard',
            'admin'      => '/admin/dashboard',
            'peninjau'   => '/peninjau/laporan',
            'karyawan'   => '/karyawan/dashboard',
            default      => '/',
        };
    }

    public function render()
    {
        return view('livewire.pilih-role');
    }
}