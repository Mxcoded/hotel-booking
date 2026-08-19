@php
    $unreadContacts = \App\Models\Contact::where('is_read', false)->count();
    $unreadFeedback = \App\Models\Feedback::where('is_read', false)->count();
    $user = Auth::user();
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Brickspoint Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50: '#fffbeb', 100: '#fef3c7', 200: '#fde68a', 300: '#fcd34d', 400: '#fbbf24', 500: '#f59e0b', 600: '#d97706', 700: '#b45309', 800: '#92400e', 900: '#78350f' }
                    }
                }
            }
        }
    </script>
    @stack('styles')
    <style>
        /* Sidebar transition */
        #sidebar { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        /* Overlay */
        #sidebar-overlay {
            opacity: 0; pointer-events: none;
            transition: opacity 0.3s ease;
        }
        #sidebar-overlay.active { opacity: 1; pointer-events: auto; }
        /* Nav active */
        .nav-link { transition: all 0.15s ease; }
        .nav-link.active { background: rgba(255,255,255,0.12); color: #fbbf24; }
        .nav-link.active i { color: #fbbf24; }
        /* Scrollbar */
        #sidebar nav::-webkit-scrollbar { width: 4px; }
        #sidebar nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 2px; }
        /* Badge pulse */
        .badge-pulse { animation: pulse-badge 2s infinite; }
        @keyframes pulse-badge { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
        /* Alpine cloak */
        [x-cloak] { display: none !important; }
        /* Header shadow on scroll */
        .header-scrolled { box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-gray-100 h-full font-sans antialiased">

    {{-- Sidebar overlay (mobile) --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 md:hidden" onclick="closeSidebar()" aria-hidden="true"></div>

    <div class="flex min-h-full">

        {{-- Sidebar --}}
        <aside id="sidebar" class="fixed top-0 left-0 z-50 h-full w-72 bg-gray-900 text-gray-300 flex flex-col -translate-x-full md:translate-x-0 md:z-30" aria-label="Admin sidebar">

            {{-- Brand --}}
            <div class="flex items-center justify-between px-5 py-5 border-b border-white/10">
                <a href="{{ route('filament.admin.pages.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-lg">B</span>
                    </div>
                    <div>
                        <span class="text-white font-bold text-base leading-none block">Brickspoint</span>
                        <span class="text-[11px] text-gray-500 tracking-wider uppercase">Admin Panel</span>
                    </div>
                </a>
                <button onclick="closeSidebar()" class="md:hidden text-gray-400 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors" aria-label="Close sidebar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">

                {{-- Main --}}
                <p class="px-3 pt-1 pb-2 text-[10px] font-semibold text-gray-500 uppercase tracking-widest">Main</p>

                <a href="{{ route('filament.admin.pages.dashboard') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-white/10 {{ request()->routeIs('filament.admin.pages.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-th-large w-5 text-center text-gray-500"></i>
                    <span>Dashboard</span>
                </a>

                {{-- Management --}}
                <p class="px-3 pt-5 pb-2 text-[10px] font-semibold text-gray-500 uppercase tracking-widest">Management</p>

                <a href="{{ route('filament.admin.resources.rooms.index') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-white/10 {{ request()->routeIs('filament.admin.resources.rooms.*') ? 'active' : '' }}">
                    <i class="fas fa-bed w-5 text-center text-gray-500"></i>
                    <span>Rooms</span>
                </a>

                <a href="{{ route('filament.admin.resources.galleries.index') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-white/10 {{ request()->routeIs('filament.admin.resources.galleries.*') ? 'active' : '' }}">
                    <i class="fas fa-images w-5 text-center text-gray-500"></i>
                    <span>Gallery</span>
                </a>

                <a href="{{ route('admin.menu.index') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-white/10 {{ request()->routeIs('admin.menu.*') ? 'active' : '' }}">
                    <i class="fas fa-utensils w-5 text-center text-gray-500"></i>
                    <span>Food Menu</span>
                </a>

                <a href="{{ route('filament.admin.resources.attractions.index') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-white/10 {{ request()->routeIs('filament.admin.resources.attractions.*') ? 'active' : '' }}">
                    <i class="fas fa-map-signs w-5 text-center text-gray-500"></i>
                    <span>Attractions</span>
                </a>

                {{-- Communications --}}
                <p class="px-3 pt-5 pb-2 text-[10px] font-semibold text-gray-500 uppercase tracking-widest">Communications</p>

                <a href="{{ route('filament.admin.resources.contacts.index') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-white/10 {{ request()->routeIs('filament.admin.resources.contacts.*') ? 'active' : '' }}">
                    <i class="fas fa-envelope w-5 text-center text-gray-500"></i>
                    <span>Messages</span>
                    @if($unreadContacts > 0)
                        <span class="ml-auto bg-red-500 text-white text-[10px] font-bold rounded-full px-2 py-0.5 badge-pulse">{{ $unreadContacts }}</span>
                    @endif
                </a>

                <a href="{{ route('filament.admin.resources.feedback.index') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-white/10 {{ request()->routeIs('filament.admin.resources.feedback.*') ? 'active' : '' }}">
                    <i class="fas fa-star w-5 text-center text-gray-500"></i>
                    <span>Feedback</span>
                    @if($unreadFeedback > 0)
                        <span class="ml-auto bg-red-500 text-white text-[10px] font-bold rounded-full px-2 py-0.5 badge-pulse">{{ $unreadFeedback }}</span>
                    @endif
                </a>

                <a href="{{ route('filament.admin.resources.whatsapp-leads.index') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-white/10 {{ request()->routeIs('filament.admin.resources.whatsapp-leads.*') ? 'active' : '' }}">
                    <i class="fab fa-whatsapp w-5 text-center text-gray-500"></i>
                    <span>WhatsApp Leads</span>
                </a>

                {{-- System --}}
                <p class="px-3 pt-5 pb-2 text-[10px] font-semibold text-gray-500 uppercase tracking-widest">System</p>

                <a href="{{ route('filament.admin.resources.settings.index') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-white/10 {{ request()->routeIs('filament.admin.resources.settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog w-5 text-center text-gray-500"></i>
                    <span>Settings</span>
                </a>

                <a href="{{ route('home') }}" target="_blank" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-white/10">
                    <i class="fas fa-globe w-5 text-center text-gray-500"></i>
                    <span>View Website</span>
                    <i class="fas fa-external-link-alt ml-auto text-[10px] text-gray-600"></i>
                </a>
            </nav>

            {{-- User card --}}
            <div class="border-t border-white/10 px-4 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-white text-sm font-semibold truncate">{{ $user->name }}</p>
                        <p class="text-gray-500 text-xs truncate">{{ $user->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-red-400 p-1.5 rounded-lg hover:bg-white/10 transition-colors" title="Logout" aria-label="Logout">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main area --}}
        <div class="flex-1 md:ml-72 flex flex-col min-h-full">

            {{-- Header --}}
            <header id="main-header" class="sticky top-0 z-20 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between px-4 sm:px-6 h-16">
                    {{-- Left --}}
                    <div class="flex items-center gap-3">
                        <button onclick="openSidebar()" class="md:hidden text-gray-500 hover:text-gray-700 p-2 -ml-2 rounded-lg hover:bg-gray-100 transition-colors" aria-label="Open sidebar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <div>
                            <h1 class="text-lg font-bold text-gray-800 leading-tight">@yield('title', 'Dashboard')</h1>
                        </div>
                    </div>

                    {{-- Right --}}
                    <div class="flex items-center gap-2 sm:gap-3">
                        {{-- Notification badges --}}
                        @if($unreadContacts > 0 || $unreadFeedback > 0)
                            <div class="hidden sm:flex items-center gap-2">
                                @if($unreadContacts > 0)
                                    <a href="{{ route('filament.admin.resources.contacts.index') }}" class="inline-flex items-center gap-1.5 bg-red-50 text-red-600 text-xs font-semibold px-3 py-1.5 rounded-full hover:bg-red-100 transition-colors">
                                        <i class="fas fa-envelope text-[10px]"></i>
                                        <span>{{ $unreadContacts }}</span>
                                    </a>
                                @endif
                                @if($unreadFeedback > 0)
                                    <a href="{{ route('filament.admin.resources.feedback.index') }}" class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-600 text-xs font-semibold px-3 py-1.5 rounded-full hover:bg-amber-100 transition-colors">
                                        <i class="fas fa-star text-[10px]"></i>
                                        <span>{{ $unreadFeedback }}</span>
                                    </a>
                                @endif
                            </div>
                        @endif

                        {{-- User menu --}}
                        <div class="relative" x-data="{ open: false }" @click.away="open = false" @keydown.escape.window="open = false">
                            <button x-on:click="open = !open" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white font-bold text-xs">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="hidden sm:block text-sm font-medium text-gray-700">{{ $user->name }}</span>
                                <svg class="w-4 h-4 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50">
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-800">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                                <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-globe w-4 text-center text-gray-400"></i> View Website
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <i class="fas fa-sign-out-alt w-4 text-center"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Page content --}}
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>

            {{-- Footer --}}
            <footer class="border-t border-gray-200 bg-white px-6 py-4">
                <p class="text-xs text-gray-400 text-center">&copy; {{ date('Y') }} Brickspoint Hotel. All rights reserved.</p>
            </footer>
        </div>
    </div>

    {{-- Alpine.js for dropdown --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        function openSidebar() {
            document.getElementById('sidebar').classList.remove('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.add('active');
            document.body.classList.add('overflow-hidden');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.add('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.remove('active');
            document.body.classList.remove('overflow-hidden');
        }

        // Escape key closes sidebar
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeSidebar();
        });

        // Header shadow on scroll
        window.addEventListener('scroll', function() {
            const header = document.getElementById('main-header');
            if (window.scrollY > 0) {
                header.classList.add('header-scrolled');
            } else {
                header.classList.remove('header-scrolled');
            }
        });

        // Close sidebar on resize to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                closeSidebar();
                document.body.classList.remove('overflow-hidden');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
