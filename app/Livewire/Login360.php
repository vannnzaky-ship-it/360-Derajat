<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

#[Layout('layouts.app')]
#[Title('Login - Sistem Penilaian 360')]
class Login360 extends Component
{
    public string $email = '';
    public string $password = '';

    public function login()
    {
        $credentials = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            /** @var \App\Models\User $user */
            $user->load('roles'); 
            
            $roles = $user->roles;

            if ($roles->isEmpty()) {
                Auth::logout();
                $this->addError('email', 'Akun Anda tidak memiliki peran. Hubungi Admin.');
                return;
            }

            // --- LOGIKA BARU: JIKA PUNYA ROLE SUPERADMIN, LANGSUNG MASUK ---
            // Mengabaikan role lain jika dia adalah superadmin
            if ($roles->contains('name', 'superadmin')) {
                Session::put('selected_role', 'superadmin');
                return $this->redirect('/superadmin/dashboard', navigate: true);
            }

            // JIKA BUKAN SUPERADMIN & ROLE LEBIH DARI 1, ARAHKAN KE HALAMAN PILIH ROLE
            if ($roles->count() > 1) {
                return $this->redirect('/pilih-role', navigate: true);
            }

            // JIKA ROLE HANYA 1, LANGSUNG MASUK DASHBOARD SESUAI ROLENYA
            $roleName = $roles->first()->name;
            Session::put('selected_role', $roleName);
            
            return $this->redirect($this->getRedirectPath($roleName), navigate: true);

        } else {
            $this->addError('email', 'Email atau Password salah.');
        }
    }

    protected function getRedirectPath(string $roleName): string
    {
        return match ($roleName) {
            'superadmin' => '/superadmin/dashboard', 
            'admin'      => '/admin/dashboard',
            'peninjau'   => '/peninjau/dashboard',
            'karyawan'   => '/karyawan/dashboard',
            default      => '/', 
        };
    }

    public function render()
    {
        return view('livewire.login360');
    }
}