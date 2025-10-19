<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Ambil role yang dipilih dari session
        $selectedRole = Session::get('selected_role');

        // 2. Jika tidak ada role di session, paksa pilih role
        if (!$selectedRole) {
            return redirect('/pilih-role');
        }

        // 3. Cek apakah role di session ada di daftar $roles yang diizinkan
        if (in_array($selectedRole, $roles)) {
            // Izinkan akses
            return $next($request);
        }

        // 4. Jika tidak diizinkan, kembalikan ke halaman pilih role
        return redirect('/pilih-role')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
    }
}