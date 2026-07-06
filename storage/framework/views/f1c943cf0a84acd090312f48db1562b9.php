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
    <div id="dashboard-root" data-dashboard-url="<?php echo e(route('dashboard.data')); ?>" data-refresh-interval="15000" class="space-y-3">
        <div class="flex items-center justify-between">
           
        </div>

        <!-- Stats Grid -->
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
                        <svg class="w-5 h-5" style="color: #000000;" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Transaction -->
            <div class="border border-gray-200 p-3 hover:shadow-sm transition" style="border-radius: 20px; background-color: #3b3b3b;">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-black text-xs font-semibold" style="color: #ffffff;">Total Transaction</p>
                        <div class="mt-1">
                            <p id="transactionsValue" class="text-2xl font-bold" style="color: #ffffff;">—</p>
                            <p id="transactionsComparison" class="text-xs mt-1 font-medium" style="color: #ffffffb6;">Loading…</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 20px; background-color: #ff4800;">
                        <svg class="w-5 h-5" style="color: #ffffff;" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path d="M6.99 11L3 15l3.99 4v-3H14v-2H6.99v-3zM21 9l-3.99-4v3H10v2h7.01v3L21 9z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Profit -->
            <div class="border border-gray-200 p-3 hover:shadow-sm transition" style="border-radius: 20px; background-color: #3b3b3b;">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-black text-xs font-semibold" style="color: #ffffff;">Total Profit</p>
                        <div class="mt-1">
                            <p id="profitValue" class="text-2xl font-bold" style="color: #f0f0f0;">—</p>
                            <p id="profitComparison" class="text-xs mt-1 font-medium" style="color: #ffffffb6">Loading…</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 20px; background-color: #005fcc;">
                        <svg class="w-5 h-5" style="color: #ffffff;" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Item Sold -->
            <div class="border border-gray-200 p-3 hover:shadow-sm transition" style="border-radius: 20px; background-color: #3b3b3b;">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-black text-xs font-semibold" style="color: #ffffff;">Total Item Sold</p>
                        <div class="mt-1">
                            <p id="itemsSoldValue" class="text-2xl font-bold" style="color: #ffffff;">—</p>
                            <p id="itemsSoldComparison" class="text-xs mt-1 font-medium" style="color: #ffffffb6;">Loading…</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 20px; background-color: #FF0000;">
                        <svg class="w-5 h-5" style="color: #ffffff;" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
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
                    <div class="flex gap-1">
                        <button type="button" data-range="daily" class="sales-range-btn px-2 py-0.5 text-xs font-medium text-gray-300 bg-neutral-800 rounded transition">Day</button>
                        <button type="button" data-range="weekly" class="sales-range-btn px-2 py-0.5 text-xs font-medium text-gray-300 bg-neutral-800 rounded transition">Week</button>
                        <button type="button" data-range="monthly" class="sales-range-btn px-2 py-0.5 text-xs font-medium text-black rounded transition" style="background-color: #1ab3ce;">Month</button>
                    </div>
                </div>

                <!-- Body (chart) -->
                <div id="salesOverviewBody" class="relative w-full" style="height:300px;">
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

                    <!-- View Report button -->
                    <button type="button" id="viewReportBtn" class="report-toggle-btn absolute bottom-3 left-3 flex items-center gap-0 px-2.5 py-2 rounded-[10px] text-xs font-semibold text-white" style="background-color: #0f0f0f; backdrop-filter: blur(4px); ">
                        <span id="viewReportLabel" style="font-family: 'Poppins', sans-serif;">View Report</span>
                        <svg id="viewReportArrow" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
    <path d="M9 6l6 6-6 6" />
</svg>
                    </button>

                    <!-- Calendar icon (toggles the panel below) -->
                    <button type="button" id="calendarBtn" class="cal-toggle-btn absolute top-3 right-3 w-9 h-9 flex items-center justify-center rounded-full transition" style="background-color: #0f0f0f; backdrop-filter: blur(4px);">
                        <svg class="w-5 h-5" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="3"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </button>

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
                        <div class="text-right mb-2">
                            <p class="uppercase tracking-[0.35em] text-[9px] md:text-[10px] text-white/60 mb-1">Calendar</p>
                            <h2 id="monthYearDisplay" class="font-bold leading-none text-xl md:text-2xl">July 2026</h2>
                        </div>

                        <!-- Days of Week Header -->
                        <div class="grid grid-cols-7 gap-1 mb-1 text-center">
                            <div class="text-[10px] font-medium text-white/60 py-1">Mon</div>
                            <div class="text-[10px] font-medium text-white/60 py-1">Tue</div>
                            <div class="text-[10px] font-medium text-white/60 py-1">Wed</div>
                            <div class="text-[10px] font-medium text-white/60 py-1">Thu</div>
                            <div class="text-[10px] font-medium text-white/60 py-1">Fri</div>
                            <div class="text-[10px] font-medium text-white/60 py-1">Sat</div>
                            <div class="text-[10px] font-medium text-white/60 py-1">Sun</div>
                        </div>

                        <!-- Calendar Grid -->
                        <div id="calendarGrid" class="grid grid-cols-7 gap-1 flex-1 content-start">
                            <!-- Generated dynamically by JavaScript -->
                        </div>
                    </div>
                    <!-- =================== END CALENDAR PANEL =================== -->
                </div>
            </div>

            <!-- Sales by Category (full-circle ring + white knockout center + neon-on-sale legend) -->
            <div class="border border-gray-200 p-3" style="border-radius: 20px; background-color: #0f0f0f;">
                <h2 class="text-sm font-bold text-white mb-3" style="font-family: 'Poppins', sans-serif;">Sales by Category</h2>
                <div class="flex flex-col items-center gap-4">
                    <!--
                        FIXED: ring wrapper is now forced to a perfect square via
                        aspect-ratio (in case flex/grid sizing ever squishes it),
                        and max-width/height match so the canvas always draws a
                        true circle instead of an oval.
                    -->
                    <div style="position: relative; width: 160px; height: 160px; max-width: 160px; max-height: 160px; aspect-ratio: 1 / 1;">
                        <canvas id="categoryChart"></canvas>
                        <!--
                            FIXED: center "knockout" text.
                            Nudged slightly lower (top: 52% instead of 50%) and the
                            caption gets a touch more top margin, so the "0 / No sales
                            today" text sits visually centered inside the ring instead
                            of leaning high/off-center.
                        -->
                        <div style="
                            position: absolute; top: 56%; left: 50%;
                            transform: translate(-50%, -50%);
                            width: 115px; height: 115px;
                            border-radius: 9999px;
                          background-color: #0f0f0f;
                            display: flex; flex-direction: column;
                            align-items: center; justify-content: center;
                            text-align: center;
                            pointer-events: none;">
                            <span id="categoryCenterValue" style="color: #ffffff; font-weight: 700; font-size: 20px; line-height: 1.1;">0</span>
                            <span id="categoryCenterCaption" style="color: rgba(255,255,255,0.6); font-size: 9px; margin-top: 6px;">No sales today</span>
                        </div>
                    </div>

                    <!-- Legend: laging pare-parehong 7 category (Exhaust, Helmets, Tires,
                         Brakes, Oils, Batteries, Accessories). Dot ay muted/gray kapag
                         walang benta ang category sa araw na 'yon; kapag may benta,
                         nagiging neon ang dot (kulay mula sa palette) + glow, kasabay
                         ng segment sa ring. -->
                    <div id="categoryLegend" class="w-full space-y-2 text-xs"></div>
                </div>
            </div>
        </div>

        <!-- Tables Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">

            <!-- Inventory Levels (compact card, stacked/overlapping rows, no popup) -->
            <div id="inventoryCardWrap" class="relative">

                <div id="inventoryCard" class="border border-gray-200 p-3" style="border-radius: 20px; background-color: #ffffff;">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-black text-sm font-bold">Inventory Levels</h2>
                    </div>

                    <!--
                        Stacked rows: all containers share the same black background now.
                        Overlap order stays black-on-top -> down, kept visually distinct via
                        a subtle top separator line and colorful icon circles per row.
                    -->
                    <div class="inv-stack" style="position: relative;">

                        <!-- Total Products (top of the stack) -->
                        <div class="inv-row flex items-center gap-2 px-3 py-3" style="border-radius: 18px; background-color: #0f0f0f; position: relative; z-index: 40;">
                            <div class="w-7 h-7 flex items-center justify-center flex-shrink-0 rounded-full" style="background-color: #e2e2e2;">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path d="M21 8l-9-5-9 5 9 5 9-5z"></path>
                                    <path d="M3 8v8l9 5 9-5V8"></path>
                                </svg>
                            </div>
                            <span class="text-white text-xs font-semibold flex-1">Total Products</span>
                            <span id="totalProductsValue" class="text-white text-xs font-bold">—</span>
                        </div>

                        <!-- Low Stock Items -->
                        <div class="inv-row flex items-center gap-2 px-3" style="border-radius: 18px; background-color: #10b8b8; position: relative; z-index: 30; margin-top: -12px; padding-top: 20px; padding-bottom: 12px; border-top: 1px solid rgba(255,255,255,0.08);">
                            <div class="w-7 h-7 flex items-center justify-center flex-shrink-0 rounded-full" style="background-color: #000000;">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path d="M12 9v4"></path>
                                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                    <path d="M12 17h.01"></path>
                                </svg>
                            </div>
                            <span class="text-black text-xs font-semibold flex-1">Low Stock Items</span>
                            <span id="lowStockValue" class="text-black text-xs font-bold">—</span>
                        </div>

                        <!-- Out of Stock Items -->
                        <div class="inv-row flex items-center gap-2 px-3" style="border-radius: 18px; background-color: #414040; position: relative; z-index: 20; margin-top: -12px; padding-top: 20px; padding-bottom: 12px; border-top: 1px solid rgba(255,255,255,0.08);">
                            <div class="w-7 h-7 flex items-center justify-center flex-shrink-0 rounded-full" style="background-color: #ffffff;">
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
                        <div class="inv-row flex items-center gap-2 px-3" style="border-radius: 18px; background-color: #b3b3b3; position: relative; z-index: 10; margin-top: -12px; padding-top: 20px; padding-bottom: 12px; border-top: 1px solid rgba(255,255,255,0.08);">
                            <div class="w-7 h-7 flex items-center justify-center flex-shrink-0 rounded-full" style="background-color: #000000;">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <polyline points="8 12 11 15 16 9"></polyline>
                                </svg>
                            </div>
                            <span class="text-black text-xs font-semibold flex-1">In Stock Items</span>
                            <span id="inStockValue" class="text-black text-xs font-bold">—</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Sales Comparison -->
            <div class="lg:col-span-2 bg-white border border-gray-200 p-3" style="border-radius: 20px;">
                <h2 class="text-sm font-bold text-gray-900 mb-2">Monthly Sales Comparison</h2>
                <canvas id="barChart" height="60"></canvas>
            </div>
        </div>

        <!-- Top Selling Item (moved down, now full width on its own row) -->
        <div class="bg-white border border-gray-200 p-3" style="border-radius: 20px;">
            <h2 class="text-sm font-bold text-gray-900 mb-2">Top Selling Item</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left text-gray-600 font-medium py-1 px-1">Rank</th>
                            <th class="text-left text-gray-600 font-medium py-1 px-1">Item</th>
                            <th class="text-left text-gray-600 font-medium py-1 px-1">Category</th>
                            <th class="text-left text-gray-600 font-medium py-1 px-1">Qty</th>
                            <th class="text-left text-gray-600 font-medium py-1 px-1">Revenue</th>
                        </tr>
                    </thead>
                    <tbody id="topItemsTableBody" class="divide-y divide-gray-100">
                        <tr>
                            <td colspan="5" class="py-2 px-1 text-center text-gray-500 text-xs">Loading…</td>
                        </tr>
                    </tbody>
                </table>
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
            background-color: rgba(15,15,15,0.8) !important;
        }
        #viewReportBtn:hover {
             background-color: rgba(15,15,15,0.8) !important;
        }
        #closeSalesOverviewBtn:hover {
            background-color: #2a2a2a !important;
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
            gap: 8px;
        }
        #categoryLegend .cat-dot {
            width: 10px;
            height: 10px;
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
    </style>

<?php $__env->startPush('scripts'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/js/dashboard.js'); ?>

    <script>
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
                viewReportBtn.classList.add('is-active');
                viewReportLabel.style.display = 'none';
                viewReportArrow.style.transform = 'rotate(180deg)';
                if (closeSalesOverviewBtn) {
                    closeSalesOverviewBtn.classList.remove('is-active');
                }
                isReportOpen = true;
            };

            const closeReport = () => {
                playSlideIn();
                viewReportBtn.classList.remove('is-active');
                viewReportLabel.style.display = 'inline';
                viewReportArrow.style.transform = 'rotate(0deg)';
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
                let selectedDate = null; // single-date selection only (no more range)
                const today = new Date();

                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'];

                const generateCalendar = () => {
                    calendarGrid.innerHTML = '';
                    const firstDay = new Date(currentYear, currentMonth, 1);
                    const lastDay = new Date(currentYear, currentMonth + 1, 0);
                    const daysInMonth = lastDay.getDate();
                    const startingDayOfWeek = (firstDay.getDay() + 6) % 7; // Monday = 0

                    monthYearDisplay.textContent = monthNames[currentMonth] + ' ' + currentYear;

                    // Lock navigation to the current year (today.getFullYear()) —
                    // no January of next year and beyond.
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
                        dateCell.className = 'cal-day flex items-center justify-center mx-auto text-[11px] md:text-xs font-medium rounded-full transition';
                        dateCell.style.width = '28px';
                        dateCell.style.height = '28px';
                        dateCell.style.aspectRatio = '1 / 1';
                        dateCell.textContent = day;

                        const cellDate = new Date(currentYear, currentMonth, day);
                        const isToday = cellDate.toDateString() === today.toDateString();
                        const isSelected = selectedDate && cellDate.toDateString() === selectedDate.toDateString();

                        if (isToday) {
                            // Current date: always cyan background + white font
                            dateCell.style.backgroundColor = '#0C7B93';
                            dateCell.style.color = '#ffffff';
                        } else if (isSelected) {
                            // Clicked/selected date (not today): white background + black font
                            dateCell.style.backgroundColor = '#ffffff';
                            dateCell.style.color = '#000000';
                        } else {
                            // Not selected, not today: no fill, hover only
                            dateCell.style.backgroundColor = 'transparent';
                            dateCell.style.color = 'rgba(255,255,255,0.85)';
                        }

                        // SINGLE-CLICK ONLY: bawat click ay pumili ng ISANG araw lang.
                        // Wala nang range/multi-select — kaya hindi na sabay-sabay
                        // mahighlight ang ibang numero.
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
                    // single day lang ang ipapadala (start_date = end_date)
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
                    // Mananatiling bukas ang calendar hangga't hindi pinindot
                    // ang closeCalendarBtn (X) — hindi na ito auto-close.
                };

                // --- smooth open/close (fade + scale instead of display:none jump) ---
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
                        return; // already at December of the current year — don't go further
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