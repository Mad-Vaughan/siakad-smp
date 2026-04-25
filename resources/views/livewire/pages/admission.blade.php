@php
    use App\Settings\WebsiteSetting;

    $settings = WebsiteSetting::resolveWithFallback();
    $steps = [
        ['title' => 'Registrasi Online', 'desc' => 'Formulir dan unggahan dokumen awal tersedia melalui portal PPDB.'],
        ['title' => 'Verifikasi Berkas', 'desc' => 'Tim kami menghubungi orang tua untuk memastikan kelengkapan data.'],
        ['title' => 'Tes Observasi', 'desc' => 'Siswa mengikuti observasi akademik ringan dan sesi wawancara karakter.'],
        ['title' => 'Pengumuman & Daftar Ulang', 'desc' => 'Hasil akan dikirim via email dan dashboard. Lakukan daftar ulang sesuai jadwal.'],
    ];
    $requirements = [
        'Scan rapor semester terakhir (PDF).',
        'Akta kelahiran & kartu keluarga.',
        'Pas foto terbaru ukuran 3x4.',
        'Surat rekomendasi kepala sekolah asal (opsional).',
    ];
@endphp

<x-page-wrapper class="space-y-16">
    <section class="max-w-4xl mx-auto px-6 lg:px-8 pt-16 text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.4em] text-site-secondary">PPDB</p>
        <h1 class="mt-4 text-4xl font-semibold text-slate-900">Bergabung dengan {{ $settings->site_name }} dimulai dari sini.</h1>
        <p class="mt-6 text-base text-slate-600 pb-8">Kami membuka pendaftaran untuk siswa kelas VII yang siap tumbuh dalam ekosistem belajar inovatif. Semua proses dapat dilakukan secara daring.</p>
        <a href="mailto:{{ $settings->email }}" class="mt-6 inline-flex items-center gap-2 rounded-full bg-site-primary px-5 py-2.5 text-sm font-semibold text-white">
            Konsultasi PPDB
            <span aria-hidden="true">&rarr;</span>
        </a>
    </section>

    <section class="max-w-5xl mx-auto px-6 lg:px-8">
        <div class="grid gap-6 md:grid-cols-2">
            @foreach ($steps as $index => $step)
                <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">
                    <p class="text-sm font-semibold text-site-primary">Langkah {{ $index + 1 }}</p>
                    <h2 class="mt-2 text-2xl font-semibold text-slate-900">{{ data_get($step, 'title') }}</h2>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ data_get($step, 'desc') }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="max-w-4xl mx-auto px-6 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-[0.4em] text-site-secondary">Dokumen</p>
            <h2 class="mt-3 text-3xl font-semibold text-slate-900">Syarat yang perlu disiapkan.</h2>
            <div class="mt-6 space-y-3">
                @foreach ($requirements as $requirement)
                    <div class="flex gap-3 text-sm text-slate-600">
                        <span class="mt-1 h-2 w-2 rounded-full bg-site-primary"></span>
                        <p>{{ $requirement }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-page-wrapper>
