<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Livewire\Login360;
use App\Livewire\PilihRole;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\VerifyOtp;
// Import Middleware dengan Alias agar lebih pendek
use App\Http\Middleware\PreventBackHistory; 

// --- 1. RUTE PUBLIC & AUTH ---
// Halaman Login & Lupa Password bisa diakses siapa saja (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', Login360::class)->name('login');
    Route::get('/lupa-password', ForgotPassword::class)->name('password.request');
    Route::get('/verifikasi-otp', VerifyOtp::class)->name('password.verify');
});

// Logout Route
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');


// --- 2. RUTE TERPROTEKSI (Harus Login) ---
Route::middleware(['auth', PreventBackHistory::class])->group(function () {

    // Route Root '/' sekarang ditangani Controller (Rapi & Aman)
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/pilih-role', PilihRole::class)->name('pilih-role');

    // --- GRUP SUPER ADMIN ---
    Route::prefix('superadmin')
        ->middleware('role:superadmin') 
        ->name('superadmin.')
        ->group(function () {
            Route::get('/dashboard', \App\Livewire\Superadmin\Dashboard::class)->name('dashboard');
            Route::get('/manajemen-admin', \App\Livewire\Superadmin\ManajemenAdmin::class)->name('manajemen-admin');
            Route::get('/data-pegawai', \App\Livewire\Superadmin\DataPegawai::class)->name('data-pegawai');
        });

    // --- GRUP ADMINISTRATOR ---
    Route::prefix('admin')
        ->middleware('role:admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard'); 
            Route::get('/struktur-jabatan', \App\Livewire\Superadmin\ManajemenJabatan::class)->name('jabatan');
            Route::get('/data-pegawai', \App\Livewire\Superadmin\DataPegawai::class)->name('data-pegawai');
            Route::get('/siklus-semester', \App\Livewire\Admin\SiklusSemester::class)->name('siklus-semester');
            Route::get('/skema-penilaian', \App\Livewire\Admin\ManajemenSkema::class)->name('skema-penilaian');
            Route::get('/kompetensi', \App\Livewire\Admin\KompetensiCrud::class)->name('kompetensi');
            Route::get('/pertanyaan', \App\Livewire\Admin\PertanyaanCrud::class)->name('pertanyaan');
            Route::get('/profil', \App\Livewire\Common\Profil::class)->name('profil');
            Route::get('/random-penilai', \App\Livewire\Admin\RandomPenilai::class)->name('random-penilai');
            Route::get('/siklus/{siklusId}/rekap', \App\Livewire\Admin\RekapSiklus::class)->name('rekap-siklus');
            Route::get('/siklus/{siklusId}/pegawai/{userId}', \App\Livewire\Admin\DetailNilai::class)->name('detail-nilai');
            Route::get('/proses-penilai', \App\Livewire\Admin\ProgressPenilaian::class)->name('progress-penilaian');
            Route::get('/proses-penilai/{siklusId}/detail/{userId}', \App\Livewire\Admin\DetailProgress::class)->name('detail-progress');
        });

    // --- GRUP PENINJAU ---
    Route::prefix('peninjau')
        ->middleware('role:peninjau')
        ->name('peninjau.')
        ->group(function () {
            Route::get('/profil', \App\Livewire\Common\Profil::class)->name('profil');
            Route::get('/laporan', \App\Livewire\Peninjau\LaporanHasil::class)->name('laporan');
            Route::get('/laporan/{siklusId}/ranking', \App\Livewire\Peninjau\RankingPeninjau::class)->name('laporan.ranking');
            Route::get('/laporan/{siklusId}/pegawai/{userId}', \App\Livewire\Peninjau\DetailPeninjau::class)->name('laporan.detail');
        });

    // --- GRUP KARYAWAN ---
    Route::prefix('karyawan')
        ->middleware('role:karyawan')
        ->name('karyawan.')
        ->group(function () {
            Route::get('/dashboard', \App\Livewire\Karyawan\Dashboard::class)->name('dashboard');
            Route::get('/penilaian', \App\Livewire\Karyawan\Penilaian::class)->name('penilaian');
            Route::get('/raport', \App\Livewire\Karyawan\Raport::class)->name('raport');
            Route::get('/profil', \App\Livewire\Common\Profil::class)->name('profil');
            Route::get('/penilaian/{id}', \App\Livewire\Karyawan\IsiPenilaian::class)->name('isi-penilaian');
        });
});

// --- RUTE DARURAT (HAPUS SETELAH WEBSITE JALAN) ---
// Gunakan ini untuk membersihkan cache hosting tanpa SSH
Route::get('/fix-server', function() {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    return 'Cache server berhasil dibersihkan! Silahkan coba login kembali.';
});