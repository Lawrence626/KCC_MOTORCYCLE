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
                @auth
                    <div class="mb-6 flex items-center justify-end gap-4">
                        <div class="relative">
                            <button id="profileDropdownButton" type="button" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-teal-500">
                                @if(optional(auth()->user())->avatar)
                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="avatar" class="h-8 w-8 rounded-full object-cover" />
                                @else
                                    <span class="h-8 w-8 rounded-full bg-teal-100 text-teal-700 grid place-items-center text-sm font-semibold">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                                @endif
                                <span class="hidden sm:inline-block">{{ auth()->user()->name }}</span>
                                <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/></svg>
                            </button>
                            <div id="profileDropdownMenu" class="hidden absolute right-0 z-50 mt-2 w-48 rounded-2xl border border-slate-200 bg-white shadow-xl ring-1 ring-black/5 transition duration-200 ease-out transform opacity-0 scale-95">
                                <div class="px-4 py-3 border-b border-slate-200">
                                    <p class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</p>
                                    <p class="text-xs text-cyan-600 font-medium mt-1">{{ ucfirst(str_replace('_', ' ', auth()->user()->role ?? 'user')) }}</p>
                                </div>
                                <div class="py-2">
                                    <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">View Profile</a>
                                    <a href="{{ route('settings.general') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">Settings</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-slate-100">Logout</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endauth

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

        const profileButton = document.getElementById('profileDropdownButton');
        const profileMenu = document.getElementById('profileDropdownMenu');

        if (profileButton && profileMenu) {
            let profileMenuTimeout;

            const showProfileMenu = () => {
                clearTimeout(profileMenuTimeout);
                profileMenu.classList.remove('hidden', 'opacity-0', 'scale-95');
                profileMenu.classList.add('opacity-100', 'scale-100');
            };

            const hideProfileMenu = () => {
                profileMenuTimeout = setTimeout(() => {
                    profileMenu.classList.add('opacity-0', 'scale-95');
                    profileMenu.classList.remove('opacity-100', 'scale-100');
                    setTimeout(() => profileMenu.classList.add('hidden'), 150);
                }, 180);
            };

            [profileButton, profileMenu].forEach((element) => {
                element.addEventListener('mouseenter', showProfileMenu);
                element.addEventListener('mouseleave', hideProfileMenu);
            });

            // allow click to toggle (helps on touch devices and when hover isn't available)
            const toggleProfileMenu = () => {
                if (profileMenu.classList.contains('hidden')) {
                    profileMenu.classList.remove('hidden', 'opacity-0', 'scale-95');
                    profileMenu.classList.add('opacity-100', 'scale-100');
                } else {
                    profileMenu.classList.add('opacity-0', 'scale-95');
                    profileMenu.classList.remove('opacity-100', 'scale-100');
                    setTimeout(() => profileMenu.classList.add('hidden'), 150);
                }
            };

            profileButton.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleProfileMenu();
            });

            document.addEventListener('click', function(event) {
                if (!event.target.closest('#profileDropdownMenu') && !event.target.closest('#profileDropdownButton')) {
                    profileMenu.classList.add('hidden', 'opacity-0', 'scale-95');
                }
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
