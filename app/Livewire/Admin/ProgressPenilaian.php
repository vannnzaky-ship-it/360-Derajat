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

#[Layout('layouts.admin', ['title' => 'Proses Penilai'])]
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
        // 1. Load Data Terbaru
        $this->loadData(); 

        if (empty($this->dataProgress)) {
            session()->flash('error', 'Tidak ada data untuk diunduh.');
            return;
        }

        // 2. Siapkan Logo Base64
        $pathLogo = public_path('images/logo-polkam.png');
        $logoBase64 = null;
        if (file_exists($pathLogo)) {
            try {
                $type = pathinfo($pathLogo, PATHINFO_EXTENSION);
                $data = file_get_contents($pathLogo);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            } catch (\Exception $e) {}
        }

        // 3. Format Nama File: Laporan Progress - Tanggal - Tahun Semester
        $tanggalStr = date('d-m-Y');
        $siklusStr = $this->activeSiklus 
            ? $this->activeSiklus->tahun_ajaran . '-' . $this->activeSiklus->semester 
            : 'Siklus-NA';
        
        // Bersihkan nama file dari spasi atau karakter miring agar aman didownload
        $siklusClean = str_replace([' ', '/'], '-', $siklusStr); 
        $filename = "Laporan-Progress-{$tanggalStr}-{$siklusClean}.pdf";

        // 4. Data untuk View
        $pdfData = [
            'data' => $this->dataProgress,
            'siklus' => $this->activeSiklus,
            'logoBase64' => $logoBase64,
            // Tambahkan Waktu Cetak (Format Indonesia)
            'waktu_cetak' => now()->translatedFormat('d F Y, H:i') . ' WIB' 
        ];

        $pdf = Pdf::loadView('livewire.admin.pdf-progress', $pdfData)->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    public function render()
    {
        return view('livewire.admin.progress-penilaian');
    }
}