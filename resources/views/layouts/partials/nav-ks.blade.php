@php $currentRoute = request()->route()->getName(); @endphp

<p class="px-3 mb-1.5 text-[10px] font-semibold text-slate-400 dark:text-zinc-600 uppercase tracking-widest">Menu</p>

<a href="{{ route('ks.dashboard') }}"
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all {{ str_starts_with($currentRoute, 'ks.dashboard') ? 'bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400' : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800 hover:text-slate-800 dark:hover:text-zinc-100' }}">
    <i data-lucide="layout-dashboard" class="w-4 h-4 flex-shrink-0"></i>
    <span>Dashboard</span>
</a>

<a href="{{ route('ks.jurnal.index') }}"
   class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all {{ str_starts_with($currentRoute, 'ks.jurnal') ? 'bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400' : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800 hover:text-slate-800 dark:hover:text-zinc-100' }}">
    <i data-lucide="notebook-text" class="w-4 h-4 flex-shrink-0"></i>
    <span>Jurnal Guru</span>
</a>
