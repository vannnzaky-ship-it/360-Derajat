<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Ambil user yang sedang login
        $user = $request->user();

        // 1. Jika tidak ada user yang login (seharusnya tidak terjadi karena middleware 'auth')
        if (!$user) {
            Auth::logout();
            return redirect('/login');
        }
        
        // Ambil peran yang sudah dipilih dari session
        $selectedRole = Session::get('selected_role');

        // ==== HAPUS dd() DARI SINI ====
        // dd('RoleMiddleware melihat session:', $selectedRole, 'Role yang diizinkan:', $roles); 

        // 2. Cek apakah peran di session valid DAN diizinkan
        if ($selectedRole && in_array($selectedRole, $roles)) {
            // Peran di session cocok, izinkan akses
            return $next($request);
        }

        // 3. FALLBACK: Jika session tidak cocok ATAU kosong, cek peran asli user
        $hasAllowedRole = false;
        foreach ($roles as $allowedRole) {
            if ($user->hasRole($allowedRole)) {
                $hasAllowedRole = true;
                // Perbaiki session jika user punya peran tapi session salah/kosong
                if ($selectedRole !== $allowedRole) {
                    Session::put('selected_role', $allowedRole);
                }
                break; // Cukup temukan satu peran yang cocok
            }
        }

        if ($hasAllowedRole) {
            // User punya peran asli yang diizinkan (session sudah diperbaiki), izinkan akses
            return $next($request);
        }

        // 4. Jika session TIDAK cocok DAN peran asli user juga TIDAK cocok
        // Cek apakah user punya > 1 role (mungkin dia salah pilih?)
        if ($user->roles()->count() > 1) {
            // Arahkan kembali ke halaman pilih peran
             return redirect('/pilih-role')->with('error', 'Peran yang Anda pilih tidak memiliki akses ke halaman ini.');
        } else {
             // Jika hanya punya 1 role dan itu salah, logout saja
             Auth::logout();
             return redirect('/login')->withErrors('Peran Anda tidak diizinkan mengakses halaman ini.');
        }
    }
}