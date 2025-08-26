<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Brickspoint Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white p-6">
            <h1 class="text-2xl font-bold mb-8">Brickspoint Admin</h1>
            <nav class="space-y-4">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center p-2 rounded hover:bg-gray-700">
                    <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                </a>
                <a href="{{ route('admin.rooms.index') }}" class="flex items-center p-2 rounded hover:bg-gray-700">
                    <i class="fas fa-bed mr-3"></i> Manage Rooms
                </a>
                <a href="{{ route('admin.gallery.index') }}" class="flex items-center p-2 rounded hover:bg-gray-700">
                    <i class="fas fa-images mr-3"></i> Manage Gallery
                </a>
                <a href="{{ route('admin.settings.index') }}" class="flex items-center p-2 rounded hover:bg-gray-700">
                    <i class="fas fa-cog mr-3"></i> Site Settings
                </a>
                <a href="{{ route('home') }}" target="_blank" class="flex items-center p-2 rounded hover:bg-gray-700">
                     <i class="fas fa-globe mr-3"></i> View Website
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1">
            <!-- Top Bar -->
            <header class="bg-white shadow p-4 flex justify-between items-center">
                <h2 class="text-xl font-bold">@yield('title', 'Dashboard')</h2>
                <div>
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       class="text-gray-600 hover:text-red-500">
                        <i class="fas fa-sign-out-alt mr-1"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>

</body>
</html>
