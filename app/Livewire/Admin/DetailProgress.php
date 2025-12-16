<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Siklus;
use App\Models\PenilaianAlokasi;

#[Layout('layouts.admin')]
class DetailProgress extends Component
{
    public $user, $siklus;
    public $groupedTargets = [];
    public $stats = [];

    public function mount($siklusId, $userId)
    {
        $this->user = User::with('pegawai.jabatans')->findOrFail($userId);
        $this->siklus = Siklus::with('penilaianSession')->findOrFail($siklusId);
        
        $sessionId = $this->siklus->penilaianSession->id;

        // Ambil data
        $alokasi = PenilaianAlokasi::with(['targetUser.pegawai', 'targetJabatan']) 
                    ->where('penilaian_session_id', $sessionId)
                    ->where('user_id', $userId) 
                    ->get();

        // Hitung Statistik
        $this->stats = [
            'total' => $alokasi->count(),
            'sudah' => $alokasi->where('status_nilai', 'Sudah')->count(),
            'persen' => $alokasi->count() > 0 ? round(($alokasi->where('status_nilai', 'Sudah')->count() / $alokasi->count()) * 100) : 0,
        ];

        // --- [PERBAIKAN LOGIKA DISINI] ---
        // Kita kelompokkan ulang secara manual agar Label Tampilan sesuai logika manusia
        // Data di DB -> Masuk ke Folder Mana?
        
        $rawGrouped = $alokasi->groupBy('sebagai');

        $finalGroups = [
            // Judul 'Atasan' diisi oleh data dimana User bertindak sbg 'Bawahan' (Menilai Bos)
            'Atasan'       => $rawGrouped->get('Bawahan', collect([])), 
            
            // Judul 'Bawahan' diisi oleh data dimana User bertindak sbg 'Atasan' (Menilai Staf)
            'Bawahan'      => $rawGrouped->get('Atasan', collect([])),
            
            // Rekan & Diri Sendiri tetap
            'Rekan'        => $rawGrouped->get('Rekan', collect([])),
            'Diri Sendiri' => $rawGrouped->get('Diri Sendiri', collect([])),
        ];

        // Formatting Data untuk View (Termasuk Sensor Nama)
        $this->groupedTargets = collect($finalGroups)->map(function ($items) {
            return $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    // TETAP DISENSOR SESUAI PERMINTAAN
                    'nama_sensor' => '****** (Disamarkan)', 
                    'jabatan' => $item->targetJabatan->nama_jabatan ?? '-',
                    'status' => $item->status_nilai,
                    'tanggal' => $item->updated_at ? $item->updated_at->format('d M Y H:i') : '-',
                ];
            });
        })->filter(fn($items) => $items->isNotEmpty())->toArray(); 
        // Filter agar kategori kosong tidak muncul
    }

    public function render()
    {
        return view('livewire.admin.detail-progress');
    }
}