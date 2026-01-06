<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Siklus;
use App\Models\Pegawai;
use App\Models\PenilaianAlokasi;
use App\Models\PenilaianSession;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan ini ada

#[Layout('layouts.admin')]
class ProgressPenilaian extends Component
{
    public $search = '';
    public $activeSession = null;
    public $activeSiklus = null;
    public $dataProgress = [];
    public $isExpired = false;

    public function mount()
    {
        $siklusAktif = Siklus::where('status', 'Aktif')->first();

        if ($siklusAktif) {
            $this->activeSession = PenilaianSession::with('siklus')
                ->where('siklus_id', $siklusAktif->id)
                ->latest()
                ->first();
        }

        if ($this->activeSession) {
            $this->activeSiklus = $this->activeSession->siklus;
            if (Carbon::now()->greaterThan($this->activeSession->batas_waktu)) {
                $this->isExpired = true;
            }
            $this->loadData();
        }
    }

    public function loadData()
    {
        if (!$this->activeSession) return;

        $sessionId = $this->activeSession->id;

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

        $this->dataProgress = collect($this->dataProgress)->sortBy('persen')->values()->all();
    }

    public function updatedSearch()
    {
        $this->loadData();
    }

    // --- FUNGSI DOWNLOAD PDF ---
    public function downloadPdf()
    {
        $this->loadData(); // Load data terbaru

        if (empty($this->dataProgress)) {
            session()->flash('error', 'Tidak ada data untuk diunduh.');
            return;
        }

        // Load view dari folder: resources/views/livewire/admin/pdf-progress.blade.php
        $pdf = Pdf::loadView('livewire.admin.pdf-progress', [
            'data' => $this->dataProgress,
            'siklus' => $this->activeSiklus
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'Laporan-Progress-' . date('d-m-Y') . '.pdf');
    }

    public function render()
    {
        return view('livewire.admin.progress-penilaian');
    }
}