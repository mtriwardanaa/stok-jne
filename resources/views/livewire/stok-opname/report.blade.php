<div>
    <div class="mb-6">
        <!-- Header -->
        <div class="relative rounded-3xl p-1 bg-gradient-to-b from-white to-slate-50 shadow-sm border border-slate-200 mb-8">
            <div class="absolute inset-0 bg-grid-slate-100 [mask-image:linear-gradient(0deg,white,rgba(255,255,255,0.6))] rounded-3xl"></div>
            <div class="relative bg-white/60 backdrop-blur-xl rounded-[1.4rem] p-6">
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-gradient-to-br from-purple-500/10 to-indigo-500/10 rounded-full blur-3xl opacity-60 pointer-events-none"></div>
                
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2.5 bg-purple-500/10 rounded-xl">
                                <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Report Stock Opname</h2>
                        </div>
                        <p class="text-sm text-slate-500 ml-1">Riwayat semua penyesuaian stok untuk audit.</p>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('stok-opname') }}" class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <div class="text-sm text-slate-500 mb-1">Total Opname</div>
                <div class="text-2xl font-bold text-slate-800">{{ number_format($stats['total']) }}</div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <div class="text-sm text-slate-500 mb-1">Bulan Ini</div>
                <div class="text-2xl font-bold text-slate-800">{{ number_format($stats['this_month']) }}</div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <div class="text-sm text-slate-500 mb-1">Total Selisih (+)</div>
                <div class="text-2xl font-bold text-green-600">+{{ number_format($stats['total_selisih_plus']) }}</div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <div class="text-sm text-slate-500 mb-1">Total Selisih (-)</div>
                <div class="text-2xl font-bold text-red-600">{{ number_format($stats['total_selisih_minus']) }}</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 mb-6">
            <div class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari barang / no opname..."
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                </div>
                <div>
                    <input type="date" wire:model.live="dateFrom" class="px-4 py-2.5 border border-slate-200 rounded-xl text-sm">
                </div>
                <div>
                    <input type="date" wire:model.live="dateTo" class="px-4 py-2.5 border border-slate-200 rounded-xl text-sm">
                </div>
                <div>
                    <select wire:model.live="filterUser" class="px-4 py-2.5 border border-slate-200 rounded-xl text-sm">
                        <option value="">Semua User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-[1.5rem] border border-slate-200/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase">No. Opname</th>
                            <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase">Tanggal</th>
                            <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase">Barang</th>
                            <th class="px-4 py-4 text-center text-xs font-bold text-slate-500 uppercase">Sistem</th>
                            <th class="px-4 py-4 text-center text-xs font-bold text-slate-500 uppercase">Fisik</th>
                            <th class="px-4 py-4 text-center text-xs font-bold text-slate-500 uppercase">Selisih</th>
                            <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase">Alasan</th>
                            <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase">User</th>
                            <th class="px-4 py-4 text-center text-xs font-bold text-slate-500 uppercase">Bukti</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($opnames as $opname)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-3">
                                    <span class="text-sm font-mono font-semibold text-slate-700">{{ $opname->no_opname }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm text-slate-600">{{ \Carbon\Carbon::parse($opname->tanggal)->format('d M Y H:i') }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-semibold text-slate-800">{{ $opname->nama_barang }}</div>
                                    <div class="text-xs text-slate-400">{{ $opname->kode_barang }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-sm font-mono text-slate-600">{{ number_format($opname->stok_sistem) }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-sm font-mono text-slate-600">{{ number_format($opname->stok_fisik) }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-sm font-bold {{ $opname->selisih > 0 ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                                        {{ $opname->selisih > 0 ? '+' : '' }}{{ number_format($opname->selisih) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-slate-600 max-w-[200px] truncate" title="{{ $opname->alasan }}">
                                        {{ $opname->alasan }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm text-slate-600">{{ $opname->user_name ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($opname->foto_bukti)
                                        <a href="{{ asset('storage/' . $opname->foto_bukti) }}" target="_blank" class="inline-flex items-center text-purple-600 hover:text-purple-800">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-16 text-center text-slate-500">
                                    Tidak ada data opname.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($opnames->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $opnames->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
