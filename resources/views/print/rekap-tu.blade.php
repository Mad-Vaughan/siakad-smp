<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekapitulasi Akademik</title>
    <style>
        /* Setup Dasar */
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }

        /* Kop Surat */
        .kop-surat {
            text-align: center;
            margin-bottom: 15px;
        }
        .kop-surat h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1.5px;
            color: #0f172a; /* Warna biru dongker pekat */
        }
        .kop-surat p {
            margin: 4px 0 0 0;
            font-size: 11px;
            letter-spacing: 0.5px;
        }
        .garis-kop {
            border: 0;
            border-top: 2px solid #000; /* Garis tebal di bawah kop */
            margin-bottom: 25px;
        }

        /* Judul Laporan */
        .judul-laporan {
            text-align: center;
            margin-bottom: 30px;
        }
        .judul-laporan h3 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 1px;
        }
        .judul-laporan p {
            margin: 5px 0 0 0;
            font-size: 12px;
        }

        /* Informasi Kelas (Meta Info) */
        .meta-info {
            width: 100%;
            max-width: 450px;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .meta-info td {
            padding: 4px 0;
            vertical-align: top;
            font-size: 12px;
            font-weight: bold;
        }
        .meta-info .label {
            width: 180px;
        }

        /* Tabel Data */
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-data th, .table-data td {
            border: 1px solid #cbd5e1; /* Warna garis border abu-abu kebiruan soft */
            padding: 10px 8px;
        }
        .table-data th {
            background-color: #e2e8f0; /* Warna biru muda soft kayak di gambar */
            font-weight: bold;
            text-align: center;
            font-size: 11px;
            color: #0f172a;
        }
        .table-data td {
            font-size: 11px;
        }

        /* Helper Utilities */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        
        /* Tombol Print (Sembunyi pas diprint) */
        .no-print-area {
            margin-bottom: 20px;
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-print {
            background-color: #22c55e;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }

        /* Konfigurasi Khusus Print */
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            .table-data th {
                background-color: #e2e8f0 !important; /* Paksa warna biru tetep ngeprint */
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <div class="no-print no-print-area">
        <span style="font-size: 12px; color: #64748b;">*Gunakan tombol di sebelah kanan atau tekan CTRL+P</span>
        <button onclick="window.print()" class="btn-print">🖨️ KLIK UNTUK CETAK SEKARANG</button>
    </div>

    <div class="kop-surat">
        <h2>SMP MUARA INDONESIA</h2>
        <p>UNGGUL DALAM PRESTASI DAN AKHLAK</p>
    </div>
    <hr class="garis-kop">

    <div class="judul-laporan">
        <h3>SMP MUARA INDONESIA</h3>
        <p>Rekapitulasi Akademik Siswa</p>
    </div>

    <table class="meta-info">
        <tr>
            <td class="label">Tahun Ajaran</td>
            <td>: {{ $academicYear->name ?? '-' }}</td> 
        </tr>
        <tr>
            <td class="label">Kelas</td>
            <td>: {{ $classroom->name ?? '-' }}</td> 
        </tr>
        <tr>
            <td class="label">Wali Kelas</td>
            <td>: {{ $classroom->teacher->name ?? 'Belum ada Wali Kelas' }}</td> 
        </tr>
        <tr>
            <td class="label">Jumlah Peserta Didik</td>
            <td>: {{ $students->count() }} orang</td> 
        </tr>
        <tr>
            <td class="label">Waktu Cetak</td>
            <td>: {{ now()->format('d F Y H:i') }} WIB</td>
        </tr>
    </table>

    <table class="table-data">
        <thead>
            <tr>
                <th rowspan="2" width="5%">NO</th>
                <th rowspan="2" class="text-left">NAMA PESERTA DIDIK</th>
                <th colspan="4">STATUS KEHADIRAN</th>
                <th rowspan="2" width="15%">RATA-RATA NILAI</th>
            </tr>
            <tr>
                <th width="7%">H</th>
                <th width="7%">S</th>
                <th width="7%">I</th>
                <th width="7%">A</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($students as $siswa)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $siswa->name }}</td>
                
                <td class="text-center">{{ $siswa->total_h ?? 0 }}</td>
                <td class="text-center">{{ $siswa->total_s ?? 0 }}</td>
                <td class="text-center">{{ $siswa->total_i ?? 0 }}</td>
                <td class="text-center">{{ $siswa->total_a ?? 0 }}</td>
                <td class="text-center">{{ number_format($siswa->rata_nilai ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Data siswa kosong atau belum ada.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>