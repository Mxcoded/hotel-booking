<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Brickspoint</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <div class="flex md:flex-row flex-col">
        <!-- Sidebar -->
        <div id="sidebar"
            class="bg-gray-800 text-white w-full md:w-64 min-h-screen md:fixed md:translate-x-0 -translate-x-full transition-transform duration-300 ease-in-out z-30">
            <div class="p-4 flex justify-between items-center">
                <a href="{{ route('admin.dashboard') }}" class="text-2xl font-bold">Admin Panel</a>
                <button id="close-sidebar-btn" class="md:hidden text-white">&times;</button>
            </div>
            <nav class="mt-10">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center p-4 rounded hover:bg-gray-700 transition-colors">
                    <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                </a>
                <a href="{{ route('admin.rooms.index') }}"
                    class="flex items-center p-4 rounded hover:bg-gray-700 transition-colors">
                    <i class="fas fa-bed mr-3"></i> Manage Rooms
                </a>
                <a href="{{ route('admin.gallery.index') }}"
                    class="flex items-center p-4 rounded hover:bg-gray-700 transition-colors">
                    <i class="fas fa-images mr-3"></i> Manage Gallery
                </a>
                <a href="{{ route('admin.contacts.index') }}"
                    class="flex items-center p-4 rounded hover:bg-gray-700 transition-colors">
                    <i class="fas fa-envelope mr-3"></i> Contact Messages
                    @php
                        $unreadCount = \App\Models\Contact::where('is_read', false)->count();
                    @endphp
                    @if ($unreadCount > 0)
                        <span
                            class="ml-auto bg-red-500 text-white text-xs font-semibold rounded-full px-2 py-0.5">{{ $unreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.feedback.index') }}"
                    class="flex items-center p-4 rounded hover:bg-gray-700 transition-colors">
                    <i class="fas fa-star mr-3"></i> Guest Feedback
                    @if ($unreadFeedbackCount > 0)
                        <span
                            class="ml-auto bg-red-500 text-white text-xs font-semibold rounded-full px-2 py-0.5">{{ $unreadFeedbackCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.whatsapp-leads.index') }}"
                    class="flex items-center p-4 rounded hover:bg-gray-700 transition-colors">
                    <i class="fab fa-whatsapp mr-3"></i> WhatsApp Leads
                </a>
                <div class="p-4 rounded hover:bg-gray-700 transition-colors">
                    <a href="{{ route('admin.settings.index') }}" class="flex items-center">
                        <i class="fas fa-cog mr-3"></i> Site Settings
                    </a>
                    <div class="ml-6 mt-2">
                        <a href="{{ route('admin.attractions.index') }}"
                            class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-map-signs mr-3"></i> Manage Attractions
                        </a>
                    </div>
                </div>

                <a href="{{ route('home') }}" target="_blank"
                    class="flex items-center p-4 rounded hover:bg-gray-700 transition-colors">
                    <i class="fas fa-globe mr-3"></i> View Website
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                        class="flex items-center p-4 rounded hover:bg-gray-700 transition-colors">
                        <i class="fas fa-sign-out-alt mr-3"></i> Logout
                    </a>
                </form>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 md:ml-64">
            <header class="bg-white shadow p-4 flex justify-between items-center">
                <button id="open-sidebar-btn" class="md:hidden text-gray-800">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
                <h1 class="text-xl font-semibold">@yield('title', 'Dashboard')</h1>
            </header>
            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        document.getElementById('open-sidebar-btn').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('-translate-x-full');
        });
        document.getElementById('close-sidebar-btn').addEventListener('click', function() {
            document.getElementById('sidebar').classList.add('-translate-x-full');
        });
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
        });
    </script>

</body>

</html>
