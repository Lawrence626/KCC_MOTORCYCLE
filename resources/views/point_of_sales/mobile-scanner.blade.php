<!DOCTYPE html>
<html lang="en" class="h-full bg-[#0b1320]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS Mobile Scanner - KCC Motorcycle</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }
        html, body {
            height: 100%;
            height: 100dvh;
            max-height: 100dvh;
            overflow: hidden;
            margin: 0;
            padding: 0;
            background-color: #0b1320;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        /* Custom scrollbar for recent scans */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.4);
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(100, 116, 139, 0.5);
            border-radius: 4px;
        }
        /* html5-qrcode video overrides to prevent mobile overflow */
        #reader {
            width: 100% !important;
            height: 100% !important;
            border: none !important;
            padding: 0 !important;
            position: relative !important;
            background-color: #000000 !important;
            overflow: hidden !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        #reader video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            border-radius: 1rem !important;
        }
        #reader__scan_region {
            min-height: unset !important;
            background: transparent !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        #reader__scan_region img,
        #reader__dashboard_section_swaplink,
        #reader__camera_selection,
        #reader__dashboard_section_csr,
        #reader__header_message {
            display: none !important;
        }
        #reader__status_span {
            display: none !important;
        }
    </style>
</head>
<body class="h-full w-full flex flex-col items-center justify-center p-2 sm:p-4 select-none">
    
    <!-- Scanner Card Container -->
    <div class="w-full max-w-sm h-full max-h-[100dvh] bg-[#131d2a] rounded-3xl overflow-hidden shadow-2xl border border-slate-700/60 flex flex-col justify-between">
        
        <!-- Header -->
        <div class="px-5 pt-4 pb-2.5 flex items-center justify-between flex-shrink-0 border-b border-slate-800/60">
            <div>
                <h1 class="text-lg sm:text-xl font-bold text-white tracking-tight leading-tight">POS Scanner</h1>
                <p class="text-[11px] text-slate-400 mt-0.5">Scan QR codes to add items to cart</p>
            </div>
            <button onclick="handleClose()" type="button" class="rounded-full bg-[#38bdf8] hover:bg-[#0ea5e9] active:scale-95 px-4 py-1.5 text-xs font-bold text-white transition-all shadow-sm cursor-pointer flex-shrink-0">
                Close
            </button>
        </div>

        <!-- Scanner Viewfinder Area -->
        <div class="flex-1 flex flex-col items-center justify-center px-4 py-2 min-h-0 overflow-hidden">
            <div class="relative w-[230px] h-[230px] sm:w-[250px] sm:h-[250px] max-w-full aspect-square rounded-2xl overflow-hidden bg-black border border-slate-700/60 shadow-inner flex items-center justify-center flex-shrink-0">
                <div id="reader"></div>
                
                <!-- Viewfinder Corner Accents -->
                <div class="pointer-events-none absolute inset-3 rounded-xl border border-white/20"></div>
                <div class="pointer-events-none absolute top-3 left-3 w-4 h-4 border-t-2 border-l-2 border-[#38bdf8] rounded-tl"></div>
                <div class="pointer-events-none absolute top-3 right-3 w-4 h-4 border-t-2 border-r-2 border-[#38bdf8] rounded-tr"></div>
                <div class="pointer-events-none absolute bottom-3 left-3 w-4 h-4 border-b-2 border-l-2 border-[#38bdf8] rounded-bl"></div>
                <div class="pointer-events-none absolute bottom-3 right-3 w-4 h-4 border-b-2 border-r-2 border-[#38bdf8] rounded-br"></div>
            </div>
            
            <div id="scanner-status" class="mt-2.5 text-center text-xs text-slate-400 font-medium truncate max-w-full px-2">
                Position QR code within the frame
            </div>
        </div>

        <!-- Recent Scans -->
        <div class="px-5 py-2.5 flex-shrink-0 border-t border-slate-800/80 bg-[#101824]">
            <div class="flex items-center justify-between mb-1.5">
                <h2 class="text-xs font-bold text-white tracking-wide">Recent Scans</h2>
                <span id="scan-badge" class="text-[10px] text-slate-400 font-medium">0 scanned</span>
            </div>
            <div id="recent-scans" class="space-y-1.5 max-h-[100px] overflow-y-auto custom-scrollbar pr-0.5">
                <div class="text-center text-[11px] text-slate-500 py-3">No items scanned yet</div>
            </div>
        </div>

        <!-- Connection Status Footer -->
        <div class="bg-[#0b1320] px-5 py-2.5 border-t border-slate-800 flex-shrink-0">
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-400 text-[11px]">Connection:</span>
                <div class="flex items-center gap-1.5">
                    <span id="connection-dot" class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span id="connection-status" class="text-emerald-400 font-bold text-xs">Connected</span>
                </div>
            </div>
        </div>
    </div>

    <!-- html5-qrcode library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    
    <script>
        let html5QrcodeScanner = null;
        let scannedItems = [];
        let isProcessingScan = false;

        document.addEventListener('DOMContentLoaded', function() {
            initScanner();
            checkConnection();
        });

        function handleClose() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = "{{ route('pos.terminal') }}";
            }
        }

        function initScanner() {
            try {
                html5QrcodeScanner = new Html5Qrcode("reader");
                
                const config = { 
                    fps: 15, 
                    qrbox: (viewfinderWidth, viewfinderHeight) => {
                        const minDim = Math.min(viewfinderWidth, viewfinderHeight);
                        return { width: Math.floor(minDim * 0.8), height: Math.floor(minDim * 0.8) };
                    },
                    aspectRatio: 1.0
                };

                html5QrcodeScanner.start(
                    { facingMode: "environment" },
                    config,
                    onScanSuccess,
                    onScanFailure
                ).catch(err => {
                    console.error("Camera start error:", err);
                    const statusEl = document.getElementById('scanner-status');
                    if (statusEl) {
                        statusEl.textContent = 'Camera access denied or not available';
                        statusEl.className = 'mt-2.5 text-center text-xs text-red-400 font-medium truncate max-w-full px-2';
                    }
                });
            } catch (e) {
                console.error("Scanner init error:", e);
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            if (isProcessingScan) return;
            isProcessingScan = true;

            playBeep();

            // Parse clean display name/SKU
            let skuDisplay = decodedText;
            let productDisplay = '';

            try {
                const parsed = JSON.parse(decodedText);
                if (parsed.sku) skuDisplay = parsed.sku;
                if (parsed.product_name) productDisplay = parsed.product_name;
                else if (parsed.name) productDisplay = parsed.name;
            } catch (e) {}

            // Update status UI
            const statusEl = document.getElementById('scanner-status');
            if (statusEl) {
                statusEl.textContent = '✓ Scanned: ' + skuDisplay;
                statusEl.className = 'mt-2.5 text-center text-xs text-emerald-400 font-bold truncate max-w-full px-2';
                
                setTimeout(() => {
                    statusEl.className = 'mt-2.5 text-center text-xs text-slate-400 font-medium truncate max-w-full px-2';
                    statusEl.textContent = 'Position QR code within the frame';
                    isProcessingScan = false;
                }, 1500);
            } else {
                isProcessingScan = false;
            }

            // Add to recent scans list
            addToRecentScans(decodedText, skuDisplay, productDisplay);

            // Send to terminal via localStorage (for same-browser sync)
            sendToTerminal(decodedText);

            // Send via server API (for cross-device sync)
            sendToServer(decodedText);
        }

        function onScanFailure(error) {
            // Ignore normal per-frame scan failures
        }

        function playBeep() {
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.value = 880;
                oscillator.type = 'sine';
                gainNode.gain.value = 0.15;
                
                oscillator.start();
                oscillator.stop(audioContext.currentTime + 0.12);
            } catch (e) {}
        }

        function addToRecentScans(rawCode, skuDisplay, productDisplay) {
            const timestamp = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            scannedItems.unshift({ rawCode, skuDisplay, productDisplay, timestamp });
            
            if (scannedItems.length > 8) {
                scannedItems.pop();
            }

            const badge = document.getElementById('scan-badge');
            if (badge) {
                badge.textContent = `${scannedItems.length} scanned`;
            }

            const container = document.getElementById('recent-scans');
            if (!container) return;

            container.innerHTML = scannedItems.map(item => `
                <div class="flex items-center justify-between bg-slate-800/90 border border-slate-700/60 rounded-xl px-3 py-1.5 transition">
                    <div class="min-w-0 flex-1 pr-2">
                        <div class="text-xs font-semibold text-white truncate">${item.skuDisplay}</div>
                        ${item.productDisplay ? `<div class="text-[10px] text-slate-300 truncate">${item.productDisplay}</div>` : ''}
                        <div class="text-[9px] text-slate-400 font-mono">${item.timestamp}</div>
                    </div>
                    <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold flex items-center justify-center flex-shrink-0">✓</span>
                </div>
            `).join('');
        }

        function sendToTerminal(code) {
            try {
                const scanData = {
                    code: code,
                    timestamp: Date.now(),
                    type: 'qr_scan'
                };
                localStorage.setItem('pos_scan_data', JSON.stringify(scanData));
            } catch (e) {}
        }

        async function sendToServer(code) {
            try {
                await fetch('{{ route('pos.scan') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ code: code })
                });
            } catch (error) {
                console.error('Error sending scan to server:', error);
            }
        }

        function checkConnection() {
            const updateStatus = () => {
                const status = document.getElementById('connection-status');
                const dot = document.getElementById('connection-dot');
                if (!status || !dot) return;

                if (navigator.onLine) {
                    status.textContent = 'Connected';
                    status.className = 'text-emerald-400 font-bold text-xs';
                    dot.className = 'w-2 h-2 rounded-full bg-emerald-400 animate-pulse';
                } else {
                    status.textContent = 'Offline';
                    status.className = 'text-red-400 font-bold text-xs';
                    dot.className = 'w-2 h-2 rounded-full bg-red-400';
                }
            };

            window.addEventListener('online', updateStatus);
            window.addEventListener('offline', updateStatus);
            setInterval(updateStatus, 4000);
        }

        // Cleanup camera on page exit
        window.addEventListener('beforeunload', function() {
            if (html5QrcodeScanner) {
                try {
                    html5QrcodeScanner.stop().catch(() => {});
                } catch (e) {}
            }
        });
    </script>
</body>
</html>
