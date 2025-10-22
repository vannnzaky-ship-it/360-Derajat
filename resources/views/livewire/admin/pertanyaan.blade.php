<div class="container-fluid p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-file-earmark-text-fill fs-1 text-custom-brown me-3"></i>
            <h2 class="h3 mb-0 text-dark">Pertanyaan</h2>
        </div>
        
        <a href="#" class="btn btn-success shadow-sm">
            <i class="bi bi-plus-lg me-2"></i>
            Tambah Data
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-table me-2"></i>
                    Daftar Pertanyaan
                </h5>
                
                <div class="input-group" style="width: 300px;">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="bi bi-search"></i>
                    </span>
                    <input 
                        type="text" 
                        class="form-control" 
                        placeholder="Search Data..." 
                        wire:model.live.debounce.300ms="search"
                    >
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    
                    <thead class="bg-custom-brown text-white">
                        <tr>
                            <th scope="col" class="text-center" style="width: 50px;">No</th>
                            <th scope="col">Kriteria</th>
                            <th scope="col">Sub Kriteria</th>
                            <th scope="col">Penilai</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($daftarPertanyaan as $index => $item)
                        <tr>
                            <td class="text-center fw-bold">
                                {{ ($daftarPertanyaan->currentPage() - 1) * $daftarPertanyaan->perPage() + $loop->iteration }}
                            </td>
                            <td>{{ $item->kriteria }}</td>
                            <td>{{ $item->sub_kriteria }}</td>
                            <td>
                                <small class="d-block">{{ $item->penilai }}</small>
                            </td>
                            <td>
                                @if ($item->status == 'Aktif')
                                    <span class="fw-bold text-success">Aktif</span>
                                @else
                                    <span class="fw-bold text-danger">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="#" class="btn btn-sm btn-outline-primary border-0" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-danger border-0" title="Hapus">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted p-4">
                                Data tidak ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                
                <div class="d-flex align-items-center">
                    <span class="text-muted me-2">Row per page:</span>
                    <select class="form-select form-select-sm" wire:model.live="perPage" style="width: 70px;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-muted ms-3">
                        Total {{ $daftarPertanyaan->firstItem() }} - {{ $daftarPertanyaan->lastItem() }} of {{ $daftarPertanyaan->total() }}
                    </span>
                </div>

                <div>
                    {{ $daftarPertanyaan->links() }}
                </div>
            </div>
        </div>

    </div>

</div>