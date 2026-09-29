<x-layouts.app :title="__('Mobile Scanner')">
    <div class="min-h-screen bg-slate-900 flex items-center justify-center p-3 sm:p-6 font-sans selection:bg-[#6EC1D1]/30">
        <!-- Main Scanner Card matching POS theme -->
        <div class="w-full max-w-md bg-slate-800 rounded-3xl overflow-hidden shadow-2xl border border-slate-700/60 flex flex-col">
            <!-- Header -->
            <div class="bg-slate-800 px-5 py-4 flex items-center justify-between border-b border-slate-700/60">
                <div>
                    <h1 class="text-xl font-bold text-white tracking-tight">POS Scanner</h1>
                    <p class="text-xs text-slate-400 mt-0.5">Scan QR codes to add items to cart</p>
                </div>
                <button onclick="window.location.href='{{ route('pos.terminal') }}'" class="rounded-full bg-[#6EC1D1] px-4 py-1.5 text-xs font-bold text-slate-900 hover:bg-[#59b2c2] active:scale-95 transition shadow-sm cursor-pointer">
                    Close
                </button>
            </div>

            <!-- Scanner Area -->
            <div class="p-4 flex flex-col items-center justify-center">
                <div class="relative w-full aspect-square bg-black rounded-2xl overflow-hidden border border-slate-700 shadow-inner flex items-center justify-center">
                    <div id="reader" class="w-full h-full"></div>
                </div>
                <div id="scanner-status" class="mt-4 text-center text-sm font-medium text-slate-400 transition-all">
                    Position QR code within the frame
                </div>
            </div>

            <!-- Recent Scans -->
            <div class="bg-slate-800/90 px-5 py-3 border-t border-slate-700/60">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-sm font-bold text-white">Recent Scans</h2>
                    <button onclick="clearRecentScans()" class="text-xs text-slate-400 hover:text-red-400 transition cursor-pointer">Clear</button>
                </div>
                <div id="recent-scans" class="space-y-2 max-h-40 overflow-y-auto pr-1">
                    <div class="text-center py-4 text-xs text-slate-500">No items scanned yet</div>
                </div>
            </div>

            <!-- Connection Status Bar -->
            <div class="bg-slate-900 px-5 py-3 border-t border-slate-700/80 flex items-center justify-between text-xs">
                <span class="text-slate-400">Connection:</span>
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse" id="conn-dot"></span>
                    <span id="connection-status" class="text-green-400 font-medium">Connected</span>
                </div>
            </div>
        </div>
    </div>

    <!-- html5-qrcode library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    
    <script>
        let html5QrcodeScanner = null;
        let scannedItems = [];
        let isScanLocked = false;
        let lastScannedCode = '';
        let scanCooldownTimer = null;
        let audioCtx = null;

        // Mobile audio unlocking
        function getAudioContext() {
            try {
                if (!audioCtx) {
                    audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                }
                if (audioCtx && audioCtx.state === 'suspended') {
                    audioCtx.resume();
                }
            } catch (e) {
                console.warn('Audio unlock warning:', e);
            }
            return audioCtx;
        }

        document.addEventListener('touchstart', getAudioContext, { passive: true });
        document.addEventListener('click', getAudioContext, { passive: true });

        document.addEventListener('DOMContentLoaded', function() {
            getAudioContext();
            loadRecentScans();
            initScanner();
            checkConnection();
        });

        function playScanBeep() {
            try {
                const ctx = getAudioContext();
                if (ctx) {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(900, ctx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(1300, ctx.currentTime + 0.1);

                    gain.gain.setValueAtTime(0.2, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.12);

                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.12);
                }
            } catch (e) {
                console.warn(e);
            }

            if (navigator.vibrate) {
                navigator.vibrate(60);
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
                fps: 20,
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
                    const status = document.getElementById('scanner-status');
                    if (status) {
                        status.textContent = 'Camera access denied or unavailable.';
                        status.className = 'mt-4 text-center text-sm font-medium text-red-400';
                    }
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
            // Strict 1-QR-per-scan lock: ignore frames while locked or same code in quick succession
            if (isScanLocked) return;

            isScanLocked = true;
            lastScannedCode = decodedText;

            const item = parseCodeDetails(decodedText);

            // Play clean beep sound
            playScanBeep();

            // Update status text on screen
            const statusEl = document.getElementById('scanner-status');
            if (statusEl) {
                statusEl.textContent = `✓ Scanned: ${item.name} (${item.sku || 'Added'})`;
                statusEl.className = 'mt-4 text-center text-sm font-bold text-green-400 transition-all';
            }

            // Add to recent scans list
            addToRecentScans(item, decodedText);

            // Sync to POS terminal
            sendToTerminal(decodedText);
            sendToServer(decodedText);

            // Lock for 2.5 seconds to prevent unli-scan on the same item
            if (scanCooldownTimer) clearTimeout(scanCooldownTimer);
            scanCooldownTimer = setTimeout(() => {
                isScanLocked = false;
                if (statusEl) {
                    statusEl.textContent = 'Position QR code within the frame';
                    statusEl.className = 'mt-4 text-center text-sm font-medium text-slate-400 transition-all';
                }
            }, 2500);
        }

        function onScanFailure(error) {
            // Ignore normal frame noise
        }

        function addToRecentScans(item, rawCode) {
            const timestamp = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });

            // Check if already in list to increment count instead of duplicate raw JSON
            const existing = scannedItems.find(i => 
                (item.id && i.id && String(i.id) === String(item.id)) ||
                (item.sku && i.sku && i.sku.toLowerCase() === item.sku.toLowerCase()) ||
                (i.raw === rawCode)
            );

            if (existing) {
                existing.count = (existing.count || 1) + 1;
                existing.timestamp = timestamp;
            } else {
                scannedItems.unshift({
                    name: item.name,
                    sku: item.sku,
                    id: item.id,
                    raw: rawCode,
                    count: 1,
                    timestamp: timestamp
                });
            }

            if (scannedItems.length > 20) {
                scannedItems.pop();
            }

            saveRecentScans();
            renderRecentScans();
        }

        function renderRecentScans() {
            const container = document.getElementById('recent-scans');
            if (!container) return;

            if (scannedItems.length === 0) {
                container.innerHTML = '<div class="text-center py-4 text-xs text-slate-500">No items scanned yet</div>';
                return;
            }

            container.innerHTML = scannedItems.map(item => `
                <div class="flex items-center justify-between bg-slate-700/80 rounded-xl px-3 py-2 border border-slate-600/50">
                    <div class="min-w-0 flex-1 pr-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-white truncate">${item.name}</span>
                            ${item.count > 1 ? `<span class="px-1.5 py-0.2 rounded bg-amber-500/20 text-amber-300 text-[10px] font-bold">x${item.count}</span>` : ''}
                        </div>
                        <div class="text-[10px] text-slate-400 mt-0.5">
                            ${item.sku ? `<span class="font-mono text-slate-300">${item.sku}</span> • ` : ''}
                            <span>${item.timestamp}</span>
                        </div>
                    </div>
                    <span class="text-green-400 text-xs font-bold shrink-0">✓ Added</span>
                </div>
            `).join('');
        }

        function saveRecentScans() {
            try {
                localStorage.setItem('pos_recent_mobile_scans', JSON.stringify(scannedItems));
            } catch (e) {}
        }

        function loadRecentScans() {
            try {
                const stored = localStorage.getItem('pos_recent_mobile_scans');
                if (stored) {
                    scannedItems = JSON.parse(stored);
                    renderRecentScans();
                }
            } catch (e) {}
        }

        function clearRecentScans() {
            scannedItems = [];
            saveRecentScans();
            renderRecentScans();
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
            const status = document.getElementById('connection-status');
            const dot = document.getElementById('conn-dot');

            const update = () => {
                if (navigator.onLine) {
                    if (status) {
                        status.textContent = 'Connected';
                        status.className = 'text-green-400 font-medium';
                    }
                    if (dot) dot.className = 'w-2 h-2 rounded-full bg-green-400 animate-pulse';
                } else {
                    if (status) {
                        status.textContent = 'Offline';
                        status.className = 'text-red-400 font-medium';
                    }
                    if (dot) dot.className = 'w-2 h-2 rounded-full bg-red-400';
                }
            };

            window.addEventListener('online', update);
            window.addEventListener('offline', update);
            setInterval(update, 5000);
            update();
        }

        window.addEventListener('beforeunload', function() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop().catch(err => console.error(err));
            }
        });
    </script>
</x-layouts.app>
