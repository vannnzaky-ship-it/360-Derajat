<div class="container-fluid p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-arrow-repeat fs-1 text-custom-brown me-3"></i>
            <h2 class="h3 mb-0 text-dark">Siklus Semester</h2>
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
                    Daftar Siklus Semester
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
                            <th scope="col">Tahun Ajaran</th>
                            <th scope="col">Semester</th>
                            <th scope="col">Penilaian</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($daftarSiklus as $index => $siklus)
                        <tr>
                            <td class="text-center fw-bold">{{ $index + 1 }}</td>
                            <td>{{ $siklus->tahun }}</td>
                            <td>{{ $siklus->semester }}</td>
                            <td>
                                <small class="d-block">{!! $siklus->penilaian !!}</small>
                            </td>
                            <td>
                                @if ($siklus->status == 'Aktif')
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
    </div>

</div>