<footer class="bg-slate-900 text-white mt-16">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16 grid gap-12 lg:grid-cols-3">
        <div class="space-y-4">
            <p class="text-2xl font-semibold">{{ $settings->site_name }}</p>
            <p class="text-sm text-slate-300 leading-relaxed">{{ $settings->footer_message }}</p>
            <div class="flex flex-wrap gap-3">
                @foreach ($socialLinks as $social)
                    @php($url = Str::startsWith($social['url'], ['http://', 'https://']) ? $social['url'] : url($social['url']))
                    <a href="{{ $url }}" target="_blank" rel="noopener" class="px-3 py-1.5 rounded-full border border-white/30 text-xs font-semibold uppercase tracking-wide hover:bg-white hover:text-slate-900">
                        {{ $social['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        <div>
            <p class="text-sm font-semibold text-slate-400 uppercase tracking-widest">Navigasi</p>
            <ul class="mt-4 space-y-2 text-sm text-slate-300">
                @foreach ($navLinks as $link)
                    <li>
                        <a href="{{ route($link['route']) }}" class="hover:text-white transition-colors">
                            {{ $link['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div>
            <p class="text-sm font-semibold text-slate-400 uppercase tracking-widest">Kontak</p>
            <ul class="mt-4 space-y-2 text-sm text-slate-300">
                @if ($settings->address)
                    <li>{{ $settings->address }}</li>
                @endif
                @if ($settings->phone)
                    <li>Telp: {{ $settings->phone }}</li>
                @endif
                @if ($settings->email)
                    <li>Email: {{ $settings->email }}</li>
                @endif
            </ul>
        </div>
    </div>
    <div class="border-t border-white/10 py-4 text-center text-xs text-slate-400">&copy; {{ now()->year }} {{ $settings->site_name }}. All rights reserved.</div>
</footer>
