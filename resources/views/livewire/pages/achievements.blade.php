@php
    use App\Settings\WebsiteSetting;

    $settings = WebsiteSetting::resolveWithFallback();
    $achievements = [
        ['title' => 'Medali Emas OSN IPA', 'year' => '2023', 'desc' => 'Tim sains SMP Muara meraih skor tertinggi tingkat provinsi.'],
        ['title' => 'Juara 1 Lomba Robotika Nasional', 'year' => '2024', 'desc' => 'Kolaborasi klub robotik dan coding menghasilkan robot penyelamat mini.'],
        ['title' => 'Sekolah Adiwiyata Kota', 'year' => '2022', 'desc' => 'Pengelolaan lingkungan berkelanjutan dengan bank sampah dan kebun hidroponik.'],
        ['title' => 'Best Modern Choir Competition', 'year' => '2023', 'desc' => 'Vocal Ensemble menampilkan aransemen lagu daerah modern dan meraih best performance.'],
    ];
    $highlightStats = [
        ['value' => '48', 'label' => 'Prestasi Nasional'],
        ['value' => '120', 'label' => 'Prestasi Kota/Kab'],
        ['value' => '15', 'label' => 'Kemitraan Kompetisi'],
    ];
@endphp

<x-page-wrapper class="space-y-16">
    <section class="max-w-4xl mx-auto px-6 lg:px-8 pt-16 text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.4em] text-site-secondary">Prestasi</p>
        <h1 class="mt-4 text-4xl font-semibold text-slate-900">Jejak prestasi siswa & guru {{ $settings->site_name }}.</h1>
        <p class="mt-6 text-base text-slate-600 pb-8">Kami percaya setiap siswa punya panggungnya. Prestasi akademik maupun non-akademik dipandu mentor profesional dengan ekosistem pelatihan yang konsisten.</p>
    </section>

    <section class="max-w-5xl mx-auto px-6 lg:px-8">
        <div class="grid gap-6 sm:grid-cols-3">
            @foreach ($highlightStats as $stat)
                <div class="rounded-2xl border border-slate-100 bg-white p-6 text-center shadow-sm">
                    <p class="text-3xl font-semibold text-site-primary">{{ data_get($stat, 'value') }}+</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700 uppercase tracking-[0.3em]">{{ data_get($stat, 'label') }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-6 lg:px-8 py-8">
        <div class="grid gap-6 md:grid-cols-2">
            @foreach ($achievements as $item)
                <article class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">
                    <p class="text-sm font-semibold text-site-secondary uppercase tracking-[0.4em]">{{ data_get($item, 'year') }}</p>
                    <h2 class="mt-2 text-2xl font-semibold text-slate-900">{{ data_get($item, 'title') }}</h2>
                    <p class="mt-3 text-sm text-slate-600 leading-relaxed">{{ data_get($item, 'desc') }}</p>
                </article>
            @endforeach
        </div>
    </section>
</x-page-wrapper>
