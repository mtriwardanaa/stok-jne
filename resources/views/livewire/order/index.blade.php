<div>
    <div class="mb-6">
        <!-- Header & Controls -->
        <div class="relative rounded-3xl p-1 bg-gradient-to-b from-white to-slate-50 shadow-sm border border-slate-200 mb-8">
            <div class="absolute inset-0 bg-grid-slate-100 [mask-image:linear-gradient(0deg,white,rgba(255,255,255,0.6))] rounded-3xl"></div>
            <div class="relative bg-white/60 backdrop-blur-xl rounded-[1.4rem] p-6">
                <!-- Decoration -->
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-gradient-to-br from-blue-500/10 to-indigo-500/10 rounded-full blur-3xl opacity-60 pointer-events-none"></div>
                
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                    <!-- Title & Context -->
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2.5 bg-blue-500/10 rounded-xl">
                                <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Order Request</h2>
                        </div>
                        <p class="text-sm text-slate-500 ml-1">Kelola permintaan barang dari unit/departemen lain.</p>
                    </div>

                    <!-- Controls Group -->
                    <div class="flex flex-col sm:flex-row gap-4 flex-1 justify-end">
                         <!-- Search Box -->
                        <div class="relative group w-full sm:w-64 transition-all">
                            <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl opacity-20 group-hover:opacity-40 transition duration-500 blur"></div>
                            <div class="relative flex items-center bg-white rounded-xl shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" 
                                    wire:model.live.debounce.300ms="search" 
                                    class="block w-full pl-11 pr-4 py-2.5 bg-transparent border-0 rounded-xl text-slate-900 placeholder-slate-400 focus:ring-0 sm:text-sm" 
                                    placeholder="Cari order..." 
                                    >
                            </div>
                        </div>

                        <div class="flex gap-2">
                             <!-- Month Filter -->
                            <div class="relative">
                                <select wire:model.live="month" class="appearance-none pl-4 pr-10 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 cursor-pointer shadow-sm hover:border-slate-300 transition-colors">
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
                                <select wire:model.live="year" class="appearance-none pl-4 pr-10 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 cursor-pointer shadow-sm hover:border-slate-300 transition-colors">
                                    @foreach(range(now()->year, now()->year - 3) as $y)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Tabs -->
                <div class="mt-8 flex flex-wrap gap-2">
                     <button wire:click="$set('status', '')" class="relative flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold transition-all {{ !$status ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'bg-white text-slate-500 hover:bg-slate-50 border border-slate-200' }}">
                        Semua
                        <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ !$status ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $statusCounts['all'] }}</span>
                    </button>
                    @foreach([
                        ['id' => 'menunggu', 'label' => 'Menunggu', 'color' => 'amber'],
                        ['id' => 'diproses', 'label' => 'Diproses', 'color' => 'blue'],
                        ['id' => 'selesai', 'label' => 'Selesai', 'color' => 'emerald'],
                        ['id' => 'ditolak', 'label' => 'Ditolak', 'color' => 'rose']
                    ] as $tab)
                        @php
                            $isActive = $status === $tab['id'];
                            $colors = match($tab['color']) {
                                'amber' => $isActive ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/20' : 'text-slate-500 hover:text-amber-600 hover:bg-amber-50',
                                'blue' => $isActive ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/20' : 'text-slate-500 hover:text-blue-600 hover:bg-blue-50',
                                'emerald' => $isActive ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20' : 'text-slate-500 hover:text-emerald-600 hover:bg-emerald-50',
                                'rose' => $isActive ? 'bg-rose-500 text-white shadow-lg shadow-rose-500/20' : 'text-slate-500 hover:text-rose-600 hover:bg-rose-50',
                            };
                            $badgeColors = match($tab['color']) {
                                'amber' => $isActive ? 'bg-white/20 text-white' : 'bg-amber-50 text-amber-600',
                                'blue' => $isActive ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-600',
                                'emerald' => $isActive ? 'bg-white/20 text-white' : 'bg-emerald-50 text-emerald-600',
                                'rose' => $isActive ? 'bg-white/20 text-white' : 'bg-rose-50 text-rose-600',
                            };
                        @endphp
                        <button wire:click="$set('status', '{{ $tab['id'] }}')" class="relative flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold transition-all border {{ $isActive ? 'border-transparent' : 'border-slate-200 bg-white' }} {{ $colors }}">
                            {{ $tab['label'] }}
                            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $badgeColors }}">{{ $statusCounts[$tab['id']] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="bg-white rounded-[1.5rem] border border-slate-200/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden relative fade-in">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th class="px-6 py-5 text-xs font-bold text-slate-500 uppercase tracking-wider pl-8">No. Order</th>
                            <th class="px-6 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal & Waktu</th>
                            <th class="px-6 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Pemohon</th>
                            <th class="px-6 py-5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Total Item</th>
                            <th class="px-6 py-5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($orders as $order)
                            <tr class="group hover:bg-slate-50/80 transition-all duration-200">
                                <td class="px-6 py-4 pl-8">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600">
                                             <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <span class="text-sm font-bold text-slate-800 group-hover:text-blue-600 transition-colors">
                                            {{ $order->no_order }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-slate-700">{{ $order->tanggal->format('d M Y') }}</span>
                                        <span class="text-xs text-slate-400">{{ $order->tanggal->format('H:i') }} WIB</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500 border border-slate-200 ring-2 ring-white">
                                            {{ substr($order->nama_user_request ?? $order->createdUser?->name ?? '?', 0, 1) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-semibold text-slate-700">{{ $order->nama_user_request ?? $order->createdUser?->name ?? '-' }}</span>
                                            <span class="text-[10px] font-medium text-slate-400">{{ $order->organization_name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        {{ $order->details->count() }} Item
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                     @php
                                        $statusConfig = match($order->status) {
                                            'menunggu' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'dot' => 'bg-amber-500'],
                                            'diproses' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'dot' => 'bg-blue-500'],
                                            'selesai' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-500'],
                                            'ditolak' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'dot' => 'bg-rose-500'],
                                            default => ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'dot' => 'bg-slate-500'],
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['border'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('order.detail', $order->id) }}" class="inline-flex items-center justify-center p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Lihat Detail">
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
                                            <div class="absolute inset-0 bg-blue-100 rounded-full animate-pulse"></div>
                                            <div class="absolute inset-2 bg-white rounded-full flex items-center justify-center border border-blue-50 shadow-sm">
                                                <svg class="w-8 h-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <h3 class="text-xl font-bold text-slate-900 mb-2">Belum Ada Order</h3>
                                        <p class="text-sm text-slate-500 max-w-sm mx-auto mb-6">
                                            Belum ada permintaan barang yang masuk untuk filter yang dipilih.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($orders->hasPages())
                 <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 backdrop-blur-sm">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
