@php
    $articles = [
        ['title' => 'Launching Studio Podcast Siswa', 'date' => '11 Oktober 2024', 'excerpt' => 'Fasilitas baru ini digunakan untuk project literasi digital kelas VIII dan tim broadcasting sekolah.'],
        ['title' => 'Tim Robotik Juara Nasional', 'date' => '20 September 2024', 'excerpt' => 'Robot “Aru” meraih juara I kategori penyelamat dengan sensor visi buatan siswa.'],
        ['title' => 'Kolaborasi Kurikulum Hijau', 'date' => '02 Agustus 2024', 'excerpt' => 'Siswa belajar pertanian urban bersama komunitas lokal dan membuat modul pembelajaran hijau.'],
    ];
@endphp

<x-page-wrapper class="space-y-16">
    <section class="max-w-4xl mx-auto px-6 lg:px-8 pt-16 text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.4em] text-site-secondary">Berita</p>
        <h1 class="mt-4 text-4xl font-semibold text-slate-900">Cerita terbaru dari ruang belajar kami.</h1>
        <p class="mt-6 text-base text-slate-600 pb-8">Ikuti kabar mengenai inovasi program, prestasi siswa, hingga agenda kolaborasi terbaru SMP Muara Indonesia.</p>
    </section>

    <section class="max-w-5xl mx-auto px-6 lg:px-8 space-y-6">
        @foreach ($articles as $article)
            <article class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">
                <p class="text-sm font-semibold text-site-primary">{{ data_get($article, 'date') }}</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-900">{{ data_get($article, 'title') }}</h2>
                <p class="mt-3 text-sm text-slate-600 leading-relaxed">{{ data_get($article, 'excerpt') }}</p>
                <a href="#" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-site-primary">Baca selengkapnya <span aria-hidden="true">&rarr;</span></a>
            </article>
        @endforeach
    </section>
</x-page-wrapper>
