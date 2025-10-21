<div class="container-fluid p-4">
    
    <!-- Ucapan Selamat Datang -->
    <h1 class="h3 mb-3">Selamat Datang, {{ $namaUser }}!</h1>

    <!-- Progress Bar -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h5 class="card-title">Progress Pengisian Penilaian</h5>
            <p class="card-text">Anda telah menyelesaikan 3 dari 4 formulir penilaian.</p>
            <div class="progress" style="height: 20px;">
                <div class="progress-bar bg-custom-brown" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">75%</div>
            </div>
        </div>
    </div>

    <!-- 
        Tombol/Kartu Akses Cepat (SUDAH DIPERBARUI)
        Kita tidak pakai .row dan .col lagi. Kita pakai flexbox murni.
    -->
    <div class="d-flex flex-wrap justify-content-center gap-4 py-3">
        
        <!-- Kartu 1 -->
        <div class="card text-center shadow-sm h-100" style="width: 16rem;">
            <div class="card-body d-flex flex-column justify-content-center">
                <i class="bi bi-ui-checks display-4 text-custom-brown"></i>
                <h5 class="card-title mt-3">Mulai Menilai</h5>
                <p class="card-text small">Isi formulir penilaian untuk rekan, atasan, dan diri sendiri.</p>
                <a href="#" class="btn btn-custom-brown mt-auto">Mulai</a>
            </div>
        </div>

        <!-- Kartu 2 -->
        <div class="card text-center shadow-sm h-100" style="width: 16rem;">
            <div class="card-body d-flex flex-column justify-content-center">
                <i class="bi bi-clipboard-data display-4 text-custom-brown"></i>
                <h5 class="card-title mt-3">Lihat Raport</h5>
                <p class="card-text small">Lihat hasil akhir penilaian kinerja Anda semester ini.</p>
                <a href="#" class="btn btn-outline-dark mt-auto">Lihat</a>
            </div>
        </div>

        <!-- Kartu 3 -->
         <div class="card text-center shadow-sm h-100" style="width: 16rem;">
            <div class="card-body d-flex flex-column justify-content-center">
                <i class="bi bi-trophy display-4 text-custom-brown"></i>
                <h5 class="card-title mt-3">Peringkat</h5>
                <p class="card-text small">Lihat posisi peringkat Anda di unit kerja.</p>
                <a href="#" class="btn btn-outline-dark mt-auto">Lihat</a>
            </div>
        </div>

        <!-- Jika nanti Anda tambah kartu ke-4, cukup tambahkan di sini -->

    </div>
</div>
