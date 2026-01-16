<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Login360;
use App\Livewire\PilihRole;
// Import Middleware PreventBackHistory
use App\Http\Middleware\PreventBackHistory as MiddlewarePreventBackHistory; 

use App\Livewire\Superadmin\DataPegawai as SuperadminDataPegawai;
use App\Livewire\Superadmin\ManajemenAdmin;
use App\Livewire\Peninjau\Dashboard as PeninjauDashboard;
use App\Livewire\Admin\SiklusSemester;
use App\Livewire\Admin\pertanyaanCrud;
use App\Livewire\Admin\ManajemenSkema;
use App\Livewire\Karyawan\Dashboard as KaryawanDashboard;
use App\Livewire\Karyawan\Penilaian as KaryawanPenilaian;
use App\Livewire\Karyawan\Raport as KaryawanRaport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Livewire\Admin\KompetensiCrud;
use App\Livewire\Common\Profil;
use App\Livewire\Superadmin\ManajemenJabatan;
use App\Livewire\Peninjau\LaporanHasil;
use App\Livewire\Peninjau\RankingPeninjau;
use App\Livewire\Peninjau\DetailPeninjau;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\VerifyOtp;


/*
|--------------------------------------------------------------------------
| Rute Aplikasi 360 Degree (Struktur Baru)
|--------------------------------------------------------------------------
*/
Route::get('/info', function() { phpinfo(); });
Route::get('/lupa-password', ForgotPassword::class)->name('password.request');
Route::get('/verifikasi-otp', VerifyOtp::class)->name('password.verify');
// --- RUTE AUTENTIKASI KUSTOM ---
Route::get('/login', Login360::class)->name('login')->middleware('guest');

Route::get('/pilih-role', PilihRole::class)
    ->name('pilih-role')
    ->middleware(['auth', MiddlewarePreventBackHistory::class]);

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate(); // Invalidate session biar bersih
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');


// --- RUTE BERDASARKAN PERAN (ROLE) ---

// GRUP SUPER ADMIN
Route::prefix('superadmin')
    ->middleware(['auth', 'role:superadmin', MiddlewarePreventBackHistory::class]) 
    ->name('superadmin.')
    ->group(function () {
        Route::get('/dashboard', \App\Livewire\Superadmin\Dashboard::class)->name('dashboard');
        Route::get('/manajemen-admin', ManajemenAdmin::class)->name('manajemen-admin');
        Route::get('/data-pegawai', SuperadminDataPegawai::class)->name('data-pegawai');
    });

// GRUP ADMINISTRATOR
Route::prefix('admin')
    // TAMBAHKAN MiddlewarePreventBackHistory DISINI
    ->middleware(['auth', 'role:admin', MiddlewarePreventBackHistory::class])
    ->name('admin.')
    ->group(function () {
        
        // Dashboard Admin
        Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard'); 

        Route::get('/struktur-jabatan', ManajemenJabatan::class)->name('jabatan');
        Route::get('/data-pegawai', SuperadminDataPegawai::class)->name('data-pegawai');
        Route::get('/siklus-semester', SiklusSemester::class)->name('siklus-semester');
        Route::get('/skema-penilaian', ManajemenSkema::class)->name('skema-penilaian');
        Route::get('/kompetensi', KompetensiCrud::class)->name('kompetensi');
        Route::get('/pertanyaan', PertanyaanCrud::class)->name('pertanyaan');
        Route::get('/profil', Profil::class)->name('profil');
        
        Route::get('/random-penilai', \App\Livewire\Admin\RandomPenilai::class)->name('random-penilai');
        Route::get('/siklus/{siklusId}/rekap', \App\Livewire\Admin\RekapSiklus::class)->name('rekap-siklus');

        // Halaman Detail Nilai Per Orang
        Route::get('/siklus/{siklusId}/pegawai/{userId}', \App\Livewire\Admin\DetailNilai::class)->name('detail-nilai');
        
        // Halaman List Progress
        Route::get('/proses-penilai', \App\Livewire\Admin\ProgressPenilaian::class)->name('progress-penilaian');

        // Halaman Detail Progress
        Route::get('/proses-penilai/{siklusId}/detail/{userId}', \App\Livewire\Admin\DetailProgress::class)->name('detail-progress');
    });

// GRUP PENINJAU
Route::prefix('peninjau')
    // TAMBAHKAN MiddlewarePreventBackHistory DISINI
    ->middleware(['auth', 'role:peninjau', MiddlewarePreventBackHistory::class])
    ->name('peninjau.')
    ->group(function () {
        // Route::get('/dashboard', PeninjauDashboard::class)->name('dashboard');
        Route::get('/profil', Profil::class)->name('profil');
        // 1. Halaman List Siklus
        Route::get('/laporan', LaporanHasil::class)->name('laporan');
        
        // 2. Halaman Ranking
        Route::get('/laporan/{siklusId}/ranking', RankingPeninjau::class)->name('laporan.ranking');
        
        // 3. Halaman Detail Raport Pegawai
        Route::get('/laporan/{siklusId}/pegawai/{userId}', DetailPeninjau::class)->name('laporan.detail');
    });


// GRUP KARYAWAN
Route::prefix('karyawan')
    // TAMBAHKAN MiddlewarePreventBackHistory DISINI
    ->middleware(['auth', 'role:karyawan', MiddlewarePreventBackHistory::class])
    ->name('karyawan.')
    ->group(function () {
        Route::get('/dashboard', KaryawanDashboard::class)->name('dashboard');
        Route::get('/penilaian', KaryawanPenilaian::class)->name('penilaian');
        Route::get('/raport', KaryawanRaport::class)->name('raport');
        Route::get('/profil', Profil::class)->name('profil');
        
        // Route Form Isi
        Route::get('/penilaian/{id}', \App\Livewire\Karyawan\IsiPenilaian::class)->name('isi-penilaian');
    });


// --- RUTE ROOT (HALAMAN UTAMA /) ---
Route::get('/', function () {
    if (Auth::guest()) { return redirect()->route('login'); }
    
    // --- UBAH DISINI: LOGIKA AUTO LOGOUT SAAT BACK ---
    // Jika user kembali ke halaman ROOT '/' tapi di session masih ada 'selected_role',
    // Artinya user menekan tombol BACK dari Dashboard.
    // Maka: Force Logout & Redirect Login.
    if (Session::has('selected_role')) {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        
        return redirect()->route('login');
    }
    // --------------------------------------------------

    $user = Auth::user();
    /** @var \App\Models\User $user */
    $roles = $user->roles;
    $nonKaryawanRoles = $roles->where('name', '!=', 'karyawan');
    
    if ($nonKaryawanRoles->count() > 0 && $roles->count() > 1) { 
        return redirect()->route('pilih-role'); 
    } elseif ($roles->isNotEmpty()) {
        $roleName = $roles->first()->name;
        // Simpan session otomatis jika cuma 1 role
        Session::put('selected_role', $roleName); 

        $redirectPath = match ($roleName) {
            'superadmin' => '/superadmin/dashboard', 
            'admin'      => '/admin/dashboard',
            'peninjau'   => '/peninjau/dashboard', 
            'karyawan'   => '/karyawan/dashboard',
            default      => '/login',
        };
        return redirect($redirectPath);
    } else {
        Auth::logout(); 
        return redirect('/login')->withErrors('Akun Anda tidak memiliki peran.');
    }
})->middleware(['auth', MiddlewarePreventBackHistory::class])->name('home');