{{-- Partial: Panel buat jadwal baru (digunakan di desktop sidebar & mobile bottom sheet) --}}

<div class="card p-4 border-t-4 border-purple-500">
    <h3 class="text-xs font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400 mb-3 flex items-center gap-1.5">
        <i data-lucide="hand" class="w-3.5 h-3.5"></i> Buat Jadwal Baru
    </h3>

    <div class="space-y-3 mb-4">
        <div>
            <label class="block text-xs font-medium text-slate-600 dark:text-zinc-400 mb-1">Mata Pelajaran</label>
            <select x-model="source.mapel_id"
                    class="w-full text-sm border border-slate-200 dark:border-zinc-700 rounded-lg px-2.5 py-2 bg-white dark:bg-zinc-800 text-slate-700 dark:text-zinc-200 focus:outline-none focus:border-purple-400">
                <option value="">-- Pilih Mapel --</option>
                @foreach($mapel as $m)
                    <option value="{{ $m->id }}" data-nama="{{ $m->nama }}">{{ $m->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 dark:text-zinc-400 mb-1">Guru</label>
            <select x-model="source.guru_id"
                    class="w-full text-sm border border-slate-200 dark:border-zinc-700 rounded-lg px-2.5 py-2 bg-white dark:bg-zinc-800 text-slate-700 dark:text-zinc-200 focus:outline-none focus:border-purple-400">
                <option value="">-- Pilih Guru --</option>
                @foreach($guru as $g)
                    <option value="{{ $g->id }}" data-nama="{{ $g->nama }}">{{ $g->nama }}</option>
                @endforeach
            </select>
        </div>

        <button type="button" @click="addToCart()"
                class="w-full bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white text-sm font-semibold py-2.5 px-4 rounded-lg flex items-center justify-center gap-1.5 transition-colors">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah ke Kartu
        </button>
    </div>

    {{-- Divider --}}
    <div class="border-t border-slate-100 dark:border-zinc-800 mb-3"></div>

    {{-- Cart header --}}
    <div class="flex items-center justify-between mb-2">
        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">Kartu Siap Pakai</p>
        <span x-show="cart.length > 0"
              x-text="cart.length + ' kartu'"
              class="text-[10px] font-medium text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/30 px-1.5 py-0.5 rounded-full"></span>
    </div>

    {{-- Cart Items --}}
    <div class="space-y-2 max-h-[45vh] lg:max-h-[380px] overflow-y-auto pr-0.5">
        <template x-for="(item, index) in cart" :key="index">
            <div draggable="true"
                 @dragstart="dragStartCart($event, item)"
                 @click="selectCartItem(item, index)"
                 :class="{
                     'ring-2 ring-purple-500 ring-offset-1 dark:ring-offset-zinc-900 bg-purple-100 dark:bg-purple-900/50 border-purple-400 dark:border-purple-600':
                         selectedSourceItem?.type === 'new' && selectedSourceItem?.cartIndex === index,
                     'bg-purple-50 dark:bg-purple-900/30 border-purple-300 dark:border-purple-700 hover:bg-purple-100 dark:hover:bg-purple-900/50':
                         !(selectedSourceItem?.type === 'new' && selectedSourceItem?.cartIndex === index)
                 }"
                 class="cursor-pointer select-none group p-3 border-2 border-dashed rounded-xl transition-all duration-150 active:scale-[0.97]">

                <div class="flex items-center gap-2.5">
                    {{-- Drag handle --}}
                    <div class="w-7 h-7 rounded-lg bg-purple-200 dark:bg-purple-800 flex items-center justify-center flex-shrink-0 text-purple-700 dark:text-purple-300 pointer-events-none">
                        <i data-lucide="grip-vertical" class="w-3.5 h-3.5"></i>
                    </div>

                    {{-- Label --}}
                    <div class="min-w-0 flex-1 pointer-events-none">
                        <p class="text-xs font-bold text-purple-900 dark:text-purple-100 leading-tight line-clamp-1" x-text="item.mapel_nama"></p>
                        <p class="text-[10px] text-purple-700 dark:text-purple-400 mt-0.5 line-clamp-1 flex items-center gap-1">
                            <i data-lucide="user" class="w-2.5 h-2.5 flex-shrink-0"></i>
                            <span x-text="item.guru_nama"></span>
                        </p>
                    </div>

                    {{-- Delete button --}}
                    <button @click.prevent.stop="removeFromCart(index)"
                            class="w-6 h-6 bg-red-100 hover:bg-red-500 dark:bg-red-950/50 dark:hover:bg-red-600
                                   text-red-500 dark:text-red-400 hover:text-white
                                   rounded-lg flex items-center justify-center transition-colors flex-shrink-0
                                   border border-red-200 dark:border-red-900/50 hover:border-transparent shadow-sm"
                            title="Hapus dari kartu">
                        <i data-lucide="trash-2" class="w-3 h-3"></i>
                    </button>
                </div>

                {{-- Selected indicator --}}
                <div x-show="selectedSourceItem?.type === 'new' && selectedSourceItem?.cartIndex === index"
                     class="mt-2 flex items-center gap-1 text-[10px] font-semibold text-purple-700 dark:text-purple-300">
                    <i data-lucide="mouse-pointer-click" class="w-3 h-3"></i>
                    <span>Ketuk slot jam tujuan</span>
                </div>
            </div>
        </template>

        {{-- Empty state --}}
        <div x-show="cart.length === 0"
             class="py-6 px-4 border-2 border-dashed border-slate-200 dark:border-zinc-700 rounded-xl text-center">
            <div class="w-10 h-10 bg-slate-100 dark:bg-zinc-800 rounded-xl flex items-center justify-center mx-auto mb-2">
                <i data-lucide="package-open" class="w-5 h-5 text-slate-300 dark:text-zinc-600"></i>
            </div>
            <p class="text-xs font-medium text-slate-400 dark:text-zinc-500">Belum ada kartu</p>
            <p class="text-[10px] text-slate-300 dark:text-zinc-600 mt-0.5">Pilih Mapel & Guru di atas</p>
        </div>
    </div>

    {{-- Hint --}}
    <p x-show="cart.length > 0"
       class="text-[10px] text-center text-slate-400 dark:text-zinc-600 mt-2.5 leading-snug">
        <span class="hidden lg:inline">Seret atau klik kartu → klik slot</span>
        <span class="lg:hidden">Ketuk kartu, lalu ketuk slot jam</span>
    </p>
</div>
