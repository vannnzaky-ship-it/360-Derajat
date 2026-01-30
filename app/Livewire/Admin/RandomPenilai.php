<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Siklus;
use App\Models\Pegawai;
use App\Models\Kompetensi;
use App\Models\Pertanyaan;
use App\Models\SkemaPenilaian;
use App\Models\PenilaianSession;
use App\Models\PenilaianAlokasi;
use App\Models\PenilaianSkor; // [PENTING] Import Model Ini
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

    // State Validasi Ketat
    public $statusCheck = [
        'siklus_aktif' => false,
        'bobot_100' => false,
        'pertanyaan_lengkap' => false,
        'skema_lengkap' => false
    ];

    // Detail Error untuk UI
    public $totalBobotCurrent = 0;
    public $missingKompetensiNames = [];
    public $missingLevels = [];

    public $isReadyToGenerate = false; 
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

        $this->checkSessionStatus();
        $this->checkPrerequisites(); 
    }

    public function updatedSiklusId()
    {
        $this->checkSessionStatus();
        $this->checkPrerequisites(); 
    }

    // [LOGIKA UTAMA] Validasi Ketat (Aman JSON/Array)
    public function checkPrerequisites()
    {
        $this->resetErrorBag();

        // 1. Cek Siklus Aktif
        $siklus = Siklus::find($this->siklus_id);
        $this->statusCheck['siklus_aktif'] = ($siklus && $siklus->status === 'Aktif');

        // 2. Cek Bobot Kompetensi (Harus pas 100%)
        $activeKompetensi = Kompetensi::where('status', 'Aktif')->get();
        $this->totalBobotCurrent = $activeKompetensi->sum('bobot');
        $this->statusCheck['bobot_100'] = ($this->totalBobotCurrent === 100);

        // 3. Cek Pertanyaan (Setiap Kompetensi Aktif HARUS punya minimal 1 Pertanyaan Aktif)
        $this->missingKompetensiNames = [];
        foreach ($activeKompetensi as $komp) {
            $hasPertanyaan = Pertanyaan::where('kompetensi_id', $komp->id)
                                ->where('status', 'Aktif')
                                ->exists();
            if (!$hasPertanyaan) {
                $this->missingKompetensiNames[] = $komp->nama_kompetensi;
            }
        }
        $this->statusCheck['pertanyaan_lengkap'] = empty($this->missingKompetensiNames) && $activeKompetensi->isNotEmpty();

        // 4. Cek Skema (Harus mencakup Level 1, 2, 3, 4, 5)
        $this->missingLevels = [];
        if ($this->siklus_id) {
            $skemas = SkemaPenilaian::where('siklus_id', $this->siklus_id)->get();
            $coveredLevels = [];
            
            // Gabungkan semua level target dari semua skema di siklus ini
            foreach ($skemas as $skema) {
                // Cek apakah datanya sudah Array atau string JSON
                $dataLevel = $skema->level_target;
                
                if (is_array($dataLevel)) {
                    $levels = $dataLevel;
                } else {
                    $levels = json_decode($dataLevel, true);
                }
                
                $levels = $levels ?? []; 

                // Pastikan format string agar match dengan array_diff
                $levels = array_map('strval', $levels);
                $coveredLevels = array_merge($coveredLevels, $levels);
            }
            
            $requiredLevels = ['1', '2', '3', '4', '5'];
            $this->missingLevels = array_diff($requiredLevels, array_unique($coveredLevels));
            
            $this->statusCheck['skema_lengkap'] = empty($this->missingLevels);
        } else {
            $this->statusCheck['skema_lengkap'] = false;
        }

        // --- KESIMPULAN ---
        $this->isReadyToGenerate = !$this->isSessionExists 
                                   && $this->statusCheck['siklus_aktif']
                                   && $this->statusCheck['bobot_100'] 
                                   && $this->statusCheck['pertanyaan_lengkap'] 
                                   && $this->statusCheck['skema_lengkap'];
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
        $this->selectedHistory = PenilaianSession::with([
            'siklus', 
            'alokasis.user', 
            'alokasis.targetUser', 
            'alokasis.targetJabatan' 
        ])->find($sessionId);
    }

    public function generate()
    {
        // [VALIDASI SERVER SIDE KETAT]
        $this->checkPrerequisites();
        
        if (!$this->isReadyToGenerate) {
            $errorMsg = 'Validasi Gagal: ';
            if(!$this->statusCheck['siklus_aktif']) $errorMsg .= 'Siklus Tidak Aktif. ';
            if(!$this->statusCheck['bobot_100']) $errorMsg .= 'Bobot Kompetensi bukan 100%. ';
            if(!$this->statusCheck['skema_lengkap']) $errorMsg .= 'Skema Level belum lengkap. ';
            if(!$this->statusCheck['pertanyaan_lengkap']) $errorMsg .= 'Ada kompetensi tanpa pertanyaan. ';
            
            session()->flash('error', $errorMsg);
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
            // 1. Buat Session
            $session = PenilaianSession::create([
                'siklus_id' => $this->siklus_id,
                'tanggal_mulai' => now(),
                'batas_waktu' => $this->batas_waktu,
                'limit_rekan' => $this->limit_rekan,
                'status' => 'Open'
            ]);

            $allTargets = Pegawai::with(['user', 'jabatans'])->get();
            $dataAlokasi = [];

            // 2. Logic Alokasi Penilai
            foreach ($allTargets as $target) {
                if (!$target->user) continue;

                foreach ($target->jabatans as $jabatanTarget) {
                    // Diri Sendiri
                    if (in_array('Diri Sendiri', $this->pilihan_kategori)) {
                        $this->tambahAlokasi($dataAlokasi, $session->id, $target->user_id, $jabatanTarget->id, $target->user_id, $jabatanTarget->id, 'Diri Sendiri');
                    }

                    // Atasan
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

                    // Bawahan
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

                    // Rekan
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

            // Insert Alokasi Massal
            foreach (array_chunk($dataAlokasi, 100) as $chunk) {
                PenilaianAlokasi::insertOrIgnore($chunk);
            }

            // --- STEP BARU: SNAPSHOT / KUNCI PERTANYAAN ---
            // 3. Ambil ID Alokasi yang baru dibuat
            $createdAlokasiIds = PenilaianAlokasi::where('penilaian_session_id', $session->id)->pluck('id');
            
            // 4. Ambil Pertanyaan yang AKTIF SAAT INI
            $activePertanyaanIds = Pertanyaan::where('status', 'Aktif')->pluck('id');

            $dataSkorAwal = [];
            foreach ($createdAlokasiIds as $alokasiId) {
                foreach ($activePertanyaanIds as $pertanyaanId) {
                    $dataSkorAwal[] = [
                        'penilaian_alokasi_id' => $alokasiId,
                        'pertanyaan_id' => $pertanyaanId,
                        'nilai' => 0, // Nilai 0 = Slot kosong, tapi terkunci
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }

            // 5. Insert Skor Awal Massal
            // Ini akan membuat pertanyaan 'terkunci' sesuai kondisi saat ini.
            foreach (array_chunk($dataSkorAwal, 200) as $chunkSkor) {
                PenilaianSkor::insertOrIgnore($chunkSkor);
            }
            // ----------------------------------------------

            DB::commit();
            $this->checkSessionStatus();
            $this->checkPrerequisites(); 
            session()->flash('message', "Berhasil Generate & Mengunci Daftar Pertanyaan!");

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
            'sikluses' => Siklus::with('penilaianSession')->get(), 
            'histories' => PenilaianSession::with('siklus')->latest()->paginate(5)
        ])->layout('layouts.admin');
    }
}