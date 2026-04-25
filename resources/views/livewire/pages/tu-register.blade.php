@php
    use App\Enums\Gender;
@endphp

<x-page-wrapper class="space-y-16">
    <section class="max-w-2xl mx-auto px-6 lg:px-8 pt-16 text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.4em] text-site-secondary">Tata Usaha</p>
        <h1 class="mt-4 text-4xl font-semibold text-slate-900">Pendaftaran Akun Tata Usaha</h1>
        <p class="mt-6 text-base text-slate-600 pb-8">Silakan isi formulir di bawah untuk membuat akun Tata Usaha. Setelah terdaftar, Anda dapat login ke panel admin.</p>
    </section>

    <section class="max-w-xl mx-auto px-6 lg:px-8 pb-16">
        <div class="rounded-3xl border border-slate-100 bg-white p-8 shadow-sm">
            @if (session()->has('success'))
                <div class="mb-6 rounded-lg bg-green-50 p-4 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <form wire:submit="register" class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                    <input type="text" wire:model="name" id="name" class="mt-1 block w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-site-primary focus:outline-none focus:ring-1 focus:ring-site-primary" placeholder="Masukkan nama lengkap..." required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                    <input type="email" wire:model="email" id="email" class="mt-1 block w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-site-primary focus:outline-none focus:ring-1 focus:ring-site-primary" placeholder="Masukkan email..." required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="gender" class="block text-sm font-medium text-slate-700">Jenis Kelamin</label>
                    <select wire:model="gender" id="gender" class="mt-1 block w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-site-primary focus:outline-none focus:ring-1 focus:ring-site-primary" required>
                        <option value="">Pilih jenis kelamin...</option>
                        @foreach (Gender::cases() as $gender)
                            <option value="{{ $gender->value }}">{{ $gender->getLabel() }}</option>
                        @endforeach
                    </select>
                    @error('gender')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-slate-700">Alamat</label>
                    <textarea wire:model="address" id="address" rows="3" class="mt-1 block w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-site-primary focus:outline-none focus:ring-1 focus:ring-site-primary" placeholder="Masukkan alamat..."></textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">Kata Sandi</label>
                    <input type="password" wire:model="password" id="password" class="mt-1 block w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-site-primary focus:outline-none focus:ring-1 focus:ring-site-primary" placeholder="Minimal 8 karakter..." required>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi Kata Sandi</label>
                    <input type="password" wire:model="password_confirmation" id="password_confirmation" class="mt-1 block w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-site-primary focus:outline-none focus:ring-1 focus:ring-site-primary" placeholder="Masukkan ulang kata sandi..." required>
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full rounded-full bg-site-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-site-secondary">
                    Daftar
                </button>

                <p class="text-center text-sm text-slate-600">
                    Sudah punya akun?
                    <a href="{{ url('/admin/login') }}" class="font-medium text-site-primary hover:underline">Login di sini</a>
                </p>
            </form>
        </div>
    </section>
</x-page-wrapper>