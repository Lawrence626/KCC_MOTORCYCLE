<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'KCC Motorcycle' }}</title>
    @include('partials.head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="flex h-screen relative">
        <!-- Mobile Menu Toggle -->
        <button id="mobile-menu-toggle" class="md:hidden fixed top-4 left-4 z-40 p-2 text-white bg-slate-800 rounded-lg hover:bg-slate-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Mobile Overlay (backdrop) -->
        <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-30 hidden md:hidden"></div>

        <!-- Sidebar -->
        <div id="sidebar-wrapper" class="fixed md:relative w-70 md:w-70 h-screen md:h-auto -translate-x-full md:translate-x-0 transition-transform duration-300 z-40 md:z-auto">
            @include('sidebar')
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto md:ml-0">
            <div class="p-6">
                {{ $slot }}
            </div>
        </div>
    </div>

    @include('partials.admin-purchase-order-toasts')

    <script>
        const menuToggle = document.getElementById('mobile-menu-toggle');
        const sidebarWrapper = document.getElementById('sidebar-wrapper');
        const overlay = document.getElementById('mobile-overlay');

        // Toggle sidebar on mobile
        menuToggle.addEventListener('click', function() {
            sidebarWrapper.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });

        // Close sidebar when clicking overlay
        overlay.addEventListener('click', function() {
            sidebarWrapper.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });

        // Close sidebar when clicking a link
        const links = sidebarWrapper.querySelectorAll('a');
        links.forEach(link => {
            link.addEventListener('click', function() {
                sidebarWrapper.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
