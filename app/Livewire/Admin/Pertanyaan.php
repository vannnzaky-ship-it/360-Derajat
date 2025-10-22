<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination; // 1. Import trait pagination
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class Pertanyaan extends Component
{
    // 2. Gunakan trait pagination
    use WithPagination;

    // 3. Atur tema pagination ke Bootstrap 5
    protected $paginationTheme = 'bootstrap';

    // Properti untuk search dan filter per halaman
    public $search = '';
    public $perPage = 10; // Nilai default sesuai gambar "Row per page: 10"

    /**
     * Reset halaman ke 1 setiap kali search atau perPage diubah
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    /**
     * Render komponen
     */
    public function render()
    {
        // === Data Dummy (Dibuat banyak untuk simulasi pagination) ===
        $dummyData = new Collection([
            (object)[
                'id' => 1,
                'kriteria' => 'Pedagogik',
                'sub_kriteria' => 'Menguasai standar kompetensi dan dasar mata kuliah',
                'penilai' => 'Atasan, Rekan Sejawat, Bawahan',
                'status' => 'Tidak Aktif'
            ],
            (object)[
                'id' => 2,
                'kriteria' => 'Pedagogik',
                'sub_kriteria' => 'Mengembangkan materi pembelajaran secara kreatif',
                'penilai' => 'Atasan, Rekan Sejawat, Bawahan',
                'status' => 'Tidak Aktif'
            ],
            (object)[
                'id' => 3,
                'kriteria' => 'Pedagogik',
                'sub_kriteria' => 'Lorem ipsum dolor sit amet.',
                'penilai' => 'Atasan, Rekan Sejawat, Bawahan',
                'status' => 'Tidak Aktif'
            ],
            (object)[
                'id' => 4,
                'kriteria' => 'Pedagogik',
                'sub_kriteria' => 'Consectetur adipiscing elit.',
                'penilai' => 'Atasan, Rekan Sejawat, Bawahan',
                'status' => 'Aktif'
            ],
            (object)[
                'id' => 5,
                'kriteria' => 'Pedagogik',
                'sub_kriteria' => 'Praesent quis justo ac-ante.',
                'penilai' => 'Atasan, Rekan Sejawat, Bawahan',
                'status' => 'Aktif'
            ],
            (object)[
                'id' => 6,
                'kriteria' => 'Pedagogik',
                'sub_kriteria' => 'Maecenas fringilla nisl ut-arcu.',
                'penilai' => 'Atasan, Rekan Sejawat, Bawahan',
                'status' => 'Tidak Aktif'
            ],
            // Tambahkan lebih banyak data untuk pagination
            (object)[
                'id' => 7, 'kriteria' => 'Profesional', 'sub_kriteria' => 'Integritas (Etika dan Moral)', 
                'penilai' => 'Atasan, Rekan Sejawat', 'status' => 'Aktif'
            ],
            (object)[
                'id' => 8, 'kriteria' => 'Profesional', 'sub_kriteria' => 'Keahlian dalam bidang ilmu', 
                'penilai' => 'Atasan', 'status' => 'Aktif'
            ],
            (object)[
                'id' => 9, 'kriteria' => 'Sosial', 'sub_kriteria' => 'Kemampuan berkomunikasi', 
                'penilai' => 'Rekan Sejawat, Bawahan', 'status' => 'Aktif'
            ],
            (object)[
                'id' => 10, 'kriteria' => 'Sosial', 'sub_kriteria' => 'Kerjasama tim', 
                'penilai' => 'Rekan Sejawat, Bawahan', 'status' => 'Aktif'
            ],
            (object)[
                'id' => 11, 'kriteria' => 'Kepribadian', 'sub_kriteria' => 'Kedisiplinan', 
                'penilai' => 'Atasan, Rekan Sejawat', 'status' => 'Aktif'
            ],
        ]);
        // ========================================================


        // Filter data berdasarkan pencarian
        $filteredData = $dummyData->filter(function ($item) {
            return str_contains(strtolower($item->kriteria), strtolower($this->search)) ||
                   str_contains(strtolower($item->sub_kriteria), strtolower($this->search));
        });

        // Buat Pagination secara manual dari Collection
        $currentPage = Paginator::resolveCurrentPage('page');
        $currentItems = $filteredData->slice(($currentPage - 1) * $this->perPage, $this->perPage);

        $paginatedItems = new LengthAwarePaginator(
            $currentItems,
            $filteredData->count(),
            $this->perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );
        

        return view('livewire.admin.pertanyaan', [
            'daftarPertanyaan' => $paginatedItems // Kirim data yang sudah dipaginasi
        ])
        ->layout('layouts.admin', ['title' => 'Pertanyaan']);
    }
}