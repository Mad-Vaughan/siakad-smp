@php
    use App\Settings\WebsiteSetting;

    $settings = WebsiteSetting::resolveWithFallback();
    $contacts = [
        ['label' => 'Email', 'value' => $settings->email, 'hint' => 'Tim administrasi akan merespons dalam 1x24 jam.'],
        ['label' => 'Telepon', 'value' => $settings->phone, 'hint' => 'Jam layanan Senin-Jumat 08.00 - 15.00 WIB.'],
        ['label' => 'Alamat', 'value' => $settings->address, 'hint' => 'Janji temu dapat dijadwalkan sebelum kunjungan.'],
    ];
@endphp

<x-page-wrapper class="space-y-16">
    <section class="max-w-4xl mx-auto px-6 lg:px-8 pt-16 text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.4em] text-site-secondary">Kontak</p>
        <h1 class="mt-4 text-4xl font-semibold text-slate-900">Terhubung dengan {{ $settings->site_name }}.</h1>
        <p class="mt-6 text-base text-slate-600 pb-8">Beragam kanal komunikasi tersedia untuk membantu kebutuhan informasi akademik maupun PPDB.</p>
    </section>

    <section class="max-w-6xl mx-auto px-6 lg:px-8 py-8">
        <div class="grid gap-6 md:grid-cols-3">
            @foreach ($contacts as $contact)
                @continue(blank(data_get($contact, 'value')))
                <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">
                    <p class="text-sm font-semibold text-site-secondary">{{ data_get($contact, 'label') }}</p>
                    <p class="mt-2 text-xl font-semibold text-slate-900">{{ data_get($contact, 'value') }}</p>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ data_get($contact, 'hint') }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="max-w-4xl mx-auto px-6 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-[0.4em] text-site-secondary">Butuh Balasan Cepat?</p>
            <form class="mt-6 grid gap-4">
                <div class="grid gap-2">
                    <label class="text-sm font-semibold text-slate-700" for="name">Nama</label>
                    <input id="name" type="text" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm focus-ring-site-primary" placeholder="Nama lengkap" />
                </div>
                <div class="grid gap-2">
                    <label class="text-sm font-semibold text-slate-700" for="email">Email</label>
                    <input id="email" type="email" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm focus-ring-site-primary" placeholder="email@contoh.com" />
                </div>
                <div class="grid gap-2">
                    <label class="text-sm font-semibold text-slate-700" for="message">Pesan</label>
                    <textarea id="message" rows="4" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm focus-ring-site-primary" placeholder="Ceritakan kebutuhan Anda"></textarea>
                </div>
                <button type="button" class="rounded-full bg-site-primary px-5 py-2.5 text-sm font-semibold text-white">Kirim via Email</button>
            </form>
        </div>
    </section>
</x-page-wrapper>
