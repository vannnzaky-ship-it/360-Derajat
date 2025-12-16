<!DOCTYPE html>
<html>
<head>
    <title>Laporan Peringkat Kinerja</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .header p { margin: 5px 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .center { text-align: center; }
        .footer { margin-top: 30px; text-align: right; }
        .badge { padding: 2px 5px; border-radius: 4px; color: #000; border: 1px solid #ccc; font-size: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Peringkat Kinerja Pegawai</h2>
        <p>Periode: {{ $siklus->tahun_ajaran }} - Semester {{ $siklus->semester }}</p>
        <p>Dicetak pada: {{ $tanggal }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">Rank</th>
                <th width="25%">Nama Pegawai</th>
                <th width="15%">NIP</th>
                <th width="25%">Jabatan</th>
                <th width="10%">Skor Akhir</th>
                <th width="20%">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataPegawai as $index => $row)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $row['nama'] }}</td>
                <td class="center">{{ $row['nip'] }}</td>
                <td>{{ $row['jabatan'] }}</td>
                <td class="center">
                    {{-- Tampilkan Skor Skala 100 --}}
                    @php
                        $val = floatval(str_replace(',', '', $row['skor_akhir']));
                        $displayScore = ($val <= 5) ? ($val * 20) : $val;
                    @endphp
                    <strong>{{ number_format($displayScore, 0) }}</strong>
                </td>
                <td class="center">{{ $row['predikat'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Mengetahui,</p>
        <br><br><br>
        <p>( Tim Peninjau )</p>
    </div>

</body>
</html>