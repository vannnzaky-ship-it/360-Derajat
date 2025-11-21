<?php

namespace App\Livewire\Common;

use Livewire\Component;
use Livewire\WithFileUploads; // <--- Import Ini
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; // <--- Import Storage
use Illuminate\Support\Facades\Session;
use App\Models\User;

#[Layout('layouts.admin')]
class Profil extends Component
{
    use WithFileUploads; // <--- Gunakan Trait Ini

    // Data Akun
    public $name;
    public $email;
    public $nip = '-';
    public $jabatan = '-';
    public $role_label = '-';

    // Foto Profil
    public $photo; // Menampung file upload sementara
    public $existingPhoto; // Menampung path foto dari DB

    // Password
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    public function mount()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->hasRole('superadmin')) {
            abort(403, 'Superadmin tidak dapat mengubah profil di sini.');
        }

        $this->name = $user->name;
        $this->email = $user->email;
        $this->role_label = $user->roles->first()->label ?? $user->roles->first()->name;
        $this->existingPhoto = $user->profile_photo_path; // Ambil foto dari DB

        $pegawai = DB::table('pegawai')->where('user_id', $user->id)->first();

        if ($pegawai) {
            $this->nip = $pegawai->nip ?? '-';
            $jabatanData = DB::table('jabatan')
                ->join('pegawai_jabatan', 'jabatan.id', '=', 'pegawai_jabatan.jabatan_id')
                ->where('pegawai_jabatan.pegawai_id', $pegawai->id)
                ->select('jabatan.nama_jabatan')
                ->first();
            
            if ($jabatanData) {
                $this->jabatan = $jabatanData->nama_jabatan;
            }
        }
    }

    // Fungsi validasi real-time saat user memilih file
    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'image|max:2048|mimes:jpg,jpeg,png,webp', // Max 2MB
        ]);
    }

    public function savePhoto()
    {
        $this->validate([
            'photo' => 'image|max:2048|mimes:jpg,jpeg,png,webp',
        ]);

        $user = User::find(Auth::id());

        // 1. Hapus foto lama jika ada (Cleanup)
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // 2. Simpan foto baru
        // Foto akan disimpan di storage/app/public/profile-photos
        $path = $this->photo->store('profile-photos', 'public');

        // 3. Update Database
        $user->update(['profile_photo_path' => $path]);

        // 4. Refresh tampilan
        $this->existingPhoto = $path;
        $this->photo = null; // Reset input file

        session()->flash('message', 'Foto profil berhasil diperbarui!');
    }

    public function deletePhoto()
    {
        $user = User::find(Auth::id());

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->update(['profile_photo_path' => null]);
        }

        $this->existingPhoto = null;
        $this->photo = null;
        
        session()->flash('message', 'Foto profil berhasil dihapus. Kembali ke avatar default.');
    }

    public function updatePassword()
    {
        // ... (Logika password tetap sama seperti sebelumnya) ...
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed|different:current_password',
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'new_password.different' => 'Password baru harus berbeda dengan password lama.',
        ]);

        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Password lama salah.');
            return;
        }

        User::where('id', $user->id)->update([
            'password' => Hash::make($this->new_password)
        ]);

        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Password berhasil diubah. Silakan login kembali.');
    }

    public function render()
    {
        return view('livewire.common.profil');
    }
}