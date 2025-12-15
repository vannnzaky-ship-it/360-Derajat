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
        
        .score-box { border: 1px solid #333; padding: 15px; text-align: center; margin-bottom: 20px; width: 200px; margin-left: auto; margin-right: auto; }
        .score-val { font-size: 32px; font-weight: bold; margin: 10px 0; display: block; }
        .predikat { background-color: #333; color: #fff; padding: 3px 10px; border-radius: 10px; font-size: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Raport Kinerja 360°</h1>
        <p>Siklus: {{ $siklus }}</p>
    </div>

    {{-- Info Pegawai --}}
    <table class="table-info">
        <tr>
            <td width="100">Nama Pegawai</td>
            <td width="10">:</td>
            <td class="fw-bold">{{ $namaUser }}</td>
        </tr>
        <tr>
            <td>NIP</td>
            <td>:</td>
            <td>{{ $nipUser }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td>{{ $jabatanUser }}</td>
        </tr>
    </table>

    <hr style="border: 0; border-top: 1px dashed #ccc; margin: 15px 0;">

    {{-- Kotak Nilai Utama --}}
    <div class="score-box">
        <div>SKOR AKHIR</div>
        <span class="score-val">{{ $finalScore }}</span>
        <span class="predikat">{{ $predikat }}</span>
    </div>

    {{-- Tabel Rincian --}}
    <h3>Rincian Penilaian</h3>
    <table class="table-nilai">
        <thead>
            <tr>
                <th>Kompetensi / Kategori</th>
                <th width="100" class="text-center">Nilai (0-100)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tableData as $kategori => $nilai)
            <tr>
                <td>{{ $kategori }}</td>
                <td class="text-center fw-bold">{{ $nilai }}</td>
            </tr>
            @endforeach
            <tr style="background-color: #fafafa;">
                <td class="text-right fw-bold">RATA-RATA AKHIR</td>
                <td class="text-center fw-bold">{{ $finalScore }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Tanda Tangan (Opsional) --}}
    <br><br>
    <table style="width: 100%; margin-top: 30px;">
        <tr>
            <td width="60%"></td>
            <td width="40%" class="text-center">
                <p>Dicetak pada: {{ date('d/m/Y') }}</p>
                <br><br><br>
                <p style="text-decoration: underline; font-weight: bold;">{{ $namaUser }}</p>
                <p>Karyawan</p>
            </td>
        </tr>
    </table>

</body>
</html>