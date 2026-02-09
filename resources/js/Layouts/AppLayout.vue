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
const sidebarCollapsed = ref(false)

// Get pending order count from shared data
const pendingCount = computed(() => page.props.pendingOrderCount || 0)

// Get current user
const user = computed(() => page.props.auth?.user)

const isActive = (routeName) => {
    if (routeName === '') return page.url === '/'
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
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/40">
        <!-- Mobile sidebar backdrop -->
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-300"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div 
                v-show="sidebarOpen" 
                @click="sidebarOpen = false"
                class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"
            ></div>
        </Transition>

        <!-- Sidebar -->
        <aside 
            :class="[
                sidebarOpen ? 'translate-x-0' : '-translate-x-full',
                'lg:translate-x-0'
            ]"
            class="fixed inset-y-0 left-0 z-50 w-72 transform transition-all duration-300 ease-out"
        >
            <!-- Sidebar Background with Gradient -->
            <div class="absolute inset-0 bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900"></div>
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%239C92AC\' fill-opacity=\'0.03\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
            
            <!-- Gradient Orb Decorations -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 left-0 w-40 h-40 bg-violet-500/10 rounded-full blur-3xl"></div>
            
            <div class="relative h-full flex flex-col">
                <!-- Logo -->
                <div class="flex items-center gap-3 h-20 px-6 border-b border-white/5">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-tr from-indigo-500 to-violet-500 rounded-xl blur opacity-60"></div>
                        <div class="relative flex items-center justify-center w-11 h-11 bg-gradient-to-tr from-indigo-500 to-violet-500 rounded-xl shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-white tracking-tight">Stok GA</h1>
                        <p class="text-[11px] text-indigo-300/80 font-medium tracking-wide">JNE EXPRESS</p>
                    </div>
                </div>
                
                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar">
                    <div class="px-4 mb-4">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Main Menu</p>
                    </div>
                    
                    <Link 
                        href="/" 
                        class="group relative flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200"
                        :class="page.url === '/' 
                            ? 'bg-gradient-to-r from-indigo-500/20 to-violet-500/10 text-white shadow-lg shadow-indigo-500/5' 
                            : 'text-slate-400 hover:text-white hover:bg-white/5'"
                    >
                        <div :class="page.url === '/' ? 'text-indigo-400' : 'text-slate-500 group-hover:text-indigo-400'" class="transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                        <span>Dashboard</span>
                        <div v-if="page.url === '/'" class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-gradient-to-b from-indigo-400 to-violet-400 rounded-r-full"></div>
                    </Link>
                    
                    <Link 
                        href="/order" 
                        class="group relative flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200"
                        :class="isActive('order') 
                            ? 'bg-gradient-to-r from-indigo-500/20 to-violet-500/10 text-white shadow-lg shadow-indigo-500/5' 
                            : 'text-slate-400 hover:text-white hover:bg-white/5'"
                    >
                        <div :class="isActive('order') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-indigo-400'" class="transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <span>Order Request</span>
                        <span v-if="pendingCount > 0" class="ml-auto flex items-center justify-center min-w-[1.5rem] h-6 px-2 text-[11px] font-bold text-white bg-gradient-to-r from-rose-500 to-pink-500 rounded-full shadow-lg shadow-rose-500/30 animate-pulse">
                            {{ pendingCount }}
                        </span>
                        <div v-if="isActive('order')" class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-gradient-to-b from-indigo-400 to-violet-400 rounded-r-full"></div>
                    </Link>
                    
                    <div class="px-4 mt-8 mb-4">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Inventory</p>
                    </div>
                    
                    <Link 
                        v-for="item in inventoryItems"
                        :key="item.key"
                        :href="item.route" 
                        class="group relative flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200"
                        :class="isActive(item.key) 
                            ? 'bg-gradient-to-r from-indigo-500/20 to-violet-500/10 text-white shadow-lg shadow-indigo-500/5' 
                            : 'text-slate-400 hover:text-white hover:bg-white/5'"
                    >
                        <div :class="isActive(item.key) ? 'text-indigo-400' : 'text-slate-500 group-hover:text-indigo-400'" class="transition-colors">
                            <svg v-if="item.icon === 'barang'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <svg v-else-if="item.icon === 'masuk'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                            </svg>
                            <svg v-else-if="item.icon === 'keluar'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16V4m0 0l4 4m-4-4l-4 4m-6 0v12m0 0l-4-4m4 4l4-4" />
                            </svg>
                        </div>
                        <span>{{ item.name }}</span>
                        <div v-if="isActive(item.key)" class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-gradient-to-b from-indigo-400 to-violet-400 rounded-r-full"></div>
                    </Link>
                    
                    <div class="px-4 mt-8 mb-4">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Management</p>
                    </div>
                    
                    <Link 
                        v-for="item in managementItems"
                        :key="item.key"
                        :href="item.route" 
                        class="group relative flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200"
                        :class="isActive(item.key) 
                            ? 'bg-gradient-to-r from-indigo-500/20 to-violet-500/10 text-white shadow-lg shadow-indigo-500/5' 
                            : 'text-slate-400 hover:text-white hover:bg-white/5'"
                    >
                        <div :class="isActive(item.key) ? 'text-indigo-400' : 'text-slate-500 group-hover:text-indigo-400'" class="transition-colors">
                            <svg v-if="item.icon === 'supplier'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <svg v-else-if="item.icon === 'report'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <svg v-else-if="item.icon === 'opname'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <span>{{ item.name }}</span>
                        <div v-if="isActive(item.key)" class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-gradient-to-b from-indigo-400 to-violet-400 rounded-r-full"></div>
                    </Link>
                </nav>
                
                <!-- User -->
                <div v-if="user" class="p-4 border-t border-white/5">
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-gradient-to-r from-slate-800/80 to-slate-700/50 border border-white/5 backdrop-blur-sm">
                        <div class="relative">
                            <div class="w-10 h-10 bg-gradient-to-tr from-indigo-500 to-violet-500 rounded-xl flex items-center justify-center text-white text-sm font-bold shadow-lg shadow-indigo-500/20">
                                {{ user.name?.substring(0, 2).toUpperCase() || 'U' }}
                            </div>
                            <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-400 rounded-full border-2 border-slate-800"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-white truncate">{{ user.name }}</p>
                            <p class="text-[11px] text-slate-400 truncate">{{ user.department?.name || 'Administrator' }}</p>
                        </div>
                        <Link 
                            href="/logout" 
                            method="post" 
                            as="button" 
                            class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition-all duration-200"
                            title="Logout"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <div class="lg:pl-72 min-h-screen">
            <!-- Top header -->
            <header class="sticky top-0 z-30 backdrop-blur-xl bg-white/70 border-b border-slate-200/60">
                <div class="flex items-center h-16 px-4 sm:px-6 lg:px-8">
                    <button @click="sidebarOpen = true" class="p-2 -ml-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-xl lg:hidden transition-all duration-200">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    
                    <div class="flex-1 flex items-center gap-4">
                        <div class="hidden sm:flex items-center gap-2 text-sm text-slate-500">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span class="text-slate-300">/</span>
                        </div>
                        <h2 class="text-lg font-bold text-slate-800 tracking-tight">{{ title }}</h2>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <!-- Search -->
                        <button class="hidden sm:flex items-center gap-2 px-4 py-2 text-sm text-slate-500 bg-slate-100/80 hover:bg-slate-200/80 rounded-xl transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span class="hidden md:inline">Search...</span>
                            <kbd class="hidden lg:inline px-1.5 py-0.5 text-[10px] font-medium bg-white rounded shadow-sm">⌘K</kbd>
                        </button>
                        
                        <!-- Notifications -->
                        <button class="relative p-2.5 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span v-if="pendingCount > 0" class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                            </span>
                        </button>
                        
                        <!-- User Avatar (Mobile) -->
                        <div v-if="user" class="lg:hidden">
                            <div class="w-9 h-9 bg-gradient-to-tr from-indigo-500 to-violet-500 rounded-xl flex items-center justify-center text-white text-xs font-bold shadow-md">
                                {{ user.name?.substring(0, 2).toUpperCase() || 'U' }}
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
            
            <!-- Footer -->
            <footer class="px-4 sm:px-6 lg:px-8 py-6 border-t border-slate-200/60">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-slate-500">
                    <p>© {{ new Date().getFullYear() }} <span class="font-semibold text-slate-700">Stok GA</span> • JNE Express</p>
                    <p class="flex items-center gap-1">
                        Made with <span class="text-rose-500">♥</span> by IT Team
                    </p>
                </div>
            </footer>
        </div>
    </div>
</template>

<style scoped>
/* Custom scrollbar for sidebar */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 2px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.2);
}
</style>
