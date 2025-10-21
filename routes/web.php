<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Login360;
use App\Livewire\PilihRole;
use App\Livewire\Superadmin\DataPegawai as SuperadminDataPegawai;
use App\Livewire\Superadmin\ManajemenAdmin;
// use App\Livewire\Peninjau\Dashboard as PeninjauDashboard; // Kita akan buat ini setelah error hilang
use App\Livewire\Karyawan\Dashboard as KaryawanDashboard;
use App\Livewire\Karyawan\Penilaian as KaryawanPenilaian;
use App\Livewire\Karyawan\Raport as KaryawanRaport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Rute Aplikasi 360 Degree (Struktur Baru)
|--------------------------------------------------------------------------
*/

// --- RUTE AUTENTIKASI KUSTOM ---
Route::get('/login', Login360::class)->name('login')->middleware('guest');
Route::get('/pilih-role', PilihRole::class)->name('pilih-role')->middleware('auth');
Route::post('/logout', function () {
    Auth::logout();
    Session::flush();
    return redirect('/login');
})->name('logout');


// --- RUTE BERDASARKAN PERAN (ROLE) ---

// GRUP SUPER ADMIN
Route::prefix('superadmin')
    ->middleware(['auth', 'role:superadmin'])
    ->name('superadmin.')
    ->group(function () {
        Route::redirect('/dashboard', '/superadmin/manajemen-admin')->name('dashboard');
        Route::get('/manajemen-admin', ManajemenAdmin::class)->name('manajemen-admin');
        Route::get('/data-pegawai', SuperadminDataPegawai::class)->name('data-pegawai');
});

// GRUP ADMINISTRATOR
Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.')
    ->group(function () {
        Route::redirect('/dashboard', '/admin/data-pegawai')->name('dashboard');
        Route::get('/data-pegawai', SuperadminDataPegawai::class)->name('data-pegawai');
});

// GRUP PENINJAU
// GRUP PENINJAU
Route::prefix('peninjau')
    ->middleware(['auth', 'role:peninjau'])
    ->name('peninjau.')
    ->group(function () {
        // ==== GANTI closure DENGAN KOMPONEN BARU ====
        Route::get('/dashboard', \App\Livewire\Peninjau\Dashboard::class)->name('dashboard');
        // ---------------------------------------------
        // Rute lain untuk peninjau bisa ditambahkan di sini
});

// GRUP KARYAWAN
Route::prefix('karyawan')
    ->middleware(['auth', 'role:karyawan'])
    ->name('karyawan.')
    ->group(function () {
        Route::get('/dashboard', KaryawanDashboard::class)->name('dashboard');
        Route::get('/penilaian', KaryawanPenilaian::class)->name('penilaian');
        Route::get('/raport', KaryawanRaport::class)->name('raport');
    });


/// --- RUTE ROOT (HALAMAN UTAMA /) ---
Route::get('/', function () {
    // Middleware 'auth' sudah memastikan user login di sini
    $user = Auth::user();
    /** @var \App\Models\User $user */

    $roles = $user->roles;
    $nonKaryawanRoles = $roles->where('name', '!=', 'karyawan');

    // Prioritaskan redirect ke 'pilih-role' jika perlu
    if ($nonKaryawanRoles->count() > 0 && $roles->count() > 1) {
        // Punya >1 role & salah satunya bukan hanya 'karyawan'
        return redirect()->route('pilih-role');
    } 

    // Jika hanya 1 role (atau hanya 'karyawan') atau tidak ada role sama sekali
    elseif ($roles->isNotEmpty()) {
        // Ambil role pertama (prioritas jika hanya 1, atau role 'karyawan' jika hanya itu)
        $roleName = $roles->first()->name; 
        $redirectPath = match ($roleName) {
            'superadmin' => '/superadmin/dashboard',
            'admin'      => '/admin/dashboard',
            'peninjau'   => '/peninjau/dashboard',
            'karyawan'   => '/karyawan/dashboard', // Menangani kasus hanya role 'karyawan'
            default      => '/login', // Seharusnya tidak terjadi jika user punya role
        };
        return redirect($redirectPath);
    } 

    // Kasus Aneh: User login tapi tidak punya role sama sekali
    else {
        Auth::logout(); // Logout paksa
        return redirect('/login')->withErrors('Akun Anda tidak memiliki peran yang valid.');
    }

})->middleware('auth')->name('home'); // Beri nama 'home' jika perlu