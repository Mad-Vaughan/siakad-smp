<x-filament-panels::page>
    <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 rounded-xl p-6 mb-8">
        <div class="flex items-center gap-2 mb-4">
            <x-heroicon-m-identification class="w-6 h-6 text-gray-400" />
            <h2 class="text-lg font-medium text-gray-950 dark:text-white">Identitas Siswa</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <span class="block text-sm text-gray-500 dark:text-gray-400">Nama Lengkap</span>
                <span class="block font-medium text-gray-950 dark:text-white">{{ $record->name }}</span>
            </div>
            <div>
                <span class="block text-sm text-gray-500 dark:text-gray-400">NISN</span>
                <span class="block font-medium text-gray-950 dark:text-white">{{ $record->nisn ?? '-' }}</span>
            </div>
            <div>
                <span class="block text-sm text-gray-500 dark:text-gray-400">Tempat, Tgl Lahir</span>
                <span class="block font-medium text-gray-950 dark:text-white">{{ $record->birth_place ?? '-' }}, {{ $record->date_of_birth ? \Carbon\Carbon::parse($record->date_of_birth)->format('d F Y') : '-' }}</span>
            </div>
            <div>
                <span class="block text-sm text-gray-500 dark:text-gray-400">Status</span>
                <span class="block font-medium {{ strtolower($record->active_status) === 'aktif' ? 'text-green-600 dark:text-green-400' : 'text-rose-600 dark:text-rose-400' }}">{{ strtolower($record->active_status) === 'aktif' ? 'aktif' : ($record->active_status ?? '-') }}</span>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-2 mb-4">
        <x-heroicon-o-clock class="w-6 h-6 text-gray-500" />
        <h2 class="text-lg font-medium text-gray-950 dark:text-white">Rekam Jejak Akademik (Per Semester)</h2>
    </div>

    @php
        $histories = \App\Models\StudentClassroom::where('student_id', $record->id)
            ->with(['classroom.academicYear', 'classroom.teacher'])
            ->get()
            ->sortByDesc('classroom.academicYear.name');
    @endphp

    @forelse($histories as $history)
        @php
            $presences = \App\Models\StudentPresence::where('student_id', $record->id)
                ->whereHas('presence', fn($q) => $q->where('classroom_id', $history->classroom_id))
                ->get();
        @endphp

        <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 rounded-xl p-6 mb-4">
            <div class="border-b border-gray-200 dark:border-gray-700 pb-3 mb-4">
                <h3 class="font-medium text-gray-950 dark:text-white">Kelas {{ $history->classroom->name ?? '-' }} | TP {{ $history->classroom->academicYear->name ?? '-' }}</h3>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="col-span-1 space-y-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg ring-1 ring-gray-950/5 dark:ring-white/10">
                        <span class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Informasi Kelas</span>
                        <span class="block text-sm text-gray-950 dark:text-white mb-1">Wali Kelas: {{ $history->classroom->teacher->name ?? '-' }}</span>
                        <span class="block text-sm text-gray-950 dark:text-white">Status: <span class="font-medium {{ $history->is_active ? 'text-green-600 dark:text-green-400' : 'text-rose-600 dark:text-rose-400' }}">{{ $history->is_active ? 'aktif' : 'arsip lama' }}</span></span>
                    </div>

                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg ring-1 ring-blue-950/5 dark:ring-blue-400/20">
                        <span class="block text-xs font-bold text-blue-600 dark:text-blue-400 uppercase mb-2">Ringkasan Absensi</span>
                        <div class="grid grid-cols-2 gap-2 text-sm text-gray-950 dark:text-white">
                            <div>Hadir: <span class="font-bold">{{ $presences->where('status', \App\Enums\PresenceStatus::PRESENT)->count() }}</span></div>
                            <div>Sakit: <span class="font-bold">{{ $presences->where('status', \App\Enums\PresenceStatus::SICK)->count() }}</span></div>
                            <div>Izin: <span class="font-bold">{{ $presences->where('status', \App\Enums\PresenceStatus::PERMISSION)->count() }}</span></div>
                            <div>Alpa: <span class="font-bold">{{ $presences->where('status', \App\Enums\PresenceStatus::ABSENT)->count() }}</span></div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                        <table class="min-w-full text-sm border-collapse">
                            <thead class="bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                <tr>
                                    <th class="px-4 py-3 text-left border-b border-gray-200 dark:border-gray-700">Mata Pelajaran</th>
                                    <th class="px-4 py-3 text-center border-b border-gray-200 dark:border-gray-700">Tipe</th>
                                    <th class="px-4 py-3 text-right border-b border-gray-200 dark:border-gray-700">Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $scores = \App\Models\StudentAssesment::where('student_id', $record->id)
                                        ->whereHas('assessment', fn($q) => $q->where('classroom_id', $history->classroom_id))
                                        ->with('assessment.subject')
                                        ->get();
                                @endphp
                                @forelse($scores as $score)
                                    <tr>
                                        <td class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">{{ $score->assessment->subject->name ?? 'Mapel' }}</td>
                                        <td class="px-4 py-3 text-center border-b border-gray-200 dark:border-gray-700 text-slate-500 dark:text-slate-400">{{ $score->assessment->type ?? '-' }}</td>
                                        <td class="px-4 py-3 text-right border-b border-gray-200 dark:border-gray-700 font-bold {{ $score->score < 75 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                            {{ $score->score }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-6 text-center text-sm text-slate-500 dark:text-slate-400 italic">Data nilai belum diinput oleh guru.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-gray-300 bg-white p-12 text-center shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-slate-200">
            <svg class="mb-6 h-12 w-12 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-base text-slate-600 dark:text-slate-400">Belum ada riwayat akademik yang tercatat di sistem.</p>
        </div>
    @endforelse
</x-filament-panels::page>