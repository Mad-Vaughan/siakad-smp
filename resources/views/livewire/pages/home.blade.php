@php
    $settings = App\Settings\WebsiteSetting::resolveWithFallback();
    $news = []; // Tetap kosong dulu sampe lo nemu nama Modelnya
@endphp

<div class="relative w-full flex-1 flex flex-col items-center justify-center py-12 px-4 overflow-hidden">
    
    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-blue-400/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-cyan-400/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 w-full max-w-4xl bg-white rounded-[2.5rem] shadow-2xl p-8 md:p-16 text-center border border-slate-100 mb-16">
        
        <div class="inline-flex items-center justify-center p-4 bg-slate-50 rounded-2xl mb-8 shadow-inner border border-slate-100">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--site-primary)">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
        </div>

        <h2 class="text-4xl md:text-6xl font-black text-slate-900 mb-6 tracking-tight">
            Sistem Informasi <br/>
            <span class="text-gradient">Akademik Terpadu</span>
        </h2>

        <p class="text-lg text-slate-500 mb-12 max-w-2xl mx-auto leading-relaxed">
            {{ $settings->hero_description ?? 'Selamat datang di portal layanan administrasi akademik. Akses menu admin dan guru untuk mengelola data sekolah secara efisien dan transparan.' }}
        </p>

        <a href="{{ url('/admin') }}" class="inline-flex items-center justify-center gap-3 px-10 py-4 text-lg font-bold text-white transition-all rounded-full shadow-lg hover:shadow-xl hover:-translate-y-1 hover:scale-105 btn-gradient">
            Masuk ke Sistem
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
        </a>
    </div>

    <div class="relative z-10 w-full max-w-5xl">
        <div class="text-center mb-10">
            <h3 class="text-2xl font-bold text-slate-800">Berita Acara Terkini</h3>
            <div class="h-1.5 w-16 mx-auto mt-4 rounded-full btn-gradient"></div>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            @forelse($news as $item)
                @empty
                <div class="col-span-3 text-center py-16 bg-white/60 backdrop-blur-md rounded-3xl border border-slate-200 border-dashed shadow-sm">
                    <div class="mx-auto w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l5 5v11a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="text-slate-500 font-medium">Belum ada berita acara yang diterbitkan hari ini.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>