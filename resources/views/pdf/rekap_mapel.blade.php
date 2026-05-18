<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Presensi Mata Pelajaran</title>
    <style>
        /* 👇 KITA KECILIN BASE FONT BIAR LEGA JON 👇 */
        body { font-family: sans-serif; font-size: 11px; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid black; padding-bottom: 5px; }
        .info-table { width: 100%; margin-bottom: 15px; font-weight: bold; font-size: 11px; }
        
        /* 👇 JURUS KUNCI: table-layout fixed + word-wrap biar kolom dibagi rata anti-penyok! 👇 */
        .data-table { width: 100%; border-collapse: collapse; text-align: center; table-layout: fixed; word-wrap: break-word; }
        .data-table th, .data-table td { border: 1px solid black; padding: 4px 1px; font-size: 8px; }
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

    @php
        // 👇 HITUNG LEBAR KOLOM PERTEMUAN OTOMATIS (Sisa 72% dibagi jumlah pertemuan) 👇
        $totalPertemuan = count($pertemuan);
        $pertemuanWidth = $totalPertemuan > 0 ? (72 / $totalPertemuan) : 72;
    @endphp

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" width="6%">No</th>
                <th rowspan="2" width="22%">Nama Siswa</th>
                <th colspan="{{ $totalPertemuan == 0 ? 1 : $totalPertemuan }}">Pertemuan Ke-</th>
            </tr>
            <tr>
                @forelse($pertemuan as $index => $p)
                    <th width="{{ $pertemuanWidth }}%">P-{{ $index + 1 }}<br><small style="font-size: 7px; font-weight: normal;">({{ \Carbon\Carbon::parse($p->date)->format('d/m') }})</small></th>
                @empty
                    <th>Belum ada data</th>
                @endforelse
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="text-align: left; padding-left: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $student->name }}</td>
                
                @forelse($pertemuan as $p)
                    @php
                        $absen = $p->studentPresences->where('student_id', $student->id)->first();
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