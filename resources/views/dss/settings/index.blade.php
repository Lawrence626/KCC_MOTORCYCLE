<x-layouts.app :title="__('DSS Configuration Settings')">
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="text-lg font-bold text-slate-900 leading-tight">DSS Configuration</h1>
                <p class="text-xs text-slate-500 mt-0.5">Configure Dead Stock Detection &amp; Recommendation Engine settings</p>
            </div>
            <a href="{{ route('dss.dead-stock.index') }}" class="inline-flex items-center gap-1.5 rounded-[10px] border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Dead Stock
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Main Settings Form --}}
        <div class="lg:col-span-2">
            <div class="bg-white border border-slate-200 rounded-[14px] shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-800 bg-[#0f172a] rounded-t-[14px]">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-white">Configuration Settings</h2>
                </div>
                <form action="{{ route('dss.settings.update') }}" method="POST" id="settingsForm">
                    @csrf
                    <div class="p-5 space-y-6">

                        {{-- Dead Stock Threshold --}}
                        <div>
                            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider pb-2 border-b border-slate-100 mb-3">Dead Stock Classification</h3>
                            <div>
                                <label for="dead_stock_threshold_days" class="block text-sm font-medium text-slate-700 mb-1">Days Without Sale (Dead Stock Threshold)</label>
                                <div class="flex items-center gap-2">
                                    <input type="number"
                                           class="flex-1 rounded-[10px] border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-cyan-400 @error('dead_stock_threshold_days') border-red-400 @enderror"
                                           id="dead_stock_threshold_days"
                                           name="dead_stock_threshold_days"
                                           value="{{ old('dead_stock_threshold_days', $settings->where('key', 'dead_stock_threshold_days')->first()->value ?? 90) }}"
                                           min="30" max="365" step="1">
                                    <span class="text-xs font-semibold text-slate-500 bg-slate-100 rounded-[8px] px-3 py-2">days</span>
                                </div>
                                <p class="text-xs text-slate-400 mt-1">Products with no sales for this duration are classified as Dead Stock. Recommended: 90 days (3 months)</p>
                                @error('dead_stock_threshold_days')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Slow Moving Threshold --}}
                        <div>
                            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider pb-2 border-b border-slate-100 mb-3">Slow Moving Classification</h3>
                            <div>
                                <label for="slow_moving_threshold_days" class="block text-sm font-medium text-slate-700 mb-1">Days Without Sale (Slow Moving Threshold)</label>
                                <div class="flex items-center gap-2">
                                    <input type="number"
                                           class="flex-1 rounded-[10px] border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-cyan-400 @error('slow_moving_threshold_days') border-red-400 @enderror"
                                           id="slow_moving_threshold_days"
                                           name="slow_moving_threshold_days"
                                           value="{{ old('slow_moving_threshold_days', $settings->where('key', 'slow_moving_threshold_days')->first()->value ?? 60) }}"
                                           min="30" max="365" step="1">
                                    <span class="text-xs font-semibold text-slate-500 bg-slate-100 rounded-[8px] px-3 py-2">days</span>
                                </div>
                                <p class="text-xs text-slate-400 mt-1">Products with no sales for this duration are classified as Slow Moving. Recommended: 60 days (2 months)</p>
                                @error('slow_moving_threshold_days')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Fast Moving Threshold --}}
                        <div>
                            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider pb-2 border-b border-slate-100 mb-3">Fast Moving Classification</h3>
                            <div>
                                <label for="fast_moving_threshold_units" class="block text-sm font-medium text-slate-700 mb-1">Minimum Units Sold in 30 Days</label>
                                <div class="flex items-center gap-2">
                                    <input type="number"
                                           class="flex-1 rounded-[10px] border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-cyan-400 @error('fast_moving_threshold_units') border-red-400 @enderror"
                                           id="fast_moving_threshold_units"
                                           name="fast_moving_threshold_units"
                                           value="{{ old('fast_moving_threshold_units', $settings->where('key', 'fast_moving_threshold_units')->first()->value ?? 50) }}"
                                           min="10" max="1000" step="5">
                                    <span class="text-xs font-semibold text-slate-500 bg-slate-100 rounded-[8px] px-3 py-2">units</span>
                                </div>
                                <p class="text-xs text-slate-400 mt-1">Products selling at least this many units per month are classified as Fast Moving. Used for bundle recommendations.</p>
                                @error('fast_moving_threshold_units')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Recommendation Types --}}
                        <div>
                            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider pb-2 border-b border-slate-100 mb-3">Recommendation Types</h3>
                            <div class="space-y-3">
                                @foreach([
                                    ['key' => 'promotion_recommendation_enabled', 'label' => 'Enable Promotional Campaign Recommendations', 'desc' => 'Suggest promotional campaigns for dead stock items'],
                                    ['key' => 'discount_recommendation_enabled', 'label' => 'Enable Price Reduction Recommendations', 'desc' => 'Suggest price reductions and discounts'],
                                    ['key' => 'bundle_recommendation_enabled', 'label' => 'Enable Bundle Offer Recommendations', 'desc' => 'Suggest bundling with fast-moving products'],
                                ] as $toggle)
                                <label class="flex items-start gap-3 cursor-pointer group">
                                    <div class="relative flex-shrink-0 mt-0.5">
                                        <input type="checkbox" id="{{ $toggle['key'] }}" name="{{ $toggle['key'] }}" value="1"
                                               {{ old($toggle['key'], $settings->where('key', $toggle['key'])->first()->value ?? true) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-9 h-5 bg-slate-200 rounded-full peer peer-checked:bg-cyan-500 transition-colors"></div>
                                        <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">{{ $toggle['label'] }}</p>
                                        <p class="text-xs text-slate-400">{{ $toggle['desc'] }}</p>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- System Status --}}
                        <div>
                            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider pb-2 border-b border-slate-100 mb-3">System Status</h3>
                            <label class="flex items-start gap-3 cursor-pointer">
                                <div class="relative flex-shrink-0 mt-0.5">
                                    <input type="checkbox" id="dss_analysis_enabled" name="dss_analysis_enabled" value="1"
                                           {{ old('dss_analysis_enabled', $settings->where('key', 'dss_analysis_enabled')->first()->value ?? true) ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-9 h-5 bg-slate-200 rounded-full peer peer-checked:bg-cyan-500 transition-colors"></div>
                                    <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">Enable Automatic DSS Analysis</p>
                                    <p class="text-xs text-slate-400">Automatically recalculate dead stock analysis when inventory changes</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="px-5 py-4 border-t border-slate-100 bg-slate-50 rounded-b-[14px] flex items-center justify-between">
                        <button type="reset" class="inline-flex items-center gap-1.5 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Reset to Current
                        </button>
                        <button type="submit" id="saveSettingsBtn" class="inline-flex items-center gap-1.5 rounded-[10px] bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Save Configuration
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-4">
            <div class="bg-white border border-slate-200 rounded-[14px] shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600">Priority Levels</h3>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <span class="inline-flex px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold">Critical</span>
                        <p class="text-xs text-slate-500 mt-1">Not sold for 180+ days</p>
                    </div>
                    <div>
                        <span class="inline-flex px-2 py-1 rounded-full bg-orange-100 text-orange-700 text-xs font-bold">High</span>
                        <p class="text-xs text-slate-500 mt-1">Not sold for 120–179 days</p>
                    </div>
                    <div>
                        <span class="inline-flex px-2 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">Medium</span>
                        <p class="text-xs text-slate-500 mt-1">Not sold for 90–119 days</p>
                    </div>
                    <div>
                        <span class="inline-flex px-2 py-1 rounded-full bg-sky-100 text-sky-700 text-xs font-bold">Low</span>
                        <p class="text-xs text-slate-500 mt-1">Not sold for 60–89 days</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-[14px] shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600">Recommendation Types</h3>
                </div>
                <div class="p-4 space-y-2.5">
                    @foreach([
                        ['label' => 'Promotion', 'desc' => 'Marketing campaigns'],
                        ['label' => 'Discount', 'desc' => 'Price reduction'],
                        ['label' => 'Bundle', 'desc' => 'Bundle with fast movers'],
                        ['label' => 'Relocate', 'desc' => 'Move to high-demand location'],
                        ['label' => 'Featured', 'desc' => 'Display prominence'],
                        ['label' => 'Social Media', 'desc' => 'Online promotion'],
                        ['label' => 'Supplier Return', 'desc' => 'Return to supplier'],
                    ] as $type)
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 flex-shrink-0"></span>
                        <span class="text-xs text-slate-700"><span class="font-semibold">{{ $type['label'] }}</span> — {{ $type['desc'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('settingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('saveSettingsBtn');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Saving...';

            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: formData
            })
            .then(r => r.json())
            .then(function() {
                btn.disabled = false;
                btn.innerHTML = originalText;
                // Show success state
                btn.classList.remove('bg-slate-900', 'hover:bg-slate-700');
                btn.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
                btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Saved!';
                setTimeout(() => {
                    btn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
                    btn.classList.add('bg-slate-900', 'hover:bg-slate-700');
                    btn.innerHTML = originalText;
                }, 2000);
            })
            .catch(function() {
                btn.disabled = false;
                btn.innerHTML = originalText;
                alert('Error saving settings. Please try again.');
            });
        });
    </script>
</x-layouts.app>
