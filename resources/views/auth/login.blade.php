<!DOCTYPE html>
<html lang="id"
    x-data="{ dark: localStorage.getItem('dark') === 'true' }"
    :class="dark ? 'dark' : ''"
    class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Jurnal Kelas</title>
    <link rel="icon" type="image/webp" href="{{ asset('logo.webp') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.getItem('dark') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="min-h-screen bg-amber-50 dark:bg-zinc-950 font-sans antialiased"
      style="font-family: 'Plus Jakarta Sans', sans-serif;">

<div class="min-h-screen flex flex-col lg:items-center lg:justify-center lg:p-6">

    {{-- Card wrapper: full-screen on mobile, centered rounded card on desktop --}}
    <div class="w-full lg:max-w-sm flex flex-col min-h-screen lg:min-h-0
                lg:rounded-3xl lg:overflow-hidden
                lg:shadow-2xl lg:shadow-amber-300/30 dark:lg:shadow-zinc-950/60">

        {{-- ── Hero (amber) ── --}}
        <div class="relative overflow-hidden
                    bg-gradient-to-br from-yellow-300 via-amber-400 to-amber-500
                    px-8 pt-16 pb-10 lg:pt-12 lg:pb-10 flex-shrink-0">

            {{-- Decorative circles --}}
            <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-white/10 pointer-events-none"></div>
            <div class="absolute -bottom-8 -left-8 w-40 h-40 rounded-full bg-white/10 pointer-events-none"></div>
            <div class="absolute top-1/3 right-6 w-16 h-16 rounded-full bg-white/10 pointer-events-none"></div>

            <div class="relative z-10">

                {{-- Logo + Nama Sekolah --}}
                <div class="flex items-center gap-3.5 mb-7">
                    <img src="{{ asset('logo.webp') }}" alt="Logo SMK Pemnas Sukoharjo"
                         class="w-14 h-14 object-contain drop-shadow-md flex-shrink-0">
                    <div class="leading-tight">
                        <p class="text-white font-extrabold text-[13px] leading-snug">
                            SMK Pembangunan Nasional
                        </p>
                        <p class="text-amber-100 font-semibold text-xs tracking-wide">
                            Sukoharjo
                        </p>
                    </div>
                </div>

                {{-- Headline --}}
                <h1 class="text-[1.65rem] font-extrabold text-white leading-tight mb-2">
                    Sistem Jurnal<br>Mengajar Digital
                </h1>
                <p class="text-amber-100/90 text-[13px] leading-relaxed">
                    Pantau kepatuhan pengisian dan kinerja mengajar guru secara real-time.
                </p>

            </div>
        </div>

        {{-- ── Form (putih) ── --}}
        <div class="flex-1 bg-white dark:bg-zinc-900 flex flex-col px-8 pt-7 pb-8">

            {{-- Dark mode toggle --}}
            <div class="flex justify-end mb-5">
                <button @click="dark = !dark; localStorage.setItem('dark', dark)"
                    class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs
                           text-slate-500 dark:text-zinc-400
                           hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors">
                    <i data-lucide="sun"  class="w-3.5 h-3.5" x-show="dark"></i>
                    <i data-lucide="moon" class="w-3.5 h-3.5" x-show="!dark"></i>
                    <span x-text="dark ? 'Light Mode' : 'Dark Mode'"></span>
                </button>
            </div>

            {{-- Judul form --}}
            <h2 class="text-xl font-bold text-slate-800 dark:text-white mb-1">Masuk ke Akun</h2>
            <p class="text-slate-400 dark:text-zinc-500 text-xs mb-6 leading-relaxed">
                Gunakan kode guru dan password yang diberikan administrator.
            </p>

            {{-- Error --}}
            @if($errors->any())
            <div class="mb-5 flex items-start gap-2.5 px-3.5 py-3
                        bg-red-50 dark:bg-red-950/40
                        border border-red-200 dark:border-red-800/40
                        rounded-xl text-sm">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5"></i>
                <p class="text-red-700 dark:text-red-400">{{ $errors->first() }}</p>
            </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login.post') }}" class="space-y-4 flex-1">
                @csrf

                <div>
                    <label for="username"
                           class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                        <i data-lucide="user" class="w-3.5 h-3.5 inline mr-1 -mt-0.5"></i>Kode Guru
                    </label>
                    <input type="text" id="username" name="username"
                           value="{{ old('username') }}"
                           class="input-field"
                           placeholder="Masukkan kode guru"
                           autocomplete="username"
                           required autofocus>
                </div>

                <div>
                    <label for="password"
                           class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                        <i data-lucide="lock" class="w-3.5 h-3.5 inline mr-1 -mt-0.5"></i>Password
                    </label>
                    <div class="relative" x-data="{ showPass: false }">
                        <input :type="showPass ? 'text' : 'password'"
                               id="password" name="password"
                               class="input-field pr-11"
                               placeholder="Masukkan password"
                               autocomplete="current-password"
                               required>
                        <button type="button" @click="showPass = !showPass"
                            class="absolute right-3 top-1/2 -translate-y-1/2
                                   text-slate-400 hover:text-slate-600 dark:hover:text-zinc-300
                                   transition-colors">
                            <i :data-lucide="showPass ? 'eye-off' : 'eye'" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-0.5">
                    <input type="checkbox" id="remember" name="remember"
                           class="w-4 h-4 rounded border-slate-300 dark:border-zinc-600
                                  text-amber-500 focus:ring-amber-400 focus:ring-offset-0">
                    <label for="remember" class="text-sm text-slate-600 dark:text-zinc-400">Ingat saya</label>
                </div>

                <button type="submit"
                    class="w-full py-3 mt-1
                           bg-amber-400 hover:bg-amber-500
                           text-zinc-900 font-bold text-sm
                           rounded-xl shadow-sm hover:shadow-md shadow-amber-300/40
                           transition-all duration-150 active:scale-[0.98]
                           flex items-center justify-center gap-2">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    Masuk
                </button>
            </form>

            <p class="mt-8 text-center text-[11px] text-slate-400 dark:text-zinc-600">
                Jurnal Kelas &copy; {{ date('Y') }}.
                Hubungi administrator jika lupa password.
            </p>

        </div>
    </div>
</div>

<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
    document.addEventListener('alpine:initialized', () => lucide.createIcons());
</script>
</body>
</html>
