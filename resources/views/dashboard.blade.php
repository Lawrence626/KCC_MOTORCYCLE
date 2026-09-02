<x-layouts.app :title="__('Dashboard')">
    <div id="dashboard-root" data-dashboard-url="{{ route('dashboard.data') }}" data-refresh-interval="15000" class="space-y-6">
       
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="pl-3 lg:pl-2">
                    <h1 class="text-3xl font-bold text-slate-900">Dashboard</h1>
                    <p class="text-gray-600 text-sm mt-1">Overview of sales, inventory and performance insights</p>
                </div>
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center pr-4">
                    
                   
                        <div class="relative" id="notification-bell-wrapper">
                            <button
                                type="button"
                                id="notification-bell-btn"
                                class="relative p-2 text-slate-600 hover:bg-slate-100 rounded-lg transition"
                                aria-label="Notifications"
                                onclick="toggleNotificationPanel(event)"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <span
                                    id="notification-badge"
                                    class="absolute -top-0.5 -right-0.5 hidden min-w-[18px] h-[18px] px-1 flex items-center justify-center rounded-[10px] bg-red-500 text-[10px] font-bold text-white leading-none"
                                ></span>
                            </button>

                            {{-- Notification Dropdown Panel --}}
                            <div
                                id="notification-panel"
                                class="hidden absolute right-0 top-full mt-2 w-[320px] rounded-xl bg-white border border-slate-200 shadow-2xl z-[9999] flex flex-col"
                                style="max-height: 350px;"
                            >
                                <div class="sticky top-0 z-10 flex items-center justify-between px-4 py-3 border-b border-slate-800 rounded-t-xl bg-[#0f172a]" style="background-color: #0f172a;">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-white">Notifications</span>
                                        <span id="notif-center-unread-badge" class="hidden inline-flex items-center rounded-[10px] px-2 py-0.5 text-[10px] font-medium text-white" style="background-color: #ef4444;">0</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <button
                                            type="button"
                                            onclick="markAllNotificationsRead()"
                                            class="text-xs font-semibold text-slate-300 hover:text-white transition-colors"
                                        >Mark all as read</button>
                                    </div>
                                </div>
                                <div id="notification-list" class="flex-1 overflow-y-auto">
                                    {{-- Notifications rendered by JS --}}
                                </div>
                                <div id="notification-empty" class="hidden px-4 py-8 text-center">
                                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-[#ccfbf1] mb-2">
                                        <svg class="w-5 h-5 text-[#0f766e]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-800">All caught up</p>
                                    <p class="text-xs text-slate-500 mt-1">No new inventory alerts.</p>
                                </div>
                                <div class="sticky bottom-0 z-10 px-4 py-3 bg-slate-50 border-t border-slate-100 text-center rounded-b-xl">
                                    <button type="button" onclick="openAllNotificationsModal()" class="text-xs font-semibold text-slate-700 hover:text-slate-900 transition-colors">View All Notifications</button>
                                </div>
                            </div>
                        </div>
                        <div class="relative">
                            <button type="button" id="dashboardProfileButton" class="relative inline-flex items-center gap-1.5 rounded-[20px] px-3 py-2 text-left focus:outline-none hover:bg-slate-100 transition-colors">
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-black grid place-items-center text-lg font-semibold overflow-hidden">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                                </span>
                                <div class="flex flex-col leading-tight pr-2">
                                    <span class="text-sm font-semibold text-black">{{ auth()->user()->name ?? 'Admin' }}</span>
                                    <span class="text-xs text-gray-500">{{ auth()->user()->email ?? '' }}</span>
                                </div>
                                <div class="inline-flex h-7 w-7 items-center justify-center rounded-[12px] bg-transparent text-[#0f0f0f] transition-colors duration-200" aria-hidden="true">
                                    <svg id="dashboardProfileArrow" class="w-5 h-5 text-current transition-colors duration-200" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5H7z"/></svg>
                                </div>
                            </button>

                            <div id="dashboardProfileDropdown" class="absolute right-0 top-full mt-2 w-65 min-h-[100px] rounded-[15px] bg-[#0f0f0f] shadow-2xl shadow-black/20 z-50 hidden opacity-0 transform scale-95 transition-all duration-200 origin-top-right" style="color: #ffffff;">
                                <div class="px-4 py-4 border-b border-slate-700/60">
                                    <div class="flex items-center gap-3">
                                        <span class="w-12 h-12 rounded-full bg-gray-200 text-black grid place-items-center overflow-hidden text-lg font-semibold">
                                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                        </span>
                                        <div>
                                            <div class="text-[13px] font-semibold text-white">{{ auth()->user()->name ?? 'Admin' }}</div>
                                            <div class="text-[12px] text-gray-400">{{ auth()->user()->email ?? '' }}</div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <span class="inline-flex items-center rounded-full border border-gray-600/30 bg-gray-800 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[0.12em]" style="color: #32FFFD;">
                                            {{ (auth()->user()->role ?? 'user') === 'admin' ? 'Administrator' : ucfirst(str_replace('_', ' ', auth()->user()->role ?? 'user')) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1 px-2 py-2">
                                    <a href="{{ route('profile.show') }}" class="flex items-center gap-3 rounded-[10px] px-2.5 py-2.5 text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-700/60 transition">
                                        <span class="w-6 h-6 grid place-items-center rounded-full bg-slate-700 text-white">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.879 6.196 9 9 0 015.12 17.804z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </span>
                                        <span>View Profile</span>
                                    </a>
                                    <a href="{{ route('settings.general') }}" class="flex items-center gap-3 rounded-[10px] px-2.5 py-2.5 text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-700/60 transition">
                                        <span class="w-6 h-6 grid place-items-center rounded-full bg-slate-700 text-white">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </span>
                                        <span>Settings</span>
                                    </a>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center gap-3 rounded-[10px] px-2.5 py-2.5 text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-700/60 transition">
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
        </div>



        <!-- Stats Grid -->
        <div class="w-full">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">
                <!-- Total Sales -->
                 <div class="border border-gray-200 p-4" style="border-radius: 20px; background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-black text-xs font-semibold">Total Sales</p>
                            <div class="mt-1">
                                <p id="salesValue" class="text-2xl font-bold text-black">—</p>
                                <p id="salesComparison" class="text-gray-500 text-[10px] leading-tight mt-1 font-medium whitespace-nowrap">Loading…</p>
                            </div>
                        </div>
                        <div class="border border-gray-200 w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: #00fff2ff;">
                            <svg class="w-5 h-5" style="color: #000000ff;" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Transaction -->
               <div class="border border-gray-200 p-4" style="border-radius: 20px; background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-black text-xs font-semibold" style="color: #000000;">Total Transaction</p>
                            <div class="mt-1">
                                <p id="transactionsValue" class="text-2xl font-bold" style="color: #000000;">—</p>
                                <p id="transactionsComparison" class="text-gray-500 text-[10px] leading-tight mt-1 font-medium whitespace-nowrap">Loading…</p>
                            </div>
                        </div>
                        <div class="border border-gray-200 w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: #00fff2ff;">
                            <svg class="w-5 h-5" style="color: #000000ff;" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                <path d="M6.99 11L3 15l3.99 4v-3H14v-2H6.99v-3zM21 9l-3.99-4v3H10v2h7.01v3L21 9z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Profit -->
                <div class="border border-gray-200 p-4" style="border-radius: 20px; background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-black text-xs font-semibold" style="color: #000000;">Total Profit</p>
                            <div class="mt-1">
                                <p id="profitValue" class="text-2xl font-bold" style="color: #000000;">—</p>
                                <p id="profitComparison" class="text-gray-500 text-[10px] leading-tight mt-1 font-medium whitespace-nowrap">Loading…</p>
                            </div>
                        </div>
                        <div class="border border-gray-200 w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: #00fff2ff;">
                            <svg class="w-5 h-5" style="color: #000000ff;" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Item Sold -->
                 <div class="border border-gray-200 p-4" style="border-radius: 20px; background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-black text-xs font-semibold" style="color: #000000;">Total Item Sold</p>
                            <div class="mt-1">
                                <p id="itemsSoldValue" class="text-2xl font-bold" style="color: #030303;">—</p>
                                <p id="itemsSoldComparison" class="text-gray-500 text-[10px] leading-tight mt-1 font-medium whitespace-nowrap">Loading…</p>
                            </div>
                        </div>
                        <div class="border border-gray-200 w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: #00fff2ff;">
                            <svg class="w-4 h-4" style="color: #000000ff;" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Dead Stock Alert Card -->
                <a href="{{ route('dss.dead-stock.index') }}" class="block border border-gray-200 p-4 hover:border-[#00fff2] hover:shadow-md transition cursor-pointer group" style="border-radius: 20px; background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-black text-xs font-semibold">Dead Stock</p>
                            <div class="mt-1">
                                <p id="deadStockCardItems" class="text-2xl font-bold text-black">0 Items</p>
                                <p id="deadStockCardValue" class="text-gray-500 text-[10px] leading-tight mt-1 font-medium whitespace-nowrap">Value at Risk: ₱0</p>
                            </div>
                        </div>
                        <div class="border border-gray-200 w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: #00fff2ff;">
                            <svg class="w-4.5 h-4.5 text-black" fill="currentColor" viewBox="0 0 24 24" style="transform: translateY(-1px);"><path d="M4.47 21h15.06c1.54 0 2.5-1.67 1.73-3L13.73 4.99c-.77-1.33-2.69-1.33-3.46 0L2.74 18c-.77 1.33.19 3 1.73 3zM13 18h-2v-2h2v2zm0-4h-2v-4h2v4z" style="color: #000000ff;"/></svg>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- ═══ DEAD STOCK ALERT WIDGET ═══ --}}
        @if(auth()->user() && (auth()->user()->role === 'admin' || auth()->user()->role === 'inventory_clerk'))
        <div id="deadStockAlertWidget" class="hidden">
            <div class="border border-rose-200 bg-gradient-to-r from-rose-50 via-white to-rose-50 p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3" style="border-radius: 20px;">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0 rounded-full bg-rose-100">
                        <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-rose-800">⚠ Dead Stock Alert</p>
                        <p id="deadStockAlertMsg" class="text-xs text-rose-600 mt-0.5"></p>
                        <div id="deadStockAlertPriorities" class="flex flex-wrap items-center gap-2 mt-1.5"></div>
                    </div>
                </div>
                <a href="{{ route('dss.dead-stock.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-xl bg-rose-500 text-white hover:bg-rose-600 shadow-sm transition whitespace-nowrap flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Review Dead Stock
                </a>
            </div>
        </div>
        <script>
        (function() {
            fetch('{{ route("api.dss.dashboard-stats") }}')
                .then(r => r.json())
                .then(data => {
                    if (data.total > 0) {
                        const widget = document.getElementById('deadStockAlertWidget');
                        widget.classList.remove('hidden');
                        document.getElementById('deadStockAlertMsg').textContent =
                            data.total + ' product' + (data.total > 1 ? 's have' : ' has') + ' not been sold for more than ' + (data.thresholdDays || 90) + ' days.';
                        const prioritiesEl = document.getElementById('deadStockAlertPriorities');
                        const pColors = {Critical:'bg-red-100 text-red-700',High:'bg-orange-100 text-orange-700',Medium:'bg-amber-100 text-amber-700',Low:'bg-blue-100 text-blue-700'};
                        let html = '';
                        ['Critical','High','Medium','Low'].forEach(p => {
                            const count = data.countByPriority[p] || 0;
                            if (count > 0) {
                                html += '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold ' + pColors[p] + '">' + p + ': ' + count + '</span>';
                            }
                        });
                        if (data.totalValue > 0) {
                            html += '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">₱' + Number(data.totalValue).toLocaleString('en-PH', {minimumFractionDigits:2}) + ' at risk</span>';
                        }
                        prioritiesEl.innerHTML = html;
                    }
                    
                    // Update small card
                    const dsItems = document.getElementById('deadStockCardItems');
                    if (dsItems) dsItems.textContent = (data.total || 0) + ' Items';
                    const dsValue = document.getElementById('deadStockCardValue');
                    if (dsValue) dsValue.textContent = 'Value at Risk: ₱' + Number(data.totalValue || 0).toLocaleString('en-PH', {minimumFractionDigits:2});
                })
                .catch(() => {});
        })();
        </script>
        @endif

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mt-8">
            <!-- Sales Overview Chart -->
            <div id="salesOverviewCard" class="lg:col-span-2 border border-slate-200 relative overflow-hidden rounded-[15px] bg-white shadow-sm" style="min-height: 240px; box-sizing: border-box; border-radius: 15px;">

                <!-- Header (title + range buttons) -->
                <div id="salesOverviewHeader" class="bg-[#0f172a] px-6 py-4 flex items-center justify-between border-b border-slate-800">
                    <div class="flex items-center gap-2">
                       <h2 id="salesOverviewTitle" class="font-bold tracking-wide text-white text-lg">Sales Overview</h2>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" data-range="daily" class="sales-range-btn px-3 py-1 text-sm font-medium rounded-[10px] transition">Day</button>
                        <button type="button" data-range="weekly" class="sales-range-btn px-3 py-1 text-sm font-medium rounded-[10px] transition">Week</button>
                        <button type="button" data-range="monthly" class="sales-range-btn px-3 py-1 text-sm font-medium rounded-[10px] transition active">Month</button>
                    </div>
                </div>

                <!-- Body (chart) -->
                <div id="salesOverviewBody" class="relative w-full p-4" style="height:360px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Sales by Category (full-circle ring + white knockout center + neon-on-sale legend) -->
            <div class="border border-gray-200 p-3 rounded-[15px]" style="border-radius: 15px; background-color: #ffffff;">
                <h2 class="text-sm font-bold text-black mb-2" style="font-family: 'Poppins', sans-serif;">Sales by Category</h2>
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
                            <span id="categoryCenterValue" style="color: #000000; font-weight: 700; font-size: 14px; line-height: 1.1;">0</span>
                            <span id="categoryCenterCaption" style="color: rgba(0,0,0,0.6); font-size: 9px; margin-top: 6px;">No sales today</span>
                        </div>
                    </div>

                    <div id="categoryLegend" class="w-full space-y-1 text-xs"></div>
                </div>
            </div>
        </div>

        <!-- Tables Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mt-8">

            <!-- Inventory Levels (compact card, stacked/overlapping rows, no popup) -->
         <div id="inventoryCardWrap" class="relative">
 
                <div id="inventoryCard" class="border border-gray-200 p-4" style="border-radius: 20px; background color: #ffffff;">
                    <div class="flex items-center justify-between mb-2" >
                        <h2 class="text-sm font-bold" style="color: #000000;">Inventory Levels</h2>
                    </div>
 
                    <div class="inv-stack" style="position: relative;">
 
                        <!-- Total Products (top of the stack) -->
                        <div class="inv-row flex items-center gap-2 px-3 py-3" style="border-radius: 20px; background-color: #ffffffff; position: relative; z-index: 40;">
                            <div class="w-7 h-7 rounded-full flex-shrink-0 relative" style="background-color: #00fff2ff;">
                                <svg class="absolute inset-0 m-auto" style="width: 14px; height: 14px; transform: translate(-0.5px, 0.5px);" fill="none" stroke="#000000ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path d="M21 8l-9-5-9 5 9 5 9-5z"></path>
                                    <path d="M3 8v8l9 5 9-5V8"></path>
                                </svg>
                            </div>
                            <span class="text-black text-xs font-semibold flex-1">Total Products</span>
                            <span id="totalProductsValue" class="text-black text-xs font-bold">—</span>
                        </div>
 
                        <!-- Low Stock Items -->
                        <div class="inv-row flex items-center gap-2 px-3" style="border-radius: 0px;background-color: #ffffff;; position: relative; z-index: 30 ; margin-top: -10px; padding-top: 20px; padding-bottom: 12px;">
                            <div class="w-7 h-7 rounded-full flex-shrink-0 relative" style="background-color: #00fff2ff;">
                                <svg class="absolute inset-0 m-auto" style="width: 14px; height: 14px; transform: translate(-0.5px, 0.5px);" fill="none" stroke="#000000ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path d="M12 9v4"></path>
                                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                    <path d="M12 17h.01"></path>
                                </svg>
                            </div>
                            <span class="text-black text-xs font-semibold flex-1">Low Stock Items</span>
                            <span id="lowStockValue" class="text-black text-xs font-bold">—</span>
                        </div>
 
                        <!-- Out of Stock Items -->
                        <div class="inv-row flex items-center gap-2 px-3" style="border-radius: 0px;background-color: #ffffff;; position: relative; z-index: 20; margin-top: -10px; padding-top: 20px; padding-bottom: 12px; ">
                            <div class="w-7 h-7 rounded-full flex-shrink-0 relative" style="background-color: #00fff2ff;">
                                <svg class="absolute inset-0 m-auto" style="width: 14px; height: 14px; transform: translate(-0.5px, 0.5px);" fill="none" stroke="#000000ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                </svg>  
                            </div>
                            <span class="text-black text-xs font-semibold flex-1">Out of Stock Items</span>
                            <span id="outOfStockValue" class="text-black text-xs font-bold">—</span>
                        </div>
 
                        <!-- In Stock Items (bottom of the stack) -->
                        <div class="inv-row flex items-center gap-2 px-3" style="border-radius: 20px; background-color: #ffffff; position: relative; z-index: 10; margin-top: -10px; padding-top: 20px; padding-bottom: 12px;">
                            <div class="w-7 h-7 rounded-full flex-shrink-0 relative" style="background-color: #00fff2ff;">
                                <svg class="absolute inset-0 m-auto" style="width: 14px; height: 14px; transform: translate(-0.5px, 0.5px);" fill="none" stroke="#000000ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
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
 
            <!-- Top Selling Item (slideshow widget) -->
            <div id="topSellingWidget" class="lg:col-span-1 bg-[#ffffff] border border-gray-200 p-3" style="border-radius: 20px;">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-sm font-bold text-gray-900" style="font-family: 'Poppins', sans-serif;">Top Selling Items</h2>
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
            <div id="comparisonCard" class="lg:col-span-1 border border-gray-200 p-3 flex flex-col" style="border-radius: 20px; background color: #ffffff;">
                <h2 class="text-sm font-bold text-black mb-2" style="font-family: 'Poppins', sans-serif;">Monthly Sales Comparison</h2>
                <div class="w-full flex-1 overflow-hidden" style="max-width: 100%; min-height: 0;">
                    <canvas id="barChart" class="w-full h-full" style="max-width: 100%; display: block;"></canvas>
                </div>
            </div>

        <!-- ═══ Toast Notifications Container ═══ -->
        <div id="inventory-toast-container" class="fixed top-20 right-6 z-[40] flex flex-col gap-3 pointer-events-none" style="max-width: 360px; width: 100%;"></div>

        <!-- ═══ View All Notifications Modal ═══ -->
        <div id="all-notifications-modal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="closeAllNotificationsModal()"></div>
            <div class="relative bg-white rounded-[28px] border border-slate-200 shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[85vh]">
                <div class="flex items-center justify-between border-b border-[#00fff2] bg-[#00fff2] px-6 py-5">
                    <div>
                        <h3 class="text-xl font-bold text-black">All Inventory Notifications</h3>
                        <p class="text-sm text-slate-800 font-medium">History of low stock and out of stock alerts.</p>
                    </div>
                    <button type="button" onclick="closeAllNotificationsModal()" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div id="modal-notification-list" class="divide-y divide-slate-100 overflow-y-auto p-6 space-y-3">
                    <!-- Loaded dynamically -->
                </div>
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
                    <span id="modal-notif-count" class="text-xs text-slate-500">0 notifications</span>
                    <button type="button" onclick="closeAllNotificationsModal()" class="rounded-[10px] bg-black/10 px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-black/20">Close</button>
                </div>
            </div>
        </div>

        <!-- Floating Low Stock Toast Banner (Pest test requirement) -->
        <div id="dashboardLowStockBanner" class="hidden fixed right-4 top-24 z-[100] max-w-sm rounded-2xl border border-amber-200 bg-white p-4 shadow-2xl transition-all duration-300" role="status">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-amber-50">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900 banner-title">Low Stock Alert</p>
                    <p class="mt-1 text-xs text-slate-600 banner-message">A product is running low on stock.</p>
                </div>
                <button type="button" id="dashboardLowStockBannerDismiss" class="text-slate-400 hover:text-slate-700 transition" aria-label="Dismiss toast">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <style>
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

        /* ---- Inventory Levels (compact stacked card) ---- */
        .inv-row {
            transition: transform 0.2s ease;
        }
        .inv-stack .inv-row:hover {
            transform: translateY(-2px);
        }

        /* ---- Card Heights Sync ---- */
        #inventoryCard, #topSellingWidget, #comparisonCard {
            height: 270px !important;
            max-height: 270px;
            box-sizing: border-box;
            overflow: hidden;
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
            color: rgba(0,0,0,0.65);
            font-weight: 500;
        }
        #categoryLegend .cat-label-active {
            color: #000000;
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

        /* ---- Top Selling open button: emerald background with white text, 10px radius ---- */
        #topSellingOpenBtn {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(54, 173, 163, 0.15);
            color: #000000ff;
            border: none;   
            box-shadow: none;
            transition: background-color 0.18s ease, color 0.18s ease;
            
        }
        #topSellingOpenBtn:hover {
            background-color: rgba(54, 173, 163, 0.3);
            color: #050505ff;
        }
    
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
    background-color: #ffffff;
    color: #6d6d6d;
    opacity: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border-radius: 16px;
    transform: scale(0.96) translateY(6px);
    transition: opacity 0.28s ease, transform 0.28s cubic-bezier(0.22, 1, 0.36, 1);
}
#topItemsModal.is-open .modal-panel {
    opacity: 1;
    transform: scale(1) translateY(0);
}
#topItemsModal .modal-overlay-bg {
    background: rgba(0,0,0,0.6);
    opacity: 0;
    transition: opacity 0.28s ease;
}
#topItemsModal.is-open .modal-overlay-bg {
    opacity: 1;
}
#topItemsModal .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 18px 22px;
    background-color: #00fff2;
    border-radius: 16px 16px 0 0;
    flex-shrink: 0;
}
#topItemsModal .modal-header h3 {
    margin: 0;
    color: #000000;
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    font-size: 1.15rem;
}
#topItemsModal .modal-header p {
    margin: 2px 0 0;
    color: #000000;
    font-size: 0.8rem;
}
#topItemsModal .modal-body {
    padding: 20px;
    overflow: auto;
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
    background-color: transparent !important;
    color: #000000 !important;
    border: none;
    border-radius: 10px;
    width: 28px;
    height: 28px;
    font-size: 20px;
    line-height: 1;
    cursor: pointer;
    transition: background-color 0.18s ease;
}
#topItemsModal #closeTopItemsModal:hover {
    background-color: rgba(0,0,0,0.1) !important;
    color: #000000 !important;
}
        
        /* Range buttons: unselected = original light teal tint background with teal text, selected (clicked) = solid #36ADA3 with same color teal text */
        .sales-range-btn {
            background-color: rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: none;
            padding-top: 0.35rem;
            padding-bottom: 0.35rem;
            padding-left: 0.75rem;
            padding-right: 0.75rem;
            transition: background-color 0.14s ease, color 0.14s ease;
        }

        .sales-range-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        .sales-range-btn.active {
            background-color: #00fff2ff !important;
            color: #000000ff !important;
            font-weight: 700 !important;
            border-color: transparent !important;
        }

        .sales-range-btn.active:hover {
            background-color: #00e6da !important;
            color: #000000ff !important;
        }
        

        /* Dashboard cards shadow — mimic POS terminal containers */
        .border.border-gray-200 {
            box-shadow: 0 10px 30px rgba(2,6,23,0.08);
            transition: box-shadow 0.18s ease;
        }
        /* Neutralize hover lift/shadow so shadow is constant like POS terminal */
        .border.border-gray-200:hover {
            box-shadow: 0 10px 30px rgba(2,6,23,0.08) !important;
            transform: none !important;
        }

        /* ── Toast Notifications ──────────────────────────── */
        #inventory-toast-container {
            max-width: 360px;
        }
        .inv-toast {
            pointer-events: auto;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            transform: translateX(120%);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
            position: relative;
            overflow: hidden;
            width: 100%;
        }
        .inv-toast:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }
        .inv-toast.show {
            transform: translateX(0);
            opacity: 1;
        }
        .inv-toast.show:hover {
            transform: translateY(-2px);
        }
        .inv-toast.hide {
            transform: translateX(10%);
            opacity: 0;
            margin-top: -10px;
        }
        .inv-toast .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 2px;
            transition: width linear;
        }
        .inv-toast.toast-warning .toast-progress { background: #00fff2; }
        .inv-toast.toast-critical .toast-progress { background: #0aada5; }
        .inv-toast-icon {
            width: 32px; height: 32px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 16px;
        }
        .inv-toast.toast-warning .inv-toast-icon { background: #fef3c7; color: #d97706; }
        .inv-toast.toast-critical .inv-toast-icon { background: #fee2e2; color: #dc2626; }
        .inv-toast-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 4px;
            padding: 6px 12px; border-radius: 6px;
            font-size: 12px; font-weight: 500;
            border: 1px solid transparent; cursor: pointer; transition: all 0.15s ease;
            text-decoration: none;
            line-height: 1;
        }
        .inv-toast-btn-order {
            background: #00fff2; color: #000;
        }
        .inv-toast-btn-order:hover { background: #00e6da; }
        .inv-toast-btn-dismiss {
            background: #f9fafb; color: #4b5563; border-color: #e5e7eb;
        }
        .inv-toast-btn-dismiss:hover { background: #f3f4f6; color: #111827; }

        /* ── Notification Center Dropdown ──────────────── */
        .notif-item {
            transition: background-color 0.15s ease;
            border-bottom: 1px solid #f3f4f6;
        }
        .notif-item:last-child {
            border-bottom: none;
        }
        .notif-item:hover { background-color: transparent; }
        .notif-item-unread {
            background-color: #fefce8;
        }
        .notif-item-unread:hover { background-color: #fefce8; }
        
        .notif-status-dot {
            width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
        }
        .notif-status-dot.unread  { background-color: #3b82f6; }
        .notif-status-dot.read    { background-color: transparent; }
        .notif-status-dot.resolved { background-color: #22c55e; }
        
        .notif-btn {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 4px 10px; border-radius: 6px;
            font-size: 11px; font-weight: 500;
            background: #00fff2; color: #000; border: 1px solid #00fff2;
            transition: all 0.15s ease;
            text-decoration: none;
        }
        .notif-btn:hover { background: #00e6da; border-color: #00e6da; color: #000; }
    </style>

@push('scripts')
    @vite('resources/js/dashboard.js')

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
                const skuBadge = top.sku ? `<span class="text-xs text-gray-500 font-mono font-normal">(${escapeHtml(top.sku)})</span>` : '';
                placeholder.innerHTML = `<div class="font-semibold text-gray-900">${escapeHtml(top.name)} ${skuBadge}</div><div class="text-gray-500">Rank ${idx + 1} • ${escapeHtml(top.category || '')}</div>`;
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
            <div class="modal-panel" style="position:relative;max-width:900px;width:95%;max-height:80%;margin:auto;">
                <div class="modal-header">
                    <div>
                        <h3>Top Selling Items</h3>
                        <p>Ranking of best performing products by revenue and quantity.</p>
                    </div>
                    <button id="closeTopItemsModal">×</button>
                </div>
                <div class="modal-body">
                    <table style="width:100%;border-collapse:collapse">
                        <thead>
                            <tr style="text-align:left">
                                <th style="padding:8px">Rank</th>
                                <th style="padding:8px">Item</th>
                                <th style="padding:8px">SKU</th>
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
        tr.innerHTML = `<td style="padding:8px">${i+1}</td><td style="padding:8px">${escapeHtml(it.name)}</td><td style="padding:8px;font-family:monospace;font-size:12px;color:#4b5563;">${escapeHtml(it.sku || '—')}</td><td style="padding:8px">${escapeHtml(it.category||'')}</td><td style="padding:8px">${it.qty ?? it.quantity ?? ''}</td><td style="padding:8px">${it.revenue ? (new Intl.NumberFormat('en-PH',{style:'currency',currency:'PHP'}).format(it.revenue)):''}</td>`;
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

        });
    </script>
    <script>
        // ══════════════════════════════════════════════════════════════
        // ── Inventory Alert Notification System (Toast + Bell) ───────
        // ══════════════════════════════════════════════════════════════

        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        var _shownToastIds = {}; // Track which alerts have already been toasted this session
        var TOAST_DURATION = 9000; // 9 seconds

        // ── Utility ─────────────────────────────────────────────────
        function escHtml(str) {
            var d = document.createElement('div');
            d.appendChild(document.createTextNode(str || ''));
            return d.innerHTML;
        }

        function timeAgo(dateStr) {
            if (!dateStr) return '';
            var now = new Date();
            var date = new Date(dateStr);
            var diffSec = Math.floor((now - date) / 1000);
            if (diffSec < 60) return 'Just now';
            var diffMin = Math.floor(diffSec / 60);
            if (diffMin < 60) return diffMin + (diffMin === 1 ? ' minute ago' : ' minutes ago');
            var diffHr = Math.floor(diffMin / 60);
            if (diffHr < 24) return diffHr + (diffHr === 1 ? ' hour ago' : ' hours ago');
            var diffDay = Math.floor(diffHr / 24);
            if (diffDay < 7) return diffDay + (diffDay === 1 ? ' day ago' : ' days ago');
            return date.toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
        }

        // ══════════════════════════════════════════════════════════════
        // ── TOAST NOTIFICATIONS ──────────────────────────────────────
        // ══════════════════════════════════════════════════════════════
        function showInventoryToasts(alerts) {
            var container = document.getElementById('inventory-toast-container');
            if (!container) return;

            var panel = document.getElementById('notification-panel');
            if (panel && !panel.classList.contains('hidden')) {
                return; // Do not spawn toast popups while Notification Panel is open
            }

            var newAlerts = (Array.isArray(alerts) ? alerts : []).filter(function(a) {
                var toastKey = a.id + '_' + a.notification_type;
                return a.status === 'unread' && !_shownToastIds[toastKey];
            });

            // Show max 5 toasts at once to prevent overflow
            newAlerts.slice(0, 5).forEach(function(alert, idx) {
                var toastKey = alert.id + '_' + alert.notification_type;
                _shownToastIds[toastKey] = true;
                setTimeout(function() {
                    createToast(container, alert);
                }, idx * 200); // stagger by 200ms
            });
        }

        function createToast(container, alert) {
            var isCritical = alert.notification_type === 'out_of_stock';
            var toastClass = isCritical ? 'toast-critical' : 'toast-warning';
            var iconSVG = isCritical 
                ? '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>'
                : '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            var title = isCritical ? 'Out of Stock' : 'Low Stock';
            
            var toast = document.createElement('div');
            toast.className = 'inv-toast ' + toastClass;
            toast.setAttribute('data-toast-alert-id', alert.id);
            toast.innerHTML =
                '<div class="flex items-start gap-3">' +
                    '<div class="inv-toast-icon">' +
                        iconSVG +
                    '</div>' +
                    '<div class="flex-1 min-w-0 pt-0.5">' +
                        '<div class="flex items-center justify-between mb-0.5">' +
                            '<p class="text-[10px] font-bold ' + (isCritical ? 'text-red-600' : 'text-amber-600') + ' uppercase tracking-wider">' + title + '</p>' +
                            '<span class="text-[10px] text-slate-400">Just now</span>' +
                        '</div>' +
                        '<p class="text-sm font-semibold text-slate-900 truncate leading-tight mb-1">' + escHtml(alert.product_name) + '</p>' +
                        '<div class="flex items-center gap-2 text-xs text-slate-500 mb-3">' +
                            '<span>SKU: ' + escHtml(alert.sku) + '</span>' +
                            '<span>&middot;</span>' +
                            '<span class="font-medium ' + (isCritical ? 'text-red-600' : 'text-amber-600') + '">' + (isCritical ? '0 left' : alert.current_stock + ' remaining') + '</span>' +
                        '</div>' +
                        '<div class="flex items-center gap-2">' +
                            '<button type="button" class="inv-toast-btn inv-toast-btn-dismiss" data-toast-dismiss="' + alert.id + '">' +
                                'Dismiss' +
                            '</button>' +
                            '<a href="' + escHtml(alert.order_url || '/purchase-order/create') + '" class="inv-toast-btn inv-toast-btn-order">' +
                                'Order Now' +
                            '</a>' +
                        '</div>' +
                    '</div>' +
                    '<button type="button" class="flex-shrink-0 text-slate-400 hover:text-slate-600 transition-colors" data-toast-close="' + alert.id + '" aria-label="Close">' +
                        '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>' +
                    '</button>' +
                '</div>' +
                '<div class="toast-progress" style="width:100%;"></div>';

            container.appendChild(toast);

            // Slide in
            requestAnimationFrame(function() {
                requestAnimationFrame(function() {
                    toast.classList.add('show');
                });
            });

            // Progress bar
            var progress = toast.querySelector('.toast-progress');
            if (progress) {
                progress.style.transitionDuration = TOAST_DURATION + 'ms';
                setTimeout(function() { progress.style.width = '0%'; }, 50);
            }

            // Auto-dismiss after timeout
            var autoTimer = setTimeout(function() {
                removeToast(toast);
            }, TOAST_DURATION);

            // Dismiss button — saves to notification center
            var dismissBtn = toast.querySelector('[data-toast-dismiss]');
            if (dismissBtn) {
                dismissBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    clearTimeout(autoTimer);
                    removeToast(toast);
                    // Mark as read (not dismissed from DB — keeps in notification center)
                    markNotificationRead(alert.id);
                });
            }

            // Close X button
            var closeBtn = toast.querySelector('[data-toast-close]');
            if (closeBtn) {
                closeBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    clearTimeout(autoTimer);
                    removeToast(toast);
                    markNotificationRead(alert.id);
                });
            }

            // Pause on hover
            toast.addEventListener('mouseenter', function() {
                clearTimeout(autoTimer);
                if (progress) {
                    progress.style.transitionDuration = '0ms';
                    progress.style.width = progress.getBoundingClientRect().width + 'px';
                }
            });
            toast.addEventListener('mouseleave', function() {
                var remaining = TOAST_DURATION * 0.4; // give 40% remaining time
                if (progress) {
                    progress.style.transitionDuration = remaining + 'ms';
                    progress.style.width = '0%';
                }
                autoTimer = setTimeout(function() {
                    removeToast(toast);
                }, remaining);
            });
        }

        function removeToast(toast) {
            if (!toast || toast._removing) return;
            toast._removing = true;
            toast.classList.remove('show');
            toast.classList.add('hide');
            setTimeout(function() {
                if (toast.parentNode) toast.parentNode.removeChild(toast);
            }, 500);
        }

        // Backward compatibility — called by dashboard.js
        function renderInventoryAlerts(alerts) {
            showInventoryToasts(alerts);
            loadNotificationCenter(); // Reload notification bell dropdown and badge in real-time!
        }

        // ══════════════════════════════════════════════════════════════
        // ── NOTIFICATION CENTER (Bell Icon) ──────────────────────────
        // ══════════════════════════════════════════════════════════════
        function loadNotificationCenter() {
            fetch('/api/inventory-notifications?limit=30', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                renderNotificationCenter(data.notifications || [], data.unread_count || 0);
            })
            .catch(function(err) { console.error('Failed to load notifications:', err); });
        }

        function renderNotificationCenter(notifications, unreadCount) {
            var panel = document.getElementById('notification-list');
            var empty = document.getElementById('notification-empty');
            var badge = document.getElementById('notification-badge');
            var centerBadge = document.getElementById('notif-center-unread-badge');
            if (!panel) return;

            // Update bell badge
            if (badge) {
                if (unreadCount > 0) {
                    badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
            if (centerBadge) {
                if (unreadCount > 0) {
                    centerBadge.textContent = unreadCount + ' new';
                    centerBadge.classList.remove('hidden');
                } else {
                    centerBadge.classList.add('hidden');
                }
            }

            panel.innerHTML = '';
            if (!notifications || notifications.length === 0) {
                if (empty) empty.classList.remove('hidden');
                return;
            }
            if (empty) empty.classList.add('hidden');

            notifications.forEach(function(n) {
                var isCritical = n.notification_type === 'out_of_stock';
                var iconSVG = isCritical 
                    ? '<svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>'
                    : '<svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                var typeLabel = isCritical ? 'Out of Stock' : 'Low Stock';
                var statusClass = 'notif-item notif-item-' + n.status;
                var ago = timeAgo(n.created_at);
                var stockText = isCritical ? '0 left' : n.current_stock + ' remaining';
                var shortSku = n.sku.length > 15 ? n.sku.substring(0, 15) + '...' : n.sku;

                var item = document.createElement('div');
                item.className = statusClass + ' px-4 py-3 cursor-pointer';
                item.setAttribute('data-notif-id', n.id);

                item.innerHTML =
                    '<div class="flex items-start gap-3">' +
                        '<div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center ' + (isCritical ? 'bg-red-50' : 'bg-amber-50') + '">' +
                            iconSVG +
                        '</div>' +
                        '<div class="flex-1 min-w-0">' +
                            '<div class="flex items-center justify-between mb-0.5">' +
                                '<div class="flex items-center gap-1.5">' +
                                    '<span class="text-[10px] font-bold uppercase tracking-wider ' + (isCritical ? 'text-red-600' : 'text-amber-600') + '">' + typeLabel + '</span>' +
                                    (n.status === 'unread' ? '<span class="notif-status-dot unread"></span>' : '') +
                                '</div>' +
                                '<span class="text-[10px] text-slate-400">' + escHtml(ago) + '</span>' +
                            '</div>' +
                            '<p class="text-[13px] font-semibold text-slate-900 truncate mb-1">' + escHtml(n.product_name) + '</p>' +
                            '<div class="flex items-center justify-between">' +
                                '<div class="flex items-center gap-1.5 text-[11px] text-slate-500 whitespace-nowrap">' +
                                    '<span class="truncate max-w-[80px]">' + escHtml(shortSku) + '</span>' +
                                    '<span>&middot;</span>' +
                                    '<span class="font-medium whitespace-nowrap ' + (isCritical ? 'text-red-600' : 'text-amber-600') + '">' + stockText + '</span>' +
                                '</div>' +
                                (n.status !== 'resolved'
                                    ? '<a href="' + escHtml(n.order_url || '/purchase-order/create') + '" class="notif-btn flex-shrink-0" onclick="event.stopPropagation();">Order</a>'
                                    : '<span class="inline-flex items-center gap-1 text-[10px] font-medium text-emerald-600 flex-shrink-0"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Resolved</span>'
                                ) +
                            '</div>' +
                        '</div>' +
                    '</div>';

                // Mark as read on click
                if (n.status === 'unread') {
                    item.addEventListener('click', function() {
                        markNotificationRead(n.id);
                    });
                }

                panel.appendChild(item);
            });
        }

        function markNotificationRead(notifId) {
            fetch('/api/inventory-notifications/' + notifId + '/read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            })
            .then(function() { loadNotificationCenter(); })
            .catch(function(err) { console.error('Mark read failed:', err); });
        }

        function markAllNotificationsRead() {
            fetch('/api/inventory-notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            })
            .then(function() { loadNotificationCenter(); })
            .catch(function(err) {
                console.error('Mark all read failed:', err);
                // Fallback: mark individually
                fetch('/api/inventory-notifications?limit=100', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    var ids = (data.notifications || []).filter(function(n) { return n.status === 'unread'; }).map(function(n) { return n.id; });
                    return Promise.all(ids.map(function(id) {
                        return fetch('/api/inventory-notifications/' + id + '/read', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        });
                    }));
                })
                .then(function() { loadNotificationCenter(); });
            });
        }

        function toggleNotificationPanel(e) {
            e.stopPropagation();
            var panel = document.getElementById('notification-panel');
            if (!panel) return;

            // Close profile dropdown first
            var profileDropdown = document.getElementById('dashboardProfileDropdown');
            if (profileDropdown && !profileDropdown.classList.contains('hidden')) {
                profileDropdown.classList.add('hidden');
                profileDropdown.classList.add('opacity-0', 'scale-95');
            }

            // Dismiss all toast notifications
            var toastContainer = document.getElementById('inventory-toast-container');
            if (toastContainer) {
                toastContainer.innerHTML = '';
            }

            var isHidden = panel.classList.contains('hidden');
            panel.classList.toggle('hidden');
            if (isHidden) loadNotificationCenter();
        }

        function openAllNotificationsModal() {
            var modal = document.getElementById('all-notifications-modal');
            var list = document.getElementById('modal-notification-list');
            var count = document.getElementById('modal-notif-count');
            if (!modal || !list) return;

            // Hide the dropdown panel
            var panel = document.getElementById('notification-panel');
            if (panel) panel.classList.add('hidden');

            list.innerHTML = '<div class="p-6 text-center text-slate-500">Loading history…</div>';
            modal.classList.remove('hidden');

            fetch('/api/inventory-notifications?limit=250', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var notifications = data.notifications || [];
                if (count) count.textContent = notifications.length + ' notifications';
                
                if (notifications.length === 0) {
                    list.innerHTML = '<div class="p-6 text-center text-slate-400">No notification history.</div>';
                    return;
                }

                list.innerHTML = notifications.map(function(n) {
                    var isCritical = n.notification_type === 'out_of_stock';
                    var iconSVG = isCritical 
                        ? '<svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>'
                        : '<svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                    var typeLabel = isCritical ? 'Out of Stock' : 'Low Stock';
                    var ago = timeAgo(n.created_at);
                    var stockText = isCritical ? '0 left' : n.current_stock + ' remaining';
                    var statusClass = 'notif-item notif-item-' + n.status;

                    return '<div class="' + statusClass + ' p-4 rounded-xl border border-slate-100 flex items-start justify-between gap-4 transition-colors">' +
                        '<div class="flex items-start gap-4">' +
                            '<div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center ' + (isCritical ? 'bg-red-50' : 'bg-amber-50') + '">' +
                                iconSVG +
                            '</div>' +
                            '<div>' +
                                '<div class="flex items-center gap-2 mb-0.5">' +
                                    '<span class="text-[10px] font-bold uppercase tracking-wider ' + (isCritical ? 'text-red-600' : 'text-amber-600') + '">' + typeLabel + '</span>' +
                                    (n.status === 'unread' ? '<span class="notif-status-dot unread"></span>' : '') +
                                '</div>' +
                                '<p class="text-[14px] font-semibold text-slate-900 mt-1">' + escHtml(n.product_name) + '</p>' +
                                '<div class="flex items-center gap-2 mt-1">' +
                                    '<span class="text-xs text-slate-500">SKU: ' + escHtml(n.sku) + '</span>' +
                                    '<span class="text-slate-400">&middot;</span>' +
                                    '<span class="text-xs font-medium ' + (isCritical ? 'text-red-600' : 'text-amber-600') + '">' + stockText + '</span>' +
                                '</div>' +
                                '<p class="text-[11px] text-slate-400 mt-1.5">' + escHtml(ago) + '</p>' +
                            '</div>' +
                        '</div>' +
                        (n.status !== 'resolved'
                            ? '<a href="' + escHtml(n.order_url || '/purchase-order/create') + '" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-[#00fff2] text-black text-xs font-medium hover:bg-[#00e6da] transition-colors">Order Now</a>'
                            : '<span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Resolved</span>'
                        ) +
                    '</div>';
                }).join('');
            })
            .catch(function(err) {
                list.innerHTML = '<div class="p-6 text-center text-rose-500">Failed to load notification history.</div>';
            });
        }

        function closeAllNotificationsModal() {
            var modal = document.getElementById('all-notifications-modal');
            if (modal) modal.classList.add('hidden');
        }

        // Close panel on outside click
        document.addEventListener('click', function(e) {
            var wrapper = document.getElementById('notification-bell-wrapper');
            var panel   = document.getElementById('notification-panel');
            if (panel && wrapper && !wrapper.contains(e.target)) {
                panel.classList.add('hidden');
            }
        });

        // Close notification panel when profile button is clicked
        document.getElementById('dashboardProfileButton')?.addEventListener('click', function() {
            var notifPanel = document.getElementById('notification-panel');
            if (notifPanel && !notifPanel.classList.contains('hidden')) {
                notifPanel.classList.add('hidden');
            }
        });

        // Initial load
        loadNotificationCenter();
    </script>
@endpush
</x-layouts.app>
