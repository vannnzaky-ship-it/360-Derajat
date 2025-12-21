<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordOtpMail;
use Carbon\Carbon;

class ForgotPassword extends Component
{
    public $email;

    public function sendOtp()
    {
        // 1. Validasi Email
        $this->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Email tidak ditemukan di database kami.',
        ]);

        // 2. Generate OTP
        $otp = rand(100000, 999999);

        // 3. Simpan OTP ke User
        $user = User::where('email', $this->email)->first();
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(15),
        ]);

        // 4. Kirim Email (VERSI DEBUG: TANPA TRY-CATCH)
        // Biarkan baris ini sendirian agar jika error, layar akan menampilkan pesan error asli
        Mail::to($this->email)->send(new ResetPasswordOtpMail($otp));

        // 5. Simpan email di session
        session(['reset_email' => $this->email]);

        // 6. Pindah ke halaman Verifikasi
        return redirect()->route('password.verify');
    }

    public function render()
    {
        // Pastikan layout sesuai dengan project Anda
        return view('livewire.auth.forgot-password')->layout('layouts.app');
    }
}