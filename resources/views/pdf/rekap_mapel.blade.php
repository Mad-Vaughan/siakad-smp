<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Presensi Mata Pelajaran</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid black; padding-bottom: 10px; }
        .info-table { width: 100%; margin-bottom: 20px; font-weight: bold; }
        .data-table { width: 100%; border-collapse: collapse; text-align: center; }
        .data-table th, .data-table td { border: 1px solid black; padding: 5px; }
        .data-table th { background-color: #f2f2f2; }
    </style>
</head>
<body>

    <div class="header">
        <h2>LAPORAN PRESENSI MATA PELAJARAN</h2>
        <h3>SMP LUAR BIASA (SIAKAD)</h3>
    </div>

    <table class="info-table">
        <tr>
            <td width="18%">Mata Pelajaran</td><td width="32%">: {{ $schedule->subject->name }}</td>
            <td width="18%">Kelas</td><td width="32%">: {{ $schedule->classroom->name }}</td>
        </tr>
        <tr>
            <td>Guru Pengampu</td><td>: {{ $schedule->subject->teacher->name ?? 'Belum Diatur' }}</td>
            <td>Tahun Ajaran</td><td>: {{ $schedule->classroom->academicYear->name }} (Semester {{ ucfirst($schedule->classroom->academicYear->semester) }})</td>
        </tr>
        <tr>
            <td>Hari / Waktu</td><td>: {{ $schedule->day }}, {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }} WIB</td>
            <td></td><td></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" width="5%">No</th>
                <th rowspan="2" width="25%">Nama Siswa</th>
                <th colspan="{{ count($pertemuan) == 0 ? 1 : count($pertemuan) }}">Pertemuan Ke-</th>
            </tr>
            <tr>
                @forelse($pertemuan as $index => $p)
                    <th>P-{{ $index + 1 }}<br><small>({{ \Carbon\Carbon::parse($p->date)->format('d/m') }})</small></th>
                @empty
                    <th>Belum ada data</th>
                @endforelse
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="text-align: left;">{{ $student->name }}</td>
                
                @forelse($pertemuan as $p)
                    @php
                        // Cari absen anak ini di pertemuan ini
                        $absen = $p->studentPresences->where('student_id', $student->id)->first();
                        
                        // Translate dari Inggris ke Indonesia (H, S, I, A)
                        $statusRaw = strtolower($absen->status->value ?? $absen->status ?? '');
                        $status = match($statusRaw) {
                            'present', 'hadir' => 'H',
                            'sick', 'sakit' => 'S',
                            'permission', 'izin' => 'I',
                            'absent', 'late', 'alpa', 'terlambat' => 'A',
                            default => '-'
                        };
                    @endphp
                    <td>{{ $status }}</td>
                @empty
                    <td>-</td>
                @endforelse
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>