<div>
    <div class="mb-6">
        <!-- Header & Controls -->
        <div class="relative rounded-3xl p-1 bg-gradient-to-b from-white to-slate-50 shadow-sm border border-slate-200 mb-8">
            <div class="absolute inset-0 bg-grid-slate-100 [mask-image:linear-gradient(0deg,white,rgba(255,255,255,0.6))] rounded-3xl"></div>
            <div class="relative bg-white/60 backdrop-blur-xl rounded-[1.4rem] p-6">
                <!-- Decoration -->
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-gradient-to-br from-emerald-500/10 to-teal-500/10 rounded-full blur-3xl opacity-60 pointer-events-none"></div>
                
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                    <!-- Title & Context -->
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2.5 bg-emerald-500/10 rounded-xl">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Barang Masuk</h2>
                        </div>
                        <p class="text-sm text-slate-500 ml-1">Riwayat penerimaan barang dan penambahan stok.</p>
                    </div>

                    <!-- Controls Group -->
                    <div class="flex flex-col sm:flex-row gap-4 flex-1 justify-end">
                        <div class="flex gap-2">
                            <!-- Month Filter -->
                            <div class="relative">
                                <select wire:model.live="month" class="appearance-none pl-4 pr-10 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 cursor-pointer shadow-sm hover:border-slate-300 transition-colors">
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                            </div>
                            
                            <!-- Year Filter -->
                             <div class="relative">
                                <select wire:model.live="year" class="appearance-none pl-4 pr-10 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 cursor-pointer shadow-sm hover:border-slate-300 transition-colors">
                                    @foreach(range(now()->year, now()->year - 3) as $y)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <a href="{{ route('barang-masuk.create') }}" class="group relative inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold text-white transition-all duration-200 bg-slate-900 font-pj rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 hover:shadow-lg hover:shadow-slate-900/30">
                            <span class="relative flex items-center gap-2">
                                <svg class="w-5 h-5 transition-transform duration-200 group-hover:rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Barang Masuk
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="bg-white rounded-[1.5rem] border border-slate-200/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden relative fade-in">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th class="px-6 py-5 text-xs font-bold text-slate-500 uppercase tracking-wider pl-8">No. Transaksi</th>
                            <th class="px-6 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal & Waktu</th>
                            <th class="px-6 py-5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Total Item</th>
                            <th class="px-6 py-5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Total Nilai</th>
                            <th class="px-6 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Dibuat Oleh</th>
                            <th class="px-6 py-5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($barangMasuks as $bm)
                            <tr class="group hover:bg-slate-50/80 transition-all duration-200">
                                <td class="px-6 py-4 pl-8">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                                             <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <span class="text-sm font-bold text-slate-800 group-hover:text-emerald-600 transition-colors">
                                            {{ $bm->no_barang_masuk }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-slate-700">{{ $bm->tanggal->format('d M Y') }}</span>
                                        <span class="text-xs text-slate-400">{{ $bm->tanggal->format('H:i') }} WIB</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-600 border border-blue-200">
                                        {{ $bm->total_items }} Item
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-mono text-sm font-medium text-slate-700">Rp {{ number_format($bm->total_value, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500 border border-slate-200">
                                            {{ substr($bm->createdUser?->name ?? '?', 0, 1) }}
                                        </div>
                                        <span class="text-sm text-slate-600">{{ $bm->createdUser?->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('barang-masuk.detail', $bm->id) }}" class="inline-flex items-center justify-center p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="relative w-20 h-20 mb-6">
                                            <div class="absolute inset-0 bg-emerald-100 rounded-full animate-pulse"></div>
                                            <div class="absolute inset-2 bg-white rounded-full flex items-center justify-center border border-emerald-50 shadow-sm">
                                                <svg class="w-8 h-8 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                            </div>
                                        </div>
                                        <h3 class="text-xl font-bold text-slate-900 mb-2">Belum Ada Barang Masuk</h3>
                                        <p class="text-sm text-slate-500 max-w-sm mx-auto mb-8">
                                            Data barang masuk untuk periode ini tidak ditemukan. Mulai catat penerimaan barang baru sekarang.
                                        </p>
                                        <a href="{{ route('barang-masuk.create') }}" class="inline-flex items-center px-6 py-3 text-sm font-bold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-600/20">
                                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Buat Transaksi Baru
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($barangMasuks->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 backdrop-blur-sm">
                    {{ $barangMasuks->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
