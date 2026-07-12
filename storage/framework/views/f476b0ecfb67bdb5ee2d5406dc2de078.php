<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title ?? 'KCC Motorcycle'); ?></title>
    <?php echo $__env->make('partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/js/app.js']); ?>
</head>
<body class="bg-gray-100 h-dvh m-0 overflow-hidden">
    <div class="flex h-full relative">
        <!-- Mobile Menu Toggle -->
        <button id="mobile-menu-toggle" class="md:hidden fixed top-4 left-4 z-40 p-2 text-white bg-slate-800 rounded-lg hover:bg-slate-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Mobile Overlay (backdrop) -->
        <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-30 hidden md:hidden"></div>

        <!-- Sidebar -->
        <div id="sidebar-wrapper" class="fixed top-0 left-0 w-70 h-full -translate-x-full md:translate-x-0 transition-transform duration-300 z-30">
            <?php echo $__env->make('sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col md:ml-[270px] relative z-40 h-full">
            <!-- Header Container - Optional if a header slot is provided -->
            <?php if(!empty($header)): ?>
            <div id="dashboardHeader" class="flex-shrink-0 bg-white border border-slate-300 border-b-0 px-5 py-2 sticky top-0 z-10 transition-all duration-200 rounded-tl-[10px] rounded-tr-none shadow-none">
                <?php if(auth()->guard()->check()): ?>
                    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                        <div class="flex-1">
                            <?php echo e($header); ?>

                        </div>
                        <div class="flex items-center gap-3 md:mt-2 mt-2">
                            <button id="headerNotificationButton" type="button" class="inline-flex h-9 w-9 items-center justify-center border border-slate-700 text-white transition focus:outline-none invisible" style="border-radius: 20px; background-color: #0f0f0f;" aria-label="Notifications">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a6 6 0 00-6 6v4.586l-1.707 1.707A1 1 0 005 16h14a1 1 0 00.707-1.707L18 12.586V8a6 6 0 00-6-6zm0 18a2.5 2.5 0 002.45-2h-4.9A2.5 2.5 0 0012 20z"/></svg>
                            </button>
                            <div class="relative inline-flex items-center z-50">
                                <button id="headerProfileButton" type="button" class="inline-flex h-9 items-center gap-2 border border-slate-700 px-3 text-white transition focus:outline-none invisible" style="border-radius: 20px; background-color: #0f0f0f;">
                                    <span class="w-5.5 h-5.5 rounded-full bg-cyan-500 text-white grid place-items-center overflow-hidden text-sm font-semibold">
                                        <?php if(auth()->user()->avatar): ?>
                                            <img src="<?php echo e(asset('storage/' . auth()->user()->avatar)); ?>" alt="<?php echo e(auth()->user()->name); ?>" class="w-full h-full object-cover" />
                                        <?php else: ?>
                                            <?php echo e(strtoupper(substr(auth()->user()->name ?? 'U', 0, 1))); ?>

                                        <?php endif; ?>
                                    </span>
                                    <span class="hidden sm:inline-block text-[14px] font-medium text-white"><?php echo e(auth()->user()->name ?? 'Admin'); ?></span>
                                    <svg id="headerProfileArrow" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M7 10l5 5 5-5H7z" />
                                    </svg>
                                </button>

                                <div id="headerProfileDropdown" class="absolute right-0 top-full mt-2 w-65 min-h-[100px] rounded-[15px] border border-slate-700/60 bg-gradient-to-b from-[#0b0c10] to-[#20232a] shadow-2xl shadow-black/40 z-[99999] hidden opacity-0 transform scale-95 transition-all duration-200 origin-top-right">
                                    <div class="px-4 py-4 border-b border-slate-800/60">
                                        <div class="flex items-center gap-3">
                                            <span class="w-12 h-12 rounded-full bg-cyan-500 text-white grid place-items-center overflow-hidden text-lg font-semibold">
                                                <?php if(auth()->user()->avatar): ?>
                                                    <img src="<?php echo e(asset('storage/' . auth()->user()->avatar)); ?>" alt="<?php echo e(auth()->user()->name); ?>" class="w-full h-full object-cover" />
                                                <?php else: ?>
                                                    <?php echo e(strtoupper(substr(auth()->user()->name ?? 'U', 0, 1))); ?>

                                                <?php endif; ?>
                                            </span>
                                            <div>
                                                <div class="text-[13px] font-semibold text-white"><?php echo e(auth()->user()->name ?? 'Admin'); ?></div>
                                                <div class="text-[12px] text-slate-400"><?php echo e(auth()->user()->email ?? ''); ?></div>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <span class="inline-flex items-center rounded-full border border-cyan-500/20 bg-cyan-500/10 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[0.12em] text-cyan-300">
                                                <?php echo e(ucfirst(str_replace('_', ' ', auth()->user()->role ?? 'user'))); ?>

                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-1 px-2 py-2">
                                        <a href="<?php echo e(route('profile.show')); ?>" class="flex items-center gap-3 rounded-[10px] px-2.5 py-2.5 text-sm text-slate-100 hover:bg-slate-800 transition">
                                            <span class="w-6 h-6 grid place-items-center rounded-full bg-slate-950 text-slate-300">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.879 6.196 9 9 0 015.12 17.804z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            </span>
                                            <span>View Profile</span>
                                        </a>
                                        <a href="<?php echo e(route('settings.general')); ?>" class="flex items-center gap-3 rounded-[10px] px-2.5 py-2.5 text-sm text-slate-100 hover:bg-slate-800 transition">
                                            <span class="w-6 h-6 grid place-items-center rounded-full bg-slate-950 text-slate-300">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            </span>
                                            <span>Settings</span>
                                        </a>
                                        <form action="<?php echo e(route('logout')); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="flex w-full items-center gap-3 rounded-[10px] px-2.5 py-2.5 text-sm text-rose-300 hover:bg-slate-800 transition">
                                                <span class="w-6 h-6 grid place-items-center rounded-full bg-slate-950 text-rose-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                                </span>
                                                <span>Logout</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Content Container - Scrollable -->
            <div id="mainScrollArea" class="flex-1 min-h-0 overflow-y-auto relative flex flex-col z-40">
                <div id="topScrollFade" class="pointer-events-none absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-white/95 via-white/70 to-transparent opacity-0 transition-opacity duration-200"></div>
                <div class="w-full max-w-full flex-1">
                    <div class="bg-white border border-slate-300 border-t-0 shadow-sm overflow-hidden flex-1 min-h-0 rounded-[10px]">
                        <div class="h-full px-8 py-6 space-y-8">
                            <?php echo e($slot); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php echo $__env->make('partials.admin-purchase-order-toasts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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

        // Header profile dropdown
        const headerProfileButton = document.getElementById('headerProfileButton');
        const headerProfileDropdown = document.getElementById('headerProfileDropdown');
        const headerProfileArrow = document.getElementById('headerProfileArrow');
        const headerNotificationButton = document.getElementById('headerNotificationButton');
        const headerNotificationIcon = headerNotificationButton?.querySelector('svg');
        const dashboardHeader = document.getElementById('dashboardHeader');
        const mainScrollArea = document.getElementById('mainScrollArea');
        const topScrollFade = document.getElementById('topScrollFade');
        const dashboardProfileButton = document.getElementById('dashboardProfileButton');
        const dashboardProfileDropdown = document.getElementById('dashboardProfileDropdown');
        const dashboardProfileArrow = document.getElementById('dashboardProfileArrow');

        function closeProfileDropdown() {
            headerProfileDropdown.classList.add('hidden', 'opacity-0', 'scale-95');
            headerProfileDropdown.classList.remove('block', 'opacity-100', 'scale-100');
            headerProfileArrow.classList.remove('text-cyan-400');
            headerProfileArrow.classList.add('text-white');
            headerProfileButton.blur();
        }

        function openProfileDropdown() {
            headerProfileDropdown.classList.remove('hidden', 'opacity-0', 'scale-95');
            headerProfileDropdown.classList.add('block', 'opacity-100', 'scale-100');
            headerProfileArrow.classList.remove('text-white');
            headerProfileArrow.classList.add('text-cyan-400');
        }

        if (headerProfileButton && headerProfileDropdown && headerProfileArrow) {
            headerProfileDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });

            headerProfileButton.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = !headerProfileDropdown.classList.contains('hidden');
                if (isOpen) {
                    closeProfileDropdown();
                } else {
                    openProfileDropdown();
                }
            });

            if (headerNotificationButton && headerNotificationIcon) {
                headerNotificationButton.addEventListener('click', function() {
                    headerNotificationIcon.classList.toggle('text-cyan-400');
                    headerNotificationIcon.classList.toggle('text-white');
                });
            }

            window.addEventListener('click', function(e) {
                if (!headerProfileDropdown.contains(e.target) && !headerProfileButton.contains(e.target)) {
                    closeProfileDropdown();
                }
            });
        }

        // Floating profile dropdown handlers

        // Dashboard profile dropdown handlers
        if (dashboardProfileButton && dashboardProfileDropdown && dashboardProfileArrow) {
            dashboardProfileDropdown.addEventListener('click', function(e) { e.stopPropagation(); });

            dashboardProfileButton.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = !dashboardProfileDropdown.classList.contains('hidden');
                if (isOpen) {
                    dashboardProfileDropdown.classList.add('hidden', 'opacity-0', 'scale-95');
                    dashboardProfileDropdown.classList.remove('block', 'opacity-100', 'scale-100');
                    dashboardProfileButton.blur();
                } else {
                    dashboardProfileDropdown.classList.remove('hidden', 'opacity-0', 'scale-95');
                    dashboardProfileDropdown.classList.add('block', 'opacity-100', 'scale-100');
                }
            });

            window.addEventListener('click', function(e) {
                if (!dashboardProfileDropdown.contains(e.target) && !dashboardProfileButton.contains(e.target)) {
                    dashboardProfileDropdown.classList.add('hidden', 'opacity-0', 'scale-95');
                    dashboardProfileDropdown.classList.remove('block', 'opacity-100', 'scale-100');
                    dashboardProfileButton.blur();
                }
            });
        }
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\Users\ilano\Herd\KCC_MOTORCYCLE\KCC_MOTORCYCLE\resources\views/components/layouts/app.blade.php ENDPATH**/ ?>