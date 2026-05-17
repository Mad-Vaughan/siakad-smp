<x-filament-panels::page>
    <div class="print:hidden">
        <div style="text-align: right; margin-bottom: 25px;">
            <x-filament::button icon="heroicon-o-printer" onclick="window.print()" color="primary" size="lg">
                Cetak Jadwal Mengajar
            </x-filament::button>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                @if(isset($schedules[$hari]) && $schedules[$hari]->isNotEmpty())
                    <x-filament::section compact>
                        <x-slot name="heading">
                            <div style="color: var(--primary-600); font-weight: 800; text-transform: uppercase; display: flex; align-items: center; gap: 10px;">
                                <x-filament::icon icon="heroicon-o-clock" style="width: 20px; height: 20px;" />
                                {{ $hari }}
                            </div>
                        </x-slot>

                        <div style="display: flex; flex-direction: column; gap: 20px; padding: 10px 0;">
                            @foreach($schedules[$hari] as $jadwal)
                                <div style="border-left: 4px solid var(--primary-600); padding-left: 15px;">
                                    <div style="font-size: 0.85rem; font-weight: bold; color: var(--primary-600); margin-bottom: 5px;">
                                        {{ \Carbon\Carbon::parse($jadwal->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->end_time)->format('H:i') }}
                                    </div>
                                    <div style="font-size: 1.1rem; font-weight: 800; line-height: 1.2; margin-bottom: 4px;">
                                        {{ $jadwal->subject->name }}
                                    </div>
                                    <div style="font-size: 0.9rem; opacity: 0.8; display: flex; align-items: center; gap: 6px;">
                                        <x-filament::icon icon="heroicon-o-academic-cap" style="width: 16px; height: 16px;" />
                                        Kelas: <strong style="color: var(--primary-500);">{{ $jadwal->classroom->name }}</strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </x-filament::section>
                @endif
            @endforeach
        </div>
    </div>

    <div id="teacher-print-area" style="display: none;">
        <div style="text-align: center; border-bottom: 2px solid black; padding-bottom: 10px; margin-bottom: 20px;">
            <h1 style="font-size: 20px; font-weight: bold; margin: 0; color: black !important;">JADWAL MENGAJAR GURU</h1>
            <p style="margin: 5px 0 0 0; color: black !important;">TAHUN AJARAN AKTIF</p>
        </div>

        <table style="width: 100%; margin-bottom: 20px; font-size: 13px; color: black !important;">
            <tr>
                <td style="width: 15%; padding: 2px 0;">Nama Guru</td><td style="width: 2%;">:</td><td style="padding: 2px 0;"><strong>{{ auth()->user()->name }}</strong></td>
            </tr>
        </table>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                @if(isset($schedules[$hari]) && $schedules[$hari]->isNotEmpty())
                    <div style="break-inside: avoid; page-break-inside: avoid;">
                        <table class="force-table-print" style="width: 100%; border-collapse: collapse; font-size: 11px; color: black !important; border: 1px solid black !important;">
                            <thead>
                                <tr>
                                    <th colspan="3" style="background: #f3f4f6 !important; border: 1px solid black !important; padding: 6px; -webkit-print-color-adjust: exact; print-color-adjust: exact; text-transform: uppercase;">{{ $hari }}</th>
                                </tr>
                                <tr>
                                    <th style="border: 1px solid black !important; padding: 5px; width: 25%; text-align: center;">Waktu</th>
                                    <th style="border: 1px solid black !important; padding: 5px; width: 45%; text-align: center;">Mata Pelajaran</th>
                                    <th style="border: 1px solid black !important; padding: 5px; width: 30%; text-align: center;">Kelas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schedules[$hari] as $jadwal)
                                    <tr>
                                        <td style="border: 1px solid black !important; padding: 5px; text-align: center;">{{ \Carbon\Carbon::parse($jadwal->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($jadwal->end_time)->format('H:i') }}</td>
                                        <td style="border: 1px solid black !important; padding: 5px;">{{ $jadwal->subject->name }}</td>
                                        <td style="border: 1px solid black !important; padding: 5px; text-align: center;">{{ $jadwal->classroom->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <style>
        @media print {
            body * { visibility: hidden; }
            #teacher-print-area, #teacher-print-area * { visibility: visible !important; }
            #teacher-print-area { position: absolute; left: 0; top: 0; width: 100%; display: block !important; }
            @page { size: A4 portrait; margin: 1cm; }
            
            /* Paksa layout biar kaga hancur pas print */
            #teacher-print-area > div:last-child { display: grid !important; grid-template-columns: 1fr 1fr !important; gap: 20px !important; }
            .force-table-print { display: table !important; width: 100% !important; }
            .force-table-print thead { display: table-header-group !important; }
            .force-table-print tbody { display: table-row-group !important; }
            .force-table-print tr { display: table-row !important; }
            .force-table-print th, .force-table-print td { display: table-cell !important; }
            
            .fi-sidebar, .fi-topbar, .print\:hidden { display: none !important; }
            * { color: black !important; font-family: 'Times New Roman', Times, serif !important; }
        }
    </style>
</x-filament-panels::page>