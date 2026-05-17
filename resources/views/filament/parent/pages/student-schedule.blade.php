<x-filament-panels::page>
    @if(!$activeClassroom)
        <x-filament::section>
            <div style="display: flex; align-items: center; gap: 10px; color: #dc2626;">
                <x-filament::icon icon="heroicon-o-exclamation-triangle" style="width: 24px; height: 24px;" />
                <span style="font-size: 1.1rem; font-weight: bold;">Anda belum terdaftar di kelas manapun pada Tahun Ajaran yang aktif.</span>
            </div>
        </x-filament::section>
    @else
        <div class="print:hidden">
            <div style="text-align: right; margin-bottom: 25px;">
                <x-filament::button icon="heroicon-o-printer" onclick="window.print()" color="primary" size="lg">
                    Cetak Jadwal Pelajaran
                </x-filament::button>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                    @if(isset($schedules[$hari]))
                        <x-filament::section compact>
                            <x-slot name="heading">
                                <div style="color: var(--primary-600); font-weight: 800; text-transform: uppercase; letter-spacing: 1px; display: flex; align-items: center; gap: 10px;">
                                    <x-filament::icon icon="heroicon-o-calendar-days" style="width: 20px; height: 20px;" />
                                    {{ $hari }}
                                </div>
                            </x-slot>

                            <div style="display: flex; flex-direction: column; gap: 20px; padding: 10px 0;">
                                @foreach($schedules[$hari] as $jadwal)
                                    <div style="border-left: 4px solid var(--primary-600); padding-left: 15px; margin-bottom: 5px;">
                                        <div style="font-size: 0.85rem; font-weight: bold; color: var(--primary-600); margin-bottom: 5px;">
                                            {{ \Carbon\Carbon::parse($jadwal->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->end_time)->format('H:i') }}
                                        </div>
                                        <div style="font-size: 1.1rem; font-weight: 800; line-height: 1.2; margin-bottom: 4px;">
                                            {{ $jadwal->subject->name }}
                                        </div>
                                        <div style="font-size: 0.85rem; opacity: 0.7; display: flex; align-items: center; gap: 5px;">
                                            <x-filament::icon icon="heroicon-m-user" style="width: 14px; height: 14px;" />
                                            {{ $jadwal->subject->teacher->name ?? '-' }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </x-filament::section>
                    @endif
                @endforeach
            </div>
        </div>

        <div id="print-official-area" style="display: none;">
            
            <div style="text-align: center; border-bottom: 2px solid black; padding-bottom: 10px; margin-bottom: 20px;">
                <h1 style="font-size: 18px; font-weight: bold; margin: 0; color: black !important; letter-spacing: 1px;">JADWAL PELAJARAN SISWA</h1>
                <h2 style="font-size: 13px; font-weight: normal; margin: 5px 0 0 0; color: black !important;">TAHUN AJARAN {{ $activeClassroom->academicYear->name }} ({{ strtoupper($activeClassroom->academicYear->semester) }})</h2>
            </div>

            <table class="force-table-print" style="width: 100%; margin-bottom: 20px; font-size: 12px; color: black !important;">
                <tr>
                    <td style="width: 12%; padding: 2px 0;">Nama Siswa</td>
                    <td style="width: 2%; padding: 2px 0;">:</td>
                    <td style="width: 40%; padding: 2px 0;"><strong>{{ auth()->user()->name }}</strong></td>
                    <td style="width: 10%; padding: 2px 0;">Kelas</td>
                    <td style="width: 2%; padding: 2px 0;">:</td>
                    <td style="width: 34%; padding: 2px 0;"><strong>{{ $activeClassroom->name }}</strong></td>
                </tr>
            </table>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                    @if(isset($schedules[$hari]))
                        <div style="break-inside: avoid; page-break-inside: avoid;">
                            <table class="force-table-print" style="width: 100%; border-collapse: collapse; font-size: 11px; color: black !important;">
                                <thead>
                                    <tr>
                                        <th colspan="3" style="background-color: #f3f4f6 !important; border: 1px solid black !important; padding: 6px; text-transform: uppercase; text-align: center; font-weight: bold; -webkit-print-color-adjust: exact; print-color-adjust: exact;">{{ $hari }}</th>
                                    </tr>
                                    <tr>
                                        <th style="border: 1px solid black !important; padding: 6px; background-color: white !important; width: 25%; text-align: center;">Waktu</th>
                                        <th style="border: 1px solid black !important; padding: 6px; background-color: white !important; width: 45%; text-align: center;">Mata Pelajaran</th>
                                        <th style="border: 1px solid black !important; padding: 6px; background-color: white !important; width: 30%; text-align: center;">Guru</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($schedules[$hari] as $jadwal)
                                        <tr>
                                            <td style="border: 1px solid black !important; padding: 6px; text-align: center;">{{ \Carbon\Carbon::parse($jadwal->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($jadwal->end_time)->format('H:i') }}</td>
                                            <td style="border: 1px solid black !important; padding: 6px;">{{ $jadwal->subject->name }}</td>
                                            <td style="border: 1px solid black !important; padding: 6px;">{{ $jadwal->subject->teacher->name ?? '-' }}</td>
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
                @page { size: A4 portrait; margin: 1cm; }
                
                /* Sembunyikan web */
                body * { visibility: hidden; }
                .fi-sidebar, .fi-topbar, .print\:hidden { display: none !important; }
                
                /* Munculin area cetak */
                #print-official-area, #print-official-area * { 
                    visibility: visible !important; 
                }
                
                /* Posisikan cetakan ke ujung kertas */
                #print-official-area {
                    position: absolute;
                    left: 0; top: 0; width: 100%;
                    display: block !important;
                    font-family: 'Times New Roman', Times, serif !important;
                    background: white !important;
                }

                /* JURUS DEWA: PAKSA TABEL JADI TABEL BIAR KAGA NUMPUK! */
                .force-table-print { display: table !important; width: 100% !important; }
                .force-table-print thead { display: table-header-group !important; }
                .force-table-print tbody { display: table-row-group !important; }
                .force-table-print tr { display: table-row !important; }
                .force-table-print th, .force-table-print td { display: table-cell !important; }
            }
        </style>
    @endif
</x-filament-panels::page>