@php
    use App\Settings\WebsiteSetting;
    use Illuminate\Support\Facades\Storage;
    $settings = WebsiteSetting::resolveWithFallback();
    $logoUrl = $settings->site_logo ? Storage::url($settings->site_logo) : null;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $settings->site_name }} - Sistem Pengelolaan Nilai Dan Presensi</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        <style>
            :root {
                --site-primary: {{ $settings->theme_primary_color ?? '#2563eb' }};
                --site-secondary: {{ $settings->theme_secondary_color ?? '#0ea5e9' }};
            }
            /* CSS Sakti Biar Kaga Patah Warnanya */
            .btn-gradient { background: linear-gradient(135deg, var(--site-primary), var(--site-secondary)); }
            .text-gradient { 
                background: linear-gradient(135deg, var(--site-primary), var(--site-secondary));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
        </style>
    </head>
    <body class="font-sans antialiased min-h-screen flex flex-col bg-slate-50 text-slate-800">
        
        <header class="bg-white shadow-sm py-4 px-6 md:px-12 flex justify-between items-center sticky top-0 z-50 border-b border-slate-100">
            <div class="flex items-center gap-4">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" class="h-12 w-12 rounded-full object-cover shadow-sm">
                @else
                    <div class="h-10 w-10 rounded-xl btn-gradient text-white flex items-center justify-center font-bold text-lg shadow-md" >MI</div>
                @endif
                <div>
                    <h1 class="font-bold text-xl tracking-tight text-slate-900 uppercase">{{ $settings->site_name }}</h1>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-widest">Sistem Pengelolaan Nilai Dan Absensi</p>
                </div>
            </div>
            <a href="{{ url('/admin') }}" class="btn-gradient px-6 py-2.5 rounded-full text-white font-bold text-sm shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all hidden md:block">
                Login Admin
            </a>
        </header>

        <main class="flex-1 flex flex-col">
            {{ $slot }}
        </main>

        <footer class="bg-white border-t border-slate-200 py-6 text-center text-sm text-slate-500">
            &copy; {{ date('Y') }} {{ $settings->site_name }}. Hak Cipta Dilindungi.
        </footer>
        @livewireScripts
    </body>
</html>