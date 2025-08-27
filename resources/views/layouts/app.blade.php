<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brickspoint Hotel - Your Escape Awaits</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* Custom styles for the page */
        body {
            font-family: 'Inter', sans-serif;
        }

        .hero-section {
            background-image: url('https://placehold.co/1920x1080/000000/FFFFFF?text=Stunning+Hotel+View');
            background-size: cover;
            background-position: center;
        }

        .whatsapp-bubble {
            background-color: #E2FDD7;
            /* Light green like WhatsApp chat */
        }

        .sticky-whatsapp {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 50;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">

    <!-- Header -->
    <header class="bg-gray-900/80 backdrop-blur text-white sticky top-0 z-40">
        <div class="container mx-auto flex justify-between items-center p-4">
            <a href="{{ route('home') }}" class="text-3xl font-bold">
                @if(setting('logo'))
                    <img src="{{ asset('storage/' . setting('logo')) }}" alt="Brickspoint Hotel Logo" class="h-12 w-auto">
                @else
                    Brickspoint
                @endif
            </a>
            <nav class="hidden md:flex items-center space-x-6">
                <a href="{{ route('home') }}" class="hover:text-green-400 transition-colors">Home</a>
                <a href="{{ route('rooms') }}" class="hover:text-green-400 transition-colors">Rooms</a>
                <a href="{{ route('gallery') }}" class="hover:text-green-400 transition-colors">Gallery</a>
                <a href="{{ route('home') }}#contact" class="hover:text-green-400 transition-colors">Contact</a>
                  {{-- @guest
                    <a href="{{ route('login') }}" class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded-md">Login</a>
                @endguest --}}
            </nav>
             <!-- Mobile Menu Button (optional) -->
            <div class="md:hidden">
                <button id="mobile-menu-button">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto text-center">
            <p>&copy; {{ date('Y') }} Brickspoint Hotel. All Rights Reserved.</p>
        </div>
    </footer>


    <!-- Sticky WhatsApp Button -->
    <a href="https://wa.me/+2348099999620?text=Hi,%20I'd%20like%20to%20book%20a%20room%20at%20Brickspoint." target="_blank"
        class="sticky-whatsapp bg-green-500 hover:bg-green-600 text-white rounded-full p-4 shadow-lg transition duration-300 ease-in-out transform hover:scale-110">
        <i class="fab fa-whatsapp text-4xl"></i>
    </a>

</body>

</html>
