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

// Get pending order count from shared data
const pendingCount = computed(() => page.props.pendingOrderCount || 0)

// Get current user
const user = computed(() => page.props.auth?.user)

const isActive = (routeName) => {
    return page.url.startsWith('/' + routeName)
}

const menuItems = [
    { name: 'Dashboard', route: '/', icon: 'home', key: '' },
    { name: 'Order Request', route: '/order', icon: 'order', key: 'order', badge: true },
]

const inventoryItems = [
    { name: 'Master Barang', route: '/barang', icon: 'barang', key: 'barang' },
    { name: 'Barang Masuk', route: '/barang-masuk', icon: 'masuk', key: 'barang-masuk' },
    { name: 'Barang Keluar', route: '/barang-keluar', icon: 'keluar', key: 'barang-keluar' },
]

const managementItems = [
    { name: 'Supplier Data', route: '/supplier', icon: 'supplier', key: 'supplier' },
    { name: 'Laporan', route: '/report', icon: 'report', key: 'report' },
    { name: 'Stock Opname', route: '/stok-opname', icon: 'opname', key: 'stok-opname' },
]
</script>

<template>
    <div class="min-h-screen">
        <!-- Mobile sidebar backdrop -->
        <div 
            v-show="sidebarOpen" 
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-gray-900/80 lg:hidden transition-opacity duration-300"
        ></div>

        <!-- Sidebar -->
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 border-r border-slate-800 transform transition-transform duration-300 ease-in-out lg:translate-x-0"
        >
            <!-- Logo -->
            <div class="flex items-center gap-3 h-20 px-6 border-b border-white/5">
                <div class="relative flex items-center justify-center w-10 h-10 bg-gradient-to-tr from-indigo-500 to-violet-500 rounded-xl shadow-lg shadow-indigo-500/20">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-white tracking-tight">Stok GA</h1>
                    <p class="text-xs text-slate-400 font-medium">JNE Express</p>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <div class="px-4 mb-3">
                    <p class="text-[0.65rem] font-bold text-slate-500 uppercase tracking-wider">Main Menu</p>
                </div>
                
                <Link 
                    href="/" 
                    class="group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200"
                    :class="page.url === '/' ? 'bg-indigo-500/10 text-indigo-400' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5'"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </Link>
                
                <Link 
                    href="/order" 
                    class="group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200"
                    :class="isActive('order') ? 'bg-indigo-500/10 text-indigo-400' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5'"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Order Request
                    <span v-if="pendingCount > 0" class="ml-auto flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-[0.65rem] font-bold text-white bg-rose-500 rounded-full shadow-sm shadow-rose-500/20">
                        {{ pendingCount }}
                    </span>
                </Link>
                
                <div class="px-4 mt-8 mb-3">
                    <p class="text-[0.65rem] font-bold text-slate-500 uppercase tracking-wider">Inventory</p>
                </div>
                
                <Link 
                    v-for="item in inventoryItems"
                    :key="item.key"
                    :href="item.route" 
                    class="group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200"
                    :class="isActive(item.key) ? 'bg-indigo-500/10 text-indigo-400' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5'"
                >
                    <svg v-if="item.icon === 'barang'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <svg v-else-if="item.icon === 'masuk'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                    </svg>
                    <svg v-else-if="item.icon === 'keluar'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16V4m0 0l4 4m-4-4l-4 4m-6 0v12m0 0l-4-4m4 4l4-4" />
                    </svg>
                    {{ item.name }}
                </Link>
                
                <div class="px-4 mt-8 mb-3">
                    <p class="text-[0.65rem] font-bold text-slate-500 uppercase tracking-wider">Management</p>
                </div>
                
                <Link 
                    v-for="item in managementItems"
                    :key="item.key"
                    :href="item.route" 
                    class="group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200"
                    :class="isActive(item.key) ? 'bg-indigo-500/10 text-indigo-400' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5'"
                >
                    <svg v-if="item.icon === 'supplier'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <svg v-else-if="item.icon === 'report'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <svg v-else-if="item.icon === 'opname'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    {{ item.name }}
                </Link>
            </nav>
            
            <!-- User -->
            <div v-if="user" class="p-4 border-t border-white/5">
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-800/50 border border-white/5">
                    <div class="w-9 h-9 bg-gradient-to-tr from-indigo-500 to-violet-500 rounded-lg flex items-center justify-center text-white text-sm font-bold shadow-sm">
                        {{ user.name?.substring(0, 2).toUpperCase() || 'U' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-200 truncate">{{ user.name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ user.department?.name || 'Administrator' }}</p>
                    </div>
                    <Link 
                        href="/logout" 
                        method="post" 
                        as="button" 
                        class="p-1.5 text-slate-400 hover:text-white hover:bg-white/10 rounded-lg transition-all"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <div class="lg:pl-72 bg-slate-50 min-h-screen">
            <!-- Top header -->
            <header class="sticky top-0 z-30 flex items-center h-16 px-4 bg-white/80 backdrop-blur-md border-b border-slate-200/60 sm:px-6 lg:px-8">
                <button @click="sidebarOpen = true" class="p-2 -ml-2 text-slate-500 hover:bg-slate-100 rounded-lg lg:hidden transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                
                <div class="flex-1 flex items-center gap-4">
                    <h2 class="text-lg font-bold text-slate-800 tracking-tight">{{ title }}</h2>
                </div>
                
                <div class="flex items-center gap-4">
                    <!-- Notifications -->
                    <button class="relative p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-all">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span v-if="pendingCount > 0" class="absolute top-2 right-2 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white"></span>
                    </button>
                </div>
            </header>

            <!-- Page content -->
            <main class="p-4 sm:p-6 lg:p-8 w-full">
                <slot />
            </main>
        </div>
    </div>
</template>

<style>
/* Smooth scrollbar */
::-webkit-scrollbar { width: 6px; height: 6px; }
::-webkit-scrollbar-track { background: #f1f5f9; }
::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
