<style>
    /* Style untuk header coklat */
    .table-header-fix th {
        background-color: #C38E44 !important;
        color: white !important;
    }
    
    /* Style untuk menyelaraskan kolom tabel */
    .table-fixed-layout {
        table-layout: fixed; /* Memaksa tabel mengikuti lebar yang ditentukan */
        width: 100%;
    }
    /* Mengatur lebar setiap kolom secara spesifik */
    .table-fixed-layout th:nth-child(1), .table-fixed-layout td:nth-child(1) { width: 40%; } /* Kolom Nama */
    .table-fixed-layout th:nth-child(2), .table-fixed-layout td:nth-child(2) { width: 25%; } /* Kolom Jabatan */
    .table-fixed-layout th:nth-child(3), .table-fixed-layout td:nth-child(3) { width: 15%; } /* Kolom Status */
    .table-fixed-layout th:nth-child(4), .table-fixed-layout td:nth-child(4) { width: 20%; } /* Kolom Action */
</style>

<div class="container-fluid p-4">

    <div class="mb-4">
        <h1 class="h3">Formulir Penilaian Kinerja</h1>
        <p class="text-muted">Silakan isi formulir penilaian yang tersedia sebelum batas waktu berakhir.</p>
    </div>

    <div class="card shadow-lg border mb-4 rounded-3 overflow-hidden">
        <a class="list-group-item list-group-item-action d-flex justify-content-between p-3" 
           data-bs-toggle="collapse" href="#collapseAtasan" role="button" aria-expanded="true" aria-controls="collapseAtasan">
            <div class="d-flex align-items-center">
                <i class="bi bi-person-workspace fs-4 me-3 text-custom-brown"></i>
                <span class="fw-bold">Penilaian Atasan</span>
            </div>
            <span class="badge bg-warning text-dark align-self-center">Belum Mengisi</span>
        </a>
        <div class="collapse show" id="collapseAtasan">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-fixed-layout">
                    <thead class="table-header-fix">
                        <tr>
                            <th class="py-2 px-3 small text-uppercase">Nama</th>
                            <th class="py-2 px-3 small text-uppercase">Jabatan</th>
                            <th class="py-2 px-3 small text-uppercase">Status</th>
                            <th class="py-2 px-3 small text-uppercase text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($atasan as $person)
                            <tr>
                                <td class="p-3">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <div class="fw-bold text-truncate">{{ $person['nama'] }}</div>
                                            <div class="small text-muted">{{ $person['nip'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3 small text-truncate">{{ $person['jabatan'] }}</td>
                                <td class="p-3"><span class="badge bg-warning text-dark">Belum Mengisi</span></td>
                                <td class="p-3 text-end">
                                    <a href="#" class="btn btn-custom-brown btn-sm">Mulai Menilai</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center p-3 text-muted">Tidak ada data atasan untuk dinilai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div> <div class="card shadow-lg border mb-4 rounded-3 overflow-hidden">
        <a class="list-group-item list-group-item-action d-flex justify-content-between p-3" 
           data-bs-toggle="collapse" href="#collapseRekan" role="button" aria-expanded="false" aria-controls="collapseRekan">
            <div class="d-flex align-items-center">
                <i class="bi bi-people-fill fs-4 me-3 text-custom-brown"></i>
                <span class="fw-bold">Penilaian Rekan Sejawat</span>
            </div>
            <span class="badge bg-danger text-white align-self-center">Belum Mengisi</span>
        </a>
        <div class="collapse" id="collapseRekan">
            <div class="table-responsive">
                 <table class="table table-hover align-middle mb-0 table-fixed-layout">
                    <thead class="table-header-fix">
                        <tr>
                            <th class="py-2 px-3 small text-uppercase">Nama</th>
                            <th class="py-2 px-3 small text-uppercase">Jabatan</th>
                            <th class="py-2 px-3 small text-uppercase">Status</th>
                            <th class="py-2 px-3 small text-uppercase text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rekanSejawat as $person)
                            <tr>
                                <td class="p-3">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <div class="fw-bold text-truncate">{{ $person['nama'] }}</div>
                                            <div class="small text-muted">{{ $person['nip'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3 small text-truncate">{{ $person['jabatan'] }}</td>
                                <td class="p-3"><span class="badge bg-warning text-dark">Belum Mengisi</span></td>
                                <td class="p-3 text-end">
                                    <a href="#" class="btn btn-custom-brown btn-sm">Mulai Menilai</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center p-3 text-muted">Tidak ada data rekan sejawat untuk dinilai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div> <div class="card shadow-lg border mb-4 rounded-3 overflow-hidden">
        <a class="list-group-item list-group-item-action d-flex justify-content-between p-3" 
           data-bs-toggle="collapse" href="#collapseBawahan" role="button" aria-expanded="false" aria-controls="collapseBawahan">
            <div class="d-flex align-items-center">
                <i class="bi bi-person-lines-fill fs-4 me-3 text-custom-brown"></i>
                <span class="fw-bold">Penilaian Bawahan</span>
            </div>
            <span class="badge bg-danger text-white align-self-center">Belum Mengisi</span>
        </a>
        <div class="collapse" id="collapseBawahan">
            <div class="table-responsive">
                 <table class="table table-hover align-middle mb-0 table-fixed-layout">
                    <thead class="table-header-fix">
                        <tr>
                            <th class="py-2 px-3 small text-uppercase">Nama</th>
                            <th class="py-2 px-3 small text-uppercase">Jabatan</th>
                            <th class="py-2 px-3 small text-uppercase">Status</th>
                            <th class="py-2 px-3 small text-uppercase text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bawahan as $person)
                            <tr>
                                <td class="p-3">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <div class="fw-bold text-truncate">{{ $person['nama'] }}</div>
                                            <div class="small text-muted">{{ $person['nip'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3 small text-truncate">{{ $person['jabatan'] }}</td>
                                <td class="p-3"><span class="badge bg-warning text-dark">Belum Mengisi</span></td>
                                <td class="p-3 text-end">
                                    <a href="#" class="btn btn-custom-brown btn-sm">Mulai Menilai</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center p-3 text-muted">Tidak ada data bawahan untuk dinilai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div> </div>