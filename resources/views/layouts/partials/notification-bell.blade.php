<div x-data="{ bellOpen: false }" class="relative" @click.outside="bellOpen = false">

    {{-- Bell Button --}}
    <button @click="bellOpen = !bellOpen; if(bellOpen) $dispatch('reinit-icons')"
            title="Notifikasi"
            class="relative flex items-center justify-center w-8 h-8 rounded-lg
                   text-slate-500 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800
                   hover:text-slate-700 dark:hover:text-zinc-200 transition-colors">
        <i data-lucide="bell" class="w-4 h-4"></i>
        @if($notifData['count'] > 0)
        <span class="absolute -top-1 -right-1 flex items-center justify-center
                     min-w-[16px] h-4 px-1 rounded-full
                     bg-amber-500 text-white text-[10px] font-bold leading-none">
            {{ $notifData['count'] > 9 ? '9+' : $notifData['count'] }}
        </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="bellOpen"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-1"
         class="absolute right-0 top-full mt-2 w-72 bg-white dark:bg-zinc-900
                border border-slate-200 dark:border-zinc-700 rounded-2xl shadow-xl z-50"
         style="display:none">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-zinc-700/50">
            <div class="flex items-center gap-2">
                <i data-lucide="bell" class="w-3.5 h-3.5 text-amber-500"></i>
                <span class="text-sm font-semibold text-slate-800 dark:text-white">Notifikasi</span>
            </div>
            @if($notifData['count'] > 0)
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400">
                {{ $notifData['count'] }} pending
            </span>
            @endif
        </div>

        {{-- Items --}}
        <div class="max-h-64 overflow-y-auto py-1">
            @if(empty($notifData['items']))
            <div class="flex flex-col items-center justify-center py-8 text-slate-400 dark:text-zinc-600">
                <i data-lucide="check-circle-2" class="w-7 h-7 mb-2 opacity-50"></i>
                <p class="text-xs font-medium">Tidak ada notifikasi</p>
            </div>
            @else
            @foreach($notifData['items'] as $item)
            <div class="px-4 py-2.5 hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors">
                @if(! empty($item['route']))
                <a href="{{ $item['route'] }}" @click="bellOpen = false" class="block">
                    <div class="flex items-start gap-2.5">
                        <span class="mt-1.5 w-1.5 h-1.5 rounded-full bg-amber-500 flex-shrink-0"></span>
                        <p class="text-xs text-slate-700 dark:text-zinc-300 leading-relaxed">{{ $item['text'] }}</p>
                    </div>
                </a>
                @else
                <div class="flex items-start gap-2.5">
                    <span class="mt-1.5 w-1.5 h-1.5 rounded-full bg-amber-500 flex-shrink-0"></span>
                    <p class="text-xs text-slate-700 dark:text-zinc-300 leading-relaxed">{{ $item['text'] }}</p>
                </div>
                @endif
            </div>
            @endforeach
            @endif
        </div>

        {{-- Footer untuk guru --}}
        @if(($notifData['role'] ?? null) === 'guru' && $notifData['count'] > 0)
        <div class="px-4 py-3 border-t border-slate-100 dark:border-zinc-700/50">
            <a href="{{ route('guru.jurnal.create') }}"
               class="block w-full text-center text-xs font-semibold text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 transition-colors"
               @click="bellOpen = false">
                Isi jurnal sekarang &rarr;
            </a>
        </div>
        @endif

    </div>
</div>
