@php
    $weeklyClubs = [
        ['name' => 'STEAM Lab', 'day' => 'Senin', 'time' => '15.30 - 17.30', 'desc' => 'Eksperimen sains terapan, coding, dan printing 3D.'],
        ['name' => 'English Public Speaking', 'day' => 'Selasa', 'time' => '15.30 - 17.00', 'desc' => 'Latihan story telling, debate, dan podcast.'],
        ['name' => 'Modern Choir', 'day' => 'Rabu', 'time' => '16.00 - 18.00', 'desc' => 'Aransemen lagu daerah dan kontemporer dengan koreografi ringan.'],
        ['name' => 'Ecopreneur Club', 'day' => 'Kamis', 'time' => '15.30 - 17.30', 'desc' => 'Membuat produk ramah lingkungan dan pameran mini market.'],
    ];
    $events = [
        ['title' => 'Muara Creative Week', 'date' => 'Maret 2025', 'desc' => 'Pameran karya project based learning dari seluruh kelas.'],
        ['title' => 'Student Leadership Camp', 'date' => 'Juli 2025', 'desc' => 'Camp karakter di daerah pegunungan untuk OSIS dan MPK.'],
        ['title' => 'Green Action Day', 'date' => 'September 2025', 'desc' => 'Kolaborasi dengan warga sekitar untuk urban farming dan bank sampah.'],
    ];
@endphp

<x-page-wrapper class="space-y-16">
    <section class="max-w-4xl mx-auto px-6 lg:px-8 pt-16 text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.4em] text-site-secondary">Kegiatan Siswa</p>
        <h1 class="mt-4 text-4xl font-semibold text-slate-900">Eksplorasi bakat yang terstruktur & menyenangkan.</h1>
        <p class="mt-6 text-base text-slate-600 pb-8">Setiap pekan, siswa bebas memilih klub aktivitas dengan pendampingan mentor sesuai passion. Kalender kegiatan juga diisi event kolaboratif yang memperkuat karakter.</p>
    </section>

    <section class="max-w-6xl mx-auto px-6 lg:px-8 py-8">
        <div class="grid gap-6 md:grid-cols-2">
            @foreach ($weeklyClubs as $club)
                <article class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between text-sm font-semibold text-slate-500">
                        <span>{{ data_get($club, 'day') }}</span>
                        <span>{{ data_get($club, 'time') }}</span>
                    </div>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ data_get($club, 'name') }}</h2>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ data_get($club, 'desc') }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="max-w-6xl mx-auto px-6 lg:px-8 py-8">
            <p class="text-sm font-semibold uppercase tracking-[0.4em] text-site-secondary">Agenda Besar</p>
            <div class="mt-8 space-y-6">
                @foreach ($events as $event)
                    <div class="rounded-2xl border border-slate-100 bg-site-bg p-6">
                        <p class="text-sm font-semibold text-site-primary">{{ data_get($event, 'date') }}</p>
                        <p class="text-xl font-semibold text-slate-900">{{ data_get($event, 'title') }}</p>
                        <p class="text-sm text-slate-600 leading-relaxed">{{ data_get($event, 'desc') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-page-wrapper>
