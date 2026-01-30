<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index()
    {
        // 1. Jika belum login, tendang ke halaman login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 2. Cek apakah user punya Role?
        if ($user->roles->isEmpty()) {
            Auth::logout();
            return redirect()->route('login')->withErrors('Akun Anda tidak memiliki peran aktif.');
        }

        // 3. Logika Multi-Role (Jika punya role selain karyawan)
        // Kita cek apakah perlu masuk ke halaman pilih role dulu
        $nonKaryawanRoles = $user->roles->where('name', '!=', 'karyawan');
        
        if ($nonKaryawanRoles->count() > 0 && $user->roles->count() > 1) { 
            return redirect()->route('pilih-role'); 
        }

        // 4. Jika Single Role, set session dan langsung redirect dashboard
        $roleName = $user->roles->first()->name;
        Session::put('selected_role', $roleName); 

        return redirect($this->getDashboardRoute($roleName));
    }

    /**
     * Helper untuk mendapatkan URL Dashboard berdasarkan role
     */
    private function getDashboardRoute($role)
    {
        return match ($role) {
            'superadmin' => route('superadmin.dashboard'),
            'admin'      => route('admin.dashboard'),
            'peninjau'   => route('peninjau.profil'), // Sesuaikan jika dashboard peninjau beda
            'karyawan'   => route('karyawan.dashboard'),
            default      => route('login'),
        };
    }
}