<!DOCTYPE html>
<html>
<head>
    <title>Rekapitulasi Nilai Kinerja</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center; }
        .center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>REKAPITULASI PENILAIAN KINERJA PEGAWAI</h2>
        <p>Siklus Semester: {{ $siklus }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">NIP</th>
                <th width="30%">Nama Pegawai</th>
                <th width="25%">Jabatan</th>
                <th width="10%">Skor</th>
                <th width="15%">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pegawais as $index => $p)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $p['nip'] }}</td>
                <td>{{ $p['nama'] }}</td>
                <td>{{ $p['jabatan'] }}</td>
                <td class="center"><strong>{{ $p['skor_akhir'] }}</strong></td>
                <td class="center">{{ $p['predikat'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <br>
    <p style="text-align: right;">Dicetak pada: {{ date('d-m-Y H:i') }}</p>
</body>
</html>