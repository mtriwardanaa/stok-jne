<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

defineProps({
    title: {
        type: String,
        default: 'Stok GA'
    }
})

const page = usePage()
const sidebarOpen = ref(false)

const pendingCount = computed(() => page.props.pendingOrderCount || 0)
const user = computed(() => page.props.auth?.user)

const isActive = (routeName) => {
    if (routeName === '') return page.url === '/'
    const path = '/' + routeName
    return page.url === path || page.url.startsWith(path + '/') || page.url.startsWith(path + '?')
}

const inventoryItems = [
    { name: 'Barang Masuk', route: '/barang-masuk', icon: 'masuk', key: 'barang-masuk' },
    { name: 'Barang Keluar', route: '/barang-keluar', icon: 'keluar', key: 'barang-keluar' },
    { name: 'Master Barang', route: '/barang', icon: 'barang', key: 'barang' },
]

const managementItems = [
    { name: 'Supplier', route: '/supplier', icon: 'supplier', key: 'supplier' },
    { name: 'Invoice', route: '/invoice', icon: 'invoice', key: 'invoice' },
    { name: 'Laporan', route: '/report', icon: 'report', key: 'report' },
    { name: 'Stock Opname', route: '/stok-opname', icon: 'opname', key: 'stok-opname' },
]
</script>

<template>
    <div class="min-h-screen bg-[#f0f4f9] overflow-x-hidden font-sans">
        
        <!-- Mobile backdrop -->
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-300"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-show="sidebarOpen" @click="sidebarOpen = false"
                class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm lg:hidden"></div>
        </Transition>

        <!-- ═══════════════════════════════ SIDEBAR ═══════════════════════════════ -->
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-[260px] lg:translate-x-0 transform transition-transform duration-300"
        >
            <!-- Sidebar bg -->
            <div class="absolute inset-0 bg-gradient-to-b from-[#020617] via-[#0f172a] to-[#162544]"></div>
            <!-- Subtle noise texture -->
            <div class="absolute inset-0 opacity-[0.015]" style="background-image:url('data:image/svg+xml,%3Csvg viewBox=%270 0 256 256%27 xmlns=%27http://www.w3.org/2000/svg%27%3E%3Cfilter id=%27noise%27%3E%3CfeTurbulence type=%27fractalNoise%27 baseFrequency=%270.9%27 numOctaves=%274%27/%3E%3C/filter%3E%3Crect width=%27256%27 height=%27256%27 filter=%27url(%23noise)%27 opacity=%271%27/%3E%3C/svg%3E')"></div>
            <!-- Glow accent -->
            <div class="absolute top-0 right-0 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl -mr-20 -mt-10"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-blue-600/5 rounded-full blur-3xl -ml-16 -mb-8"></div>

            <div class="relative h-full flex flex-col z-10">
                <!-- Logo -->
                <div class="flex items-center gap-3.5 h-[72px] px-6 border-b border-white/[0.06]">
                    <div class="relative group">
                        <div class="absolute inset-0 bg-indigo-500/30 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="relative w-11 h-11 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-[16px] font-extrabold text-white tracking-tight leading-tight">Stok<span class="bg-gradient-to-r from-indigo-400 to-violet-400 bg-clip-text text-transparent">GA</span></h1>
                        <p class="text-[9px] text-slate-500 font-semibold tracking-[0.2em] uppercase">JNE Express</p>
                    </div>
                </div>
                
                <!-- Navigation -->
                <nav class="flex-1 px-3 py-5 overflow-y-auto scrollbar-thin">
                    
                    <!-- Menu Utama -->
                    <div class="mb-6">
                        <p class="px-3 mb-2 text-[9px] font-bold text-slate-600 uppercase tracking-[0.2em]">Menu Utama</p>
                        
                        <!-- Dashboard -->
                        <Link href="/" 
                            class="group relative flex items-center gap-3 px-3 py-2.5 mb-0.5 text-[13px] font-semibold rounded-xl transition-all duration-300"
                            :class="page.url === '/' 
                                ? 'text-white' 
                                : 'text-slate-400 hover:text-slate-200'"
                        >
                            <div v-if="page.url === '/'" class="absolute inset-0 bg-gradient-to-r from-indigo-600/20 to-violet-600/10 rounded-xl border border-indigo-500/20"></div>
                            <div class="relative w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-300"
                                :class="page.url === '/' ? 'bg-gradient-to-br from-indigo-500 to-violet-600 shadow-lg shadow-indigo-500/30' : 'bg-white/[0.04] group-hover:bg-white/[0.08]'">
                                <svg class="w-[17px] h-[17px] transition-colors" :class="page.url === '/' ? 'text-white' : 'text-slate-500 group-hover:text-slate-300'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </div>
                            <span class="relative">Dashboard</span>
                        </Link>
                        
                        <!-- Order Request -->
                        <Link href="/order" 
                            class="group relative flex items-center gap-3 px-3 py-2.5 mb-0.5 text-[13px] font-semibold rounded-xl transition-all duration-300"
                            :class="isActive('order') 
                                ? 'text-white' 
                                : 'text-slate-400 hover:text-slate-200'"
                        >
                            <div v-if="isActive('order')" class="absolute inset-0 bg-gradient-to-r from-indigo-600/20 to-violet-600/10 rounded-xl border border-indigo-500/20"></div>
                            <div class="relative w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-300"
                                :class="isActive('order') ? 'bg-gradient-to-br from-indigo-500 to-violet-600 shadow-lg shadow-indigo-500/30' : 'bg-white/[0.04] group-hover:bg-white/[0.08]'">
                                <svg class="w-[17px] h-[17px] transition-colors" :class="isActive('order') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </div>
                            <span class="relative flex-1">Order Request</span>
                            <span v-if="pendingCount > 0" class="relative px-2 py-0.5 text-[10px] font-bold text-white bg-gradient-to-r from-red-500 to-rose-500 rounded-full shadow-sm shadow-red-500/40 animate-pulse">
                                {{ pendingCount }}
                            </span>
                        </Link>
                    </div>
                    
                    <!-- Inventory -->
                    <div class="mb-6">
                        <p class="px-3 mb-2 text-[9px] font-bold text-slate-600 uppercase tracking-[0.2em]">Inventory</p>
                        
                        <Link v-for="item in inventoryItems" :key="item.key" :href="item.route" 
                            class="group relative flex items-center gap-3 px-3 py-2.5 mb-0.5 text-[13px] font-semibold rounded-xl transition-all duration-300"
                            :class="isActive(item.key) ? 'text-white' : 'text-slate-400 hover:text-slate-200'"
                        >
                            <div v-if="isActive(item.key)" class="absolute inset-0 bg-gradient-to-r from-indigo-600/20 to-violet-600/10 rounded-xl border border-indigo-500/20"></div>
                            <div class="relative w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-300"
                                :class="isActive(item.key) ? 'bg-gradient-to-br from-indigo-500 to-violet-600 shadow-lg shadow-indigo-500/30' : 'bg-white/[0.04] group-hover:bg-white/[0.08]'">
                                <!-- Masuk icon -->
                                <svg v-if="item.icon === 'masuk'" class="w-[17px] h-[17px] transition-colors" :class="isActive(item.key) ? 'text-white' : 'text-slate-500 group-hover:text-slate-300'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                                <!-- Keluar icon -->
                                <svg v-else-if="item.icon === 'keluar'" class="w-[17px] h-[17px] transition-colors" :class="isActive(item.key) ? 'text-white' : 'text-slate-500 group-hover:text-slate-300'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16V4m0 0l4 4m-4-4l-4 4m-6 0v12m0 0l-4-4m4 4l4-4" />
                                </svg>
                                <!-- Barang icon -->
                                <svg v-else class="w-[17px] h-[17px] transition-colors" :class="isActive(item.key) ? 'text-white' : 'text-slate-500 group-hover:text-slate-300'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <span class="relative">{{ item.name }}</span>
                        </Link>
                    </div>
                    
                    <!-- Management -->
                    <div>
                        <p class="px-3 mb-2 text-[9px] font-bold text-slate-600 uppercase tracking-[0.2em]">Management</p>
                        
                        <Link v-for="item in managementItems" :key="item.key" :href="item.route" 
                            class="group relative flex items-center gap-3 px-3 py-2.5 mb-0.5 text-[13px] font-semibold rounded-xl transition-all duration-300"
                            :class="isActive(item.key) ? 'text-white' : 'text-slate-400 hover:text-slate-200'"
                        >
                            <div v-if="isActive(item.key)" class="absolute inset-0 bg-gradient-to-r from-indigo-600/20 to-violet-600/10 rounded-xl border border-indigo-500/20"></div>
                            <div class="relative w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-300"
                                :class="isActive(item.key) ? 'bg-gradient-to-br from-indigo-500 to-violet-600 shadow-lg shadow-indigo-500/30' : 'bg-white/[0.04] group-hover:bg-white/[0.08]'">
                                <!-- Supplier -->
                                <svg v-if="item.icon === 'supplier'" class="w-[17px] h-[17px] transition-colors" :class="isActive(item.key) ? 'text-white' : 'text-slate-500 group-hover:text-slate-300'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <!-- Invoice -->
                                <svg v-else-if="item.icon === 'invoice'" class="w-[17px] h-[17px] transition-colors" :class="isActive(item.key) ? 'text-white' : 'text-slate-500 group-hover:text-slate-300'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <!-- Report -->
                                <svg v-else-if="item.icon === 'report'" class="w-[17px] h-[17px] transition-colors" :class="isActive(item.key) ? 'text-white' : 'text-slate-500 group-hover:text-slate-300'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <!-- Opname -->
                                <svg v-else class="w-[17px] h-[17px] transition-colors" :class="isActive(item.key) ? 'text-white' : 'text-slate-500 group-hover:text-slate-300'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <span class="relative">{{ item.name }}</span>
                        </Link>
                    </div>
                </nav>
                
                <!-- User Profile -->
                <div v-if="user" class="p-3 border-t border-white/[0.06]">
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-white/[0.03] hover:bg-white/[0.06] border border-transparent hover:border-white/[0.06] transition-all duration-300 cursor-pointer group">
                        <div class="relative">
                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 via-violet-500 to-purple-600 rounded-xl flex items-center justify-center text-white text-[11px] font-bold shadow-lg shadow-indigo-500/20 group-hover:shadow-indigo-500/40 transition-shadow">
                                {{ user.name?.substring(0, 2).toUpperCase() || 'U' }}
                            </div>
                            <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-400 rounded-full border-2 border-[#0f172a]"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[13px] font-bold text-slate-200 truncate group-hover:text-white transition-colors">{{ user.name }}</p>
                            <p class="text-[10px] text-slate-600 truncate">{{ user.department?.name || 'Administrator' }}</p>
                        </div>
                        <Link href="/logout" method="post" as="button" 
                            class="p-1.5 text-slate-600 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-all duration-200" title="Logout">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>
        </aside>

        <!-- ═══════════════════════════════ MAIN ═══════════════════════════════ -->
        <div class="lg:pl-[260px] min-h-screen flex flex-col">
            
            <!-- Header -->
            <header class="sticky top-0 z-30 bg-white/70 backdrop-blur-2xl border-b border-slate-200/50">
                <div class="flex items-center justify-between h-[72px] px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-4">
                        <!-- Mobile hamburger -->
                        <button @click="sidebarOpen = true" class="p-2 -ml-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl lg:hidden transition-all">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        
                        <!-- Breadcrumb & Title -->
                        <div>
                            <div class="hidden sm:flex items-center gap-1.5 text-[11px] text-slate-400 mb-0.5">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <span>/ {{ title }}</span>
                            </div>
                            <h1 class="text-lg font-extrabold text-slate-800 tracking-tight">{{ title }}</h1>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-1.5">
                        <!-- Notification bell -->
                        <button class="relative p-2.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100/80 rounded-xl transition-all">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span v-if="pendingCount > 0" class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white animate-pulse"></span>
                        </button>
                        
                        <!-- Mobile avatar -->
                        <div v-if="user" class="lg:hidden">
                            <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-xl flex items-center justify-center text-white text-[10px] font-bold shadow-md shadow-indigo-500/20">
                                {{ user.name?.substring(0, 2).toUpperCase() || 'U' }}
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
            
            <!-- Footer -->
            <footer class="px-4 sm:px-6 lg:px-8 py-4 border-t border-slate-100/80 bg-white/40 backdrop-blur-sm">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-[12px] text-slate-400">
                    <p>© {{ new Date().getFullYear() }} <span class="font-bold text-slate-500">StokGA</span> • JNE Express</p>
                    <p>v2.0</p>
                </div>
            </footer>
        </div>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap');

.font-sans {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
}

/* Custom scrollbar for sidebar */
.scrollbar-thin::-webkit-scrollbar {
    width: 3px;
}
.scrollbar-thin::-webkit-scrollbar-track {
    background: transparent;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.08);
    border-radius: 999px;
}
.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: rgba(255,255,255,0.15);
}

/* Smooth page transitions */
main {
    animation: pageEnter 0.35s ease-out;
}

@keyframes pageEnter {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
