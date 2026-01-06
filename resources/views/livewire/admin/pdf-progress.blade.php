<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Progress Penilaian</title>
    <style>
        body { font-family: sans-serif; font-size: 11pt; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #000; }
        th { background-color: #f0f0f0; padding: 8px; text-align: left; font-size: 10pt; }
        td { padding: 6px; font-size: 10pt; vertical-align: top; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge { 
            display: inline-block; padding: 2px 5px; 
            font-size: 8pt; font-weight: bold; text-transform: uppercase;
            border-radius: 3px;
        }
        .badge-success { color: #198754; border: 1px solid #198754; }
        .badge-warning { color: #ffc107; border: 1px solid #ffc107; }
        .badge-danger { color: #dc3545; border: 1px solid #dc3545; }
        .meta-info { margin-bottom: 10px; font-size: 10pt; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Monitoring Progress Penilaian 360</h2>
        <p>Politeknik Kampar</p>
    </div>

    <div class="meta-info">
        <table style="border: none;">
            <tr style="border: none;">
                <td style="border: none; width: 150px;"><strong>Tahun Ajaran</strong></td>
                <td style="border: none;">: {{ $siklus->tahun_ajaran ?? '-' }} {{ $siklus->semester ?? '-' }}</td>
            </tr>
            <tr style="border: none;">
                <td style="border: none;"><strong>Tanggal Cetak</strong></td>
                <td style="border: none;">: {{ now()->format('d F Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="35%">Pegawai</th>
                <th width="30%">Jabatan</th>
                <th class="text-center" width="15%">Progress</th>
                <th class="text-center" width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $row['nama'] }}</strong><br>
                    <span style="color: #555; font-size: 9pt;">NIP. {{ $row['nip'] }}</span>
                </td>
                <td>{{ $row['jabatan'] }}</td>
                <td class="text-center">
                    {{ $row['persen'] }}%<br>
                    <span style="font-size: 8pt; color: #666;">({{ $row['sudah'] }} dari {{ $row['total'] }})</span>
                </td>
                <td class="text-center">
                    @if($row['persen'] == 100) <span class="badge badge-success">Selesai</span>
                    @elseif($row['persen'] > 0) <span class="badge badge-warning">Proses</span>
                    @else <span class="badge badge-danger">Belum</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>