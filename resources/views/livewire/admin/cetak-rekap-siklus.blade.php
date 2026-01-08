<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rekapitulasi Nilai Kinerja</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; }
        
        .iso-table { width: 100%; border: 1px solid #000; border-collapse: collapse; margin-bottom: 20px; }
        .iso-table td { border: 1px solid #000; padding: 5px; vertical-align: middle; }
        .logo-col { width: 15%; text-align: center; }
        .title-col { width: 50%; text-align: center; font-weight: bold; font-size: 14pt; text-transform: uppercase; }
        .meta-col { width: 35%; padding: 0 !important; }
        .inner-meta td { border: none; border-bottom: 1px solid #000; font-size: 9pt; padding: 2px 5px; }
        .inner-meta tr:last-child td { border-bottom: none; }

        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 10pt; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 5px; }
        .data-table th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        
        .center { text-align: center; }
        .left { text-align: left; }
        .bold { font-weight: bold; }

        .ttd-section { margin-top: 30px; width: 100%; page-break-inside: avoid; }
        .ttd-section td { text-align: center; vertical-align: top; }
        .ttd-space { height: 70px; }
        .ttd-name { font-weight: bold; text-decoration: underline; display: block; }
    </style>
</head>
<body>

    <table class="iso-table">
        <tr>
            <td class="logo-col" rowspan="2">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" width="65" alt="Logo">
                @else
                    <div style="border: 2px double #000; padding: 5px; font-weight:bold; border-radius:50%; width:50px; height:50px; line-height:50px; margin:auto;">POLKAM</div>
                @endif
            </td>
            <td class="title-col" rowspan="2">
                POLITEKNIK KAMPAR<br>
                <span style="font-size: 11pt; font-weight: normal;">Rekapitulasi Penilaian Kinerja Pegawai</span>
            </td>
            <td class="meta-col">
                <table class="inner-meta" width="100%">
                    <tr><td width="30%">Periode</td><td>: {{ $siklus->tahun_ajaran ?? '-' }} {{ $siklus->semester ?? '' }}</td></tr>
                    <tr><td>Tanggal</td><td>: {{ $tanggal }}</td></tr>
                    <tr><td>Halaman</td><td>: </td></tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="meta-col">
                <table class="inner-meta" width="100%">
                    <tr><td width="30%">Dokumen</td><td>: Rahasia</td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- TABEL DATA TANPA VALIDITAS --}}
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">Rank</th>
                <th width="15%">NRP</th>
                <th>Nama Pegawai</th>
                <th>Jabatan</th>
                <th width="10%">Penilai</th>
                <th width="10%">Skor</th>
                <th width="15%">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pegawais as $index => $p)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td class="center">{{ $p['nip'] }}</td>
                <td class="left">{{ $p['nama'] }}</td>
                <td class="left">{{ $p['jabatan'] }}</td>
                <td class="center">{{ $p['total_penilai'] }}</td>
                <td class="center bold">{{ number_format($p['skor_akhir'], 2) }}</td>
                <td class="center">{{ $p['predikat'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="center">Belum ada data penilaian pada siklus ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="ttd-section">
        <tr>
            <td width="50%">
                Mengetahui,<br>
                <b>Wakil Direktur I</b>
            </td>
            <td width="50%">
                Bangkinang, {{ $tanggal }}<br>
                <b>Ka. BPM</b>
            </td>
        </tr>
        <tr>
            <td class="ttd-space"></td>
            <td class="ttd-space"></td>
        </tr>
        <tr>
            <td>
                <span class="ttd-name">(....................................)</span>
                <span style="font-size: 10pt;">NRP: .......................</span>
            </td>
            <td>
                <span class="ttd-name">(....................................)</span>
                <span style="font-size: 10pt;">NRP: .......................</span>
            </td>
        </tr>
    </table>

</body>
</html>