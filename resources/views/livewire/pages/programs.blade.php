@php
    $tracks = [
        ['title' => 'Program Akademik Unggulan', 'desc' => 'Pendalaman literasi numerasi, kelas olimpiade, dan riset sains mini untuk siswa yang ingin tantangan lebih.'],
        ['title' => 'Program Kreatif Digital', 'desc' => 'Belajar desain grafis, produksi konten, podcast, hingga kolaborasi dengan industri kreatif.'],
        ['title' => 'Program Leadership & Service', 'desc' => 'OSIS, MPK, dan kegiatan pelayanan masyarakat yang menumbuhkan empati serta kecakapan komunikasi.'],
    ];
    $enrichment = [
        'Bimbingan minat karier sejak kelas VII dengan mentoring personal.',
        'Kelas bilingual untuk mapel Sains & Matematika.',
        'Kolaborasi perguruan tinggi untuk lokakarya STEAM.',
    ];
@endphp

<x-page-wrapper class="space-y-16">
    <section class="max-w-4xl mx-auto px-6 lg:px-8 pt-16 text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.4em] text-site-secondary">Program</p>
        <h1 class="mt-4 text-4xl font-semibold text-slate-900">Jalur pembelajaran sesuai potensi siswa.</h1>
        <p class="mt-6 text-base text-slate-600 py-8">Guru-guru kami merancang jalur bertahap agar siswa dapat mengeksplorasi minat tanpa meninggalkan fondasi akademik penting.</p>
    </section>

    <section class="max-w-6xl mx-auto px-6 lg:px-8 py-8">
        <div class="grid gap-6 md:grid-cols-3">
            @foreach ($tracks as $track)
                <article class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-slate-900">{{ data_get($track, 'title') }}</h2>
                    <p class="mt-3 text-sm text-slate-600 leading-relaxed">{{ data_get($track, 'desc') }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="max-w-4xl mx-auto px-6 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-[0.4em] text-site-secondary">Pengayaan</p>
            <h2 class="mt-3 text-3xl font-semibold text-slate-900">Pendampingan tambahan untuk seluruh siswa.</h2>
            <div class="mt-6 space-y-3">
                @foreach ($enrichment as $item)
                    <div class="flex gap-3 text-sm text-slate-600">
                        <span class="mt-1 h-2 w-2 rounded-full bg-site-primary"></span>
                        <p>{{ $item }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-page-wrapper>
