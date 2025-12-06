<?php

namespace App\Livewire\Common;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class Profil extends Component
{
    use WithFileUploads;

    // Data Akun
    public $name;
    public $email;
    public $nip = '-';
    public $jabatan = '-';
    public $role_label = '-';

    // Foto Profil
    public $photo; 
    public $existingPhoto;

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
        $this->existingPhoto = $user->profile_photo_path;

        $pegawai = DB::table('pegawai')->where('user_id', $user->id)->first();

        if ($pegawai) {
            $this->nip = $pegawai->nip ?? '-';

            $jabatanList = DB::table('jabatan')
                ->join('pegawai_jabatan', 'jabatan.id', '=', 'pegawai_jabatan.jabatan_id')
                ->where('pegawai_jabatan.pegawai_id', $pegawai->id)
                ->pluck('jabatan.nama_jabatan')
                ->toArray();
            
            if (!empty($jabatanList)) {
                $this->jabatan = implode(', ', $jabatanList);
            } else {
                $this->jabatan = '-';
            }
        }
    }

    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'image|max:2048|mimes:jpg,jpeg,png,webp',
        ]);
    }

    public function savePhoto()
    {
        $this->validate([
            'photo' => 'image|max:2048|mimes:jpg,jpeg,png,webp',
        ]);

        $user = User::find(Auth::id());

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $path = $this->photo->store('profile-photos', 'public');
        $user->update(['profile_photo_path' => $path]);

        $this->existingPhoto = $path;
        $this->photo = null;

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
        
        session()->flash('message', 'Foto profil berhasil dihapus.');
    }

    public function updatePassword()
    {
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
        // KARENA ANDA HANYA PAKAI 1 LAYOUT UNTUK SEMUA ROLE (admin.blade.php)
        // Maka kita langsung tembak saja ke layout tersebut.
        // Tidak perlu logika match/if yang mencari file layout lain.
        
        return view('livewire.common.profil')
            ->layout('layouts.admin');
    }
}