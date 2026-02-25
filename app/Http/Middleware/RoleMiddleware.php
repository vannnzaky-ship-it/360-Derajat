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
        $user = $request->user();

        // 1. Jika tidak ada user yang login
        if (!$user) {
            return redirect()->route('login');
        }
        
        $selectedRole = Session::get('selected_role');

        // 2. JAGA-JAGA: Jika session kosong (misal karena session kedaluwarsa tapi Auth cookie masih ada)
        if (!$selectedRole) {
            if ($user->roles()->count() > 1) {
                return redirect()->route('pilih-role');
            }
            
            // Set otomatis jika cuma punya 1 peran
            $singleRole = $user->roles()->first();
            if ($singleRole) {
                $selectedRole = $singleRole->name;
                Session::put('selected_role', $selectedRole);
            } else {
                Auth::logout();
                return redirect()->route('login')->withErrors('Akun Anda tidak memiliki peran.');
            }
        }

        // 3. LOGIKA UTAMA: Cek apakah role yang SEDANG AKTIF diizinkan untuk mengakses URL ini
        if (in_array($selectedRole, $roles)) {
            // Lapis Keamanan Ekstra: Pastikan di database user tersebut memang benar-benar punya role ini
            // (Untuk mencegah user usil memanipulasi session secara paksa)
            if ($user->hasRole($selectedRole)) {
                return $next($request);
            }
        }

        // --- 4. PENOLAKAN AKSES ---
        // Jika sampai di baris ini, berarti peran yang sedang aktif tidak diizinkan masuk halaman ini.
        // (Contoh: Sedang aktif jadi 'karyawan', tapi mau buka link '/admin/dashboard')
        
        // Khusus Superadmin: Kembalikan ke singgasananya (Superadmin tidak boleh masuk rute lain)
        if ($selectedRole === 'superadmin') {
            return redirect('/superadmin/dashboard')->with('error', 'Super Admin dibatasi hanya untuk halaman ini.');
        }

        // Jika user punya lebih dari 1 peran, arahkan ke Pilih Peran agar dia ganti baju dulu
        if ($user->roles()->count() > 1) {
            return redirect()->route('pilih-role')->with('error', 'Silakan ganti peran Anda terlebih dahulu untuk mengakses halaman tersebut.');
        }

        // Jika user cuma punya 1 peran dan memang tidak berhak, blokir dengan halaman 403 (Forbidden)
        abort(403, 'Akses Ditolak. Hak akses Anda tidak diizinkan untuk melihat halaman ini.');
    }
}