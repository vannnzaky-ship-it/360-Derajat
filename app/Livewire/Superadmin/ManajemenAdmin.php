<?php

namespace App\Livewire\Superadmin;

use Livewire\Component;
use App\Models\User;
use App\Models\Role;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth; // <-- Baris ini membuat Auth::id() berfungsi

#[Layout('layouts.admin')]
class ManajemenAdmin extends Component
{
    use WithPagination;

    public $search = '';
    public $adminRole;

    public function mount()
    {
        $this->adminRole = Role::where('name', 'admin')->firstOrFail();
    }

    public function render()
    {
        // ==== PERBAIKAN DI SINI ====
        $users = User::with('roles')
            ->where('id', '!=', Auth::id()) // Ganti ke Auth::id()
            ->where(function($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                      ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->paginate(10);

        return view('livewire.superadmin.manajemen-admin', [
            'users' => $users
        ]);
    }

    public function toggleAdmin($userId)
    {
        $user = User::find($userId);
        
        // ==== PERBAIKAN DI SINI ====
        if ($user && $user->id !== Auth::id()) { // Ganti ke Auth::id()
            $user->roles()->toggle($this->adminRole->id);
            session()->flash('message', 'Hak akses Administrator untuk ' . $user->name . ' telah diperbarui.');
        }
    }
}

