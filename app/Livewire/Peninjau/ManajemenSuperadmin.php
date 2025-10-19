<?php

namespace App\Livewire\Peninjau;

use Livewire\Component;
use App\Models\User;
use App\Models\Role;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class ManajemenSuperadmin extends Component
{
    use WithPagination;

    public $search = '';
    public $superadminRole;

    public function mount()
    {
        // Ambil role superadmin sekali saja
        $this->superadminRole = Role::where('name', 'superadmin')->first();
    }

    public function render()
    {
        $users = User::with('roles')
            ->where('name', 'like', '%'.$this->search.'%')
            ->where('email', 'like', '%'.$this->search.'%')
            ->paginate(10);

        return view('livewire.peninjau.manajemen-superadmin', [
            'users' => $users
        ]); // Asumsi layout admin
    }

    /**
     * Menambah atau mencabut peran superadmin dari user
     */
    public function toggleSuperadmin($userId)
    {
        $user = User::find($userId);
        
        if ($user) {
            // toggle() akan attach jika belum ada, dan detach jika sudah ada.
            $user->roles()->toggle($this->superadminRole->id);
            session()->flash('message', 'Hak akses Superadmin untuk ' . $user->name . ' telah diperbarui.');
        }
    }
}