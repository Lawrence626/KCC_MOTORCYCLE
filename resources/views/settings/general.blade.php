<x-layouts.app :title="__('Profile & Settings')">

    <x-slot name="header">
        <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Profile & Settings</h1>
                <p class="text-xs text-slate-500 mt-0.5">Manage your account and interface preferences</p>
            </div>
            <a href="{{ route('profile.show', ['from' => 'settings']) }}" class="inline-flex w-fit items-center px-5 py-2.5 rounded-[10px] text-sm font-bold bg-theme-primary border border-[#59b2c2] shadow-sm hover:shadow transition cursor-pointer">View Profile</a>
        </div>
    </x-slot>

    <div class="space-y-4">

        <div class="grid grid-cols-1 lg:grid-cols-1 gap-6">
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-4 border-b border-slate-100 pb-3 mb-6">
                    <button data-tab="preferences" class="settings-tab rounded-[10px] border px-4 py-2 text-sm font-semibold transition-all bg-[#0f172a] text-white border-[#0f172a] shadow-sm focus:outline-none focus:ring-0 cursor-default">Preferences & Appearance</button>
                </div>

                <!-- Preferences Pane -->
                <div id="tabPreferences" class="settings-pane flex flex-col space-y-6">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900 mb-1">Theme Mode</h3>
                        <p class="text-xs text-slate-500 mb-4">Choose your preferred interface appearance across the system.</p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-xl">
                            <!-- Light Mode Option -->
                            <button type="button" onclick="selectThemeOption('light')" id="themeBtnLight" class="theme-option-card flex flex-col items-center justify-center p-4 rounded-[10px] border-2 border-slate-200 bg-slate-50 hover:bg-slate-100 hover:border-slate-300 transition text-center group cursor-pointer focus:outline-none focus:ring-0">
                                <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 grid place-items-center mb-2 group-hover:scale-110 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-slate-900">Light Mode</span>
                                <span class="text-xs text-slate-500 mt-0.5">Classic clean look</span>
                            </button>

                            <!-- Dark Mode Option -->
                            <button type="button" onclick="selectThemeOption('dark')" id="themeBtnDark" class="theme-option-card flex flex-col items-center justify-center p-4 rounded-[10px] border-2 border-slate-200 bg-slate-50 hover:bg-slate-100 hover:border-slate-300 transition text-center group cursor-pointer focus:outline-none focus:ring-0">
                                <div class="w-10 h-10 rounded-full bg-cyan-950 text-cyan-400 grid place-items-center mb-2 group-hover:scale-110 transition border border-cyan-500/30">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-slate-900">Dark Mode</span>
                                <span class="text-xs text-slate-500 mt-0.5">Eye-friendly deep dark</span>
                            </button>

                            <!-- System Default Option -->
                            <button type="button" onclick="selectThemeOption('system')" id="themeBtnSystem" class="theme-option-card flex flex-col items-center justify-center p-4 rounded-[10px] border-2 border-slate-200 bg-slate-50 hover:bg-slate-100 hover:border-slate-300 transition text-center group cursor-pointer focus:outline-none focus:ring-0">
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

                    <!-- System Logo & Branding Section -->
                    <div class="order-2 border-t border-slate-100 dark:border-slate-800 pt-6">
                        <div class="mb-1">
                            <h3 class="text-base font-semibold text-slate-900 dark:text-white">System Logo & Branding</h3>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-5">
                            Upload an alternative logo for the sidebar and system navigation. Any image uploaded will automatically fit the sidebar's designated dimensions without distortion or stretching.
                        </p>

                        <!-- Alerts / Status Message Container -->
                        <div id="logoAlertContainer" class="hidden mb-4"></div>

                        @if(session('success'))
                            <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center gap-2">
                                <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                <span>{{ session('error') }}</span>
                            </div>
                        @endif

                        <div class="w-full">
                            <form id="logoUploadForm" action="/settings/logo" method="POST" enctype="multipart/form-data" class="space-y-4 w-full">
                                @csrf

                                <!-- Hidden File Input -->
                                <input type="file" id="logoFileInput" name="logo" accept="image/*,.svg,.png,.jpg,.jpeg,.webp,.gif,.bmp,.ico,.avif,.jfif,.tif,.tiff,.heic,.heif" class="hidden" />

                                <!-- Upload Trigger Dropzone Box (Full width, sagad sa right) -->
                                <div id="dropzoneArea"
                                     onclick="document.getElementById('logoFileInput').click()"
                                     class="w-full group border-2 border-dashed border-slate-200 dark:border-slate-700 hover:border-cyan-500 dark:hover:border-cyan-400 rounded-2xl p-6 sm:p-8 flex flex-col items-center justify-center text-center cursor-pointer transition bg-slate-50/60 hover:bg-cyan-50/20 dark:bg-slate-800/40">

                                    <!-- Empty State (Default view before selecting an image) -->
                                    <div id="dropzoneEmptyState" class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-full bg-cyan-100 text-cyan-600 dark:bg-cyan-950 dark:text-cyan-400 flex items-center justify-center mb-3 group-hover:scale-110 transition shadow-xs">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                                            Click to upload <span class="font-normal text-slate-500">or drag and drop</span>
                                        </p>
                                        <p class="text-xs text-slate-400 mt-1">
                                            Any image format supported • Automatic instant update
                                        </p>
                                    </div>

                                    <!-- Preview State (Appears right inside this container when image is chosen) -->
                                    <div id="dropzonePreviewState" class="hidden flex flex-col items-center justify-center w-full py-2">
                                        <div class="p-5 sm:p-6 rounded-2xl flex flex-col items-center justify-center border border-slate-700/60 shadow-xl mb-3.5"
                                             style="background: linear-gradient(to bottom, #000000, #2b2b2b);">
                                            <div class="w-64 sm:w-72 h-40 sm:h-48 flex items-center justify-center overflow-hidden">
                                                <img id="settingsLogoPreview"
                                                     src=""
                                                     alt="Sidebar Preview"
                                                     class="max-w-full max-h-full w-auto h-auto object-contain transition-all duration-200" />
                                            </div>
                                            <div class="mt-2.5 text-[11px] text-slate-400 font-mono flex items-center gap-1.5">
                                                <span class="inline-block w-2 h-2 rounded-full bg-[#22c55e]"></span>
                                                <span>Fixed Size (192 × 128 px container)</span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="relative flex h-2.5 w-2.5 items-center justify-center">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#22c55e] opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-[#22c55e]"></span>
                                            </span>
                                            <span class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Sidebar Live Preview</span>
                                        </div>
                                        <p class="text-xs font-medium text-slate-700 dark:text-slate-200">
                                            Click anywhere in this box to choose a different image
                                        </p>
                                    </div>
                                </div>

                                <!-- Selected File Information (Visible after file chosen) -->
                                <div id="selectedFileInfo" class="hidden w-full p-3.5 rounded-xl bg-cyan-50/70 border border-cyan-200 dark:bg-cyan-950/40 dark:border-cyan-900/60 flex items-center justify-between">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-9 h-9 rounded-lg bg-cyan-500/20 text-cyan-600 dark:text-cyan-400 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p id="selectedFileName" class="text-xs font-semibold text-slate-900 dark:text-white truncate">image.png</p>
                                            <p id="selectedFileSize" class="text-[11px] text-slate-500 dark:text-slate-400">120 KB</p>
                                        </div>
                                    </div>
                                    <button type="button" onclick="cancelSelectedLogo()" class="text-xs text-slate-900 hover:text-slate-700 dark:text-white dark:hover:text-slate-300 px-2.5 py-1 font-bold transition cursor-pointer">
                                        Cancel
                                    </button>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-wrap items-center gap-3 pt-1">
                                    <button type="submit"
                                            id="uploadLogoSubmitBtn"
                                            class="px-5 py-2.5 rounded-[10px] text-sm font-bold bg-[#6EC1D1] hover:bg-[#59b2c2] text-slate-900 border border-[#59b2c2] shadow-sm hover:shadow transition flex items-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-0">
                                        <svg id="uploadBtnSpinner" class="w-4 h-4 animate-spin hidden text-slate-900" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span id="uploadBtnText">Upload Logo</span>
                                    </button>

                                    <button type="button"
                                            id="resetLogoBtn"
                                            onclick="resetToDefaultLogo()"
                                            class="{{ ($isCustomLogo ?? \App\Services\AppSettingService::hasCustomLogo()) ? '' : 'hidden' }} px-4 py-2.5 rounded-[10px] text-sm font-medium border border-rose-200 text-rose-600 hover:bg-rose-50 dark:border-rose-900 dark:text-rose-400 dark:hover:bg-rose-950/40 transition cursor-pointer flex items-center gap-1.5 focus:outline-none focus:ring-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        <span>Reset Logo</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Color Theme Section -->
                    <div class="order-1 border-t border-slate-100 dark:border-slate-800 pt-6">
                        <div class="mb-1">
                            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Color Theme</h3>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-5">
                            Choose a primary accent color for buttons and interactive elements across the entire system.
                        </p>

                        <!-- Preset Color Swatches -->
                        <div class="space-y-2.5 mb-5">
                            <!-- Row 1 -->
                            <div class="flex flex-wrap gap-2.5">
                                <button type="button" onclick="window.applyThemeColor('#2563EB', true); syncThemeSwatches();" data-color="#2563EB" class="theme-swatch w-9 h-9 rounded-full border-2 border-transparent transition-all duration-150 cursor-pointer focus:outline-none" style="background-color: #2563EB;" title="Blue"></button>
                                <button type="button" onclick="window.applyThemeColor('#00A3FF', true); syncThemeSwatches();" data-color="#00A3FF" class="theme-swatch w-9 h-9 rounded-full border-2 border-transparent transition-all duration-150 cursor-pointer focus:outline-none" style="background-color: #00A3FF;" title="Sky Blue"></button>
                                <button type="button" onclick="window.applyThemeColor('#00C2E0', true); syncThemeSwatches();" data-color="#00C2E0" class="theme-swatch w-9 h-9 rounded-full border-2 border-transparent transition-all duration-150 cursor-pointer focus:outline-none" style="background-color: #00C2E0;" title="Cyan"></button>
                                <button type="button" onclick="window.applyThemeColor('#0D9488', true); syncThemeSwatches();" data-color="#0D9488" class="theme-swatch w-9 h-9 rounded-full border-2 border-transparent transition-all duration-150 cursor-pointer focus:outline-none" style="background-color: #0D9488;" title="Teal"></button>
                                <button type="button" onclick="window.applyThemeColor('#2E7D32', true); syncThemeSwatches();" data-color="#2E7D32" class="theme-swatch w-9 h-9 rounded-full border-2 border-transparent transition-all duration-150 cursor-pointer focus:outline-none" style="background-color: #2E7D32;" title="Green"></button>
                                <button type="button" onclick="window.applyThemeColor('#E91E63', true); syncThemeSwatches();" data-color="#E91E63" class="theme-swatch w-9 h-9 rounded-full border-2 border-transparent transition-all duration-150 cursor-pointer focus:outline-none" style="background-color: #E91E63;" title="Pink"></button>
                                <button type="button" onclick="window.applyThemeColor('#FFA000', true); syncThemeSwatches();" data-color="#FFA000" class="theme-swatch w-9 h-9 rounded-full border-2 border-transparent transition-all duration-150 cursor-pointer focus:outline-none" style="background-color: #FFA000;" title="Amber"></button>
                                <button type="button" onclick="window.applyThemeColor('#E64A19', true); syncThemeSwatches();" data-color="#E64A19" class="theme-swatch w-9 h-9 rounded-full border-2 border-transparent transition-all duration-150 cursor-pointer focus:outline-none" style="background-color: #E64A19;" title="Orange Red"></button>
                                <button type="button" onclick="window.applyThemeColor('#D32F2F', true); syncThemeSwatches();" data-color="#D32F2F" class="theme-swatch w-9 h-9 rounded-full border-2 border-transparent transition-all duration-150 cursor-pointer focus:outline-none" style="background-color: #D32F2F;" title="Red"></button>
                            </div>
                            <!-- Row 2 -->
                            <div class="flex flex-wrap gap-2.5">
                                <button type="button" onclick="window.applyThemeColor('#6A1B9A', true); syncThemeSwatches();" data-color="#6A1B9A" class="theme-swatch w-9 h-9 rounded-full border-2 border-transparent transition-all duration-150 cursor-pointer focus:outline-none" style="background-color: #6A1B9A;" title="Purple"></button>
                                <button type="button" onclick="window.applyThemeColor('#3949AB', true); syncThemeSwatches();" data-color="#3949AB" class="theme-swatch w-9 h-9 rounded-full border-2 border-transparent transition-all duration-150 cursor-pointer focus:outline-none" style="background-color: #3949AB;" title="Indigo"></button>
                                <button type="button" onclick="window.applyThemeColor('#546E7A', true); syncThemeSwatches();" data-color="#546E7A" class="theme-swatch w-9 h-9 rounded-full border-2 border-transparent transition-all duration-150 cursor-pointer focus:outline-none" style="background-color: #546E7A;" title="Slate"></button>
                                <button type="button" onclick="window.applyThemeColor('#4E342E', true); syncThemeSwatches();" data-color="#4E342E" class="theme-swatch w-9 h-9 rounded-full border-2 border-transparent transition-all duration-150 cursor-pointer focus:outline-none" style="background-color: #4E342E;" title="Charcoal"></button>
                                <button type="button" onclick="window.applyThemeColor('#1E293B', true); syncThemeSwatches();" data-color="#1E293B" class="theme-swatch w-9 h-9 rounded-full border-2 border-transparent transition-all duration-150 cursor-pointer focus:outline-none" style="background-color: #1E293B;" title="Black"></button>
                                <button type="button" onclick="window.applyThemeColor('#6EC1D1', true); syncThemeSwatches();" data-color="#6EC1D1" class="theme-swatch w-9 h-9 rounded-full border-2 border-transparent transition-all duration-150 cursor-pointer focus:outline-none" style="background-color: #6EC1D1;" title="Default Teal"></button>
                                <!-- Custom/White button with border -->
                                <button type="button" onclick="window.applyThemeColor('#FFFFFF', true); syncThemeSwatches();" data-color="#FFFFFF" class="theme-swatch w-9 h-9 rounded-full border-2 border-slate-300 transition-all duration-150 cursor-pointer focus:outline-none" style="background-color: #FFFFFF;" title="White"></button>
                                <!-- Open Custom Picker button -->
                                <button type="button" id="openColorPickerBtn" class="no-theme w-9 h-9 rounded-full border-2 border-dashed border-slate-300 hover:border-slate-400 transition-all duration-150 cursor-pointer focus:outline-none flex items-center justify-center bg-white dark:bg-slate-800" title="Custom Color">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Active Color Display -->
                        <div class="flex items-center gap-3 mb-1">
                            <div id="activeColorSwatch" class="w-8 h-8 rounded-lg border border-slate-200 shadow-sm" style="background-color: var(--primary-color);"></div>
                            <div>
                                <p class="text-xs font-semibold text-slate-700 dark:text-slate-300">Active Color</p>
                                <p id="activeColorHex" class="text-[11px] text-slate-500 font-mono">{{ $currentThemeColor ?? '#6EC1D1' }}</p>
                            </div>
                        </div>

                        <!-- Custom Color Picker Popover -->
                        <div id="colorPickerPopover" class="hidden fixed inset-0 z-[9999]" style="background: rgba(0,0,0,0.25);">
                            <div class="absolute" id="colorPickerCard" style="top: 50%; left: 50%; transform: translate(-50%, -50%); width: 320px;">
                                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl p-5 space-y-4">
                                    <!-- Header -->
                                    <div class="-mx-5 -mt-5 flex items-center justify-between rounded-t-2xl px-5 py-4" style="background-color: var(--primary-color); color: var(--primary-text);">
                                        <h4 class="text-sm font-bold" style="color: var(--primary-text);">Custom Color</h4>
                                        <button type="button" id="closeColorPickerBtn" onclick="closeColorPicker()" class="no-theme p-1.5 rounded-lg transition cursor-pointer">
                                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>

                                    <!-- Saturation/Value Canvas -->
                                    <div class="relative w-full rounded-xl overflow-hidden cursor-crosshair" id="svCanvas" style="height: 180px; background: linear-gradient(to right, #fff, hsl(190, 100%, 50%));">
                                        <div style="position:absolute;inset:0;background:linear-gradient(to top,#000,transparent);"></div>
                                        <div id="svCursor" style="position:absolute;width:14px;height:14px;border:2px solid #fff;border-radius:50%;box-shadow:0 0 3px rgba(0,0,0,0.5);transform:translate(-50%,-50%);pointer-events:none;top:10%;left:90%;"></div>
                                    </div>

                                    <!-- Hue Slider -->
                                    <div class="relative w-full rounded-full cursor-pointer" id="hueSlider" style="height: 16px; background: linear-gradient(to right, #f00 0%, #ff0 17%, #0f0 33%, #0ff 50%, #00f 67%, #f0f 83%, #f00 100%);">
                                        <div id="hueCursor" style="position:absolute;width:8px;height:22px;background:#fff;border:2px solid #334155;border-radius:4px;top:50%;transform:translate(-50%,-50%);pointer-events:none;left:52%;box-shadow:0 1px 4px rgba(0,0,0,0.25);"></div>
                                    </div>

                                    <!-- Preview + Hex Input -->
                                    <div class="flex items-center gap-3">
                                        <div id="pickerPreviewSwatch" class="w-11 h-11 rounded-xl border border-slate-200 shadow-sm flex-shrink-0" style="background-color: #6EC1D1;"></div>
                                        <div class="flex-1">
                                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 block">Hex</label>
                                            <div class="flex items-center gap-1">
                                                <span class="text-sm font-bold text-slate-400">#</span>
                                                <input type="text" id="pickerHexInput" value="6EC1D1" maxlength="6" class="no-theme w-full px-2 py-1.5 text-sm font-mono font-semibold text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-1 focus:ring-slate-400" />
                                            </div>
                                        </div>
                                        <!-- Native color input for eyedropper -->
                                        <div class="flex-shrink-0">
                                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 block text-center">Pick</label>
                                            <input type="color" id="nativeColorInput" value="#6EC1D1" class="no-theme w-9 h-9 rounded-xl border border-slate-200 cursor-pointer" style="padding:0; -webkit-appearance:none; appearance:none;" />
                                        </div>
                                    </div>

                                    <!-- Apply Button -->
                                    <button type="button" id="applyPickerColorBtn" class="no-theme w-full py-2.5 rounded-[10px] text-sm font-bold transition cursor-pointer shadow-sm" style="background-color: var(--primary-color); color: var(--primary-text);" onclick="applyPickerColor()">
                                        Apply Color
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Full-screen Loading Overlay for Logo Upload / Reset -->
    <div id="logoLoadingOverlay" class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/70 backdrop-blur-sm hidden transition-all duration-300">
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 sm:p-8 max-w-sm w-full mx-4 shadow-2xl border border-slate-200 dark:border-slate-800 text-center flex flex-col items-center">
            <!-- Spinner -->
            <div class="relative w-16 h-16 mb-4">
                <div class="w-16 h-16 rounded-full border-4 border-cyan-500/20 border-t-cyan-500 animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-7 h-7 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <h4 id="loadingOverlayTitle" class="text-base font-bold text-slate-900 dark:text-white mb-1">Updating System Logo</h4>
            <p id="loadingOverlayText" class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                Please wait while the new logo is being uploaded and applied...
            </p>
        </div>
    </div>

    <script>
        // Tab switching
        document.querySelectorAll('.settings-tab').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.settings-tab').forEach(b => {
                    b.classList.remove('bg-[#0f172a]', 'text-white', 'border-[#0f172a]', 'shadow-sm');
                    b.classList.add('bg-white', 'text-slate-700', 'border-slate-200', 'hover:bg-slate-50');
                });
                btn.classList.add('bg-[#0f172a]', 'text-white', 'border-[#0f172a]', 'shadow-sm');
                btn.classList.remove('bg-white', 'text-slate-700', 'border-slate-200', 'hover:bg-slate-50');

                const tab = btn.dataset.tab;
                document.querySelectorAll('.settings-pane').forEach(p => p.classList.add('hidden'));
                const targetPane = document.getElementById('tab' + tab.charAt(0).toUpperCase() + tab.slice(1));
                if (targetPane) targetPane.classList.remove('hidden');
            });
        });

        // Theme option selection
        function syncThemeCards() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            ['light', 'dark', 'system'].forEach(t => {
                const card = document.getElementById('themeBtn' + t.charAt(0).toUpperCase() + t.slice(1));
                if (!card) return;
                if (savedTheme === t) {
                    card.classList.add('border-[#59b2c2]', 'bg-[#59b2c2]/10');
                    card.classList.remove('border-slate-200', 'bg-slate-50', 'border-cyan-500', 'bg-cyan-500/10', 'ring-2', 'ring-cyan-500/30', 'ring-[#59b2c2]');
                } else {
                    card.classList.remove('border-[#59b2c2]', 'bg-[#59b2c2]/10', 'border-cyan-500', 'bg-cyan-500/10', 'ring-2', 'ring-cyan-500/30', 'ring-[#59b2c2]');
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

        /* ═══ COLOR THEME CONTROLLER & HSV PICKER ═══ */
        (function() {
            function getCurrentThemeColor() {
                let col = null;
                try { col = localStorage.getItem('theme_color'); } catch(e) {}
                if (!col) {
                    const style = getComputedStyle(document.documentElement);
                    col = style.getPropertyValue('--primary-color').trim();
                }
                if (!col) col = '{{ $currentThemeColor ?? "#6EC1D1" }}';
                return (col || '#6EC1D1').toUpperCase();
            }

            window.syncThemeSwatches = function() {
                const active = getCurrentThemeColor();
                const swatches = document.querySelectorAll('.theme-swatch');
                swatches.forEach(btn => {
                    const c = (btn.dataset.color || '').toUpperCase();
                    if (c === active) {
                        btn.classList.add('ring-1', 'ring-offset-2', 'ring-slate-900', 'dark:ring-white', 'scale-110');
                        btn.classList.remove('border-transparent');
                    } else {
                        btn.classList.remove('ring-1', 'ring-offset-2', 'ring-slate-900', 'dark:ring-white', 'scale-110');
                        if (c !== '#FFFFFF') {
                            btn.classList.add('border-transparent');
                        }
                    }
                });

                const swatch = document.getElementById('activeColorSwatch');
                const hexLabel = document.getElementById('activeColorHex');
                if (swatch) swatch.style.backgroundColor = active;
                if (hexLabel) hexLabel.textContent = active;
            };

            // Color conversion helpers
            function hexToRgb(hex) {
                hex = hex.replace(/^#/, '');
                if (hex.length === 3) hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
                const n = parseInt(hex, 16);
                return { r: (n >> 16) & 255, g: (n >> 8) & 255, b: n & 255 };
            }

            function rgbToHex(r, g, b) {
                const h = [r, g, b].map(v => {
                    const s = Math.max(0, Math.min(255, Math.round(v))).toString(16);
                    return s.length < 2 ? '0' + s : s;
                }).join('');
                return '#' + h.toUpperCase();
            }

            function rgbToHsv(r, g, b) {
                r /= 255; g /= 255; b /= 255;
                const max = Math.max(r, g, b), min = Math.min(r, g, b);
                const d = max - min;
                let h = 0;
                const s = max === 0 ? 0 : d / max;
                const v = max;

                if (max !== min) {
                    switch (max) {
                        case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                        case g: h = (b - r) / d + 2; break;
                        case b: h = (r - g) / d + 4; break;
                    }
                    h /= 6;
                }
                return { h: h * 360, s: s, v: v };
            }

            function hsvToRgb(h, s, v) {
                h = (h % 360 + 360) % 360;
                const c = v * s;
                const x = c * (1 - Math.abs(((h / 60) % 2) - 1));
                const m = v - c;
                let r = 0, g = 0, b = 0;

                if (h < 60) { r = c; g = x; b = 0; }
                else if (h < 120) { r = x; g = c; b = 0; }
                else if (h < 180) { r = 0; g = c; b = x; }
                else if (h < 240) { r = 0; g = x; b = c; }
                else if (h < 300) { r = x; g = 0; b = c; }
                else { r = c; g = 0; b = x; }

                return {
                    r: Math.round((r + m) * 255),
                    g: Math.round((g + m) * 255),
                    b: Math.round((b + m) * 255)
                };
            }

            let curH = 190, curS = 0.5, curV = 0.8;
            let isDraggingSV = false;
            let isDraggingHue = false;

            function updateFromHsv(triggerHexInput = true) {
                const rgb = hsvToRgb(curH, curS, curV);
                const hex = rgbToHex(rgb.r, rgb.g, rgb.b);

                const svCanvas = document.getElementById('svCanvas');
                if (svCanvas) {
                    svCanvas.style.background = `linear-gradient(to right, #fff, hsl(${Math.round(curH)}, 100%, 50%))`;
                }

                const svCursor = document.getElementById('svCursor');
                if (svCursor) {
                    svCursor.style.left = (curS * 100) + '%';
                    svCursor.style.top = ((1 - curV) * 100) + '%';
                }

                const hueCursor = document.getElementById('hueCursor');
                if (hueCursor) {
                    hueCursor.style.left = ((curH / 360) * 100) + '%';
                }

                const previewSwatch = document.getElementById('pickerPreviewSwatch');
                if (previewSwatch) previewSwatch.style.backgroundColor = hex;

                const hexInput = document.getElementById('pickerHexInput');
                if (hexInput && triggerHexInput) {
                    hexInput.value = hex.replace('#', '');
                }

                const nativeInput = document.getElementById('nativeColorInput');
                if (nativeInput) {
                    nativeInput.value = hex;
                }
            }

            function setFromHex(hex) {
                hex = (hex || '').replace(/^#/, '').trim();
                if (hex.length === 3) {
                    hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
                }
                if (!/^[0-9A-Fa-f]{6}$/.test(hex)) return;

                const rgb = hexToRgb(hex);
                const hsv = rgbToHsv(rgb.r, rgb.g, rgb.b);
                curH = hsv.h;
                curS = hsv.s;
                curV = hsv.v;
                updateFromHsv(false);
            }

            window.openColorPicker = function() {
                const popover = document.getElementById('colorPickerPopover');
                if (!popover) return;
                popover.classList.remove('hidden');

                const initial = getCurrentThemeColor();
                setFromHex(initial);
                const hexInput = document.getElementById('pickerHexInput');
                if (hexInput) hexInput.value = initial.replace('#', '');
            };

            window.closeColorPicker = function() {
                const popover = document.getElementById('colorPickerPopover');
                if (popover) popover.classList.add('hidden');
            };

            window.applyPickerColor = function() {
                const hexInput = document.getElementById('pickerHexInput');
                let hex = (hexInput ? hexInput.value : '').trim().replace(/^#/, '');
                if (hex.length === 3) hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
                if (!/^[0-9A-Fa-f]{6}$/.test(hex)) {
                    alert('Please enter a valid 6-character hexadecimal color.');
                    return;
                }
                hex = '#' + hex.toUpperCase();
                window.applyThemeColor(hex, true);
                window.syncThemeSwatches();
                window.closeColorPicker();
                if (typeof showAlert === 'function') {
                    showAlert('success', 'Color theme updated to ' + hex + ' across all modules.');
                }
            };

            // Event Listeners for Pickers
            document.addEventListener('DOMContentLoaded', function() {
                window.syncThemeSwatches();

                const openBtn = document.getElementById('openColorPickerBtn');
                if (openBtn) {
                    openBtn.addEventListener('click', window.openColorPicker);
                }

                const popover = document.getElementById('colorPickerPopover');
                if (popover) {
                    popover.addEventListener('click', function(e) {
                        if (e.target === popover) window.closeColorPicker();
                    });
                }

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && popover && !popover.classList.contains('hidden')) {
                        window.closeColorPicker();
                    }
                });

                // Saturation/Value drag handling
                const svCanvas = document.getElementById('svCanvas');
                function handleSvMove(e) {
                    if (!svCanvas) return;
                    const rect = svCanvas.getBoundingClientRect();
                    const clientX = e.clientX ?? (e.touches ? e.touches[0].clientX : 0);
                    const clientY = e.clientY ?? (e.touches ? e.touches[0].clientY : 0);
                    const x = Math.max(0, Math.min(rect.width, clientX - rect.left));
                    const y = Math.max(0, Math.min(rect.height, clientY - rect.top));
                    curS = x / rect.width;
                    curV = 1 - (y / rect.height);
                    updateFromHsv(true);
                }

                if (svCanvas) {
                    svCanvas.addEventListener('mousedown', function(e) {
                        isDraggingSV = true;
                        handleSvMove(e);
                    });
                    svCanvas.addEventListener('touchstart', function(e) {
                        isDraggingSV = true;
                        handleSvMove(e);
                    }, { passive: true });
                }

                // Hue slider drag handling
                const hueSlider = document.getElementById('hueSlider');
                function handleHueMove(e) {
                    if (!hueSlider) return;
                    const rect = hueSlider.getBoundingClientRect();
                    const clientX = e.clientX ?? (e.touches ? e.touches[0].clientX : 0);
                    const x = Math.max(0, Math.min(rect.width, clientX - rect.left));
                    curH = (x / rect.width) * 360;
                    if (curH >= 360) curH = 359.9;
                    updateFromHsv(true);
                }

                if (hueSlider) {
                    hueSlider.addEventListener('mousedown', function(e) {
                        isDraggingHue = true;
                        handleHueMove(e);
                    });
                    hueSlider.addEventListener('touchstart', function(e) {
                        isDraggingHue = true;
                        handleHueMove(e);
                    }, { passive: true });
                }

                window.addEventListener('mousemove', function(e) {
                    if (isDraggingSV) handleSvMove(e);
                    if (isDraggingHue) handleHueMove(e);
                });

                window.addEventListener('touchmove', function(e) {
                    if (isDraggingSV) handleSvMove(e);
                    if (isDraggingHue) handleHueMove(e);
                }, { passive: true });

                window.addEventListener('mouseup', function() {
                    isDraggingSV = false;
                    isDraggingHue = false;
                });

                window.addEventListener('touchend', function() {
                    isDraggingSV = false;
                    isDraggingHue = false;
                });

                // Hex input typing
                const hexInput = document.getElementById('pickerHexInput');
                if (hexInput) {
                    hexInput.addEventListener('input', function() {
                        const val = hexInput.value.trim().replace(/^#/, '');
                        if (val.length === 3 || val.length === 6) {
                            setFromHex(val);
                        }
                    });
                }

                // Native input picking
                const nativeInput = document.getElementById('nativeColorInput');
                if (nativeInput) {
                    nativeInput.addEventListener('input', function() {
                        setFromHex(nativeInput.value);
                    });
                }
            });

            window.addEventListener('themeColorChanged', function() {
                window.syncThemeSwatches();
            });
        })();

        // Logo upload and management scripts
        (function() {
            const fileInput = document.getElementById('logoFileInput');
            const dropzone = document.getElementById('dropzoneArea');
            const dropzoneEmptyState = document.getElementById('dropzoneEmptyState');
            const dropzonePreviewState = document.getElementById('dropzonePreviewState');
            const previewImg = document.getElementById('settingsLogoPreview');
            const form = document.getElementById('logoUploadForm');
            const fileInfoBox = document.getElementById('selectedFileInfo');
            const fileNameText = document.getElementById('selectedFileName');
            const fileSizeText = document.getElementById('selectedFileSize');
            const submitBtn = document.getElementById('uploadLogoSubmitBtn');
            const btnSpinner = document.getElementById('uploadBtnSpinner');
            const btnText = document.getElementById('uploadBtnText');
            const resetBtn = document.getElementById('resetLogoBtn');
            const badge = document.getElementById('logoStatusBadge');
            const alertBox = document.getElementById('logoAlertContainer');

            // Loading Overlay elements
            const loadingOverlay = document.getElementById('logoLoadingOverlay');
            const overlayTitle = document.getElementById('loadingOverlayTitle');
            const overlayText = document.getElementById('loadingOverlayText');

            let initialPreviewSrc = '';
            let selectedFile = null;
            let selectedFileBase64 = null;

            function showLoadingOverlay(title, text) {
                if (overlayTitle && title) overlayTitle.textContent = title;
                if (overlayText && text) overlayText.textContent = text;
                if (loadingOverlay) loadingOverlay.classList.remove('hidden');
            }

            function hideLoadingOverlay() {
                if (loadingOverlay) loadingOverlay.classList.add('hidden');
            }

            function getCsrfToken() {
                const meta = document.querySelector('meta[name="csrf-token"]');
                if (meta && meta.content) return meta.content;
                const input = document.querySelector('input[name="_token"]');
                return input ? input.value : '';
            }

            function formatBytes(bytes) {
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
                return (bytes / 1048576).toFixed(2) + ' MB';
            }

            function showAlert(type, message) {
                if (!alertBox) return;
                alertBox.className = 'mb-4 p-3.5 rounded-xl text-xs font-medium flex items-center gap-2.5 shadow-xs ' +
                    (type === 'success'
                        ? 'bg-emerald-50 border border-emerald-200 text-emerald-800 dark:bg-emerald-950/40 dark:border-emerald-900 dark:text-emerald-300'
                        : 'bg-rose-50 border border-rose-200 text-rose-800 dark:bg-rose-950/40 dark:border-rose-900 dark:text-rose-300');
                alertBox.innerHTML = `
                    <svg class="w-4 h-4 flex-shrink-0 ${type === 'success' ? 'text-emerald-600' : 'text-rose-600'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${type === 'success' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />' : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />'}
                    </svg>
                    <span>${message}</span>
                `;
                alertBox.classList.remove('hidden');
                setTimeout(() => {
                    alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 50);
            }

            // Function to update all sidebar and navbar logos immediately on screen
            function updateAllSidebarLogos(url) {
                if (!url) return;
                const bustUrl = url.startsWith('data:') ? url : (url.includes('?') ? (url + '&t=' + Date.now()) : (url + '?t=' + Date.now()));

                const sidebarLogo = document.getElementById('sidebarAppLogo');
                if (sidebarLogo) {
                    sidebarLogo.src = bustUrl;
                }

                if (previewImg) {
                    previewImg.src = bustUrl;
                }

                document.querySelectorAll('#sidebarAppLogo, #settingsLogoPreview, img[id*="sidebarAppLogo"], img[id*="cashierNavbarAppLogo"], a[href*="dashboard"] img').forEach(img => {
                    img.src = bustUrl;
                });
            }

            // Read file as base64 DataURL
            function readFileAsDataURL(file) {
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onload = () => resolve(reader.result);
                    reader.onerror = () => reject(reader.error);
                    reader.readAsDataURL(file);
                });
            }

            // Handle file selection (PREVIEW ONLY: Appears right inside dropzone container)
            async function handleFileSelect(file) {
                if (!file) return;

                // Validate image file type
                const validExtensions = ['png', 'jpg', 'jpeg', 'webp', 'svg', 'gif', 'bmp', 'ico', 'avif'];
                const fileExt = (file.name || '').split('.').pop().toLowerCase();
                const isImageMime = file.type ? file.type.startsWith('image/') : false;
                if (!isImageMime && !validExtensions.includes(fileExt)) {
                    showAlert('error', 'Please select a valid image file (PNG, JPG, JPEG, WEBP, SVG, GIF, etc.).');
                    cancelSelectedLogo();
                    return;
                }

                // Validate size (max 15MB)
                if (file.size > 15 * 1024 * 1024) {
                    showAlert('error', 'The selected file is too large. Please select an image under 15 MB.');
                    cancelSelectedLogo();
                    return;
                }

                selectedFile = file;

                // Clear any old alerts
                if (alertBox) alertBox.classList.add('hidden');

                if (fileNameText) fileNameText.textContent = file.name;
                if (fileSizeText) fileSizeText.textContent = formatBytes(file.size);
                if (fileInfoBox) fileInfoBox.classList.remove('hidden');

                // Read and display preview directly inside the dropzone container!
                try {
                    const dataUrl = await readFileAsDataURL(file);
                    selectedFileBase64 = dataUrl;
                    if (previewImg) previewImg.src = dataUrl;
                    if (dropzoneEmptyState) dropzoneEmptyState.classList.add('hidden');
                    if (dropzonePreviewState) dropzonePreviewState.classList.remove('hidden');
                } catch (readErr) {
                    console.warn('Could not preview file locally:', readErr);
                }

                if (btnText) btnText.textContent = 'Upload Selected Logo';
            }

            // Cancel selected file and restore empty dropzone
            window.cancelSelectedLogo = function() {
                selectedFile = null;
                selectedFileBase64 = null;
                if (fileInput) fileInput.value = '';
                if (fileInfoBox) fileInfoBox.classList.add('hidden');
                if (dropzonePreviewState) dropzonePreviewState.classList.add('hidden');
                if (dropzoneEmptyState) dropzoneEmptyState.classList.remove('hidden');
                if (previewImg) previewImg.src = '';
                if (btnText) btnText.textContent = 'Upload Logo';
            };

            // Perform AJAX upload to /settings/logo and AUTOMATICALLY update sidebar logo!
            async function performUpload(fileToUpload) {
                if (!fileToUpload) {
                    if (fileInput) fileInput.click();
                    return;
                }

                if (submitBtn) submitBtn.disabled = true;
                if (btnSpinner) btnSpinner.classList.remove('hidden');
                if (btnText) btnText.textContent = 'Uploading logo...';
                showLoadingOverlay('Updating System Logo', 'Uploading and applying your new logo...');

                try {
                    let base64DataUrl = selectedFileBase64;
                    if (!base64DataUrl) {
                        try {
                            base64DataUrl = await readFileAsDataURL(fileToUpload);
                        } catch (readErr) {
                            console.warn('Could not read file as DataURL:', readErr);
                        }
                    }

                    // INSTANTLY UPDATE SIDEBAR LOGO ON SCREEN AS SOON AS USER CLICKS UPLOAD!
                    if (base64DataUrl) {
                        updateAllSidebarLogos(base64DataUrl);
                        if (previewImg) previewImg.src = base64DataUrl;
                    }

                    // Send FormData containing file and base64 fallback
                    const formData = new FormData();
                    formData.append('logo', fileToUpload);
                    if (base64DataUrl) {
                        formData.append('logo_base64', base64DataUrl);
                    }
                    formData.append('_token', getCsrfToken());

                    const response = await fetch('/settings/logo', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    let data = null;
                    const text = await response.text();
                    try {
                        data = JSON.parse(text);
                    } catch (parseErr) {
                        const jsonMatch = text.match(/\{[\s\S]*\}/);
                        if (jsonMatch) {
                            try {
                                data = JSON.parse(jsonMatch[0]);
                            } catch (e) {}
                        }
                    }

                    if (!data) {
                        data = { success: false, message: 'Server returned unexpected response.' };
                    }

                    if (response.ok && data.success) {
                        const newLogoUrl = data.logo_url || ('/images/custom_logo.png?v=' + Date.now());
                        initialPreviewSrc = newLogoUrl;

                        // Ensure sidebar and settings logos have the final server URL with cache buster
                        updateAllSidebarLogos(newLogoUrl);
                        if (previewImg) previewImg.src = newLogoUrl;
                        if (dropzonePreviewState) dropzonePreviewState.classList.remove('hidden');
                        if (dropzoneEmptyState) dropzoneEmptyState.classList.add('hidden');

                        if (badge) {
                            badge.textContent = 'Custom Logo Active';
                            badge.className = 'text-xs font-semibold px-2.5 py-0.5 rounded-full bg-cyan-50 text-cyan-700 border border-cyan-200 dark:bg-cyan-950 dark:text-cyan-300 dark:border-cyan-800';
                        }

                        if (resetBtn) resetBtn.classList.remove('hidden');
                        if (fileInfoBox) fileInfoBox.classList.add('hidden');
                        if (fileInput) fileInput.value = '';
                        selectedFile = null;
                        selectedFileBase64 = null;

                        // Automatic update successful alert!
                        showAlert('success', data.message || 'System logo updated successfully! The sidebar logo has been updated automatically.');
                    } else {
                        // Revert on error
                        if (selectedFileBase64) {
                            if (previewImg) previewImg.src = selectedFileBase64;
                        } else {
                            if (dropzonePreviewState) dropzonePreviewState.classList.add('hidden');
                            if (dropzoneEmptyState) dropzoneEmptyState.classList.remove('hidden');
                            if (previewImg) previewImg.src = '';
                        }
                        showAlert('error', (data && data.message) ? data.message : 'Failed to update logo.');
                    }
                } catch (err) {
                    console.error('Upload error:', err);
                    if (selectedFileBase64) {
                        if (previewImg) previewImg.src = selectedFileBase64;
                    } else {
                        if (dropzonePreviewState) dropzonePreviewState.classList.add('hidden');
                        if (dropzoneEmptyState) dropzoneEmptyState.classList.remove('hidden');
                        if (previewImg) previewImg.src = '';
                    }
                    showAlert('error', 'Network error while uploading logo. Please try again.');
                } finally {
                    hideLoadingOverlay();
                    if (submitBtn) {
                        submitBtn.disabled = false;
                    }
                    if (btnSpinner) btnSpinner.classList.add('hidden');
                    if (btnText) btnText.textContent = 'Upload Logo';
                }
            }

            // File input change handler
            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (fileInput.files && fileInput.files[0]) {
                        handleFileSelect(fileInput.files[0]);
                    }
                });
            }

            // Drag and drop handlers
            if (dropzone) {
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropzone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropzone.classList.add('border-cyan-500', 'bg-cyan-50/30');
                    });
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropzone.classList.remove('border-cyan-500', 'bg-cyan-50/30');
                    });
                });

                dropzone.addEventListener('drop', (e) => {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    if (files && files.length) {
                        try {
                            fileInput.files = files;
                        } catch(ex) {}
                        handleFileSelect(files[0]);
                    }
                });
            }

            // Form Submit Button Click
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const fileToUpload = selectedFile || (fileInput && fileInput.files ? fileInput.files[0] : null);
                    if (!fileToUpload) {
                        if (fileInput) fileInput.click();
                        return;
                    }

                    performUpload(fileToUpload);
                });
            }

            // Reset Logo
            window.resetToDefaultLogo = async function() {
                if (!confirm('Are you sure you want to reset back to the default KCC logo?')) {
                    return;
                }

                if (resetBtn) resetBtn.disabled = true;
                showLoadingOverlay('Resetting Logo', 'Restoring the default KCC logo...');

                try {
                    const response = await fetch('/settings/logo/reset', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ _token: getCsrfToken() })
                    });

                    let data;
                    const text = await response.text();
                    try {
                        data = JSON.parse(text);
                    } catch(e) {
                        const jsonMatch = text.match(/\{[\s\S]*\}/);
                        if (jsonMatch) {
                            try { data = JSON.parse(jsonMatch[0]); } catch (err2) {}
                        }
                    }

                    if (!data) {
                        data = { success: false, message: 'Server error while resetting logo.' };
                    }

                    if (response.ok && data.success) {
                        const defaultLogoUrl = data.logo_url || '/images/Logo.png';
                        initialPreviewSrc = '';

                        updateAllSidebarLogos(defaultLogoUrl);

                        if (badge) {
                            badge.textContent = 'Default Logo';
                            badge.className = 'text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700';
                        }

                        if (dropzonePreviewState) dropzonePreviewState.classList.add('hidden');
                        if (dropzoneEmptyState) dropzoneEmptyState.classList.remove('hidden');
                        if (previewImg) previewImg.src = '';

                        if (resetBtn) resetBtn.classList.add('hidden');
                        if (fileInfoBox) fileInfoBox.classList.add('hidden');
                        if (fileInput) fileInput.value = '';
                        selectedFile = null;
                        selectedFileBase64 = null;

                        showAlert('success', data.message || 'Logo reset successfully! The sidebar logo has been restored.');
                    } else {
                        showAlert('error', data.message || 'Failed to reset logo.');
                    }
                } catch (err) {
                    console.error('Reset error:', err);
                    showAlert('error', 'Error resetting logo.');
                } finally {
                    hideLoadingOverlay();
                    if (resetBtn) resetBtn.disabled = false;
                }
            };
        })();
    </script>
</x-layouts.app>
