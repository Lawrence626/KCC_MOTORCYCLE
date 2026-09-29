<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>POS Scanner - KCC Motorcycle</title>
    @include('partials.head')
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        #reader video {
            object-fit: cover !important;
            border-radius: 1rem !important;
        }
        #reader {
            border: none !important;
        }
        #reader__scan_region {
            background: transparent !important;
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-3 sm:p-6 font-sans">
    <!-- Main Modal Card matching user screenshot -->
    <div class="w-full max-w-md bg-slate-800 rounded-3xl overflow-hidden shadow-2xl border border-slate-700/60 flex flex-col">
        <!-- Card Header -->
        <div class="bg-slate-800 px-5 py-4 flex items-center justify-between border-b border-slate-700/60">
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">POS Scanner</h1>
                <p class="text-xs text-slate-400 mt-0.5">Scan QR codes to add items to cart</p>
            </div>
            <button onclick="window.location.href='{{ route('pos.terminal') }}'" class="rounded-full bg-[#6EC1D1] px-4 py-1.5 text-xs font-bold text-slate-900 hover:bg-[#59b2c2] active:scale-95 transition shadow-sm cursor-pointer">
                Close
            </button>
        </div>

        <!-- Scanner Viewport Area -->
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
                <button onclick="clearScannedList()" class="text-xs text-slate-400 hover:text-red-400 transition cursor-pointer">Clear</button>
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

    <script>
        let html5QrcodeScanner = null;
        let scannedItems = [];
        let isScanningPaused = false;
        let lastScannedText = '';
        let pauseTimeout = null;
        let audioCtx = null;

        // Unlock audio for mobile browser
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
            loadScannedList();
            initScanner();
            checkConnection();
        });

        function playBeep(isWarning = false) {
            try {
                const ctx = getAudioContext();
                if (ctx) {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    if (isWarning) {
                        osc.type = 'square';
                        osc.frequency.setValueAtTime(440, ctx.currentTime);
                        osc.frequency.setValueAtTime(330, ctx.currentTime + 0.08);
                        gain.gain.setValueAtTime(0.15, ctx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.18);
                        osc.start(ctx.currentTime);
                        osc.stop(ctx.currentTime + 0.18);
                    } else {
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(880, ctx.currentTime);
                        osc.frequency.exponentialRampToValueAtTime(1320, ctx.currentTime + 0.1);
                        gain.gain.setValueAtTime(0.2, ctx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.12);
                        osc.start(ctx.currentTime);
                        osc.stop(ctx.currentTime + 0.12);
                    }
                }
            } catch (e) {
                console.warn(e);
            }

            if (navigator.vibrate) {
                navigator.vibrate(isWarning ? [100, 50, 100] : 60);
            }
        }

        function initScanner() {
            html5QrcodeScanner = new Html5Qrcode("reader", {
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

        function parseCode(rawCode) {
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
            // Strict 1-QR-per-scan lock: ignore frames while paused
            if (isScanningPaused) return;

            isScanningPaused = true;
            lastScannedText = decodedText;

            const item = parseCode(decodedText);
            const statusEl = document.getElementById('scanner-status');

            // Check if already scanned in current session list
            const existing = scannedItems.find(i => 
                (item.sku && i.sku && i.sku.toLowerCase() === item.sku.toLowerCase()) ||
                (item.id && i.id && String(item.id) === String(item.id)) ||
                (i.raw === decodedText)
            );

            if (existing) {
                // Already scanned
                existing.count = (existing.count || 1) + 1;
                existing.timestamp = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });

                playBeep(true); // Warning beep

                if (statusEl) {
                    statusEl.textContent = 'This item already scanned';
                    statusEl.className = 'mt-4 text-center text-sm font-bold text-amber-400 transition-all';
                }
            } else {
                // New scan
                scannedItems.unshift({
                    name: item.name,
                    sku: item.sku,
                    id: item.id,
                    raw: decodedText,
                    count: 1,
                    timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' })
                });

                playBeep(false); // Success beep

                if (statusEl) {
                    statusEl.textContent = `✓ Scanned: ${item.name} (${item.sku || 'Added'})`;
                    statusEl.className = 'mt-4 text-center text-sm font-bold text-green-400 transition-all';
                }
            }

            if (scannedItems.length > 20) {
                scannedItems.pop();
            }

            saveScans();
            renderScans();

            // Sync to POS Terminal
            sendToTerminal(decodedText);
            sendToServer(decodedText);

            // Cooldown 2.5s before allowing next scan (avoids continuous unli-scan)
            if (pauseTimeout) clearTimeout(pauseTimeout);
            pauseTimeout = setTimeout(() => {
                isScanningPaused = false;
                if (statusEl) {
                    statusEl.textContent = 'Position QR code within the frame';
                    statusEl.className = 'mt-4 text-center text-sm font-medium text-slate-400 transition-all';
                }
            }, 2500);
        }

        function onScanFailure(error) {
            // Ignore normal frame noise
        }

        function renderScans() {
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

        function saveScans() {
            try {
                localStorage.setItem('pos_recent_mobile_scans', JSON.stringify(scannedItems));
            } catch (e) {}
        }

        function loadScannedList() {
            try {
                const stored = localStorage.getItem('pos_recent_mobile_scans');
                if (stored) {
                    scannedItems = JSON.parse(stored);
                    renderScans();
                }
            } catch (e) {}
        }

        function clearScannedList() {
            scannedItems = [];
            saveScans();
            renderScans();
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
</body>
</html>
