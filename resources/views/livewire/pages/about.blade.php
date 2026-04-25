@php
    use App\Settings\WebsiteSetting;

    $settings = WebsiteSetting::resolveWithFallback();
    $missions = [
        'Menumbuhkan karakter unggul melalui pembelajaran berbasis nilai dan keteladanan.',
        'Mendampingi siswa mengembangkan potensi akademik, seni, olahraga, dan teknologi.',
        'Mewujudkan lingkungan belajar yang kolaboratif, aman, dan ramah digital.',
    ];
    $pillars = [
        ['title' => 'Akademik Adaptif', 'description' => 'Implementasi Kurikulum Merdeka yang diperkaya dengan project STEAM dan literasi riset sejak kelas VII.'],
        ['title' => 'Karakter & Kepemimpinan', 'description' => 'Program mentoring, organisasi siswa, dan bakti sosial menjaga kepedulian terhadap sekitar.'],
        ['title' => 'Lingkungan Digital', 'description' => 'Setiap kelas ditunjang perangkat multimedia, akses LMS, dan literasi AI tingkat dasar.'],
    ];
    $timeline = [
        ['year' => '2008', 'title' => 'Berdiri', 'caption' => 'SMP Muara Indonesia berdiri dengan 3 rombel dan 12 tenaga pendidik.'],
        ['year' => '2014', 'title' => 'Akreditasi A', 'caption' => 'Memperoleh akreditasi A dan mulai mengembangkan laboratorium STEAM.'],
        ['year' => '2020', 'title' => 'Digital Learning', 'caption' => 'Implementasi pembelajaran hybrid dan penambahan studio kreatif.'],
        ['year' => '2024', 'title' => 'Kampus Hijau', 'caption' => 'Revitalisasi ruang terbuka dan program green habit untuk seluruh siswa.'],
    ];
@endphp

<x-page-wrapper class="space-y-20">
    <section class="max-w-5xl lg:max-w-6xl mx-auto px-6 lg:px-8 py-8 pt-10">
        <p class="text-sm font-semibold tracking-[0.4em] uppercase text-site-secondary">Tentang Sekolah</p>
        <h1 class="mt-4 text-4xl lg:text-5xl font-semibold text-slate-900">{{ $settings->site_name }} menghadirkan pendidikan holistik berkarakter.</h1>
        <p class="mt-6 text-lg text-slate-600 leading-relaxed">Sejak berdiri, kami berkomitmen memberi pengalaman belajar yang relevan, hangat, dan menumbuhkan rasa percaya diri. Seluruh aktivitas dirancang untuk mendampingi siswa tumbuh sebagai pembelajar sepanjang hayat.</p>
    </section>

    <section class="bg-white">
        <div class="lg:max-w-6xl mx-auto px-6 lg:px-8 py-8 py-14 grid gap-10 lg:grid-cols-2">
            <div>
                <p class="text-sm font-semibold text-site-secondary uppercase tracking-[0.4em]">Misi Sekolah</p>
                <h2 class="mt-3 text-3xl font-semibold text-slate-900">Bergerak bersama keluarga dan masyarakat.</h2>
            </div>
            <div class="space-y-3">
                @foreach ($missions as $mission)
                    <div class="flex gap-4">
                        <span class="mt-1 h-2 w-2 rounded-full bg-site-primary"></span>
                        <p class="text-base text-slate-600">{{ $mission }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-6 lg:px-8 py-8">
        <div class="grid gap-6 md:grid-cols-3">
            @foreach ($pillars as $pillar)
                <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">
                    <p class="text-sm font-semibold text-site-primary uppercase tracking-[0.25em]">Pilar</p>
                    <h3 class="mt-3 text-xl font-semibold text-slate-900">{{ data_get($pillar, 'title') }}</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ data_get($pillar, 'description') }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="bg-site-bg py-16">
        <div class="max-w-5xl lg:max-w-6xl mx-auto px-6 lg:px-8 py-8">
            <p class="text-sm font-semibold text-site-secondary uppercase tracking-[0.4em]">Perjalanan</p>
            <h2 class="mt-3 text-3xl font-semibold text-slate-900">Tonggak perkembangan sekolah.</h2>
            <div class="mt-10 space-y-8 border-l border-slate-200 pl-6">
                @foreach ($timeline as $item)
                    <div class="relative">
                        <span class="absolute -left-[29px] h-4 w-4 rounded-full border-2 border-site-primary bg-white"></span>
                        <p class="text-sm font-semibold text-site-primary">{{ data_get($item, 'year') }}</p>
                        <p class="text-xl font-semibold text-slate-900">{{ data_get($item, 'title') }}</p>
                        <p class="text-sm text-slate-600 leading-relaxed">{{ data_get($item, 'caption') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-page-wrapper>
