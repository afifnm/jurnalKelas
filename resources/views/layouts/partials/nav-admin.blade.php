@php
    $currentRoute = request()->route()->getName();
    function navItem($href, $icon, $label, $active) {
        $base = 'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150';
        $cls = $active
            ? $base . ' bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400'
            : $base . ' text-slate-600 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800 hover:text-slate-800 dark:hover:text-zinc-100';
        return "<a href=\"$href\" class=\"$cls\"><i data-lucide=\"$icon\" class=\"w-4 h-4 flex-shrink-0\"></i><span>$label</span></a>";
    }
@endphp

<p class="px-3 mb-1.5 text-[10px] font-semibold text-slate-400 dark:text-zinc-600 uppercase tracking-widest">Menu</p>

{!! navItem(route('admin.dashboard'), 'layout-dashboard', 'Dashboard', str_starts_with($currentRoute, 'admin.dashboard')) !!}
{!! navItem(route('admin.jurnal.index'), 'notebook-pen', 'Jurnal Guru', str_starts_with($currentRoute, 'admin.jurnal')) !!}
{!! navItem(route('admin.jadwal.index'), 'calendar-clock', 'Jadwal Pelajaran', str_starts_with($currentRoute, 'admin.jadwal')) !!}

<p class="px-3 mt-4 mb-1.5 text-[10px] font-semibold text-slate-400 dark:text-zinc-600 uppercase tracking-widest">Master Data</p>

{!! navItem(route('admin.users.index'), 'users', 'Pengguna', str_starts_with($currentRoute, 'admin.users')) !!}
{!! navItem(route('admin.kelas.index'), 'school', 'Kelas', str_starts_with($currentRoute, 'admin.kelas')) !!}
{!! navItem(route('admin.mapel.index'), 'book-marked', 'Mata Pelajaran', str_starts_with($currentRoute, 'admin.mapel')) !!}
{!! navItem(route('admin.tahun-ajaran.index'), 'calendar-range', 'Tahun Ajaran', str_starts_with($currentRoute, 'admin.tahun-ajaran')) !!}
