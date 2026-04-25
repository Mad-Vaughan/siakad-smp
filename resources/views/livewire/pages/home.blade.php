@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $stats = collect($settings->highlight_stats ?? []);
    $features = collect($settings->excellence_features ?? []);
    $programs = collect($settings->program_cards ?? []);
    $testimonials = collect($settings->testimonials ?? []);
    $heroHighlights = collect($settings->hero_highlights ?? []);
    $heroMedia = $settings->hero_media ? Storage::url($settings->hero_media) : null;

    $resolveUrl = static function (?string $value): ?string {
        if (blank($value)) {
            return null;
        }

        return Str::startsWith($value, ['http://', 'https://']) ? $value : url($value);
    };
@endphp

<x-page-wrapper class="space-y-24 lg:space-y-32">

<div class="space-y-24 lg:space-y-32">
    <section class="relative isolate overflow-hidden">
        <div class="absolute inset-0 -z-10">
            <div class="absolute inset-0 opacity-80" style="background-image: radial-gradient(circle at 10% 20%, color-mix(in srgb, var(--site-secondary) 35%, transparent), transparent 55%), radial-gradient(circle at 80% 10%, color-mix(in srgb, var(--site-primary) 30%, transparent), transparent 60%);"></div>
        </div>
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16 lg:py-24 grid gap-12 lg:grid-cols-[minmax(0,1fr)_minmax(0,0.95fr)] items-center">
            <div class="space-y-6">
                <p class="text-sm font-semibold uppercase tracking-[0.4em] text-site-secondary">{{ $settings->hero_tagline }}</p>
                <h1 class="text-4xl lg:text-5xl font-semibold leading-tight text-slate-900">{{ $settings->hero_title }}</h1>
                <p class="text-base lg:text-lg text-slate-600 leading-relaxed">{{ $settings->hero_description }}</p>
                <div class="flex flex-wrap gap-4">
                    @php($primaryCta = $resolveUrl($settings->hero_primary_cta_url))
                    @php($secondaryCta = $resolveUrl($settings->hero_secondary_cta_url))
                    @if ($primaryCta)
                        <a href="{{ $primaryCta }}" class="btn-primary inline-flex items-center gap-2">
                            {{ $settings->hero_primary_cta_label }}
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    @endif
                    @if ($secondaryCta)
                        <a href="{{ $secondaryCta }}" class="btn-secondary inline-flex items-center gap-2">
                            {{ $settings->hero_secondary_cta_label }}
                        </a>
                    @endif
                </div>
                @if ($heroHighlights->isNotEmpty())
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 mt-10">
                        @foreach ($heroHighlights as $highlight)
                            <div class="flex items-start gap-3 rounded-2xl bg-white/80 backdrop-blur px-4 py-3 shadow-sm">
                                <span class="mt-1 h-2.5 w-2.5 rounded-full bg-site-primary"></span>
                                <p class="text-sm text-slate-700">{{ $highlight }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="relative">
                <div class="absolute -top-8 -right-4 h-32 w-32 rounded-full blur-[90px]" style="background-color: color-mix(in srgb, var(--site-primary) 55%, transparent);"></div>
                <div class="relative rounded-[2.5rem] border border-slate-100 bg-white shadow-2xl overflow-hidden">
                    @if ($heroMedia)
                        <img src="{{ $heroMedia }}" alt="Sekolah" class="h-full w-full object-cover" />
                    @else
                        <div class="p-10 text-white" style="background-image: linear-gradient(145deg, color-mix(in srgb, var(--site-primary) 85%, transparent), color-mix(in srgb, var(--site-secondary) 65%, transparent));">
                            <p class="text-2xl font-semibold">{{ $settings->site_name }}</p>
                            <p class="mt-4 text-sm text-white/80 max-w-md">{{ $settings->hero_description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</x-page-wrapper>


