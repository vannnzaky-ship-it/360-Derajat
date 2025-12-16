<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Login360;
use App\Livewire\PilihRole;
use App\Livewire\Superadmin\DataPegawai as SuperadminDataPegawai;
use App\Livewire\Superadmin\ManajemenAdmin;
use App\Livewire\Peninjau\Dashboard as PeninjauDashboard; // Pastikan ini ada
use App\Livewire\Admin\SiklusSemester; // Import SiklusSemester
use App\Livewire\Admin\pertanyaanCrud;
use App\Livewire\Admin\ManajemenSkema;
// use App\Livewire\Admin\Dashboard as AdminDashboard; // Import Dashboard Admin (jika ada)
use App\Livewire\Karyawan\Dashboard as KaryawanDashboard;
use App\Livewire\Karyawan\Penilaian as KaryawanPenilaian;
use App\Livewire\Karyawan\Raport as KaryawanRaport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Livewire\Admin\KompetensiCrud; // <-- Tambahkan ini
use App\Livewire\Common\Profil;
use App\Livewire\Superadmin\ManajemenJabatan;

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

// GRUP ADMINISTRATOR (Blok Pertama Anda)
// Route::prefix('admin')
//     ->middleware(['auth', 'role:admin'])
//     ->name('admin.')
//     ->group(function () {
//         Route::redirect('/dashboard', '/admin/data-pegawai')->name('dashboard'); // Redirect ini tetap ada
//         Route::get('/data-pegawai', SuperadminDataPegawai::class)->name('data-pegawai');
//         // Tambahkan siklus-semester dan pertanyaan di sini
//         Route::get('/siklus-semester', SiklusSemester::class)->name('siklus-semester');
//         Route::get('/pertanyaan', \App\Livewire\Admin\Pertanyaan::class)->name('pertanyaan'); // Gunakan class yang benar
// });

// GRUP ADMINISTRATOR (Blok Kedua Anda - Duplikasi)
Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.')
    ->group(function () {
        // HAPUS REDIRECT INI:
        // Route::redirect('/dashboard', '/admin/siklus-semester')->name('dashboard'); 

        // ==== TAMBAHKAN RUTE INI UNTUK KOMPONEN DASHBOARD ANDA ====
        Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard'); 
        // ==========================================================

        // Rute lain tetap ada
        Route::get('/struktur-jabatan', ManajemenJabatan::class)->name('jabatan');
         Route::get('/data-pegawai', SuperadminDataPegawai::class)->name('data-pegawai');
        Route::get('/siklus-semester', SiklusSemester::class)->name('siklus-semester');
        Route::get('/skema-penilaian', ManajemenSkema::class)->name('skema-penilaian');
        Route::get('/kompetensi', KompetensiCrud::class)->name('kompetensi'); // Nama rute 'kompetensi
        Route::get('/pertanyaan', PertanyaanCrud::class)->name('pertanyaan');
        Route::get('/profil', Profil::class)->name('profil');
        // ... di dalam Route group 'admin' ...
        Route::get('/random-penilai', \App\Livewire\Admin\RandomPenilai::class)->name('random-penilai');
        Route::get('/siklus/{siklusId}/rekap', \App\Livewire\Admin\RekapSiklus::class)->name('rekap-siklus');

// 2. Halaman Detail Nilai Per Orang (Tampilan Raport untuk Admin)
        Route::get('/siklus/{siklusId}/pegawai/{userId}', \App\Livewire\Admin\DetailNilai::class)->name('detail-nilai');
        // Halaman List Progress (Daftar Pegawai)
        // Halaman Utama (List Progress) - Tidak butuh ID di URL lagi
Route::get('/proses-penilai', \App\Livewire\Admin\ProgressPenilaian::class)->name('progress-penilaian');

// Halaman Detail (Tetap butuh ID agar tau siapa yang dilihat)
Route::get('/proses-penilai/{siklusId}/detail/{userId}', \App\Livewire\Admin\DetailProgress::class)->name('detail-progress');
        
});

// GRUP PENINJAU (Ditambahkan kembali)
Route::prefix('peninjau')
    ->middleware(['auth', 'role:peninjau'])
    ->name('peninjau.')
    ->group(function () {
        Route::get('/dashboard', PeninjauDashboard::class)->name('dashboard');
        Route::get('/profil', Profil::class)->name('profil');
});


// GRUP KARYAWAN
Route::prefix('karyawan')
    ->middleware(['auth', 'role:karyawan'])
    ->name('karyawan.')
    ->group(function () {
        Route::get('/dashboard', KaryawanDashboard::class)->name('dashboard');
        Route::get('/penilaian', KaryawanPenilaian::class)->name('penilaian');
        Route::get('/raport', KaryawanRaport::class)->name('raport');
        Route::get('/profil', Profil::class)->name('profil');
        // Route List Penilaian
    
    // Route Form Isi (Parameter ID Alokasi)
    Route::get('/penilaian/{id}', \App\Livewire\Karyawan\IsiPenilaian::class)->name('isi-penilaian');
    });


// --- RUTE ROOT (HALAMAN UTAMA /) ---
Route::get('/', function () {
    if (Auth::guest()) { return redirect()->route('login'); }
    $user = Auth::user();
    /** @var \App\Models\User $user */
    $roles = $user->roles;
    $nonKaryawanRoles = $roles->where('name', '!=', 'karyawan');
    if ($nonKaryawanRoles->count() > 0 && $roles->count() > 1) { return redirect()->route('pilih-role'); }
    elseif ($roles->isNotEmpty()) {
        $roleName = $roles->first()->name;
        $redirectPath = match ($roleName) {
            'superadmin' => '/superadmin/dashboard', 'admin' => '/admin/dashboard', // Akan mengikuti redirect pertama
            'peninjau' => '/peninjau/dashboard', 'karyawan' => '/karyawan/dashboard',
            default => '/login',
        };
        return redirect($redirectPath);
    } else {
        Auth::logout(); return redirect('/login')->withErrors('Akun Anda tidak memiliki peran.');
    }
})->middleware('auth')->name('home');