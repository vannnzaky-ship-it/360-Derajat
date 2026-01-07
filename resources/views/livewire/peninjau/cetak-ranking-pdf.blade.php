<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Ranking Kinerja</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        
        /* KOP SURAT ISO STYLE */
        .iso-table { width: 100%; border: 1px solid #000; border-collapse: collapse; margin-bottom: 20px; }
        .iso-table td { border: 1px solid #000; padding: 5px; vertical-align: middle; }
        .logo-col { width: 10%; text-align: center; }
        .title-col { width: 60%; text-align: center; font-weight: bold; font-size: 14pt; text-transform: uppercase; }
        .meta-col { width: 30%; padding: 0 !important; }
        .inner-meta td { border: none; border-bottom: 1px solid #000; font-size: 8pt; padding: 2px 5px; }
        .inner-meta tr:last-child td { border-bottom: none; }

        /* TABEL DATA */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 6px; font-size: 9pt; }
        .data-table th { background-color: #f0f0f0; text-align: center; font-weight: bold; text-transform: uppercase; }
        .center { text-align: center; }
        .left { text-align: left; }

        /* TANDA TANGAN */
        .ttd-table { width: 100%; margin-top: 30px; page-break-inside: avoid; }
        .ttd-table td { text-align: center; vertical-align: top; }
        .ttd-space { height: 60px; }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
    <table class="iso-table">
        <tr>
            <td class="logo-col" rowspan="2">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" width="60" alt="Logo">
                @else
                    <b>POLKAM</b>
                @endif
            </td>
            <td class="title-col" rowspan="2">
                POLITEKNIK KAMPAR<br>
                <span style="font-size: 10pt; font-weight: normal;">Laporan Peringkat Kinerja Pegawai (360 Derajat)</span>
            </td>
            <td class="meta-col">
                <table class="inner-meta" width="100%">
                    <tr><td>Periode</td><td>: {{ $siklus->tahun_ajaran }} {{ $siklus->semester }}</td></tr>
                    <tr><td>Tanggal</td><td>: {{ $tanggal }}</td></tr>
                    <tr><td>Halaman</td><td>: 1 dari 1</td></tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="meta-col">
                <table class="inner-meta" width="100%">
                    <tr><td>Dokumen</td><td>: Rahasia</td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- TABEL DATA --}}
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">Rank</th>
                <th>Nama Pegawai</th>
                <th width="15%">NIP / NIK</th>
                <th>Jabatan</th>
                <th width="8%">Total Penilai</th>
                <th width="8%">Skor Akhir</th>
                <th width="12%">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pegawais as $index => $p)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td class="left">{{ $p['nama'] }}</td>
                <td class="center">{{ $p['nip'] }}</td>
                <td class="left">{{ $p['jabatan'] }}</td>
                <td class="center">{{ $p['total_penilai'] }}</td>
                <td class="center" style="font-weight: bold;">{{ number_format($p['skor_akhir'], 2) }}</td>
                <td class="center">{{ $p['predikat'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="center">Belum ada data penilaian.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- TANDA TANGAN --}}
    <table class="ttd-table">
        <tr>
            <td width="30%">
                Mengetahui,<br>
                <b>Wakil Direktur I</b>
            </td>
            <td width="40%"></td> <td width="30%">
                Bangkinang, {{ $tanggal }}<br>
                <b>Ka. BPM</b>
            </td>
        </tr>
        <tr>
            <td class="ttd-space"></td>
            <td></td>
            <td class="ttd-space"></td>
        </tr>
        <tr>
            <td>
                <b>(....................................)</b><br>
                NIP/NRP: .......................
            </td>
            <td></td>
            <td>
                <b>.........................</b><br>
                NRP: ............
            </td>
        </tr>
    </table>

</body>
</html>