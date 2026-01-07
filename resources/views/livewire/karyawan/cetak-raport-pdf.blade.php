<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Raport Kinerja - {{ $namaUser }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; color: #000; }
        table { width: 100%; border-collapse: collapse; }
        
        /* HEADER KONTROL DOKUMEN (ISO STYLE) */
        .iso-table { width: 100%; border: 1px solid #000; margin-bottom: 20px; }
        .iso-table td { border: 1px solid #000; padding: 5px; vertical-align: middle; }
        
        .logo-col { width: 15%; text-align: center; }
        .title-col { width: 45%; text-align: center; font-weight: bold; font-size: 14pt; text-transform: uppercase; }
        .meta-col { width: 40%; padding: 0 !important; }
        
        .inner-meta td { border: none; border-bottom: 1px solid #000; font-size: 9pt; padding: 2px 5px; }
        .inner-meta tr:last-child td { border-bottom: none; }

        /* BIODATA */
        .bio-table { width: 100%; margin-bottom: 15px; font-size: 11pt; }
        .bio-table td { padding: 3px 0; vertical-align: top; }
        .label { width: 100px; }
        .titik { width: 10px; text-align: center; }

        /* TABEL NILAI UTAMA */
        .nilai-table { width: 100%; border: 1px solid #000; margin-bottom: 15px; font-size: 11pt; }
        .nilai-table th, .nilai-table td { border: 1px solid #000; padding: 6px 10px; }
        .nilai-table th { background-color: #f2f2f2; text-align: center; font-weight: bold; text-transform: uppercase; }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }

        /* KETERANGAN */
        .notes { font-size: 10pt; margin-top: 10px; }
        .legend-table { width: 60%; border: 1px solid #000; font-size: 9pt; margin-top: 5px; }
        .legend-table td { border: 1px solid #000; padding: 2px 8px; }

        /* TANDA TANGAN */
        .ttd-section { margin-top: 40px; width: 100%; }
        .ttd-section td { text-align: center; vertical-align: top; }
        .ttd-space { height: 70px; }
        .ttd-name { font-weight: bold; text-decoration: underline; display: block; }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
    <table class="iso-table">
        <tr>
            <td class="logo-col" rowspan="2">
                {{-- PENGAMAN: Cek apakah Server punya GD Extension --}}
                <?php
                    $showLogo = false;
                    $base64 = '';
                    // Kita cek manual apakah fungsi gambar tersedia
                    if (function_exists('imagecreate') || extension_loaded('gd')) {
                        $path = public_path('images/logo-polkam.png');
                        if (file_exists($path)) {
                            // Bungkus try-catch agar PDF tidak crash jika file gambar bermasalah
                            try {
                                $type = pathinfo($path, PATHINFO_EXTENSION);
                                $data = file_get_contents($path);
                                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                                $showLogo = true;
                            } catch (\Exception $e) {
                                $showLogo = false;
                            }
                        }
                    }
                ?>

                @if($showLogo)
                    <img src="{{ $base64 }}" width="65" alt="Logo">
                @else
                    {{-- Tampilkan teks pengganti jika GD mati / Gambar error --}}
                    <div style="font-weight:bold; font-size:12pt; border:1px dashed #000; padding:5px;">POLKAM</div>
                @endif
            </td>
            <td class="title-col" rowspan="2">
                POLITEKNIK KAMPAR
            </td>
            <td class="meta-col">
                <table class="inner-meta" width="100%">
                    <tr><td width="40%">No. Dok</td><td>: -</td></tr>
                    <tr><td>Tanggal Terbit</td><td>: -</td></tr>
                    <tr><td>Revisi</td><td>: -</td></tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="meta-col">
                <table class="inner-meta" width="100%">
                    <tr><td width="40%">Dokumen Level</td><td>: Formulir</td></tr>
                    <tr><td>Tanggal Efektif</td><td>: -</td></tr>
                    <tr><td>Halaman</td><td>: 1 dari 1</td></tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="center" style="background-color: #f2f2f2; font-weight: bold; padding: 8px;">
                RAPORT HASIL EVALUASI 360 DERAJAT
            </td>
        </tr>
    </table>

    {{-- BIODATA --}}
    <table class="bio-table">
        <tr>
            <td class="label">Nama</td><td class="titik">:</td><td><strong>{{ $namaUser }}</strong></td>
        </tr>
        <tr>
            <td class="label">Unit Kerja</td><td class="titik">:</td><td>{{ $unitKerja }}</td>
        </tr>
        <tr>
            <td class="label">Jabatan</td><td class="titik">:</td><td>{{ $jabatan }}</td>
        </tr>
    </table>

    {{-- TABEL NILAI KOMPETENSI (HANYA 360) --}}
    <table class="nilai-table">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th>Kompetensi / Aspek Penilaian</th>
                <th width="20%">Nilai Rata-rata</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($tableData as $kompetensi => $nilai)
            <tr>
                <td class="center">{{ $no++ }}</td>
                <td>{{ $kompetensi }}</td>
                <td class="center">{{ number_format($nilai, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="right bold" style="padding-right: 15px;">RATA-RATA NILAI AKHIR</td>
                <td class="center bold" style="background-color: #f9f9f9;">
                    {{ number_format($finalScore, 2, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td colspan="2" class="right bold" style="padding-right: 15px;">MUTU / PREDIKAT</td>
                <td class="center bold" style="background-color: #f9f9f9;">
                    {{ $mutu }}
                </td>
            </tr>
        </tfoot>
    </table>

    {{-- KETERANGAN --}}
    <div class="notes">
        <div style="margin-bottom: 10px;">
            <strong>Catatan:</strong> <br>
            Nilai di atas merupakan hasil rata-rata dari penilaian Atasan, Rekan Sejawat, Bawahan, dan Diri Sendiri.
        </div>

        <strong>Keterangan:</strong><br>
        Nilai Mutu = Nilai x Nilai Mutu <br>
        
        <table class="legend-table">
            <tr>
                <td colspan="2" class="center" style="background-color: #f2f2f2;"><strong>Rentang Nilai</strong></td>
                <td class="center" style="background-color: #f2f2f2;"><strong>Keterangan</strong></td>
            </tr>
            <tr><td>87.5 < A &le; 100</td><td class="center">A</td><td>Baik Sekali</td></tr>
            <tr><td>75 < B &le; 87.5</td><td class="center">B</td><td>Baik</td></tr>
            <tr><td>62.5 < C &le; 75</td><td class="center">C</td><td>Cukup</td></tr>
            <tr><td>50 < D &le; 62.5</td><td class="center">D</td><td>Kurang</td></tr>
            <tr><td>0 &le; E &le; 50</td><td class="center">E</td><td>Buruk</td></tr>
        </table>
    </div>

    {{-- TANDA TANGAN --}}
    {{-- TANDA TANGAN (HARDCODED DI VIEW) --}}
    <table class="ttd-section">
        <tr>
            <td width="50%">
                Mengetahui,<br>
                <b>Direktur Politeknik Kampar</b>
            </td>
            <td width="50%">
                Bangkinang, {{ $tanggal_cetak }}<br>
                <b>BPM Politeknik Kampar</b>
            </td>
        </tr>
        <tr>
            <td class="ttd-space"></td>
            <td class="ttd-space"></td>
        </tr>
        <tr>
            <td>
                <span class="ttd-name">..............</span>
                <span style="font-size: 10pt;">NRP: ......</span>
            </td>
            <td>
                <span class="ttd-name">..............</span>
                <span style="font-size: 10pt;">NRP: ......</span>
            </td>
        </tr>
    </table>

</body>
</html>