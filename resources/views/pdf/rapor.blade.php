<!DOCTYPE html>
<html>
<head>
    <title>Rapor Siswa - {{ $student->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12pt; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px double #000; padding-bottom: 10px; }
        .title { text-align: center; font-weight: bold; font-size: 14pt; margin-bottom: 20px; text-decoration: underline; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 2px; vertical-align: top; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid black; padding: 8px; text-align: left; }
        .data-table th { background-color: #f2f2f2; text-align: center; }
        .footer { margin-top: 50px; width: 100%; }
        .footer td { text-align: center; width: 50%; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0;">PEMERINTAH KABUPATEN / KOTA</h2>
        <h3 style="margin:0;">SMP NEGERI TERPADU SIAKAD</h3>
        <p style="margin:5px 0 0 0;">Jl. Pendidikan No. 123, Indonesia. Telp: (021) 123456</p>
    </div>

    <div class="title">LAPORAN HASIL BELAJAR (RAPOR)</div>

    <table class="info-table">
        <tr>
            <td width="20%">Nama Siswa</td><td width="2%">:</td><td width="38%"><strong>{{ $student->name }}</strong></td>
            <td width="15%">Kelas</td><td width="2%">:</td><td width="23%">{{ $classroom->name }}</td>
        </tr>
        <tr>
            <td>NISN</td><td>:</td><td>{{ $student->nisn }}</td>
            <td>Semester</td><td>:</td><td>{{ ucfirst($classroom->academicYear->semester ?? 'Ganjil') }}</td>
        </tr>
        <tr>
            <td>Tahun Ajaran</td><td>:</td><td>{{ $classroom->academicYear->name ?? '-' }}</td>
            <td>Fase</td><td>:</td><td>D</td>
        </tr>
    </table>

    <h4>A. Nilai Akademik</h4>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Mata Pelajaran</th>
                <th width="15%">Tipe Ujian</th>
                <th width="15%">Nilai Akhir</th>
                <th width="35%">Capaian Kompetensi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assessments as $index => $item)
                @php
                    // Ambil nama mapel dengan aman dari relasi model lu Jon
                    $mapelName = $item->assessment->subject->name ?? ($item->subject->name ?? 'Mata Pelajaran');
                    
                    // Tarik tipe ujian (UTS/UAS/Tugas) dan translate ke Indonesia
                    $tipeEnum = $item->assessment->type ?? null;
                    $rawType = strtolower(is_object($tipeEnum) ? ($tipeEnum->value ?? $tipeEnum->name) : ($tipeEnum ?? ''));
                    $tipeIndo = match($rawType) {
                        'assignment' => 'Tugas',
                        'exam'       => 'Ujian',
                        'quiz'       => 'Kuis',
                        'project'    => 'Proyek',
                        'midterm'    => 'UTS',
                        'final'      => 'UAS',
                        'practice'   => 'Praktek',
                        default      => ucfirst($rawType ?: 'Nilai'),
                    };

                    // Ambil kolom nilai yang bener (score)
                    $nilaiAkhir = $item->score ?? ($item->final_score ?? 0);
                    $color = $nilaiAkhir < 75 ? 'color: #dc2626; font-weight: bold;' : 'color: #16a34a; font-weight: bold;';
                @endphp
                <tr>
                    <td style="text-align:center;">{{ $index + 1 }}</td>
                    <td>{{ $mapelName }}</td>
                    <td style="text-align:center; color: #6b7280;">{{ $tipeIndo }}</td>
                    <td style="text-align:center; {{$color}}">{{ $nilaiAkhir }}</td>
                    <td style="font-size: 10pt;">
                        @if($nilaiAkhir >= 75)
                            Menunjukkan penguasaan yang sangat baik dalam mencapai kompetensi {{ $mapelName }}.
                        @else
                            Perlu bimbingan dan peningkatan dalam menguasai kompetensi {{ $mapelName }}.
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center; color:#6b7280; font-style:italic; padding: 20px;">
                        Data nilai belum tersedia untuk semester ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <table width="100%">
            <tr>
                <td>
                    <br>Mengetahui,<br>Orang Tua/Wali<br><br><br><br><br>
                    ( ................................ )
                </td>
                <td>
                    Jakarta, {{ date('d F Y') }}<br>Wali Kelas<br><br><br><br><br>
                    <strong>( {{ $classroom->teacher->name ?? ($classroom->teacher->user->name ?? 'Nama Guru') }} )</strong>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>