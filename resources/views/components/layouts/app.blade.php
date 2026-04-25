@php
    use App\Settings\WebsiteSetting;
    use App\Support\SiteNavigation;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $settings = WebsiteSetting::resolveWithFallback();
    $logoUrl = $settings->site_logo ? Storage::url($settings->site_logo) : null;
    $navLinks = SiteNavigation::primary();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ $settings->hero_description }}">
        <title>{{ $title ?? $settings->site_name }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        <style>
            :root {
                --site-primary: {{ $settings->theme_primary_color }};
                --site-secondary: {{ $settings->theme_secondary_color }};
                --site-accent: {{ $settings->theme_accent_color }};
                --site-bg: {{ $settings->theme_background_color }};
                --site-text: {{ $settings->theme_text_color }};
            }

            .text-site-primary {
                color: var(--site-primary);
            }

            .text-site-secondary {
                color: var(--site-secondary);
            }

            .text-site-body {
                color: var(--site-text);
            }

            .bg-site-primary {
                background-color: var(--site-primary);
            }

            .bg-site-secondary {
                background-color: var(--site-secondary);
            }

            .bg-site-bg {
                background-color: var(--site-bg);
            }

            .bg-site-primary-soft {
                background-color: color-mix(in srgb, var(--site-primary) 15%, transparent);
            }

            .bg-site-secondary-soft {
                background-color: color-mix(in srgb, var(--site-secondary) 15%, transparent);
            }

            .border-site-primary {
                border-color: var(--site-primary);
            }

            .focus-ring-site-primary:focus {
                outline: none;
                border-color: var(--site-primary);
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--site-primary) 25%, transparent);
            }

            .page-wrapper {
                width: 100%;
                padding-inline: clamp(1.5rem, 5vw, 4rem);
                padding-block: clamp(2.5rem, 6vw, 4.5rem);
            }

            @media (min-width: 1024px) {
                .page-wrapper {
                    padding-inline: clamp(2.5rem, 5vw, 5rem);
                }
            }

            .btn-primary {
                background: linear-gradient(135deg, var(--site-primary), var(--site-secondary));
                color: #fff;
                border-radius: 999px;
                padding: 0.85rem 1.75rem;
                font-weight: 600;
                transition: opacity 0.2s ease;
            }

            .btn-primary:hover {
                opacity: 0.9;
            }

            .btn-secondary:hover {
                background: #fff;
                color: var(--site-primary);
            }
        </style>
    </head>
    <body class="font-sans antialiased" style="background-color: var(--site-bg); color: var(--site-text);">
        <div class="min-h-screen flex flex-col">
            <header class="sticky top-0 z-40 bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-6 lg:px-8">
                    <div class="flex items-center justify-between py-4">
                        <a href="{{ route('home') }}" class="flex items-center gap-3">
                            @if ($logoUrl)
                                <img src="{{ $logoUrl }}" alt="{{ $settings->site_name }}" class="h-12 w-12 rounded-full object-cover border border-white/50 shadow" />
                            @else
                                <div class="h-12 w-12 rounded-full bg-site-primary text-white flex items-center justify-center font-semibold">
                                    {{ Str::of($settings->site_name)->split('/\s+/')->map(fn ($segment) => Str::upper(Str::substr($segment, 0, 1)))->take(2)->implode('') }}
                                </div>
                            @endif
                            <div>
                                <p class="text-base font-semibold text-slate-900">{{ $settings->site_name }}</p>
                                <p class="text-sm text-slate-500">{{ $settings->hero_tagline }}</p>
                            </div>
                        </a>
                        <nav class="hidden lg:flex items-center gap-6 text-sm font-medium">
                            @foreach ($navLinks as $link)
                                <a
                                    href="{{ route($link['route']) }}"
                                    @class([
                                        'transition-colors hover:text-site-primary',
                                        'text-site-primary' => request()->routeIs($link['route']),
                                        'text-slate-500' => ! request()->routeIs($link['route']),
                                    ])
                                >
                                    {{ $link['label'] }}
                                </a>
                            @endforeach
                        </nav>
                        <div class="hidden lg:flex items-center gap-3">
                            <a href="{{ url('/admin') }}" class="btn-primary">Login</a>
                        </div>
                        <button class="lg:hidden inline-flex items-center justify-center rounded-full border border-slate-200 p-2 text-slate-600" type="button" data-mobile-toggle>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 7.5h16.5M3.75 12h16.5m-16.5 4.5h16.5" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="lg:hidden hidden border-t border-slate-200 bg-white" data-mobile-menu>
                    <div class="px-6 py-4 space-y-1">
                        @foreach ($navLinks as $link)
                            <a
                                href="{{ route($link['route']) }}"
                                @class([
                                    'block px-3 py-2 rounded-md text-sm font-medium transition-colors',
                                    'text-white bg-site-primary' => request()->routeIs($link['route']),
                                    'text-slate-600 hover:bg-slate-50' => ! request()->routeIs($link['route']),
                                ])
                            >
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                        <a href="{{ route('admission') }}" class="block px-3 py-2 rounded-md text-sm font-semibold text-white bg-site-primary">PPDB</a>
                    </div>
                </div>
            </header>
            <main class="flex-1">
                {{ $slot }}
            </main>
            <livewire:footer />
        </div>
        @livewireScripts
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const toggle = document.querySelector('[data-mobile-toggle]');
                const menu = document.querySelector('[data-mobile-menu]');

                if (!toggle || !menu) {
                    return;
                }

                toggle.addEventListener('click', () => {
                    menu.classList.toggle('hidden');
                });
            });
        </script>
    </body>
</html>
