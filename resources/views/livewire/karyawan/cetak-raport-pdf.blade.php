<!DOCTYPE html>
<html>
<head>
    <title>Raport Kinerja - {{ $namaUser }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 11px; color: #666; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table-info td { padding: 4px; vertical-align: top; }
        
        .table-nilai th, .table-nilai td { border: 1px solid #999; padding: 6px 8px; text-align: left; }
        .table-nilai th { background-color: #f0f0f0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        .score-box { border: 1px solid #333; padding: 15px; text-align: center; margin-bottom: 20px; width: 220px; margin-left: auto; margin-right: auto; }
        .score-val { font-size: 32px; font-weight: bold; margin: 5px 0; display: block; }
        .predikat { background-color: #333; color: #fff; padding: 4px 12px; border-radius: 10px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        
        .ranking-info { margin-top: 10px; font-size: 11px; color: #555; font-style: italic; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Raport Kinerja Pegawai (360°)</h1>
        <p>Tahun Ajaran / Semester: {{ $siklus }}</p>
    </div>

    {{-- Info Pegawai --}}
    <table class="table-info">
        <tr>
            <td width="120">Nama Pegawai</td>
            <td width="10">:</td>
            <td class="fw-bold">{{ $namaUser }}</td>
        </tr>
        <tr>
            <td>NIP / ID</td>
            <td>:</td>
            <td>{{ $nipUser }}</td>
        </tr>
        <tr>
            <td>Status Jabatan</td>
            <td>:</td>
            {{-- MENGGUNAKAN labelJabatan AGAR SINKRON DENGAN PILIHAN DROPDOWN --}}
            <td class="fw-bold" style="color: #8E652E;">{{ $labelJabatan }}</td>
        </tr>
    </table>

    <hr style="border: 0; border-top: 1px dashed #ccc; margin: 15px 0;">

    {{-- Kotak Nilai Utama --}}
    <div class="score-box">
        <div style="font-size: 10px; letter-spacing: 1px;">TOTAL SKOR AKHIR</div>
        <span class="score-val">{{ number_format($finalScore, 2) }}</span>
        <span class="predikat">{{ $predikat }}</span>
        
        {{-- Menampilkan Ranking di PDF --}}
        <div class="ranking-info">
            Peringkat: {{ $ranking }} dari {{ $totalPegawai }} Pegawai
        </div>
    </div>

    {{-- Tabel Rincian --}}
    <h3 style="font-size: 14px; border-left: 4px solid #333; padding-left: 10px;">Rincian Penilaian Kompetensi</h3>
    <table class="table-nilai">
        <thead>
            <tr>
                <th>Kompetensi / Kategori</th>
                <th width="120" class="text-center">Nilai Rata-rata</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tableData as $kategori => $nilai)
            <tr>
                <td>{{ $kategori }}</td>
                <td class="text-center fw-bold">{{ number_format($nilai, 2) }}</td>
            </tr>
            @endforeach
            <tr style="background-color: #f9f9f9;">
                <td class="text-right fw-bold">SKOR AKHIR (PEMBULATAN)</td>
                <td class="text-center fw-bold" style="font-size: 14px;">{{ number_format($finalScore, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <p style="font-size: 10px; color: #777;">* Raport ini dicetak otomatis oleh Sistem Penilaian Kinerja 360°.</p>

    {{-- Tanda Tangan --}}
    <table style="width: 100%; margin-top: 50px;">
        <tr>
            <td width="60%"></td>
            <td width="40%" class="text-center">
                <p>Dicetak pada: {{ date('d F Y') }}</p>
                <br><br><br><br>
                <p style="text-decoration: underline; font-weight: bold; margin-bottom: 0;">{{ $namaUser }}</p>
                <p style="margin-top: 2px;">Pegawai Terkait</p>
            </td>
        </tr>
    </table>

</body>
</html>