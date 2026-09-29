<x-layouts.app :title="__('POS Mobile Scanner')">
    <div class="min-h-screen bg-[#0f172a] text-slate-100 flex flex-col font-sans selection:bg-[#6EC1D1]/30">
        <!-- Header -->
        <header class="bg-[#1e293b]/90 backdrop-blur-md px-4 py-3 border-b border-slate-700/80 sticky top-0 z-30 flex items-center justify-between shadow-md">
            <div class="flex items-center gap-2.5">
                <div class="bg-[#6EC1D1]/20 p-2 rounded-xl text-[#6EC1D1]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-sm font-bold text-white leading-tight">POS Mobile Scanner</h1>
                    <p class="text-[11px] text-slate-400">Scan QR codes to add items to cart</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="window.location.href='{{ route('pos.terminal') }}'" class="rounded-xl bg-[#6EC1D1] px-3.5 py-1.5 text-xs font-bold text-slate-950 hover:bg-[#59b2c2] active:scale-95 transition shadow-sm flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Terminal</span>
                </button>
            </div>
        </header>

        <!-- Scanner Viewport Area -->
        <main class="flex-1 flex flex-col items-center justify-start p-4 max-w-lg w-full mx-auto space-y-4">
            <!-- Toast notification container -->
            <div id="toast-container" class="fixed top-16 left-4 right-4 z-50 flex flex-col items-center pointer-events-none space-y-2"></div>

            <!-- Video Scanner Frame -->
            <div class="relative w-full aspect-square max-w-[340px] rounded-2xl overflow-hidden bg-black border-2 border-slate-700 shadow-2xl flex items-center justify-center">
                <div id="reader" class="w-full h-full"></div>
                
                <!-- Target Overlay Corners -->
                <div class="absolute inset-0 pointer-events-none p-6 flex flex-col justify-between">
                    <div class="flex justify-between">
                        <div class="w-8 h-8 border-t-4 border-l-4 border-[#6EC1D1] rounded-tl-lg"></div>
                        <div class="w-8 h-8 border-t-4 border-r-4 border-[#6EC1D1] rounded-tr-lg"></div>
                    </div>
                    <div class="flex justify-between">
                        <div class="w-8 h-8 border-b-4 border-l-4 border-[#6EC1D1] rounded-bl-lg"></div>
                        <div class="w-8 h-8 border-b-4 border-r-4 border-[#6EC1D1] rounded-br-lg"></div>
                    </div>
                </div>
            </div>

            <!-- Status Banner -->
            <div id="scanner-status" class="w-full max-w-[340px] rounded-xl bg-slate-800/90 border border-slate-700 px-4 py-2.5 text-center text-xs font-semibold text-slate-300 transition-all duration-300 shadow-sm">
                Position QR code within camera frame
            </div>

            <!-- Scanned Items History Section -->
            <div class="w-full bg-[#1e293b] rounded-2xl p-4 border border-slate-700/80 shadow-lg space-y-3">
                <div class="flex items-center justify-between border-b border-slate-700 pb-2.5">
                    <div class="flex items-center gap-2">
                        <h2 class="text-xs font-bold text-white uppercase tracking-wider">Recent Scans</h2>
                        <span id="scan-count-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#6EC1D1]/20 text-[#6EC1D1]">0</span>
                    </div>
                    <button onclick="clearScannedHistory()" class="text-[11px] font-semibold text-slate-400 hover:text-red-400 active:scale-95 transition cursor-pointer">
                        Clear All
                    </button>
                </div>

                <div id="recent-scans" class="space-y-2 max-h-56 overflow-y-auto pr-1">
                    <div class="text-center py-6 text-xs text-slate-500">
                        No items scanned yet. Point camera at product QR code.
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer / Connection Status -->
        <footer class="bg-[#1e293b]/90 border-t border-slate-800 px-4 py-2.5 text-xs text-slate-400 flex items-center justify-between mt-auto">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" id="connection-dot"></span>
                <span id="connection-status" class="font-medium text-emerald-400">Online & Connected</span>
            </div>
            <div class="text-[10px] text-slate-500">Auto-syncs to POS Cart</div>
        </footer>
    </div>

    <!-- html5-qrcode library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    
    <script>
        let html5QrcodeScanner = null;
        let scannedItems = [];
        let audioContext = null;
        let lastScannedRaw = '';
        let lastScanTimestamp = 0;

        // Unlock audio context on any user interaction (essential for mobile Safari/Chrome)
        function initAudioContext() {
            try {
                if (!audioContext) {
                    audioContext = new (window.AudioContext || window.webkitAudioContext)();
                }
                if (audioContext && audioContext.state === 'suspended') {
                    audioContext.resume();
                }
            } catch (e) {
                console.warn('AudioContext init error:', e);
            }
        }

        document.addEventListener('touchstart', initAudioContext, { passive: true });
        document.addEventListener('click', initAudioContext, { passive: true });

        document.addEventListener('DOMContentLoaded', function() {
            initAudioContext();
            loadScannedHistory();
            initScanner();
            checkConnection();
        });

        function playSuccessBeep() {
            try {
                initAudioContext();
                if (audioContext) {
                    const osc = audioContext.createOscillator();
                    const gain = audioContext.createGain();
                    osc.connect(gain);
                    gain.connect(audioContext.destination);

                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(880, audioContext.currentTime); // A5
                    osc.frequency.exponentialRampToValueAtTime(1320, audioContext.currentTime + 0.12); // E6

                    gain.gain.setValueAtTime(0.2, audioContext.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.15);

                    osc.start(audioContext.currentTime);
                    osc.stop(audioContext.currentTime + 0.15);
                }
            } catch (e) {
                console.warn('Beep error:', e);
            }

            if (navigator.vibrate) {
                navigator.vibrate(70);
            }
        }

        function playWarningBeep() {
            try {
                initAudioContext();
                if (audioContext) {
                    const osc = audioContext.createOscillator();
                    const gain = audioContext.createGain();
                    osc.connect(gain);
                    gain.connect(audioContext.destination);

                    osc.type = 'square';
                    osc.frequency.setValueAtTime(440, audioContext.currentTime); // A4
                    osc.frequency.setValueAtTime(330, audioContext.currentTime + 0.08); // E4

                    gain.gain.setValueAtTime(0.15, audioContext.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);

                    osc.start(audioContext.currentTime);
                    osc.stop(audioContext.currentTime + 0.2);
                }
            } catch (e) {
                console.warn('Warning beep error:', e);
            }

            if (navigator.vibrate) {
                navigator.vibrate([100, 60, 100]);
            }
        }

        function initScanner() {
            html5QrcodeScanner = new Html5Qrcode("reader", {
                experimentalFeatures: {
                    useBarCodeDetectorIfSupported: true
                },
                verbose: false
            });
            
            const config = { 
                fps: 25,
                videoConstraints: {
                    facingMode: "environment",
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            };

            html5QrcodeScanner.start(
                { facingMode: "environment" },
                config,
                onScanSuccess,
                onScanFailure
            ).catch(err => {
                console.warn("Back camera failed, trying user camera:", err);
                html5QrcodeScanner.start(
                    { facingMode: "user" },
                    config,
                    onScanSuccess,
                    onScanFailure
                ).catch(userErr => {
                    console.error("Scanner error:", userErr);
                    setBanner('Camera access denied. Please allow camera permissions.', 'error');
                });
            });
        }

        function parseCodeDetails(rawCode) {
            const raw = String(rawCode || '').trim();
            try {
                const parsed = JSON.parse(raw);
                if (parsed && typeof parsed === 'object') {
                    return {
                        name: parsed.product_name || parsed.name || parsed.sku || 'Product Item',
                        sku: parsed.sku || '',
                        id: parsed.product_id || parsed.id || null,
                        price: parsed.price || parsed.unit_price || null,
                        raw: raw
                    };
                }
            } catch (e) {}

            return {
                name: raw,
                sku: raw,
                id: null,
                price: null,
                raw: raw
            };
        }

        function onScanSuccess(decodedText, decodedResult) {
            const now = Date.now();
            // Debounce rapid frame repeats within 1.2s
            if (decodedText === lastScannedRaw && (now - lastScanTimestamp) < 1200) {
                return;
            }
            lastScanTimestamp = now;
            lastScannedRaw = decodedText;

            const itemDetails = parseCodeDetails(decodedText);

            // Check if this item is already scanned in the recent list
            const existingIndex = scannedItems.findIndex(item => 
                (itemDetails.id && item.id && String(item.id) === String(itemDetails.id)) ||
                (itemDetails.sku && item.sku && item.sku.toLowerCase() === itemDetails.sku.toLowerCase()) ||
                (item.raw === itemDetails.raw)
            );

            if (existingIndex !== -1) {
                // Item ALREADY SCANNED!
                playWarningBeep();
                scannedItems[existingIndex].count = (scannedItems[existingIndex].count || 1) + 1;
                scannedItems[existingIndex].timestamp = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                
                // Move to top of list
                const duplicateItem = scannedItems.splice(existingIndex, 1)[0];
                scannedItems.unshift(duplicateItem);
                
                saveScannedHistory();
                renderScannedList();

                setBanner(`⚠️ This item is already scanned! (Count: ${duplicateItem.count})`, 'warning');
                showToast(`This item already scanned (${itemDetails.name})`, 'warning');

                // Sync to terminal (it will increment qty in POS Cart)
                sendToTerminal(decodedText);
                sendToServer(decodedText);
                return;
            }

            // NEW ITEM SCANNED!
            playSuccessBeep();
            scannedItems.unshift({
                name: itemDetails.name,
                sku: itemDetails.sku,
                id: itemDetails.id,
                price: itemDetails.price,
                raw: itemDetails.raw,
                count: 1,
                timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' })
            });

            if (scannedItems.length > 25) {
                scannedItems.pop();
            }

            saveScannedHistory();
            renderScannedList();

            setBanner(`✓ Scanned: ${itemDetails.name} ${itemDetails.sku ? `(${itemDetails.sku})` : ''}`, 'success');
            showToast(`✓ Added: ${itemDetails.name}`, 'success');

            // Send to terminal via localStorage (for same browser)
            sendToTerminal(decodedText);

            // Also send via API (for cross-device mobile-to-terminal sync)
            sendToServer(decodedText);
        }

        function onScanFailure(error) {
            // Normal scan noise — ignore
        }

        function setBanner(message, type = 'info') {
            const banner = document.getElementById('scanner-status');
            if (!banner) return;

            banner.textContent = message;
            if (type === 'success') {
                banner.className = 'w-full max-w-[340px] rounded-xl bg-emerald-950/80 border border-emerald-500 text-emerald-300 px-4 py-2.5 text-center text-xs font-bold transition-all duration-300 shadow-md';
            } else if (type === 'warning') {
                banner.className = 'w-full max-w-[340px] rounded-xl bg-amber-950/80 border border-amber-500 text-amber-300 px-4 py-2.5 text-center text-xs font-bold transition-all duration-300 shadow-md';
            } else if (type === 'error') {
                banner.className = 'w-full max-w-[340px] rounded-xl bg-red-950/80 border border-red-500 text-red-300 px-4 py-2.5 text-center text-xs font-bold transition-all duration-300 shadow-md';
            } else {
                banner.className = 'w-full max-w-[340px] rounded-xl bg-slate-800/90 border border-slate-700 text-slate-300 px-4 py-2.5 text-center text-xs font-semibold transition-all duration-300 shadow-sm';
            }

            setTimeout(() => {
                banner.textContent = 'Position QR code within camera frame';
                banner.className = 'w-full max-w-[340px] rounded-xl bg-slate-800/90 border border-slate-700 text-slate-300 px-4 py-2.5 text-center text-xs font-semibold transition-all duration-300 shadow-sm';
            }, 3500);
        }

        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            const bgClass = type === 'warning' ? 'bg-amber-500 text-slate-950 border-amber-400' : (type === 'error' ? 'bg-red-600 text-white border-red-400' : 'bg-[#6EC1D1] text-slate-950 border-[#88d6e4]');
            toast.className = `px-4 py-2 rounded-xl text-xs font-bold shadow-2xl border flex items-center gap-2 transform transition-all duration-300 translate-y-2 opacity-0 ${bgClass}`;
            toast.innerHTML = `
                <span>${message}</span>
            `;

            container.appendChild(toast);
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            });

            setTimeout(() => {
                toast.classList.add('opacity-0', '-translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 2500);
        }

        function renderScannedList() {
            const container = document.getElementById('recent-scans');
            const countBadge = document.getElementById('scan-count-badge');
            if (countBadge) countBadge.textContent = scannedItems.length;

            if (!container) return;

            if (scannedItems.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-6 text-xs text-slate-500">
                        No items scanned yet. Point camera at product QR code.
                    </div>
                `;
                return;
            }

            container.innerHTML = scannedItems.map(item => `
                <div class="flex items-center justify-between bg-slate-800/80 border border-slate-700/60 rounded-xl px-3.5 py-2.5 transition">
                    <div class="min-w-0 flex-1 pr-2">
                        <div class="flex items-center gap-2">
                            <h4 class="text-xs font-bold text-white truncate">${item.name || 'Product'}</h4>
                            ${item.count > 1 ? `<span class="px-1.5 py-0.2 rounded-md bg-amber-500/20 text-amber-300 text-[10px] font-bold border border-amber-500/30">x${item.count}</span>` : ''}
                        </div>
                        <div class="flex items-center gap-2 text-[11px] text-slate-400 mt-0.5">
                            ${item.sku ? `<span class="font-mono text-slate-300">${item.sku}</span> • ` : ''}
                            <span>${item.timestamp}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <span class="px-2 py-0.5 rounded-lg bg-emerald-500/15 text-emerald-400 text-[11px] font-bold border border-emerald-500/30 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            In Cart
                        </span>
                    </div>
                </div>
            `).join('');
        }

        function saveScannedHistory() {
            try {
                localStorage.setItem('pos_mobile_scanned_history', JSON.stringify(scannedItems));
            } catch (e) {}
        }

        function loadScannedHistory() {
            try {
                const stored = localStorage.getItem('pos_mobile_scanned_history');
                if (stored) {
                    scannedItems = JSON.parse(stored);
                    renderScannedList();
                }
            } catch (e) {}
        }

        function clearScannedHistory() {
            scannedItems = [];
            saveScannedHistory();
            renderScannedList();
            showToast('Scan history cleared', 'info');
        }

        function sendToTerminal(code) {
            const scanData = {
                code: code,
                timestamp: Date.now(),
                type: 'qr_scan'
            };
            localStorage.setItem('pos_scan_data', JSON.stringify(scanData));
        }

        async function sendToServer(code) {
            try {
                const response = await fetch('{{ route('pos.scan') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ code: code })
                });
                
                if (response.ok) {
                    console.log('Scan sent to server successfully');
                }
            } catch (error) {
                console.error('Error sending scan to server:', error);
            }
        }

        function checkConnection() {
            const dot = document.getElementById('connection-dot');
            const status = document.getElementById('connection-status');
            
            const updateStatus = () => {
                if (navigator.onLine) {
                    if (status) {
                        status.textContent = 'Online & Connected';
                        status.className = 'font-medium text-emerald-400';
                    }
                    if (dot) dot.className = 'w-2 h-2 rounded-full bg-emerald-400 animate-pulse';
                } else {
                    if (status) {
                        status.textContent = 'Offline';
                        status.className = 'font-medium text-red-400';
                    }
                    if (dot) dot.className = 'w-2 h-2 rounded-full bg-red-400';
                }
            };

            window.addEventListener('online', updateStatus);
            window.addEventListener('offline', updateStatus);
            setInterval(updateStatus, 5000);
            updateStatus();
        }

        window.addEventListener('beforeunload', function() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop().catch(err => console.error(err));
            }
        });
    </script>
</x-layouts.app>
