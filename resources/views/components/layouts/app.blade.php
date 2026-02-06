<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Stok GA' }} - JNE</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- jQuery & Select2 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                        dark: {
                            800: '#1e1e2d',
                            900: '#151521',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js is bundled with Livewire 3 - no separate include needed -->
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Smooth scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* Sidebar Links */
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #94a3b8;
            border-radius: 0.75rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
        }
        .sidebar-link:hover {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(37, 99, 235, 0.15) 100%);
            color: #e2e8f0;
            transform: translateX(4px);
        }
        .sidebar-link.active {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 8px 20px -4px rgba(37, 99, 235, 0.5);
        }
        .sidebar-link.active svg { filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2)); }
        
        /* Cards - Glassmorphism */
        .card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 20px 25px -5px rgba(0, 0, 0, 0.04);
            transform: translateY(-2px);
        }
        
        /* Stat Cards */
        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 1rem;
            padding: 1.25rem;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(30%, -30%);
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -8px rgba(59, 130, 246, 0.15);
        }
        
        /* Buttons - Premium */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px -2px rgba(37, 99, 235, 0.4);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -4px rgba(37, 99, 235, 0.5);
        }
        .btn-primary:active { transform: translateY(0); }
        
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            background: #f1f5f9;
            color: #475569;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.25s ease;
            text-decoration: none;
            border: 1px solid #e2e8f0;
            cursor: pointer;
        }
        .btn-secondary:hover {
            background: #e2e8f0;
            border-color: #cbd5e1;
        }
        
        .btn-success {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            text-decoration: none;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px -2px rgba(22, 163, 74, 0.4);
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -4px rgba(22, 163, 74, 0.5);
        }
        
        .btn-danger {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            text-decoration: none;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px -2px rgba(220, 38, 38, 0.4);
        }
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -4px rgba(220, 38, 38, 0.5);
        }
        
        /* Badges - Modern */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.025em;
        }
        .badge-green { background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: #15803d; }
        .badge-yellow { background: linear-gradient(135deg, #fef9c3 0%, #fef08a 100%); color: #a16207; }
        .badge-red { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #b91c1c; }
        .badge-blue { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1d4ed8; }
        .badge-purple { background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%); color: #7c3aed; }
        .badge-gray { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        
        /* Table styling - Clean */
        table { width: 100%; border-collapse: collapse; }
        thead { background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); }
        th {
            padding: 0.875rem 1.25rem;
            text-align: left;
            font-size: 0.7rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.075em;
            border-bottom: 2px solid #e2e8f0;
        }
        td { padding: 1rem 1.25rem; }
        tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s ease;
        }
        tbody tr:hover { background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%); }
        tbody tr:last-child { border-bottom: none; }
        
        /* Form inputs - Refined */
        input[type="text"],
        input[type="number"],
        input[type="password"],
        input[type="email"],
        input[type="datetime-local"],
        select,
        textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            background: #ffffff;
            transition: all 0.25s ease;
        }
        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
            background: #fafbfc;
        }
        input:hover:not(:focus),
        select:hover:not(:focus) {
            border-color: #cbd5e1;
        }
        
        /* Modal backdrop */
        .modal-backdrop {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
        }
        
        /* Animation classes */
        .fade-in {
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Page header styling */
        .page-header {
            margin-bottom: 1.5rem;
        }
        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: -0.025em;
        }
        .page-header p {
            color: #64748b;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
    
    @livewireStyles
</head>
<body class="h-full bg-gray-50 font-sans antialiased">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen">
        <!-- Mobile sidebar backdrop -->
        <div x-show="sidebarOpen" x-cloak
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-gray-900/80 lg:hidden"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-50 w-72 bg-gradient-to-b from-dark-800 to-dark-900 transform transition-transform duration-300 ease-in-out lg:translate-x-0">
            
            <!-- Logo -->
            <div class="flex items-center justify-center h-20 px-6 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-white">Stok GA</h1>
                        <p class="text-xs text-gray-400">JNE Express</p>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <p class="px-4 mb-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Menu</p>
                
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('order.index') }}" class="sidebar-link {{ request()->routeIs('order.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span>Order</span>
                    @php $pendingCount = \App\Models\Order::pending()->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="ml-auto px-2 py-0.5 text-xs font-semibold bg-red-500 text-white rounded-full">{{ $pendingCount }}</span>
                    @endif
                </a>
                
                <p class="px-4 mt-6 mb-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok</p>
                
                <a href="{{ route('barang.index') }}" class="sidebar-link {{ request()->routeIs('barang.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span>Master Barang</span>
                </a>
                
                <a href="{{ route('barang-masuk.index') }}" class="sidebar-link {{ request()->routeIs('barang-masuk.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                    </svg>
                    <span>Barang Masuk</span>
                </a>
                
                <a href="{{ route('barang-keluar.index') }}" class="sidebar-link {{ request()->routeIs('barang-keluar.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16V4m0 0l4 4m-4-4l-4 4m-6 0v12m0 0l-4-4m4 4l4-4" />
                    </svg>
                    <span>Barang Keluar</span>
                </a>
                
                <p class="px-4 mt-6 mb-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Master</p>
                
                <a href="{{ route('supplier.index') }}" class="sidebar-link {{ request()->routeIs('supplier.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>Supplier</span>
                </a>
                
                <a href="{{ route('report.index') }}" class="sidebar-link {{ request()->routeIs('report.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Laporan</span>
                </a>
            </nav>
            
            <!-- User -->
            @auth
            <div class="p-4 border-t border-white/10">
                <div class="flex items-center gap-3 px-4 py-3 rounded-lg bg-white/5">
                    <div class="w-10 h-10 bg-primary-600 rounded-full flex items-center justify-center text-white font-semibold">
                        {{ auth()->user()->initials() ?? 'U' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ auth()->user()->department?->name ?? 'GA Team' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-2 text-gray-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            @endauth
        </aside>

        <!-- Main content -->
        <div class="lg:pl-72">
            <!-- Top header -->
            <header class="sticky top-0 z-30 flex items-center h-16 px-4 bg-white border-b border-gray-200 sm:px-6 lg:px-8">
                <button @click="sidebarOpen = true" class="p-2 -ml-2 text-gray-500 lg:hidden">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                
                <div class="flex-1">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $title ?? 'Dashboard' }}</h2>
                </div>
                
                <div class="flex items-center gap-4">
                    <!-- Notifications -->
                    <button class="relative p-2 text-gray-500 hover:text-gray-700 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if(isset($pendingCount) && $pendingCount > 0)
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        @endif
                    </button>
                </div>
            </header>

            <!-- Page content -->
            <main class="p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
    
    <!-- Select2 Initialization -->
    <script>
        let select2InitTimer = null;
        
        function initSelect2() {
            // Only init selects that haven't been initialized yet
            $('select:not(.select2-hidden-accessible):not(.no-select2)').each(function() {
                const $el = $(this);
                
                $el.select2({
                    width: '100%',
                    placeholder: $el.find('option:first').text() || 'Pilih...',
                    allowClear: !$el.prop('required'),
                    minimumResultsForSearch: 5
                }).on('select2:select select2:clear', function(e) {
                    // Trigger native change for Livewire
                    let event = new Event('change', { bubbles: true });
                    this.dispatchEvent(event);
                });
            });
        }
        
        function debouncedInitSelect2() {
            // Debounce to prevent multiple rapid calls
            clearTimeout(select2InitTimer);
            select2InitTimer = setTimeout(function() {
                // Don't reinit if any dropdown is currently open
                if ($('.select2-container--open').length === 0) {
                    initSelect2();
                }
            }, 100);
        }
        
        // Initialize on page load
        $(document).ready(initSelect2);
        
        // Reinitialize after Livewire updates (debounced)
        document.addEventListener('livewire:init', () => {
            Livewire.hook('morph.updated', () => debouncedInitSelect2());
        });
        
        // Handle navigation
        document.addEventListener('livewire:navigated', () => setTimeout(initSelect2, 50));
    </script>
    
    <style>
        /* Select2 Custom Theme */
        .select2-container { font-family: 'Inter', sans-serif; }
        .select2-container--default .select2-selection--single {
            height: 46px;
            padding: 8px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: #fff;
            display: flex;
            align-items: center;
        }
        .select2-container--default .select2-selection--single:hover {
            border-color: #cbd5e1;
        }
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
            outline: none;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1f2937;
            line-height: 28px;
            padding-left: 0;
            font-size: 14px;
        }
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #9ca3af;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px;
            right: 10px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #6b7280 transparent transparent transparent;
        }
        .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #6b7280 transparent;
        }
        .select2-dropdown {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            margin-top: 4px;
            overflow: hidden;
        }
        .select2-container--default .select2-search--dropdown {
            padding: 10px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            outline: none;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .select2-results__option {
            padding: 12px 16px;
            font-size: 14px;
            color: #374151;
        }
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background: #3b82f6;
            color: white;
        }
        .select2-container--default .select2-results__option--selected {
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 500;
        }
        .select2-container--default .select2-selection--single .select2-selection__clear {
            position: absolute;
            right: 32px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: #9ca3af;
            font-weight: normal;
            cursor: pointer;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__clear:hover {
            color: #ef4444;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-right: 50px;
        }
        .select2-results__options { max-height: 280px; }
    </style>
</body>
</html>
