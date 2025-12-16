<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Siklus;
use App\Models\Pegawai;
use App\Models\PenilaianAlokasi;
use App\Models\PenilaianSession;
use Carbon\Carbon; // Import Carbon untuk Cek Waktu

#[Layout('layouts.admin')]
class ProgressPenilaian extends Component
{
    public $search = '';
    public $activeSession = null;
    public $activeSiklus = null;
    public $dataProgress = [];
    public $isExpired = false; // Variabel baru untuk status waktu

    public function mount()
    {
        // 1. Cari Siklus yang AKTIF dulu
        $siklusAktif = Siklus::where('status', 'Aktif')->first();

        if ($siklusAktif) {
            // 2. Cari Sesi Penilaian TERAKHIR di siklus tersebut
            // Kita hapus "where status open" agar sesi yang barusan expired tetap muncul
            $this->activeSession = PenilaianSession::with('siklus')
                                    ->where('siklus_id', $siklusAktif->id)
                                    ->latest() // Ambil yang paling baru dibuat
                                    ->first();
        }

        // 3. Jika Sesi Ditemukan, Cek Waktunya
        if ($this->activeSession) {
            $this->activeSiklus = $this->activeSession->siklus;
            
            // Cek apakah waktu sekarang SUDAH MELEWATI batas waktu?
            if (Carbon::now()->greaterThan($this->activeSession->batas_waktu)) {
                $this->isExpired = true; // Tandai sudah expired
            }

            $this->loadData();
        }
    }

    public function loadData()
    {
        if (!$this->activeSession || $this->isExpired) return;

        $sessionId = $this->activeSession->id;

        // Ambil Pegawai
        $pegawais = Pegawai::with(['user', 'jabatans'])
            ->whereHas('user', function($q) {
                $q->where('name', 'like', '%'.$this->search.'%');
            })
            ->whereHas('jabatans')
            ->get();

        $this->dataProgress = [];

        foreach ($pegawais as $peg) {
            if(!$peg->user) continue;

            $totalTarget = PenilaianAlokasi::where('penilaian_session_id', $sessionId)
                            ->where('user_id', $peg->user->id)
                            ->count();

            $sudahDinilai = PenilaianAlokasi::where('penilaian_session_id', $sessionId)
                            ->where('user_id', $peg->user->id)
                            ->where('status_nilai', 'Sudah')
                            ->count();

            $persen = $totalTarget > 0 ? round(($sudahDinilai / $totalTarget) * 100) : 0;

            // Logika Badge Status Pegawai
            $badge = 'bg-secondary';
            if ($persen == 100) $badge = 'bg-success';
            elseif ($persen > 0) $badge = 'bg-warning text-dark';
            elseif ($totalTarget == 0) $badge = 'bg-light text-secondary border';
            else $badge = 'bg-danger';

            $this->dataProgress[] = [
                'user_id' => $peg->user->id,
                'nip' => $peg->nip,
                'nama' => $peg->user->name,
                'jabatan' => $peg->jabatans->pluck('nama_jabatan')->implode(', ') ?: '-',
                'total' => $totalTarget,
                'sudah' => $sudahDinilai,
                'persen' => $persen,
                'badge' => $badge
            ];
        }

        // Sort
        $this->dataProgress = collect($this->dataProgress)->sortBy('persen')->values()->all();
    }

    public function updatedSearch()
    {
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.admin.progress-penilaian');
    }
}