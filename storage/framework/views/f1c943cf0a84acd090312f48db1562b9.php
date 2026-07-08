<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('Dashboard')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Dashboard'))]); ?>
    <?php
        $dashboardAlert = collect($lowStockNotifications ?? [])->first(fn ($notification) => ($notification['status'] ?? 'active') !== 'dismissed');
    ?>
    <div id="dashboard-root" data-dashboard-url="<?php echo e(route('dashboard.data')); ?>" data-refresh-interval="15000" class="space-y-3">
       
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="pl-3 lg:pl-6">
                    <h1 class="text-3xl font-bold text-slate-900">Dashboard</h1>
                    <p class="text-gray-600 text-sm mt-1">Overview of sales, inventory and performance insights</p>
                </div>
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center">
                    
                   
                    <div class="relative z-30 inline-flex items-center gap-2 rounded-[20px] px-3 py-2 text-left">
                        <button id="dashboardNotificationButton" type="button" class="relative z-40 inline-flex h-11 w-11 cursor-pointer items-center justify-center transition text-black" aria-label="Notifications" style="background: transparent; border: none;" onclick="event.stopPropagation(); const dropdown=document.getElementById('dashboardNotificationDropdown'); if (dropdown) { dropdown.classList.toggle('hidden'); }">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a6 6 0 00-6 6v4.586l-1.707 1.707A1 1 0 005 16h14a1 1 0 00.707-1.707L18 12.586V8a6 6 0 00-6-6zm0 18a2.5 2.5 0 002.45-2h-4.9A2.5 2.5 0 0012 20z"/></svg>
                            <?php if(!empty($lowStockNotifications)): ?>
                                <span class="absolute -top-1 -right-1 inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-rose-600 px-1.5 text-[10px] font-semibold text-white"><?php echo e(count($lowStockNotifications)); ?></span>
                            <?php endif; ?>
                        </button>
                        <div id="dashboardNotificationDropdown" class="hidden absolute right-0 top-full z-50 mt-2 w-[24rem] overflow-hidden rounded-[20px] border border-slate-200 bg-white shadow-xl shadow-slate-900/10">
                            <div class="px-4 py-4 border-b border-slate-200">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">Notifications</p>
                                        <p class="text-xs text-slate-500">Recent alerts and purchase order reminders.</p>
                                    </div>
                                    <button id="dashboardNotificationClose" type="button" class="text-slate-400 transition hover:text-slate-700" aria-label="Close notifications">×</button>
                                </div>
                            </div>
                            <div id="dashboardNotificationList" class="max-h-80 overflow-y-auto">
                                <?php if(empty($lowStockNotifications)): ?>
                                    <div class="p-4 text-sm text-slate-600">You have no new reorder notifications.</div>
                                <?php else: ?>
                                    <?php $__currentLoopData = $lowStockNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="border-b border-slate-100 px-4 py-3 last:border-b-0 <?php echo e(($notification['is_dashboard_alert'] ?? false) ? 'bg-amber-50' : ''); ?>" data-notification-item data-product-id="<?php echo e($notification['product_id'] ?? ''); ?>">
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <p class="text-sm font-semibold text-slate-900"><?php echo e($notification['product_name'] ?? 'Product'); ?></p>
                                                    <p class="mt-1 text-sm text-slate-600"><?php echo e($notification['message'] ?? 'Low stock alert.'); ?></p>
                                                </div>
                                            </div>
                                            <div class="mt-3 flex items-center justify-between gap-2">
                                                <span class="text-xs font-semibold uppercase tracking-[0.16em] text-amber-600">Stock <?php echo e($notification['stock_quantity'] ?? 0); ?> • Reorder <?php echo e($notification['reorder_level'] ?? 0); ?></span>
                                                <a href="<?php echo e($notification['url'] ?? route('order.create', ['product_id' => $notification['product_id']])); ?>" class="rounded-xl bg-emerald-600 px-3 py-2 text-[13px] font-semibold text-white transition hover:bg-emerald-700">Create order</a>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="inline-flex items-center gap-1.5 rounded-[20px] px-3 py-2 text-left">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-cyan-500 text-white grid place-items-center text-lg font-semibold overflow-hidden">
                            <?php echo e(strtoupper(substr(auth()->user()->name ?? 'A', 0, 1))); ?>

                        </span>
                        <div class="flex flex-col leading-tight">
                            <span class="text-sm font-semibold text-black"><?php echo e(auth()->user()->name ?? 'Admin'); ?></span>
                            <span class="text-xs text-gray-500"><?php echo e(auth()->user()->email ?? ''); ?></span>
                        </div>
                        <button type="button" id="dashboardProfileButton" class="inline-flex h-7 w-7 items-center justify-center rounded-[12px] bg-transparent text-[#0f0f0f] transition-colors duration-200 focus:outline-none hover:bg-transparent focus:bg-transparent active:bg-transparent hover:text-slate-400 border-none" style="background: transparent !important; border: none !important; box-shadow: none !important;" aria-label="Open profile menu">
                            <svg id="dashboardProfileArrow" class="w-5 h-5 text-current transition-colors duration-200" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5H7z"/></svg>
                        </button>

                        <div id="dashboardProfileDropdown" class="absolute right-0 top-full mt-2 w-65 min-h-[100px] rounded-[15px] bg-[#f0f0f0] shadow-2xl shadow-black/20 z-50 hidden opacity-0 transform scale-95 transition-all duration-200 origin-top-right" style="color: #000;">
                            <div class="px-4 py-4 border-b border-slate-300/60">
                                <div class="flex items-center gap-3">
                                    <span class="w-12 h-12 rounded-full bg-cyan-500 text-white grid place-items-center overflow-hidden text-lg font-semibold">
                                        <?php echo e(strtoupper(substr(auth()->user()->name ?? 'U', 0, 1))); ?>

                                    </span>
                                    <div>
                                        <div class="text-[13px] font-semibold text-black"><?php echo e(auth()->user()->name ?? 'Admin'); ?></div>
                                        <div class="text-[12px] text-black"><?php echo e(auth()->user()->email ?? ''); ?></div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <span class="inline-flex items-center rounded-full border border-cyan-500/20 bg-cyan-500/10 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[0.12em] text-cyan-700">
                                        <?php echo e(ucfirst(str_replace('_', ' ', auth()->user()->role ?? 'user'))); ?>

                                    </span>
                                </div>
                            </div>
                            <div class="flex flex-col gap-1 px-2 py-2">
                                <a href="<?php echo e(route('profile.show')); ?>" class="flex items-center gap-3 rounded-[10px] px-2.5 py-2.5 text-sm text-black hover:bg-slate-200 transition">
                                    <span class="w-6 h-6 grid place-items-center rounded-full bg-slate-700 text-white">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.879 6.196 9 9 0 015.12 17.804z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </span>
                                    <span>View Profile</span>
                                </a>
                                <a href="<?php echo e(route('settings.general')); ?>" class="flex items-center gap-3 rounded-[10px] px-2.5 py-2.5 text-sm text-black hover:bg-slate-200 transition">
                                    <span class="w-6 h-6 grid place-items-center rounded-full bg-slate-700 text-white">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </span>
                                    <span>Settings</span>
                                </a>
                                <form action="<?php echo e(route('logout')); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="flex w-full items-center gap-3 rounded-[10px] px-2.5 py-2.5 text-sm text-black hover:bg-slate-200 transition">
                                        <span class="w-6 h-6 grid place-items-center rounded-full bg-slate-700 text-white">
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
        </div>

        <?php if($dashboardAlert): ?>
            <div id="dashboardLowStockBanner"
                 role="status"
                 aria-live="polite"
                 class="pointer-events-auto fixed right-4 top-24 z-[60] hidden w-[min(24rem,calc(100%-2rem))] translate-x-6 rounded-[20px] border border-amber-200 bg-white/95 p-4 shadow-2xl shadow-slate-900/20 backdrop-blur transition-all duration-300"
                 data-alert-product-id="<?php echo e($dashboardAlert['product_id'] ?? ''); ?>"
                 data-alert-delay-ms="<?php echo e($dashboardAlert['dashboard_alert_delay_ms'] ?? 60000); ?>">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1">
                        <div class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-amber-700">Low stock alert</div>
                        <p class="banner-title mt-2 text-sm font-semibold text-slate-900">Low stock alert</p>
                        <p class="banner-message mt-1 text-sm text-slate-600"><?php echo e($dashboardAlert['message'] ?? 'A product is running low on stock.'); ?></p>
                    </div>
                    <button type="button" id="dashboardLowStockBannerDismiss" class="rounded-full p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" aria-label="Dismiss alert">×</button>
                </div>
                <div class="mt-3 flex items-center justify-between gap-2">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.16em] text-amber-600">Moves to notifications after a minute</span>
                    <a href="<?php echo e($dashboardAlert['url'] ?? route('order.create', ['product_id' => $dashboardAlert['product_id']])); ?>" class="rounded-xl bg-emerald-600 px-3 py-2 text-[13px] font-semibold text-white transition hover:bg-emerald-700">Create order</a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Stats Grid -->
        <div class="container mx-auto px-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Total Sales -->
                <div class="border border-gray-200 p-3 hover:shadow-sm transition" style="border-radius: 20px; background-color: #0f0f0f;">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-white text-xs font-semibold">Total Sales</p>
                            <div class="mt-1">
                                <p id="salesValue" class="text-2xl font-bold text-white">—</p>
                                <p id="salesComparison" class="text-cyan-400 text-xs mt-1 font-medium">Loading…</p>
                            </div>
                        </div>
                        <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 20px; background-color: #A4DD00;">
                            <svg class="w-5 h-5" style="color: #0f0f0f;" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                            </svg>
                        </div>
                    </div>
                </div>

            <!-- Total Transaction -->
            <div class="border border-gray-200 p-3 hover:shadow-sm transition" style="border-radius: 20px; background-color: #0f0f0f;">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-black text-xs font-semibold" style="color: #ffffff;">Total Transaction</p>
                        <div class="mt-1">
                            <p id="transactionsValue" class="text-2xl font-bold" style="color: #ffffff;">—</p>
                            <p id="transactionsComparison" class="text-cyan-400 text-xs mt-1 font-medium;">Loading…</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 20px; background-color: #ffe600;">
                        <svg class="w-5 h-5" style="color: #000000;" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path d="M6.99 11L3 15l3.99 4v-3H14v-2H6.99v-3zM21 9l-3.99-4v3H10v2h7.01v3L21 9z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Profit -->
            <div class="border border-gray-200 p-3 hover:shadow-sm transition" style="border-radius: 20px; background-color: #0f0f0f;">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-black text-xs font-semibold" style="color: #ffffff;">Total Profit</p>
                        <div class="mt-1">
                            <p id="profitValue" class="text-2xl font-bold" style="color: #f0f0f0;">—</p>
                            <p id="profitComparison" class="text-cyan-400 text-xs mt-1 font-medium;">Loading…</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 20px; background-color: #ff9900;">
                        <svg class="w-5 h-5" style="color: #000000;" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Item Sold -->
            <div class="border border-gray-200 p-3 hover:shadow-sm transition" style="border-radius: 20px; background-color: #0f0f0f;">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-black text-xs font-semibold" style="color: #ffffff;">Total Item Sold</p>
                        <div class="mt-1">
                            <p id="itemsSoldValue" class="text-2xl font-bold" style="color: #ffffff;">—</p>
                            <p id="itemsSoldComparison" class="text-cyan-400 text-xs mt-1 font-medium;">Loading…</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 20px; background-color: #ff6600;">
                        <svg class="w-5 h-5" style="color: #000000;" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 pt-4">
            <!-- Sales Overview Chart -->
            <div id="salesOverviewCard" class="lg:col-span-2 border border-gray-200 p-4 relative overflow-hidden" style="border-radius: 20px; background-color: #0f0f0f; min-height: 240px; box-sizing: border-box;">

                <!-- Header (title + range buttons) -->
                <div id="salesOverviewHeader" class="flex items-center justify-between mb-3 relative">
                    <div class="flex items-center gap-2">
                        <button type="button" id="closeSalesOverviewBtn" title="Close report"
                            class="w-7 h-7 flex items-center justify-center rounded-full flex-shrink-0 transition"
                            style="background-color: #1a1a1a; border: 1px solid rgba(255,255,255,0.15);">
                            <svg class="w-4 h-4" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <polyline points="15 5 8 12 15 19"></polyline>
                            </svg>
                        </button>
                       <h2 id="salesOverviewTitle" class="font-semibold tracking-wide text-white" style="font-size: 20px; font-family: 'Poppins', sans-serif;">Sales Overview</h2>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" data-range="daily" class="sales-range-btn px-3 py-1 text-sm font-medium text-gray-300 bg-neutral-800 rounded transition">Day</button>
                        <button type="button" data-range="weekly" class="sales-range-btn px-3 py-1 text-sm font-medium text-gray-300 bg-neutral-800 rounded transition">Week</button>
                        <button type="button" data-range="monthly" class="sales-range-btn px-3 py-1 text-sm font-medium text-black rounded transition" style="background-color: #1ab3ce;">Month</button>
                    </div>
                </div>

                <!-- Body (chart) -->
                <div id="salesOverviewBody" class="relative w-full" style="height:360px;">
                    <canvas id="salesChart"></canvas>
                </div>

                <!--
                    FIXED: Overlay Picture container.
                    Instead of relying only on the Tailwind "absolute inset-0" utility (which can
                    fail to size correctly if not compiled), we now pin every edge explicitly and
                    force width/height to 100% + border-box, so this overlay is GUARANTEED to be
                    exactly the same size/shape as its parent (#salesOverviewCard), corner for corner.
                -->
                <div id="salesOverviewOverlay" class="absolute" style="
                    top: 0; left: 0; right: 0; bottom: 0;
                    width: 100%; height: 100%;
                    box-sizing: border-box;
                    border-radius: 20px;
                    overflow: hidden;
                    z-index: 40;
                    background-color: #0f0f0f;">
                    <div style="width: 100%; height: 100%; overflow: hidden; border-radius: 20px;">
                        <img src="<?php echo e(asset('images/H.png')); ?>" alt="Sales Report" id="salesOverviewImg" style="
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                            object-position: center 85%;
                            transform: scale(1);
                            transform-origin: center 15%;
                            display: block;">
                    </div>

                    <div class="absolute top-3 right-3 flex items-center gap-2">
                        <!-- View Report button -->
                        <button type="button" id="viewReportBtn" class="report-toggle-btn flex items-center px-3 py-2 rounded-[10px] text-xs font-semibold text-white" style="background-color: #0f0f0f; backdrop-filter: blur(4px);">
                            <span id="viewReportLabel" style="font-family: 'Poppins', sans-serif;">View Report</span>
                        </button>

                        <!-- Calendar icon (toggles the panel below) -->
                        <button type="button" id="calendarBtn" class="cal-toggle-btn w-9 h-9 flex items-center justify-center rounded-full transition" style="background-color: #0f0f0f; backdrop-filter: blur(4px);">
                            <svg class="w-5 h-5" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="3"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </button>
                    </div>

                    <!--
                        FIXED: CALENDAR PANEL.
                        Same fix here - explicit top/left/right/bottom:0 + width/height:100% +
                        box-sizing:border-box + overflow:hidden + matching border-radius, so the
                        dark calendar background is pinned exactly to the picture container's
                        bounds. No more square corner poking out past the rounded card.
                    -->
                    <div id="calendarPanel"
                         class="flex flex-col p-4 md:p-5 text-white opacity-0 scale-[0.98] pointer-events-none"
                         style="position: absolute; top: 14px; left: 14px; right: 14px; bottom: 14px;
                                width: calc(100% - 28px); height: calc(100% - 28px); box-sizing: border-box;
                                background-color: rgba(0,0,0,0.6); border-radius: 16px;
                                overflow: hidden; z-index: 45;
                                transition: opacity 0.35s ease, transform 0.35s cubic-bezier(0.22, 1, 0.36, 1);">

                        <!-- Top row: nav arrows (left) + close (right) -->
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex gap-2">
                                <button type="button" id="prevMonthBtn"
                                    class="w-8 h-8 flex items-center justify-center rounded-full transition"
                                    style="background-color: rgba(255,255,255,0.12);">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <polyline points="15 19 8 12 15 5"></polyline>
                                    </svg>
                                </button>
                                <button type="button" id="nextMonthBtn"
                                    class="w-8 h-8 flex items-center justify-center rounded-full transition"
                                    style="background-color: rgba(255,255,255,0.12);">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <polyline points="9 5 16 12 9 19"></polyline>
                                    </svg>
                                </button>
                            </div>
                            <button type="button" id="closeCalendarBtn"
                                class="w-8 h-8 flex items-center justify-center rounded-full transition"
                                style="background-color: rgba(255,255,255,0.12);">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>

                        <!-- CALENDAR label + big year/month, right aligned -->
                        <div class="text-right mb-5 pr-4 md:pr-7">
                            <p class="uppercase tracking-[0.35em] text-[10px] md:text-[11px] text-white/70 mb-1">Calendar</p>
                            <h2 id="monthYearDisplay" class="font-bold leading-none text-xl md:text-3xl">July 2026</h2>
                        </div>

                        <!-- Days of Week Header -->
                        <div class="grid grid-cols-7 gap-1 mb-5 text-center">
                            <div class="text-[10px] md:text-[14px] font-semibold text-white/70 py-1">Mon</div>
                            <div class="text-[10px] md:text-[14px] font-semibold text-white/70 py-1">Tue</div>
                            <div class="text-[10px] md:text-[14px] font-semibold text-white/70 py-1">Wed</div>
                            <div class="text-[10px] md:text-[14px] font-semibold text-white/70 py-1">Thu</div>
                            <div class="text-[10px] md:text-[14px] font-semibold text-white/70 py-1">Fri</div>
                            <div class="text-[10px] md:text-[14px] font-semibold text-white/70 py-1">Sat</div>
                            <div class="text-[10px] md:text-[14px] font-semibold text-white/70 py-1">Sun</div>
                        </div>

                        <!-- Calendar Grid -->
                        <div id="calendarGrid" class="grid grid-cols-7 gap-1 flex-1 content-start" style="grid-template-columns: repeat(7, minmax(0, 1fr));">
                            <!-- Generated dynamically by JavaScript -->
                        </div>
                    </div>
                    <!-- =================== END CALENDAR PANEL =================== -->
                </div>
            </div>

            <!-- Sales by Category (full-circle ring + white knockout center + neon-on-sale legend) -->
            <div class="border border-gray-200 p-3" style="border-radius: 20px; background-color: #0f0f0f;">
                <h2 class="text-sm font-bold text-white mb-2" style="font-family: 'Poppins', sans-serif;">Sales by Category</h2>
                <div class="flex flex-col items-center gap-3">
                    <div style="position: relative; width: 150px; height: 160px; max-width: 160px; max-height: 160px; aspect-ratio: 1 / 1;">
                        <canvas id="categoryChart"></canvas>
                        <div style="
                            position: absolute; top: 56%; left: 50%;
                            transform: translate(-50%, -50%);
                            width: 115px; height: 115px;
                            border-radius: 9999px;
                            background-color: transparent;
                            display: flex; flex-direction: column;
                            align-items: center; justify-content: center;
                            text-align: center;
                            pointer-events: none;">
                            <span id="categoryCenterValue" style="color: #ffffff; font-weight: 700; font-size: 15px; line-height: 1.1;">0</span>
                            <span id="categoryCenterCaption" style="color: rgba(255,255,255,0.6); font-size: 9px; margin-top: 6px;">No sales today</span>
                        </div>
                    </div>

                    <div id="categoryLegend" class="w-full space-y-1 text-xs"></div>
                </div>
            </div>
        </div>

        <!-- Tables Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 pt-4">

            <!-- Inventory Levels (compact card, stacked/overlapping rows, no popup) -->
            <div id="inventoryCardWrap" class="relative">

                <div id="inventoryCard" class="border border-gray-200 p-3" style="border-radius: 20px; background-color: #f0f0f0;">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-black text-sm font-bold">Inventory Levels</h2>
                    </div>

                    <div class="inv-stack" style="position: relative;">

                        <!-- Total Products (top of the stack) -->
                        <div class="inv-row flex items-center gap-2 px-3 py-3" style="border-radius: 18px; background-color: #0f0f0f; position: relative; z-index: 40;">
                            <div class="w-7 h-7 flex items-center justify-center flex-shrink-0 rounded-full" style="background-color: #A4DD00;">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path d="M21 8l-9-5-9 5 9 5 9-5z"></path>
                                    <path d="M3 8v8l9 5 9-5V8"></path>
                                </svg>
                            </div>
                            <span class="text-white text-xs font-semibold flex-1">Total Products</span>
                            <span id="totalProductsValue" class="text-white text-xs font-bold">—</span>
                        </div>

                        <!-- Low Stock Items -->
                        <div class="inv-row flex items-center gap-2 px-3" style="border-radius: 18px; background-color: #202020; position: relative; z-index: 30; margin-top: -12px; padding-top: 20px; padding-bottom: 12px; border-top: 1px solid rgba(255,255,255,0.08);">
                            <div class="w-7 h-7 flex items-center justify-center flex-shrink-0 rounded-full" style="background-color: #ff3300;">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="#030303" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path d="M12 9v4"></path>
                                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                    <path d="M12 17h.01"></path>
                                </svg>
                            </div>
                            <span class="text-white text-xs font-semibold flex-1">Low Stock Items</span>
                            <span id="lowStockValue" class="text-white text-xs font-bold">—</span>
                        </div>

                        <!-- Out of Stock Items -->
                        <div class="inv-row flex items-center gap-2 px-3" style="border-radius: 18px; background-color: #414040; position: relative; z-index: 20; margin-top: -12px; padding-top: 20px; padding-bottom: 12px; border-top: 1px solid rgba(255,255,255,0.08);">
                            <div class="w-7 h-7 flex items-center justify-center flex-shrink-0 rounded-full" style="background-color: #ffe600;">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                </svg>
                            </div>
                            <span class="text-white text-xs font-semibold flex-1">Out of Stock Items</span>
                            <span id="outOfStockValue" class="text-white text-xs font-bold">—</span>
                        </div>

                        <!-- In Stock Items (bottom of the stack) -->
                        <div class="inv-row flex items-center gap-2 px-3" style="border-radius: 18px; background-color: #5c5c5c; position: relative; z-index: 10; margin-top: -12px; padding-top: 20px; padding-bottom: 12px; border-top: 1px solid rgba(255,255,255,0.08);">
                            <div class="w-7 h-7 flex items-center justify-center flex-shrink-0 rounded-full" style="background-color: #ff9900;">    
                                <svg class="w-3.5 h-3.5" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <polyline points="8 12 11 15 16 9"></polyline>
                                </svg>
                            </div>
                            <span class="text-white text-xs font-semibold flex-1">In Stock Items</span>
                            <span id="inStockValue" class="text-white text-xs font-bold">—</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Selling Item (slideshow widget) -->
            <div id="topSellingWidget" class="lg:col-span-1 bg-[#f0f0f0] border border-gray-200 p-3" style="border-radius: 20px;">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-sm font-bold text-gray-900" style="font-family: 'Poppins', sans-serif;">Top Selling Item</h2>
                        <button id="topSellingOpenBtn" type="button" aria-label="Open top selling" class="inline-flex items-center justify-center rounded-full" style="width:32px; height:32px;">
                            <svg class="w-4 h-4 top-selling-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17l10-10"/><path d="M7 7h10v10"/></svg>
                        </button>
                </div>

                <div id="topSellingCarousel" class="rounded-lg overflow-hidden" style="background:#fff; position: relative;">
                    <div id="topSlideTrack" style="display:flex;width:100%;height:140px;position:relative;">
                        <!-- slides inserted here (absolute positioned, cross-fade) -->
                    </div>
                    <!-- Small slideshow dot indicators, centered at the bottom of the carousel -->
                    <div id="topSlideDots" style="position:absolute; left:0; right:0; bottom:6px; display:flex; align-items:center; justify-content:center; gap:5px; z-index:5;"></div>
                </div>

                <div id="topSellingInfo" class="mt-3 text-xs text-gray-700">
                    <!-- rank and product name shown here -->
                    <div id="topSellingPlaceholder" class="text-sm text-gray-500">Loading…</div>
                </div>
            </div>

            <!-- Monthly Sales Comparison -->
            <div class="lg:col-span-1 border border-gray-200 p-3" style="border-radius: 20px; background-color: #0f0f0f;">
                <h2 class="text-sm font-bold text-white mb-2" style="font-family: 'Poppins', sans-serif;">Monthly Sales Comparison</h2>
                <div class="w-full overflow-hidden" style="max-width: 100%;">
                    <canvas id="barChart" class="w-full" height="180" style="max-width: 100%; display: block;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* ---- Sales Overview picture-overlay animations ---- */
        @keyframes salesOverlaySlideOut {
            from { transform: translateX(0); }
            to   { transform: translateX(-100%); }
        }
        @keyframes salesOverlaySlideIn {
            from { transform: translateX(-100%); }
            to   { transform: translateX(0); }
        }
        #salesOverviewOverlay.overlay-slide-out {
            animation: salesOverlaySlideOut 0.6s ease forwards;
        }
        #salesOverviewOverlay.overlay-slide-in {
            animation: salesOverlaySlideIn 0.6s ease forwards;
        }
        #calendarBtn:hover {
            background-color: #4e4e4e !important;
        }
        #viewReportBtn:hover {
            background-color: #4e4e4e !important;
        }
        #closeSalesOverviewBtn:hover {
            background-color: #2a2a2a !important;
        }
        #dashboardNotificationButton {
            position: relative;
            z-index: 60;
            cursor: pointer;
            pointer-events: auto;
        }

        #dashboardNotificationButton:hover {
            color: #0f0f0f;
        }

        #dashboardNotificationDropdown {
            pointer-events: auto;
            z-index: 70;
        }

        #dashboardProfileButton {
            background-color: transparent !important;
            color: #0f0f0f !important;
        }

        #dashboardProfileButton:hover {
            background-color: transparent !important;
            color: #9ca3af !important;
        }

        #dashboardProfileButton:hover #dashboardProfileArrow {
            color: #9ca3af !important;
        }

        #closeSalesOverviewBtn.is-active {
            background-color: #1ab3ce !important;
            border-color: #1ab3ce !important;
        }

        .report-toggle-btn {
            transition: background-color 0.2s ease, color 0.2s ease,
                        padding 0.3s ease, border-radius 0.3s ease, width 0.3s ease;
        }
        .report-toggle-btn.is-active {
            background-color: #1ab3ce !important;
            color: #ffffff !important;
            border-color: #1ab3ce !important;
        }
        .report-toggle-btn.is-active {
            width: 32px;
            height: 32px;
            padding: 0;
            border-radius: 50%;
            justify-content: center;
        }

        /* ---- Calendar panel: pinned exactly to the picture container, smooth fade + scale ---- */
        #calendarPanel.is-open {
            opacity: 1 !important;
            transform: scale(1) !important;
            pointer-events: auto !important;
        }
        #calendarBtn.is-hidden,
        #viewReportBtn.is-hidden {
            opacity: 0 !important;
            pointer-events: none !important;
            transition: opacity 0.25s ease;
        }
        #prevMonthBtn:hover,
        #nextMonthBtn:hover,
        #closeCalendarBtn:hover {
            background-color: rgba(255,255,255,0.2) !important;
        }
        #closeCalendarCancelBtn:hover {
            background-color: rgba(255,255,255,0.18) !important;
        }
        #applyDateRangeBtn:hover {
            filter: brightness(1.08);
        }
        #calendarGrid button.cal-day:hover {
            background-color: rgba(255,255,255,0.14) !important;
        }

        /* ---- Inventory Levels (compact stacked card) ---- */
        .inv-row {
            transition: transform 0.2s ease;
        }
        .inv-stack .inv-row:hover {
            transform: translateY(-2px);
        }

        /* ---- Sales by Category (full-circle ring + legend) ---- */
        #categoryLegend .cat-legend-row {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 4px;
        }
        #categoryLegend .cat-dot {
            width: 8px;
            height: 8px;
            border-radius: 9999px;
            flex-shrink: 0;
            display: inline-block;
            transition: background-color 0.25s ease, box-shadow 0.25s ease;
        }
        #categoryLegend .cat-label {
            color: rgba(255,255,255,0.5);
            font-weight: 500;
        }
        #categoryLegend .cat-label-active {
            color: #ffffff;
            font-weight: 600;
        }

        /* ---- Top Selling slideshow: cross-fade slides ---- */
        #topSlideTrack .top-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.5s ease;
            pointer-events: none;
        }
        #topSlideTrack .top-slide.is-active {
            opacity: 1;
            pointer-events: auto;
        }
        #topSlideTrack .top-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
            display: block;
        }

        /* ---- Top Selling open button: dark circle (#0f0f0f), turns gray on hover ---- */
        #topSellingOpenBtn {
            width: 32px;
            height: 32px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #0f0f0f;
            color: #ffffff;
            border: none;
            box-shadow: 0 2px 6px rgba(2,6,23,0.12);
            transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }
        #topSellingOpenBtn:hover {
            background-color: #6b7280; /* gray on hover */
            color: #ffffff;
            box-shadow: 0 6px 18px rgba(2,6,23,0.18);
        }
        #topSellingOpenBtn:active { transform: none; }
        #topSellingOpenBtn .top-selling-icon { color: currentColor; width: 16px; height: 16px; }

        /* ---- Small slideshow dot indicators ---- */
        #topSlideDots .top-dot {
            width: 6px;
            height: 6px;
            border-radius: 9999px;
            background-color: #afafaf;
            box-shadow: 0 0 0 1px rgba(0,0,0,0.12);
            transition: background-color 0.18s ease, transform 0.18s ease;
            cursor: pointer;
        }
        #topSlideDots .top-dot.is-active {
            background-color: #949494;
            transform: scale(1.15);
        }

        /* ---- Top Selling Items modal (light theme) ---- */
        #topItemsModal .modal-panel {
            background-color: #f1f1f1;
            color: #6d6d6d;
            opacity: 0;
            transform: translateX(140px) scale(0.96) translateY(6px);
            transition: opacity 0.28s ease, transform 0.28s cubic-bezier(0.22, 1, 0.36, 1);
        }
        #topItemsModal.is-open .modal-panel {
            opacity: 1;
            transform: translateX(110px) scale(1) translateY(0);
        }
        #topItemsModal .modal-overlay-bg {
            background: rgba(0,0,0,0.6);
            opacity: 0;
            transition: opacity 0.28s ease;
        }
        #topItemsModal.is-open .modal-overlay-bg {
            opacity: 1;
        }
        #topItemsModal table thead th {
            color: #464545;
            font-weight: 600;
            border-bottom: 1px solid rgba(0,0,0,0.20);
        }
        #topItemsModal table tbody td {
            color: #6b7280;
            border-bottom: 1px solid rgba(107,114,128,0.20);
        }
        #topItemsModal #closeTopItemsModal {
            background-color: #0f0f0f !important;
            color: #ffffff !important;
            border-radius: 9999px;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease, color 0.2s ease;
        }
        #topItemsModal #closeTopItemsModal:hover {
            background-color: #6b7280 !important;
        }
    </style>

<?php $__env->startPush('scripts'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/js/dashboard.js'); ?>

    <script>
        // Top Selling widget script: uses dashboard.data -> top_items and localStorage posProductImages
        document.addEventListener('DOMContentLoaded', () => {
            const root = document.getElementById('dashboard-root');
            if (!root) return;
            const url = root.dataset.dashboardUrl;
            const track = document.getElementById('topSlideTrack');
            const dotsWrap = document.getElementById('topSlideDots');
            const info = document.getElementById('topSellingInfo');
            const placeholder = document.getElementById('topSellingPlaceholder');
            const openBtn = document.getElementById('topSellingOpenBtn');

            let items = [];
            let idx = 0;
            let rot = null;

            function loadLocalImages(){
                try { return JSON.parse(localStorage.getItem('posProductImages') || '{}'); } catch(e){ return {}; }
            }

            // Cross-fade to a given slide index (used by both auto-rotate and the arrow button)
            function goToSlide(newIdx){
                if (!items || items.length === 0) return;
                newIdx = ((newIdx % items.length) + items.length) % items.length;
                idx = newIdx;

                const slides = track.querySelectorAll('.top-slide');
                slides.forEach((s, i) => {
                    s.classList.toggle('is-active', i === idx);
                });

                const dots = dotsWrap.querySelectorAll('.top-dot');
                dots.forEach((d, i) => {
                    d.classList.toggle('is-active', i === idx);
                });

                showInfo();
            }

            function renderSlides(){
                if (!track) return;
                track.innerHTML = '';
                if (dotsWrap) dotsWrap.innerHTML = '';
                const localImages = loadLocalImages();

                items.forEach((it, i) => {
                    const slide = document.createElement('div');
                    slide.className = 'top-slide' + (i === 0 ? ' is-active' : '');

                    const img = document.createElement('img');
                    // try localStorage image by product_id first
                    const pid = it.product_id ?? it.id ?? null;
                    const keyCandidates = [];
                    if (pid !== null && pid !== undefined) {
                        keyCandidates.push(pid);
                        keyCandidates.push(String(pid));
                    }
                    if (it.name) keyCandidates.push(it.name);

                    let found = null;
                    for (const k of keyCandidates) {
                        if (k in localImages) { found = localImages[k]; break; }
                    }
                    if (found) {
                        img.src = found;
                    } else if (it.image_url || it.image) {
                        img.src = it.image_url || it.image;
                    } else {
                        img.src = '/images/placeholder.png';
                    }
                    img.alt = it.name || it.item || 'Product';
                    slide.appendChild(img);
                    track.appendChild(slide);

                    // dot indicator (small circle), clickable to jump to that slide
                    if (dotsWrap) {
                        const dot = document.createElement('span');
                        dot.className = 'top-dot' + (i === 0 ? ' is-active' : '');
                        dot.addEventListener('click', () => { goToSlide(i); startRotate(); });
                        dotsWrap.appendChild(dot);
                    }
                });

                idx = 0;
                showInfo();
            }

            function showInfo(){
                if (!items || items.length === 0) {
                    placeholder.textContent = 'No top items';
                    return;
                }
                const top = items[idx] || items[0];
                placeholder.innerHTML = `<div class="font-semibold text-gray-900">${escapeHtml(top.name)}</div><div class="text-gray-500">Rank ${idx + 1} • ${escapeHtml(top.category || '')}</div>`;
            }

            function startRotate(){
                if (rot) clearInterval(rot);
                rot = setInterval(() => { goToSlide(idx + 1); }, 4000);
            }

            // Arrow button: fade to the next picture in the slideshow, then open the (centered) modal
            openBtn?.addEventListener('click', ()=>{
                goToSlide(idx + 1);
                startRotate();
                openTopItemsModal(items);
            });

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(r=>r.json()).then(data=>{
                items = data.top_items || data.topItems || [];
                renderSlides(); startRotate();
            }).catch(()=>{ placeholder.textContent = 'Failed to load'; });

            function openTopItemsModal(list){
                let modal = document.getElementById('topItemsModal');
                if (!modal) {
                    modal = document.createElement('div'); modal.id = 'topItemsModal';
                    // Fixed + centered on the whole dashboard screen (not off to the side)
                    modal.style.position = 'fixed';
                    modal.style.inset = '0';
                    modal.style.display = 'flex';
                    modal.style.alignItems = 'center';
                    modal.style.justifyContent = 'center';
                    modal.style.zIndex = '1200';
                    modal.innerHTML = `
                        <div class="modal-overlay-bg" style="position:absolute;inset:0;"></div>
                        <div class="modal-panel" style="position:relative;border-radius:16px;padding:18px;max-width:900px;width:95%;max-height:80%;overflow:auto;margin:auto;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
                                <h3 style="margin:0;color:#000000;font-family:'Poppins',sans-serif;font-weight:700;font-size:1.15rem;">Top Selling Items</h3>
                                <button id="closeTopItemsModal" style="border:none;background:#0f0f0f;color:#ffffff;border-radius:9999px;width:30px;height:30px;cursor:pointer">×</button>
                            </div>
                            <div>
                                <table style="width:100%;border-collapse:collapse">
                                    <thead>
                                        <tr style="text-align:left">
                                            <th style="padding:8px">Rank</th>
                                            <th style="padding:8px">Item</th>
                                            <th style="padding:8px">Category</th>
                                            <th style="padding:8px">Qty</th>
                                            <th style="padding:8px">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody id="topItemsModalBody"></tbody>
                                </table>
                            </div>
                        </div>`;
                    document.body.appendChild(modal);

                    const doClose = () => closeTopItemsModal(modal);
                    modal.querySelector('#closeTopItemsModal').addEventListener('click', doClose);
                    modal.querySelector('.modal-panel').addEventListener('click', (e)=>{ e.stopPropagation(); });
                    modal.addEventListener('click', doClose);
                }

                const body = modal.querySelector('#topItemsModalBody'); body.innerHTML = '';
                (list||[]).forEach((it, i)=>{
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td style="padding:8px">${i+1}</td><td style="padding:8px">${escapeHtml(it.name)}</td><td style="padding:8px">${escapeHtml(it.category||'')}</td><td style="padding:8px">${it.qty ?? it.quantity ?? ''}</td><td style="padding:8px">${it.revenue ? (new Intl.NumberFormat('en-PH',{style:'currency',currency:'PHP'}).format(it.revenue)):''}</td>`;
                    body.appendChild(tr);
                });

                // trigger the open (fade-in + scale) transition
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => modal.classList.add('is-open'));
                });
            }

            // Nice close effect: fade + scale out, then remove from DOM
            function closeTopItemsModal(modal){
                modal.classList.remove('is-open');
                window.setTimeout(() => {
                    if (modal && modal.parentNode) modal.parentNode.removeChild(modal);
                }, 280);
            }

            function escapeHtml(s){ return String(s||'').replace(/[&<>\"]/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])); }
        });
        document.addEventListener('DOMContentLoaded', () => {
            const overlay = document.getElementById('salesOverviewOverlay');
            const viewReportBtn = document.getElementById('viewReportBtn');
            const viewReportLabel = document.getElementById('viewReportLabel');
            const viewReportArrow = document.getElementById('viewReportArrow');
            const calendarBtn = document.getElementById('calendarBtn');
            const closeSalesOverviewBtn = document.getElementById('closeSalesOverviewBtn');

            if (!overlay || !viewReportBtn) {
                return;
            }

            let isReportOpen = false;

            const playSlideOut = () => {
                overlay.classList.remove('overlay-slide-in');
                void overlay.offsetWidth;
                overlay.classList.add('overlay-slide-out');
            };

            const playSlideIn = () => {
                overlay.classList.remove('overlay-slide-out');
                void overlay.offsetWidth;
                overlay.classList.add('overlay-slide-in');
            };

            const openReport = () => {
                playSlideOut();
                try {
                    viewReportBtn.classList.add('is-active');
                    if (viewReportLabel) viewReportLabel.style.display = 'none';
                    if (viewReportArrow) viewReportArrow.style.transform = 'rotate(180deg)';
                    if (closeSalesOverviewBtn) closeSalesOverviewBtn.classList.remove('is-active');
                    // make sure the button is enabled
                    viewReportBtn.disabled = false;
                } catch (e) {
                    // swallow to avoid breaking future interactions
                    console.warn('openReport fallback:', e);
                }
                isReportOpen = true;
            };

            const closeReport = () => {
                playSlideIn();
                try {
                    viewReportBtn.classList.remove('is-active');
                    if (viewReportLabel) viewReportLabel.style.display = 'inline';
                    if (viewReportArrow) viewReportArrow.style.transform = 'rotate(0deg)';
                    // re-enable interactions explicitly in case overlay animations blocked pointer events
                    viewReportBtn.disabled = false;
                    viewReportBtn.classList.remove('is-hidden');
                } catch (e) {
                    console.warn('closeReport fallback:', e);
                }
                isReportOpen = false;
            };

            viewReportBtn.addEventListener('click', () => {
                if (!isReportOpen) {
                    openReport();
                } else {
                    closeReport();
                }
            });

            if (closeSalesOverviewBtn) {
                closeSalesOverviewBtn.addEventListener('click', (event) => {
                    event.stopPropagation();
                    if (isReportOpen) {
                        closeSalesOverviewBtn.classList.add('is-active');
                        closeReport();
                    }
                });
            }

            // ===================== CALENDAR (pinned to the picture container, smooth open/close) =====================
            if (calendarBtn) {
                const calendarPanel = document.getElementById('calendarPanel');
                const calendarGrid = document.getElementById('calendarGrid');
                const monthYearDisplay = document.getElementById('monthYearDisplay');
                const prevMonthBtn = document.getElementById('prevMonthBtn');
                const nextMonthBtn = document.getElementById('nextMonthBtn');
                const closeCalendarBtn = document.getElementById('closeCalendarBtn');

                let currentMonth = new Date().getMonth();
                let currentYear = new Date().getFullYear();
                let selectedDate = null;
                const today = new Date();

                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'];

                const generateCalendar = () => {
                    calendarGrid.innerHTML = '';
                    const firstDay = new Date(currentYear, currentMonth, 1);
                    const lastDay = new Date(currentYear, currentMonth + 1, 0);
                    const daysInMonth = lastDay.getDate();
                    const startingDayOfWeek = (firstDay.getDay() + 6) % 7;

                    monthYearDisplay.textContent = monthNames[currentMonth] + ' ' + currentYear;

                    const atMaxMonth = (currentYear === today.getFullYear() && currentMonth === 11);
                    if (nextMonthBtn) {
                        nextMonthBtn.disabled = atMaxMonth;
                        nextMonthBtn.style.opacity = atMaxMonth ? '0.3' : '1';
                        nextMonthBtn.style.cursor = atMaxMonth ? 'not-allowed' : 'pointer';
                        nextMonthBtn.style.pointerEvents = atMaxMonth ? 'none' : 'auto';
                    }

                    for (let i = 0; i < startingDayOfWeek; i++) {
                        const emptyCell = document.createElement('div');
                        calendarGrid.appendChild(emptyCell);
                    }

                    for (let day = 1; day <= daysInMonth; day++) {
                        const dateCell = document.createElement('button');
                        dateCell.type = 'button';
                        dateCell.className = 'cal-day flex items-center justify-center mx-auto text-[11px] md:text-[12px] font-semibold rounded-full transition';
                        dateCell.style.width = '32px';
                        dateCell.style.height = '32px';
                        dateCell.style.minWidth = '32px';
                        dateCell.style.minHeight = '32px';
                        dateCell.textContent = day;

                        const cellDate = new Date(currentYear, currentMonth, day);
                        const isToday = cellDate.toDateString() === today.toDateString();
                        const isSelected = selectedDate && cellDate.toDateString() === selectedDate.toDateString();

                        if (isToday) {
                            dateCell.style.backgroundColor = '#0C7B93';
                            dateCell.style.color = '#ffffff';
                        } else if (isSelected) {
                            dateCell.style.backgroundColor = '#ffffff';
                            dateCell.style.color = '#000000';
                        } else {
                            dateCell.style.backgroundColor = 'transparent';
                            dateCell.style.color = 'rgba(255,255,255,0.85)';
                        }

                        dateCell.addEventListener('click', () => {
                            selectedDate = cellDate;
                            generateCalendar();
                            applySelectedDate();
                        });

                        calendarGrid.appendChild(dateCell);
                    }
                };

                const applySelectedDate = () => {
                    const date = selectedDate.toISOString().split('T')[0];
                    const url = document.getElementById('dashboard-root').dataset.dashboardUrl;
                    fetch(url + '?start_date=' + date + '&end_date=' + date, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                        .then(response => response.json())
                        .then(data => {
                            renderMetric(data.metrics?.sales, 'sales');
                            renderMetric(data.metrics?.transactions, 'transactions');
                            renderMetric(data.metrics?.profit, 'profit');
                            renderMetric(data.metrics?.items_sold, 'itemsSold');
                            renderSalesChart(data.sales_chart);
                            renderCategoryChart(data.category_chart);
                            renderComparisonChart(data.comparison_chart);
                            renderTopItems(data.top_items);
                            renderInventory(data.inventory);
                        });
                };

                const openCalendar = () => {
                    calendarBtn.classList.add('is-hidden');
                    if (viewReportBtn) viewReportBtn.classList.add('is-hidden');
                    calendarPanel.classList.remove('pointer-events-none');
                    void calendarPanel.offsetWidth;
                    calendarPanel.classList.add('is-open');
                };

                const closeCalendarModal = () => {
                    calendarPanel.classList.remove('is-open');
                    calendarBtn.classList.remove('is-hidden');
                    if (viewReportBtn) viewReportBtn.classList.remove('is-hidden');
                    window.setTimeout(() => {
                        calendarPanel.classList.add('pointer-events-none');
                    }, 350);
                };

                calendarBtn.addEventListener('click', (event) => {
                    event.stopPropagation();
                    currentMonth = today.getMonth();
                    currentYear = today.getFullYear();
                    generateCalendar();
                    openCalendar();
                });

                prevMonthBtn.addEventListener('click', () => {
                    currentMonth--;
                    if (currentMonth < 0) {
                        currentMonth = 11;
                        currentYear--;
                    }
                    generateCalendar();
                });

                nextMonthBtn.addEventListener('click', () => {
                    const atMaxMonth = (currentYear === today.getFullYear() && currentMonth === 11);
                    if (atMaxMonth) {
                        return;
                    }
                    currentMonth++;
                    if (currentMonth > 11) {
                        currentMonth = 0;
                        currentYear++;
                    }
                    generateCalendar();
                });

                if (closeCalendarBtn) {
                    closeCalendarBtn.addEventListener('click', closeCalendarModal);
                }
            }

            const notificationButton = document.getElementById('dashboardNotificationButton');
            const notificationDropdown = document.getElementById('dashboardNotificationDropdown');
            const notificationClose = document.getElementById('dashboardNotificationClose');

            if (notificationButton && notificationDropdown) {
                notificationButton.addEventListener('click', function(event) {
                    event.stopPropagation();
                    notificationDropdown.classList.toggle('hidden');
                });
            }

            if (notificationClose && notificationDropdown) {
                notificationClose.addEventListener('click', function() {
                    notificationDropdown.classList.add('hidden');
                });
            }

            window.addEventListener('click', function(event) {
                if (notificationDropdown && !notificationDropdown.contains(event.target) && !notificationButton.contains(event.target)) {
                    notificationDropdown.classList.add('hidden');
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?><?php /**PATH C:\Users\Paulo\OneDrive\Desktop\KCC_MOTORCYCLE\resources\views/dashboard.blade.php ENDPATH**/ ?>