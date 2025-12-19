<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail; // Tambahan Wajib
use App\Mail\ResetPasswordOtpMail;   // Tambahan Wajib

class VerifyOtp extends Component
{
    public $otp;
    public $password;
    public $password_confirmation;
    public $email;

    public function mount()
    {
        // Ambil email dari session halaman sebelumnya
        $this->email = session('reset_email');

        // Kalau user nyasar kesini tanpa input email dulu, tendang balik
        if (!$this->email) {
            return redirect()->route('password.request');
        }
    }

    // --- FITUR BARU: KIRIM ULANG OTP ---
    public function resendOtp()
    {
        // 1. Cek User berdasarkan email di session
        $user = User::where('email', $this->email)->first();

        if (!$user) {
            return redirect()->route('password.request');
        }

        // 2. Generate OTP Baru 6 Digit
        $otp = rand(100000, 999999);

        // 3. Update Database (Perpanjang expired 15 menit lagi)
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(15),
        ]);

        // 4. Kirim Email Lagi
        try {
            Mail::to($this->email)->send(new ResetPasswordOtpMail($otp));
            
            // Kirim pesan sukses (akan muncul di alert hijau di view)
            session()->flash('success', 'Kode OTP baru berhasil dikirim ke email Anda.');
            
        } catch (\Exception $e) {
            // Jika gagal (internet mati / SMTP error)
            $this->addError('otp', 'Gagal mengirim email. Silakan coba sesaat lagi.');
        }
    }
    // ------------------------------------

    public function resetPassword()
    {
        $this->validate([
            'otp' => 'required|numeric|digits:6',
            'password' => 'required|min:8|confirmed',
        ], [
            'otp.digits' => 'Kode OTP harus 6 angka.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $user = User::where('email', $this->email)->first();

        // Cek OTP Benar atau Salah
        if (!$user || $user->otp_code !== $this->otp) {
            $this->addError('otp', 'Kode OTP salah!');
            return;
        }

        // Cek Kadaluarsa
        if (Carbon::now()->gt($user->otp_expires_at)) {
            $this->addError('otp', 'Kode OTP sudah kadaluarsa. Silakan minta ulang.');
            return;
        }

        // SUKSES: Update Password & Hapus OTP
        $user->update([
            'password' => Hash::make($this->password),
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        // Bersihkan session
        session()->forget('reset_email');
        session()->flash('success', 'Password berhasil diubah! Silakan login.');

        return redirect()->route('login');
    }

    public function render()
    {
        // Pastikan path layout sesuai dengan file yang Anda buat
        // Jika Anda menggunakan layout default Livewire 3, gunakan 'components.layouts.app'
        // Jika Anda membuat folder sendiri 'layouts/app.blade.php', gunakan 'layouts.app'
        return view('livewire.auth.verify-otp')->layout('layouts.app');
    }
}