<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Nilai Peserta Didik</title>
    <style>
        @page {
            margin: 32px 36px;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #0f172a;
            font-size: 11.5px;
            line-height: 1.6;
        }

        .letterhead-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .logo-cell {
            width: 120px;
            vertical-align: top;
        }

        .logo-image {
            max-height: 90px;
            max-width: 110px;
        }

        .identity-cell {
            text-align: left;
        }

        .organization-name {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .organization-tagline {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .contact-line {
            font-size: 11px;
        }

        .letterhead-divider {
            border: none;
            border-top: 2px solid #0f172a;
            margin: 4px 0 18px;
        }

        .heading {
            text-align: center;
            margin-bottom: 16px;
        }

        .heading h1 {
            margin: 0;
            font-size: 16px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .heading p {
            margin: 4px 0 0;
            font-size: 12px;
        }

        .meta-table,
        .scores-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .meta-table td {
            padding: 4px 6px;
            vertical-align: top;
        }

        .meta-table td:first-child {
            width: 160px;
        }

        .scores-table th,
        .scores-table td {
            padding: 9px 8px;
            border: 1px solid #cbd5f5;
            text-align: left;
        }

        .scores-table th {
            background-color: #e2e8f0;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
        }

        .notes {
            margin-top: 28px;
            font-size: 11px;
        }

        .signature {
            margin-top: 48px;
            display: flex;
            justify-content: flex-end;
        }

        .signature-block {
            width: 240px;
            text-align: center;
        }

        .signature-block p {
            margin: 4px 0;
        }

        .signature-block .name {
            margin-top: 64px;
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    @php
        use Carbon\Carbon;

        $letterhead = \App\Settings\LetterheadSetting::resolveWithFallback();
        $printedAt = Carbon::now()->translatedFormat('d F Y H:i');
        $schoolName = $letterhead->organization_name ?? config('app.name', 'Sistem Informasi Akademik');
        $typeLabel = $assesment->type?->getLabel() ?? 'Penilaian';
        $footerNote = $letterhead->footer_note ?? 'Dokumen ini dicetak otomatis oleh sistem informasi akademik.';
    @endphp

    @include('prints.partials.letterhead', ['letterhead' => $letterhead])

    <div class="heading">
        <h1>{{ strtoupper($schoolName) }}</h1>
        <p>Laporan Nilai Peserta Didik</p>
    </div>

    <table class="meta-table">
        <tr>
            <td><strong>Mata Pelajaran</strong></td>
            <td>: {{ $assesment->subject->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Jenis Penilaian</strong></td>
            <td>: {{ $typeLabel }}</td>
        </tr>
        <tr>
            <td><strong>Kelas</strong></td>
            <td>: {{ optional($assesment->classroom)->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Pengampu/Wali Kelas</strong></td>
            <td>: {{ optional(optional($assesment->classroom)->teacher)->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Jumlah Peserta Didik</strong></td>
            <td>: {{ $assesment->studentAssesments->count() }} orang</td>
        </tr>
        <tr>
            <td><strong>Waktu Cetak</strong></td>
            <td>: {{ $printedAt }}</td>
        </tr>
    </table>

    <table class="scores-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Nama Peserta Didik</th>
                <th style="width: 120px;">NISN</th>
                <th style="width: 140px;">Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($assesment->studentAssesments as $record)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $record->student->name ?? '-' }}</td>
                    <td>{{ $record->student->nisn ?? '-' }}</td>
                    <td>{{ $record->score !== null ? number_format($record->score, 2, ',', '.') : 'Belum Diisi' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="notes">
        {{ $footerNote }}
    </p>

    <div class="signature">
        <div class="signature-block">
            <p>Wali Kelas</p>
            <p style="margin-top: 52px;">&nbsp;</p>
            <p class="name">{{ optional(optional($assesment->classroom)->teacher)->name ?? '______________' }}</p>
        </div>
    </div>
</body>
</html>
