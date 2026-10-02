<x-layouts.app :title="__('Mobile Scanner')">
    <div class="min-h-screen bg-[#0b1320] flex flex-col items-center justify-center p-3 sm:p-6">
        <div class="w-full max-w-md bg-[#131d2a] rounded-3xl overflow-hidden shadow-2xl border border-slate-700/60 flex flex-col">
            <!-- Header -->
            <div class="px-5 pt-5 pb-3 flex items-start justify-between">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">POS Scanner</h1>
                    <p class="text-xs text-slate-400 mt-0.5">Scan QR codes to add items to cart</p>
                </div>
                <button onclick="window.location.href='{{ route('pos.terminal') }}'" class="rounded-full bg-[#38bdf8] hover:bg-[#0ea5e9] px-4 py-1.5 text-xs font-bold text-white transition-all shadow-sm cursor-pointer">
                    Close
                </button>
            </div>

            <!-- Scanner Area -->
            <div class="px-5 py-2 flex flex-col items-center justify-center">
                <div id="reader" class="w-full bg-black rounded-2xl overflow-hidden border border-slate-700/60 min-h-[260px]"></div>
                <div id="scanner-status" class="mt-4 text-center text-xs sm:text-sm text-slate-400 font-medium">
                    Position QR code within the frame
                </div>
            </div>

            <!-- Recent Scans -->
            <div class="px-5 py-3 mt-1">
                <h2 class="text-xs sm:text-sm font-bold text-white mb-2">Recent Scans</h2>
                <div id="recent-scans" class="space-y-1.5 max-h-36 overflow-y-auto">
                    <div class="text-center text-xs text-slate-400 py-3">No items scanned yet</div>
                </div>
            </div>

            <!-- Connection Status -->
            <div class="bg-[#0b1320] px-5 py-3 border-t border-slate-700/60 mt-auto">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-400">Connection:</span>
                    <span id="connection-status" class="text-emerald-400 font-bold">Connected</span>
                </div>
            </div>
        </div>
    </div>

    <!-- html5-qrcode library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    
    <script>
        let html5QrcodeScanner = null;
        let scannedItems = [];

        document.addEventListener('DOMContentLoaded', function() {
            initScanner();
            checkConnection();
        });

        function initScanner() {
            html5QrcodeScanner = new Html5Qrcode("reader");
            
            const config = { 
                fps: 10, 
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            };

            html5QrcodeScanner.start(
                { facingMode: "environment" },
                config,
                onScanSuccess,
                onScanFailure
            ).catch(err => {
                console.error("Scanner error:", err);
                document.getElementById('scanner-status').textContent = 'Camera access denied or not available';
                document.getElementById('scanner-status').classList.add('text-red-400');
            });
        }

        function onScanSuccess(decodedText, decodedResult) {
            // Play beep sound
            playBeep();

            // Update status
            document.getElementById('scanner-status').textContent = 'Scanned: ' + decodedText;
            document.getElementById('scanner-status').classList.add('text-green-400');
            
            setTimeout(() => {
                document.getElementById('scanner-status').classList.remove('text-green-400');
                document.getElementById('scanner-status').textContent = 'Position QR code within the frame';
            }, 2000);

            // Add to recent scans
            addToRecentScans(decodedText);

            // Send to terminal via localStorage (for same browser)
            sendToTerminal(decodedText);

            // Also try to send via API (for cross-device)
            sendToServer(decodedText);
        }

        function onScanFailure(error) {
            // Ignore scan failures, they're normal
        }

        function playBeep() {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            gainNode.gain.value = 0.1;
            
            oscillator.start();
            oscillator.stop(audioContext.currentTime + 0.1);
        }

        function addToRecentScans(code) {
            const timestamp = new Date().toLocaleTimeString();
            scannedItems.unshift({ code, timestamp });
            
            if (scannedItems.length > 10) {
                scannedItems.pop();
            }

            const container = document.getElementById('recent-scans');
            container.innerHTML = scannedItems.map(item => `
                <div class="flex items-center justify-between bg-slate-700 rounded-lg px-3 py-2">
                    <div>
                        <div class="text-xs font-medium text-white">${item.code}</div>
                        <div class="text-[10px] text-slate-400">${item.timestamp}</div>
                    </div>
                    <span class="text-green-400 text-xs">✓</span>
                </div>
            `).join('');
        }

        function sendToTerminal(code) {
            // Use localStorage for same-browser sync
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
            setInterval(() => {
                const status = document.getElementById('connection-status');
                if (navigator.onLine) {
                    status.textContent = 'Connected';
                    status.classList.remove('text-red-400');
                    status.classList.add('text-green-400');
                } else {
                    status.textContent = 'Offline';
                    status.classList.remove('text-green-400');
                    status.classList.add('text-red-400');
                }
            }, 5000);
        }

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop().catch(err => console.error(err));
            }
        });
    </script>
</x-layouts.app>
