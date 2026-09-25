<x-layouts.app :title="__('Profile & Settings')">
    <x-slot name="header">
        <div>
            <h1 class="text-lg font-bold text-slate-900 leading-tight">Profile & Settings</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage your account, security, and interface preferences</p>
        </div>
    </x-slot>

    <div class="space-y-4">

        <div class="grid grid-cols-1 lg:grid-cols-1 gap-6">
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-4 border-b border-slate-100 pb-3 mb-6">
                    <button data-tab="preferences" class="settings-tab px-4 py-2 rounded-lg text-sm font-medium bg-cyan-600 text-white transition">Preferences & Appearance</button>
                    <button data-tab="security" class="settings-tab px-4 py-2 rounded-lg text-sm font-medium text-slate-700 bg-slate-50 transition">Security</button>
                    <a href="{{ route('profile.show') }}" class="ml-auto text-sm text-cyan-600 hover:text-cyan-500 font-medium transition">View Profile &rarr;</a>
                </div>

                <!-- Preferences Pane -->
                <div id="tabPreferences" class="settings-pane space-y-6">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900 mb-1">Theme Mode</h3>
                        <p class="text-xs text-slate-500 mb-4">Choose your preferred interface appearance across the system.</p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-xl">
                            <!-- Light Mode Option -->
                            <button type="button" onclick="selectThemeOption('light')" id="themeBtnLight" class="theme-option-card flex flex-col items-center justify-center p-4 rounded-xl border-2 border-slate-200 bg-slate-50 hover:bg-slate-100 hover:border-slate-300 transition text-center group cursor-pointer">
                                <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 grid place-items-center mb-2 group-hover:scale-110 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-slate-900">Light Mode</span>
                                <span class="text-xs text-slate-500 mt-0.5">Classic clean look</span>
                            </button>

                            <!-- Dark Mode Option -->
                            <button type="button" onclick="selectThemeOption('dark')" id="themeBtnDark" class="theme-option-card flex flex-col items-center justify-center p-4 rounded-xl border-2 border-slate-200 bg-slate-50 hover:bg-slate-100 hover:border-slate-300 transition text-center group cursor-pointer">
                                <div class="w-10 h-10 rounded-full bg-cyan-950 text-cyan-400 grid place-items-center mb-2 group-hover:scale-110 transition border border-cyan-500/30">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-slate-900">Dark Mode</span>
                                <span class="text-xs text-slate-500 mt-0.5">Eye-friendly deep dark</span>
                            </button>

                            <!-- System Default Option -->
                            <button type="button" onclick="selectThemeOption('system')" id="themeBtnSystem" class="theme-option-card flex flex-col items-center justify-center p-4 rounded-xl border-2 border-slate-200 bg-slate-50 hover:bg-slate-100 hover:border-slate-300 transition text-center group cursor-pointer">
                                <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-700 grid place-items-center mb-2 group-hover:scale-110 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-slate-900">System</span>
                                <span class="text-xs text-slate-500 mt-0.5">Match device setting</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Security Pane -->
                <div id="tabSecurity" class="settings-pane hidden space-y-6">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-2">Two-factor Authentication</h3>
                        <p class="text-xs text-slate-500 mb-3">Manage your two-factor authentication settings (TOTP, SMS).</p>
                        <div class="flex gap-2">
                            <button type="button" class="px-3 py-2 rounded-lg border border-slate-200 text-sm text-slate-700 hover:bg-slate-50 transition">Enable</button>
                            <button type="button" class="px-3 py-2 rounded-lg border border-slate-200 text-sm text-slate-700 hover:bg-slate-50 transition">Disable</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab switching
        document.querySelectorAll('.settings-tab').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.settings-tab').forEach(b => {
                    b.classList.remove('bg-cyan-600', 'text-white');
                    b.classList.add('bg-slate-50', 'text-slate-700');
                });
                btn.classList.add('bg-cyan-600', 'text-white');
                btn.classList.remove('bg-slate-50', 'text-slate-700');

                const tab = btn.dataset.tab;
                document.querySelectorAll('.settings-pane').forEach(p => p.classList.add('hidden'));
                const targetPane = document.getElementById('tab' + tab.charAt(0).toUpperCase() + tab.slice(1));
                if (targetPane) targetPane.classList.remove('hidden');
            });
        });

        // Theme option selection
        function syncThemeCards() {
            const savedTheme = localStorage.getItem('theme') || 'system';
            ['light', 'dark', 'system'].forEach(t => {
                const card = document.getElementById('themeBtn' + t.charAt(0).toUpperCase() + t.slice(1));
                if (!card) return;
                if (savedTheme === t) {
                    card.classList.add('border-cyan-500', 'bg-cyan-500/10', 'ring-2', 'ring-cyan-500/30');
                    card.classList.remove('border-slate-200', 'bg-slate-50');
                } else {
                    card.classList.remove('border-cyan-500', 'bg-cyan-500/10', 'ring-2', 'ring-cyan-500/30');
                    card.classList.add('border-slate-200', 'bg-slate-50');
                }
            });
        }

        window.selectThemeOption = function(mode) {
            if (window.setTheme) {
                window.setTheme(mode);
            }
            syncThemeCards();
        };

        window.addEventListener('themeChanged', syncThemeCards);
        document.addEventListener('DOMContentLoaded', syncThemeCards);
    </script>
</x-layouts.app>
