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

        // Ambil data orang-orang yang harus dinilai oleh User ini
        $alokasi = PenilaianAlokasi::with(['targetUser.pegawai', 'targetJabatan']) // Load target data
                    ->where('penilaian_session_id', $sessionId)
                    ->where('user_id', $userId) // User ini sebagai PENILAI
                    ->get();

        // Hitung Statistik
        $this->stats = [
            'total' => $alokasi->count(),
            'sudah' => $alokasi->where('status_nilai', 'Sudah')->count(),
            'persen' => $alokasi->count() > 0 ? round(($alokasi->where('status_nilai', 'Sudah')->count() / $alokasi->count()) * 100) : 0,
        ];

        // Grouping berdasarkan 'sebagai' (Atasan, Bawahan, Rekan, Diri Sendiri)
        // DAN SENSOR NAMA TARGET
        $this->groupedTargets = $alokasi->groupBy('sebagai')->map(function ($items) {
            return $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    // SENSOR NAMA: Ganti dengan ******
                    'nama_sensor' => '****** (Disamarkan)', 
                    'jabatan' => $item->targetJabatan->nama_jabatan ?? '-',
                    'status' => $item->status_nilai,
                    'tanggal' => $item->updated_at ? $item->updated_at->format('d M Y H:i') : '-',
                ];
            });
        });
    }

    public function render()
    {
        return view('livewire.admin.detail-progress');
    }
}