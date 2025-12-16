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
        $this->roles = $user->roles;

        // --- LOGIKA BARU: AUTO LOGOUT JIKA TEKAN BACK ---
        // Jika user masuk ke sini tapi 'selected_role' sudah ada,
        // berarti dia menekan tombol BACK dari Dashboard.
        // Maka: Logout-kan dia & lempar ke halaman login.
        if (Session::has('selected_role')) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            
            return redirect()->route('login');
        }
        // ------------------------------------------------

        // Logika Otomatis (Jika user baru login & cuma punya 1 role)
        if ($this->roles->count() === 1) {
            $singleRole = $this->roles->first()->name;
            Session::put('selected_role', $singleRole);
            return $this->redirect($this->getRedirectPath($singleRole));
        }

        if ($this->roles->isEmpty()) {
            Auth::logout();
            return redirect()->route('login')->withErrors('Akun tidak punya peran.');
        }
    }

    public function selectRole(string $roleName)
    {
        if (!$this->roles->contains('name', $roleName)) {
            return session()->flash('error', 'Peran tidak valid.');
        }

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