<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Progress Penilaian</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; }
        
        /* KOP SURAT ISO */
        .iso-table { width: 100%; border: 1px solid #000; border-collapse: collapse; margin-bottom: 20px; }
        .iso-table td { border: 1px solid #000; padding: 5px; vertical-align: middle; }
        .logo-col { width: 15%; text-align: center; }
        .title-col { width: 50%; text-align: center; font-weight: bold; font-size: 14pt; text-transform: uppercase; }
        .meta-col { width: 35%; padding: 0 !important; }
        .inner-meta td { border: none; border-bottom: 1px solid #000; font-size: 9pt; padding: 2px 5px; }
        .inner-meta tr:last-child td { border-bottom: none; }

        /* TABEL DATA */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10pt; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 6px; }
        .data-table th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        
        .text-center { text-align: center; }
        .badge { display: inline-block; padding: 2px 6px; font-size: 8pt; font-weight: bold; border-radius: 3px; }
        .badge-success { color: #198754; border: 1px solid #198754; } 
        .badge-warning { color: #ffc107; border: 1px solid #ffc107; } 
        .badge-danger { color: #dc3545; border: 1px solid #dc3545; }

        .meta-info table { width: 100%; border: none; margin-bottom: 15px; font-size: 10pt; }
        .meta-info td { border: none; padding: 2px; }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
    <table class="iso-table">
        <tr>
            <td class="logo-col" rowspan="2">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" width="65" alt="Logo">
                @else
                    <div style="border:2px double #000; padding:5px; border-radius:50%; width:50px; height:50px; line-height:50px; margin:auto; font-weight:bold;">POLKAM</div>
                @endif
            </td>
            <td class="title-col" rowspan="2">
                POLITEKNIK KAMPAR<br>
                <span style="font-size: 11pt; font-weight: normal;">Laporan Progress Penilaian</span>
            </td>
            <td class="meta-col">
                <table class="inner-meta" width="100%">
                    <tr><td width="40%">No. Dok</td><td>: -</td></tr>
                    <tr><td>Tanggal</td><td>: {{ now()->format('d/m/Y') }}</td></tr>
                    <tr><td>Halaman</td><td>: 1 dari 1</td></tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="meta-col">
                <table class="inner-meta" width="100%">
                    <tr><td width="40%">Dokumen</td><td>: Rahasia</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="meta-info">
        <table>
            <tr>
                <td width="150"><strong>Tahun Ajaran</strong></td>
                <td>: {{ $siklus->tahun_ajaran ?? '-' }} {{ $siklus->semester ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Status Siklus</strong></td>
                <td>: {{ $siklus->status ?? '-' }}</td>
            </tr>
            {{-- BARIS BARU: WAKTU CETAK --}}
            <tr>
                <td><strong>Waktu Cetak Progress</strong></td>
                <td>: {{ $waktu_cetak }}</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Pegawai</th>
                <th width="30%">Jabatan</th>
                <th width="15%">Progress</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $row['nama'] }}</strong><br>
                    <span style="color: #555; font-size: 9pt;">NRP : {{ $row['nip'] }}</span>
                </td>
                <td>{{ $row['jabatan'] }}</td>
                <td class="text-center">
                    {{ $row['persen'] }}%<br>
                    <span style="font-size: 8pt; color: #666;">({{ $row['sudah'] }} dari {{ $row['total'] }})</span>
                </td>
                <td class="text-center">
                    @if($row['persen'] == 100) 
                        <span class="badge badge-success">SELESAI</span>
                    @elseif($row['persen'] > 0) 
                        <span class="badge badge-warning">PROSES</span>
                    @else 
                        <span class="badge badge-danger">BELUM</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>