<!DOCTYPE html>
<html lang="id"
    x-data="appLayout()"
    x-init="init()"
    :class="dark ? 'dark' : ''"
    class="h-full preload">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Jurnal Kelas') — {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#FACC15">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Jurnal Kelas">
    <link rel="apple-touch-icon" href="/icon-192.png">
    <link rel="icon" type="image/webp" href="{{ asset('logo.webp') }}">

    <!-- Suppress dark mode flash: read localStorage BEFORE paint -->
    <script>
        (function() {
            if (localStorage.getItem('dark') === 'true') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-50 dark:bg-zinc-950 font-sans antialiased" style="font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;">

<div class="flex min-h-screen" x-data="{ sidebarOpen: false }">

    <!-- Overlay mobile -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         class="fixed inset-0 z-20 bg-black/40 lg:hidden" x-transition.opacity></div>

    <!-- Sidebar -->
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-30 w-64 flex flex-col bg-white dark:bg-zinc-900 border-r border-slate-200/80 dark:border-zinc-700/50 shadow-xl lg:shadow-none lg:translate-x-0 lg:static lg:z-auto transition-transform duration-200">

        <!-- Logo -->
        <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-200/80 dark:border-zinc-700/50">
            <img src="{{ asset('logo.webp') }}" alt="Logo" class="w-9 h-9 object-contain">
            <div>
                <p class="text-sm font-bold text-slate-800 dark:text-white leading-tight">Jurnal Kelas</p>
                <p class="text-[10px] text-slate-400 dark:text-zinc-500 uppercase tracking-wider font-medium">
                    {{ auth()->user()->getRoleNames()->first() }}
                </p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            @auth
                @if(auth()->user()->hasRole('admin'))
                    @include('layouts.partials.nav-admin')
                @elseif(auth()->user()->hasRole('guru'))
                    @include('layouts.partials.nav-guru')
                @elseif(auth()->user()->hasRole('ks'))
                    @include('layouts.partials.nav-ks')
                @endif
            @endauth
        </nav>

        <!-- User Profile -->
        <div class="px-3 py-4 border-t border-slate-200/80 dark:border-zinc-700/50">
            <div class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-zinc-800 cursor-pointer group">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-400 to-orange-400 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">{{ auth()->user()->nama }}</p>
                    <p class="text-xs text-slate-400 dark:text-zinc-500 truncate">@{{ auth()->user()->username }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Logout"
                        class="text-slate-400 dark:text-zinc-500 hover:text-red-500 dark:hover:text-red-400 transition-colors">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0">

        <!-- Top Header -->
        <header class="flex items-center gap-4 px-4 lg:px-6 py-3.5 bg-white dark:bg-zinc-900 border-b border-slate-200/80 dark:border-zinc-700/50 sticky top-0 z-10 backdrop-blur-sm bg-white/95 dark:bg-zinc-900/95">

            <!-- Mobile menu button -->
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-200">
                <i data-lucide="menu" class="w-5 h-5"></i>
            </button>

            <!-- Breadcrumb -->
            <div class="flex-1 min-w-0">
                <h1 class="text-base font-semibold text-slate-800 dark:text-white truncate">@yield('page-title', 'Dashboard')</h1>
                @hasSection('breadcrumb')
                <nav class="flex items-center gap-1.5 text-xs text-slate-400 dark:text-zinc-500 mt-0.5">
                    @yield('breadcrumb')
                </nav>
                @endif
            </div>

            <!-- Header actions -->
            <div class="flex items-center gap-2">
                <!-- Dark mode toggle -->
                <button @click="toggleDark()"
                    class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors">
                    <template x-if="dark"><i data-lucide="sun" class="w-4 h-4" x-effect="$nextTick(() => lucide.createIcons())"></i></template>
                    <template x-if="!dark"><i data-lucide="moon" class="w-4 h-4" x-effect="$nextTick(() => lucide.createIcons())"></i></template>
                </button>

                <!-- Tahun ajaran badge -->
                @php $tahunAktif = \App\Models\TahunAjaran::aktif(); @endphp
                @if($tahunAktif)
                <span class="hidden md:inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 rounded-lg text-xs font-medium border border-amber-200/60 dark:border-amber-800/30">
                    <i data-lucide="calendar" class="w-3 h-3"></i>
                    {{ $tahunAktif->nama }} {{ $tahunAktif->semester }}
                </span>
                @endif
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-4 lg:p-6 page-content">
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 class="mb-4 flex items-center gap-3 px-4 py-3 bg-green-50 dark:bg-green-950/40 border border-green-200 dark:border-green-800/40 text-green-700 dark:text-green-400 rounded-xl text-sm">
                <i data-lucide="check-circle-2" class="w-4 h-4 flex-shrink-0"></i>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 class="mb-4 flex items-center gap-3 px-4 py-3 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800/40 text-red-700 dark:text-red-400 rounded-xl text-sm">
                <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i>
                {{ session('error') }}
            </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<!-- Libraries CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
function appLayout() {
    return {
        dark: false,
        init() {
            this.dark = localStorage.getItem('dark') === 'true';
            // Remove preload class after Alpine sets dark mode — prevents transition flash
            this.$nextTick(() => {
                document.documentElement.classList.remove('preload');
                lucide.createIcons();
            });
        },
        toggleDark() {
            this.dark = !this.dark;
            localStorage.setItem('dark', this.dark);
            // Update html class immediately for non-Alpine elements
            document.documentElement.classList.toggle('dark', this.dark);
        }
    }
}

// Re-init lucide icons after Alpine updates DOM
document.addEventListener('alpine:initialized', () => lucide.createIcons());
document.addEventListener('reinit-icons', () => lucide.createIcons());
</script>

@stack('scripts')
</body>
</html>
