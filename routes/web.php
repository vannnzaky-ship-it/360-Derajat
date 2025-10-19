<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Login360;
use App\Livewire\PilihRole;
use App\Livewire\Superadmin\DataPegawai;
use App\Livewire\Peninjau\ManajemenSuperadmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Rute Kustom Aplikasi 360 Degree
|--------------------------------------------------------------------------
*/

// --- RUTE AUTENTIKASI KUSTOM ---

// Rute untuk menampilkan halaman login (hanya untuk tamu)
Route::get('/login', Login360::class)->name('login')->middleware('guest');

// Rute untuk halaman pemilihan peran (hanya untuk yang sudah login)
Route::get('/pilih-role', PilihRole::class)->name('pilih-role')->middleware('auth');

// Rute untuk proses logout
Route::post('/logout', function () {
    Auth::logout();
    Session::flush();
    return redirect('/login');
})->name('logout');


// --- RUTE BERDASARKAN PERAN (ROLE) ---

// GRUP SUPER ADMIN
Route::prefix('superadmin')
    ->middleware(['auth', 'role:superadmin']) // Dilindungi middleware auth & superadmin
    ->name('superadmin.')
    ->group(function () {
        
        // Arahkan /superadmin/dashboard ke /superadmin/data-pegawai
        Route::redirect('/dashboard', '/superadmin/data-pegawai')->name('dashboard');
        
        Route::get('/data-pegawai', DataPegawai::class)->name('data-pegawai');
});

// GRUP PENINJAU
Route::prefix('peninjau')
    ->middleware(['auth', 'role:peninjau']) // Dilindungi middleware auth & peninjau
    ->name('peninjau.')
    ->group(function () {

        Route::get('/dashboard', function() { // Halaman dashboard sementara
            return 'Ini Dashboard Peninjau. <a href="/peninjau/manajemen-superadmin">Manajemen Superadmin</a>';
        })->name('dashboard');

        Route::get('/manajemen-superadmin', ManajemenSuperadmin::class)->name('manajemen-superadmin');
});

// GRUP KARYAWAN
Route::prefix('karyawan')
    ->middleware(['auth', 'role:karyawan']) // Dilindungi middleware auth & karyawan
    ->name('karyawan.')
    ->group(function () {
        Route::get('/dashboard', function() { // Halaman dashboard sementara
            return 'Ini Dashboard Karyawan.';
        })->name('dashboard');
    });


// --- RUTE ROOT (HALAMAN UTAMA /) ---

// Ini harus diletakkan paling bawah sebagai penangkap
Route::get('/', function () {
    if (Auth::guest()) {
        // Jika tamu, paksa ke halaman login
        return redirect()->route('login');
    }
    // Jika sudah login, paksa ke halaman pilih role
    // (PilihRole akan mengarahkan ke dashboard yang benar)
    return redirect()->route('pilih-role');
});

// HAPUS 'require __DIR__.'/auth.php';' KARENA KITA SUDAH BUAT LOGIC SENDIRI
// require __DIR__.'/auth.php';