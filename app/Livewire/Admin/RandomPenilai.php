<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Siklus;
use App\Models\Pegawai;
use App\Models\Kompetensi; // Import Model
use App\Models\Pertanyaan; // Import Model
use App\Models\SkemaPenilaian; // Import Model
use App\Models\PenilaianSession;
use App\Models\PenilaianAlokasi;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin', ['title' => 'Random Penilai'])]
class RandomPenilai extends Component
{
    use WithPagination;

    public $siklus_id;
    
    // Form Inputs
    public $batas_waktu;
    public $limit_rekan = 5;
    public $pilihan_kategori = ['Atasan', 'Bawahan', 'Rekan', 'Diri Sendiri']; 

    // Status State
    public $isSessionExists = false; 
    public $existingSession = null;  
    public $isExpired = false;       
    public $isProcessing = false;

    // [BARU] State untuk Validasi Prasyarat
    public $statusCheck = [
        'kompetensi' => false,
        'pertanyaan' => false,
        'skema' => false
    ];
    public $isReadyToGenerate = false; // Master switch agar tombol aktif/mati

    public $selectedHistory = null;

    protected $rules = [
        'siklus_id' => 'required|exists:siklus,id',
        'batas_waktu' => 'required|date|after_or_equal:today',
        'limit_rekan' => 'required|integer|min:1|max:50',
        'pilihan_kategori' => 'required|array|min:1',
    ];

    public function mount()
    {
        $freshSiklus = Siklus::where('status', 'Aktif')
            ->whereDoesntHave('penilaianSession')
            ->first();

        if ($freshSiklus) {
            $this->siklus_id = $freshSiklus->id;
        } else {
            $anySiklus = Siklus::where('status', 'Aktif')->first();
            if($anySiklus) $this->siklus_id = $anySiklus->id;
        }

        // Cek status sesi & Cek prasyarat data
        $this->checkSessionStatus();
        $this->checkPrerequisites(); 
    }

    public function updatedSiklusId()
    {
        $this->checkSessionStatus();
        $this->checkPrerequisites(); // Cek ulang saat ganti siklus (karena skema nempel di siklus)
    }

    // [FUNGSI BARU] Cek Kelengkapan Data
    public function checkPrerequisites()
    {
        // 1. Cek Kompetensi (Harus ada yang aktif)
        $this->statusCheck['kompetensi'] = Kompetensi::where('status', 'Aktif')->exists();

        // 2. Cek Pertanyaan (Harus ada yang aktif)
        $this->statusCheck['pertanyaan'] = Pertanyaan::where('status', 'Aktif')->exists();

        // 3. Cek Skema (Harus ada skema untuk siklus yang dipilih ini)
        if ($this->siklus_id) {
            $this->statusCheck['skema'] = SkemaPenilaian::where('siklus_id', $this->siklus_id)->exists();
        } else {
            $this->statusCheck['skema'] = false;
        }

        // Tombol Ready jika: Session belum ada DAN semua checklist TRUE
        $this->isReadyToGenerate = !$this->isSessionExists 
                                   && $this->statusCheck['kompetensi'] 
                                   && $this->statusCheck['pertanyaan'] 
                                   && $this->statusCheck['skema'];
    }

    public function checkSessionStatus()
    {
        $this->existingSession = PenilaianSession::where('siklus_id', $this->siklus_id)->first();
        
        if ($this->existingSession) {
            $this->isSessionExists = true;
            $this->isExpired = now() > $this->existingSession->batas_waktu;
            $this->batas_waktu = \Carbon\Carbon::parse($this->existingSession->batas_waktu)->format('Y-m-d\TH:i');
        } else {
            $this->isSessionExists = false;
            $this->isExpired = false;
            $this->batas_waktu = null;
            $this->limit_rekan = 5;
            $this->pilihan_kategori = ['Atasan', 'Bawahan', 'Rekan', 'Diri Sendiri'];
        }
    }

    public function showDetail($sessionId)
    {
        $this->selectedHistory = null;
        $this->selectedHistory = PenilaianSession::with([
            'siklus', 
            'alokasis.user', 
            'alokasis.targetUser', 
            'alokasis.targetJabatan' 
        ])->find($sessionId);
    }

    public function generate()
    {
        // [VALIDASI SERVER SIDE] Cegah user bypass via inspect element
        $this->checkPrerequisites();
        if (!$this->isReadyToGenerate) {
            session()->flash('error', 'Data master (Kompetensi/Pertanyaan/Skema) belum lengkap! Tidak dapat memproses.');
            return;
        }

        $this->validate();
        
        if ($this->isSessionExists) {
             session()->flash('error', 'Siklus ini sudah memiliki data penilaian.'); 
             return;
        }

        $this->isProcessing = true;
        DB::beginTransaction();
        try {
            $session = PenilaianSession::create([
                'siklus_id' => $this->siklus_id,
                'tanggal_mulai' => now(),
                'batas_waktu' => $this->batas_waktu,
                'limit_rekan' => $this->limit_rekan,
                'status' => 'Open'
            ]);

            $allTargets = Pegawai::with(['user', 'jabatans'])->get();
            $dataAlokasi = [];

            foreach ($allTargets as $target) {
                if (!$target->user) continue;

                foreach ($target->jabatans as $jabatanTarget) {
                    if (in_array('Diri Sendiri', $this->pilihan_kategori)) {
                        $this->tambahAlokasi($dataAlokasi, $session->id, $target->user_id, $jabatanTarget->id, $target->user_id, $jabatanTarget->id, 'Diri Sendiri');
                    }

                    if (in_array('Atasan', $this->pilihan_kategori) && $jabatanTarget->parent_id) {
                        $atasanList = Pegawai::whereHas('jabatans', function($q) use ($jabatanTarget) {
                            $q->where('jabatan.id', $jabatanTarget->parent_id);
                        })->with('user', 'jabatans')->get();

                        foreach ($atasanList as $atasan) {
                            if(!$atasan->user_id) continue;
                            foreach($atasan->jabatans as $jabatanAtasan) {
                                if($jabatanAtasan->id == $jabatanTarget->parent_id) {
                                    $this->tambahAlokasi($dataAlokasi, $session->id, $atasan->user_id, $jabatanAtasan->id, $target->user_id, $jabatanTarget->id, 'Atasan');
                                }
                            }
                        }
                    }

                    if (in_array('Bawahan', $this->pilihan_kategori)) {
                        $bawahanList = Pegawai::whereHas('jabatans', function($q) use ($jabatanTarget) {
                            $q->where('parent_id', $jabatanTarget->id);
                        })->with('user', 'jabatans')->get();

                        foreach ($bawahanList as $bawahan) {
                            if(!$bawahan->user_id) continue;
                            foreach($bawahan->jabatans as $jabatanBawahan) {
                                if($jabatanBawahan->parent_id == $jabatanTarget->id) {
                                    $this->tambahAlokasi($dataAlokasi, $session->id, $bawahan->user_id, $jabatanBawahan->id, $target->user_id, $jabatanTarget->id, 'Bawahan');
                                }
                            }
                        }
                    }

                    if (in_array('Rekan', $this->pilihan_kategori) && $jabatanTarget->parent_id) {
                        $rekanCandidates = Pegawai::whereHas('jabatans', function($q) use ($jabatanTarget) {
                            $q->where('parent_id', $jabatanTarget->parent_id)
                              ->where('level', $jabatanTarget->level); 
                        })->where('id', '!=', $target->id)->with('user', 'jabatans')->get();

                        $rekanList = $rekanCandidates->shuffle()->take($this->limit_rekan);

                        foreach ($rekanList as $rekan) {
                            if(!$rekan->user_id) continue;
                            foreach($rekan->jabatans as $jabatanRekan) {
                                if($jabatanRekan->parent_id == $jabatanTarget->parent_id && $jabatanRekan->level == $jabatanTarget->level) {
                                     $this->tambahAlokasi($dataAlokasi, $session->id, $rekan->user_id, $jabatanRekan->id, $target->user_id, $jabatanTarget->id, 'Rekan');
                                     break; 
                                }
                            }
                        }
                    }
                }
            }

            foreach (array_chunk($dataAlokasi, 100) as $chunk) {
                PenilaianAlokasi::insertOrIgnore($chunk);
            }

            DB::commit();
            $this->checkSessionStatus();
            $this->checkPrerequisites(); // Update status cek lagi
            session()->flash('message', "Berhasil Generate!");

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
        $this->isProcessing = false;
    }

    private function tambahAlokasi(&$array, $sessionId, $penilaiId, $penilaiJabatanId, $targetId, $targetJabatanId, $sebagai)
    {
        if ($sebagai != 'Diri Sendiri' && $penilaiId == $targetId && $penilaiJabatanId == $targetJabatanId) return;

        $key = $sessionId . '-' . $penilaiId . '-' . $penilaiJabatanId . '-' . $targetId . '-' . $targetJabatanId;
        
        if (!isset($array[$key])) {
             $array[$key] = [
                'penilaian_session_id' => $sessionId,
                'user_id' => $penilaiId,
                'penilai_jabatan_id' => $penilaiJabatanId,
                'target_user_id' => $targetId,
                'jabatan_id' => $targetJabatanId, 
                'sebagai' => $sebagai,
                'status_nilai' => 'Belum',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    }

    public function render()
    {
        return view('livewire.admin.random-penilai', [
            'sikluses' => Siklus::where('status', 'Aktif')->with('penilaianSession')->get(),
            'histories' => PenilaianSession::with('siklus')->latest()->paginate(5)
        ])->layout('layouts.admin');
    }
}