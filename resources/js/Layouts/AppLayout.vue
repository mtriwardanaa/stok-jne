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

const pendingCount = computed(() => page.props.pendingOrderCount || 0)
const user = computed(() => page.props.auth?.user)

const isActive = (routeName) => {
    if (routeName === '') return page.url === '/'
    const path = '/' + routeName
    return page.url === path || page.url.startsWith(path + '/') || page.url.startsWith(path + '?')
}

const menuItems = [
    { name: 'Dashboard', route: '/', icon: 'home', key: '' },
    { name: 'Order Request', route: '/order', icon: 'order', key: 'order', badge: true },
]

const inventoryItems = [
    { name: 'Barang Masuk', route: '/barang-masuk', icon: 'masuk', key: 'barang-masuk' },
    { name: 'Barang Keluar', route: '/barang-keluar', icon: 'keluar', key: 'barang-keluar' },
    { name: 'Master Barang', route: '/barang', icon: 'barang', key: 'barang' },
]

const managementItems = [
    { name: 'Supplier', route: '/supplier', icon: 'supplier', key: 'supplier' },
    { name: 'Laporan', route: '/report', icon: 'report', key: 'report' },
    { name: 'Stock Opname', route: '/stok-opname', icon: 'opname', key: 'stok-opname' },
]
</script>

<template>
    <div class="min-h-screen bg-gray-50 overflow-x-hidden">
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
                class="fixed inset-0 z-40 bg-black/30 backdrop-blur-sm lg:hidden"
            ></div>
        </Transition>

        <!-- Sidebar -->
        <aside 
            :class="[
                sidebarOpen ? 'translate-x-0' : '-translate-x-full',
                'lg:translate-x-0'
            ]"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 shadow-xl lg:shadow-none transform transition-transform duration-300"
        >
            <div class="h-full flex flex-col">
                <!-- Logo -->
                <div class="flex items-center gap-3 h-16 px-6 border-b border-gray-100">
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl shadow-lg shadow-blue-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Stok GA</h1>
                        <p class="text-[10px] text-gray-400 font-medium tracking-wide">JNE EXPRESS</p>
                    </div>
                </div>
                
                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 overflow-y-auto">
                    <!-- Main Menu -->
                    <div class="mb-8">
                        <p class="px-3 mb-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Menu Utama</p>
                        
                        <Link 
                            href="/" 
                            class="flex items-center gap-3 px-3 py-2.5 mb-1 text-sm font-medium rounded-xl transition-all duration-200"
                            :class="page.url === '/' 
                                ? 'bg-blue-50 text-blue-600' 
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                        >
                            <div class="w-8 h-8 flex items-center justify-center rounded-lg" :class="page.url === '/' ? 'bg-blue-100' : 'bg-gray-100'">
                                <svg class="w-5 h-5" :class="page.url === '/' ? 'text-blue-600' : 'text-gray-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </div>
                            <span>Dashboard</span>
                        </Link>
                        
                        <Link 
                            href="/order" 
                            class="flex items-center gap-3 px-3 py-2.5 mb-1 text-sm font-medium rounded-xl transition-all duration-200"
                            :class="isActive('order') 
                                ? 'bg-blue-50 text-blue-600' 
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                        >
                            <div class="w-8 h-8 flex items-center justify-center rounded-lg" :class="isActive('order') ? 'bg-blue-100' : 'bg-gray-100'">
                                <svg class="w-5 h-5" :class="isActive('order') ? 'text-blue-600' : 'text-gray-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </div>
                            <span class="flex-1">Order Request</span>
                            <span v-if="pendingCount > 0" class="px-2 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full">
                                {{ pendingCount }}
                            </span>
                        </Link>
                    </div>
                    
                    <!-- Inventory -->
                    <div class="mb-8">
                        <p class="px-3 mb-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Inventory</p>
                        
                        <Link 
                            v-for="item in inventoryItems"
                            :key="item.key"
                            :href="item.route" 
                            class="flex items-center gap-3 px-3 py-2.5 mb-1 text-sm font-medium rounded-xl transition-all duration-200"
                            :class="isActive(item.key) 
                                ? 'bg-blue-50 text-blue-600' 
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                        >
                            <div class="w-8 h-8 flex items-center justify-center rounded-lg" :class="isActive(item.key) ? 'bg-blue-100' : 'bg-gray-100'">
                                <svg v-if="item.icon === 'barang'" class="w-5 h-5" :class="isActive(item.key) ? 'text-blue-600' : 'text-gray-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <svg v-else-if="item.icon === 'masuk'" class="w-5 h-5" :class="isActive(item.key) ? 'text-blue-600' : 'text-gray-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                                <svg v-else-if="item.icon === 'keluar'" class="w-5 h-5" :class="isActive(item.key) ? 'text-blue-600' : 'text-gray-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16V4m0 0l4 4m-4-4l-4 4m-6 0v12m0 0l-4-4m4 4l4-4" />
                                </svg>
                            </div>
                            <span>{{ item.name }}</span>
                        </Link>
                    </div>
                    
                    <!-- Management -->
                    <div>
                        <p class="px-3 mb-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Management</p>
                        
                        <Link 
                            v-for="item in managementItems"
                            :key="item.key"
                            :href="item.route" 
                            class="flex items-center gap-3 px-3 py-2.5 mb-1 text-sm font-medium rounded-xl transition-all duration-200"
                            :class="isActive(item.key) 
                                ? 'bg-blue-50 text-blue-600' 
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                        >
                            <div class="w-8 h-8 flex items-center justify-center rounded-lg" :class="isActive(item.key) ? 'bg-blue-100' : 'bg-gray-100'">
                                <svg v-if="item.icon === 'supplier'" class="w-5 h-5" :class="isActive(item.key) ? 'text-blue-600' : 'text-gray-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <svg v-else-if="item.icon === 'report'" class="w-5 h-5" :class="isActive(item.key) ? 'text-blue-600' : 'text-gray-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <svg v-else-if="item.icon === 'opname'" class="w-5 h-5" :class="isActive(item.key) ? 'text-blue-600' : 'text-gray-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <span>{{ item.name }}</span>
                        </Link>
                    </div>
                </nav>
                
                <!-- User Profile -->
                <div v-if="user" class="p-4 border-t border-gray-100">
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer">
                        <div class="relative">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white text-sm font-bold shadow-md">
                                {{ user.name?.substring(0, 2).toUpperCase() || 'U' }}
                            </div>
                            <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ user.name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ user.department?.name || 'Administrator' }}</p>
                        </div>
                        <Link 
                            href="/logout" 
                            method="post" 
                            as="button" 
                            class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
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
        <div class="lg:pl-64 min-h-screen flex flex-col">
            <!-- Top header -->
            <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-lg border-b border-gray-200/60">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = true" class="p-2 -ml-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl lg:hidden transition-colors">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        
                        <div class="flex items-center gap-2">
                            <nav class="hidden sm:flex items-center gap-1 text-sm text-gray-500">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <span class="text-gray-300">/</span>
                            </nav>
                            <h1 class="text-lg font-bold text-gray-900">{{ title }}</h1>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <!-- Search -->
                        <div class="hidden md:flex items-center">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" placeholder="Search..." class="w-64 pl-10 pr-4 py-2 text-sm text-gray-700 bg-gray-100 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>
                        </div>
                        
                        <!-- Notifications -->
                        <button class="relative p-2.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span v-if="pendingCount > 0" class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 rounded-full ring-2 ring-white"></span>
                        </button>
                        
                        <!-- User Avatar (Mobile) -->
                        <div v-if="user" class="lg:hidden">
                            <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white text-xs font-bold shadow-md">
                                {{ user.name?.substring(0, 2).toUpperCase() || 'U' }}
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
            
            <!-- Footer -->
            <footer class="px-4 sm:px-6 lg:px-8 py-4 border-t border-gray-100 bg-white">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-sm text-gray-500">
                    <p>© {{ new Date().getFullYear() }} <span class="font-semibold text-gray-700">Stok GA</span> • JNE Express</p>
                    <p class="text-xs">Version 2.0</p>
                </div>
            </footer>
        </div>
    </div>
</template>
