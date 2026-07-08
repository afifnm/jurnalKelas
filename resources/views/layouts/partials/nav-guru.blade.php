@php
    $currentRoute = request()->route()?->getName() ?? '';
    $isIsiJurnalActive = in_array($currentRoute, ['guru.jurnal.create', 'guru.jurnal.edit'], true);
    $isJurnalActive = str_starts_with($currentRoute, 'guru.jurnal') && ! $isIsiJurnalActive;
    $menuClass = 'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all';
    $activeClass = 'bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400';
    $inactiveClass = 'text-slate-600 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800 hover:text-slate-800 dark:hover:text-zinc-100';
@endphp

<p class="px-3 mb-1.5 text-[10px] font-semibold text-slate-400 dark:text-zinc-600 uppercase tracking-widest">Menu</p>

<a href="{{ route('guru.dashboard') }}"
   class="{{ $menuClass }} {{ str_starts_with($currentRoute, 'guru.dashboard') ? $activeClass : $inactiveClass }}">
    <i data-lucide="layout-dashboard" class="w-4 h-4 flex-shrink-0"></i>
    <span>Dashboard</span>
</a>

<a href="{{ route('guru.jadwal.index') }}"
   class="{{ $menuClass }} {{ str_starts_with($currentRoute, 'guru.jadwal') ? $activeClass : $inactiveClass }}">
    <i data-lucide="calendar-clock" class="w-4 h-4 flex-shrink-0"></i>
    <span>Jadwal Mengajar</span>
</a>

<a href="{{ route('guru.jurnal.create') }}"
   class="{{ $menuClass }} {{ $isIsiJurnalActive ? $activeClass : $inactiveClass }}">
    <i data-lucide="square-pen" class="w-4 h-4 flex-shrink-0"></i>
    <span>Isi Jurnal</span>
</a>

<a href="{{ route('guru.jurnal.index') }}"
   class="{{ $menuClass }} {{ $isJurnalActive ? $activeClass : $inactiveClass }}">
    <i data-lucide="notebook" class="w-4 h-4 flex-shrink-0"></i>
    <span>Jurnal</span>
</a>

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="{{ $menuClass }} {{ $inactiveClass }} w-full text-left hover:!bg-red-50 dark:hover:!bg-red-950/20 hover:!text-red-600 dark:hover:!text-red-400">
        <i data-lucide="log-out" class="w-4 h-4 flex-shrink-0"></i>
        <span>Logout</span>
    </button>
</form>
