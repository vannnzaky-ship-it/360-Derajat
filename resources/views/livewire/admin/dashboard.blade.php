<div class="container-fluid p-4">

    <div class="d-flex align-items-center mb-4">
        <i class="bi bi-house-door-fill fs-1 text-custom-brown me-3"></i>
        <h2 class="h3 mb-0 text-dark">Dashboard</h2>
    </div>

    <p class="fs-5 text-dark mb-4">Selamat Datang {{ $userName }}</p>

    <div class="row g-4">
        
        <div class="col-xl-4 col-md-6">
            <a href="{{ route('admin.dashboard') }}" class="card shadow-sm border-0 bg-white dashboard-card" style="border-left: 5px solid #28a745 !important;">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <h5 class="mb-0 text-dark fw-normal">Dashboard</h5>
                    <i class="bi bi-house-door-fill fs-3 text-secondary"></i>
                </div>
            </a>
        </div>
        
        <div class="col-xl-4 col-md-6">
            <a href="{{ route('admin.data-pegawai') }}" class="card shadow-sm border-0 bg-white dashboard-card" style="border-left: 5px solid #17a2b8 !important;">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <h5 class="mb-0 text-dark fw-normal">Data Pegawai</h5>
                    <i class="bi bi-person-fill-add fs-3 text-secondary"></i>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-md-6">
            <a href="{{ route('admin.jabatan') }}" class="card shadow-sm border-0 bg-white dashboard-card" style="border-left: 5px solid #1732b8 !important;">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <h5 class="mb-0 text-dark fw-normal">Manajemen Struktur</h5>
                    <i class="bi bi-person-fill-add fs-3 text-secondary"></i>
                </div>
            </a>
        </div>
        
        <div class="col-xl-4 col-md-6">
            <a href="{{ route('admin.kompetensi') }}" class="card shadow-sm border-0 bg-white dashboard-card" style="border-left: 5px solid #ffc107 !important;">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <h5 class="mb-0 text-dark fw-normal">Bobot Kompetensi</h5>
                    <i class="bi bi-box-fill fs-3 text-secondary"></i>
                </div>
            </a>
        </div>
        
        <div class="col-xl-4 col-md-6">
            <a href="{{ route('admin.siklus-semester') }}" class="card shadow-sm border-0 bg-white dashboard-card" style="border-left: 5px solid #fd7e14 !important;">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <h5 class="mb-0 text-dark fw-normal">Siklus Semester</h5>
                    <i class="bi bi-calendar-event-fill fs-3 text-secondary"></i>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-md-6">
            <a href="{{ route('admin.skema-penilaian') }}" class="card shadow-sm border-0 bg-white dashboard-card" style="border-left: 5px solid #b314fd !important;">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <h5 class="mb-0 text-dark fw-normal">Skema Penilaian</h5>
                    <i class="bi bi-calendar-event-fill fs-3 text-secondary"></i>
                </div>
            </a>
        </div>
        
        <div class="col-xl-4 col-md-6">
            <a href="{{ route('admin.pertanyaan') }}" class="card shadow-sm border-0 bg-white dashboard-card" style="border-left: 5px solid #6c757d !important;">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <h5 class="mb-0 text-dark fw-normal">Pertanyaan</h5>
                    <i class="bi bi-file-earmark-text-fill fs-3 text-secondary"></i>
                </div>
            </a>
        </div>
        
        <div class="col-xl-4 col-md-6">
            <a href="{{ route('admin.progress-penilaian') }}" class="card shadow-sm border-0 bg-white dashboard-card" style="border-left: 5px solid #dc3545 !important;">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <h5 class="mb-0 text-dark fw-normal">Proses Penilai</h5>
                    <i class="bi bi-bell-fill fs-3 text-secondary"></i>
                </div>
            </a>
        </div>
        
        <div class="col-xl-4 col-md-6">
            <a href="{{ route('admin.random-penilai') }}" class="card shadow-sm border-0 bg-white dashboard-card" style="border-left: 5px solid #e83e8c !important;">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <h5 class="mb-0 text-dark fw-normal">Random Penilai</h5>
                    <i class="bi bi-hand-index-thumb-fill fs-3 text-secondary"></i>
                </div>
            </a>
        </div>
    </div>

    <style>
        .dashboard-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-decoration: none; 
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
    </style>

</div>