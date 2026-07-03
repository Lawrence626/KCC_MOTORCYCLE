<x-layouts.app :title="__('Mobile Scanner')">
    <div class="min-h-screen bg-slate-900 flex flex-col">
        <!-- Header -->
        <div class="bg-slate-800 px-4 py-3 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold text-white">POS Scanner</h1>
                <p class="text-xs text-slate-400">Scan QR codes to add items to cart</p>
            </div>
            <button onclick="window.location.href='{{ route('pos.terminal') }}'" class="rounded-full bg-slate-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-600">
                Back to Terminal
            </button>
        </div>

        <!-- Scanner Area -->
        <div class="flex-1 flex flex-col items-center justify-center p-4">
            <div id="reader" class="w-full max-w-md bg-black rounded-2xl overflow-hidden"></div>
            <div id="scanner-status" class="mt-4 text-center text-sm text-slate-400">
                Position QR code within the frame
            </div>
<<<<<<< HEAD
            <div id="permission-error" class="hidden mt-4 max-w-md text-center">
                <div class="bg-red-900/50 border border-red-500 rounded-lg p-4">
                    <p class="text-red-400 text-sm font-medium mb-2">Camera Access Denied</p>
                    <p class="text-red-300 text-xs mb-3">Please allow camera access to use the scanner</p>
                    <button onclick="requestCameraPermission()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        Request Camera Permission
                    </button>
                </div>
            </div>
            <button id="start-scanner-btn" onclick="initScanner()" class="hidden mt-4 bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                Start Scanner
            </button>
=======
>>>>>>> 55261fdf8c0856ecab9d26ddd532e9ca004a9774
        </div>

        <!-- Recent Scans -->
        <div class="bg-slate-800 px-4 py-3">
            <h2 class="text-sm font-semibold text-white mb-2">Recent Scans</h2>
            <div id="recent-scans" class="space-y-2 max-h-40 overflow-y-auto">
                <div class="text-center text-xs text-slate-500">No items scanned yet</div>
            </div>
        </div>

        <!-- Connection Status -->
        <div class="bg-slate-900 px-4 py-2 border-t border-slate-700">
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-400">Connection:</span>
                <span id="connection-status" class="text-green-400 font-medium">Connected</span>
            </div>
        </div>
    </div>

    <!-- html5-qrcode library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    
    <script>
        let html5QrcodeScanner = null;
        let scannedItems = [];

        document.addEventListener('DOMContentLoaded', function() {
<<<<<<< HEAD
            checkCameraSupport();
            checkConnection();
        });

        async function checkCameraSupport() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                document.getElementById('scanner-status').textContent = 'Camera not supported in this browser';
                document.getElementById('scanner-status').classList.add('text-red-400');
                document.getElementById('start-scanner-btn').classList.add('hidden');
                return;
            }

            // Check if we can access cameras
            try {
                const devices = await navigator.mediaDevices.enumerateDevices();
                const cameras = devices.filter(device => device.kind === 'videoinput');
                
                if (cameras.length === 0) {
                    document.getElementById('scanner-status').textContent = 'No camera found on this device';
                    document.getElementById('scanner-status').classList.add('text-red-400');
                    document.getElementById('start-scanner-btn').classList.add('hidden');
                } else {
                    // Camera available, show start button
                    document.getElementById('scanner-status').textContent = `Camera detected (${cameras.length} available)`;
                    document.getElementById('scanner-status').classList.add('text-green-400');
                    document.getElementById('start-scanner-btn').classList.remove('hidden');
                }
            } catch (err) {
                console.error('Error checking cameras:', err);
                document.getElementById('scanner-status').textContent = 'Unable to check camera availability';
                document.getElementById('scanner-status').classList.add('text-red-400');
                document.getElementById('start-scanner-btn').classList.remove('hidden');
            }
        }

=======
            initScanner();
            checkConnection();
        });

>>>>>>> 55261fdf8c0856ecab9d26ddd532e9ca004a9774
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
<<<<<<< HEAD
            ).then(() => {
                document.getElementById('scanner-status').textContent = 'Position QR code within the frame';
                document.getElementById('permission-error').classList.add('hidden');
                document.getElementById('start-scanner-btn').classList.add('hidden');
            }).catch(err => {
                console.error("Scanner error:", err);
                handleCameraError(err);
            });
        }

        async function requestCameraPermission() {
            try {
                // Request camera permission directly
                const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } });
                
                // Stop the stream immediately (we just wanted permission)
                stream.getTracks().forEach(track => track.stop());
                
                // Hide error and show start button
                document.getElementById('permission-error').classList.add('hidden');
                document.getElementById('start-scanner-btn').classList.remove('hidden');
                document.getElementById('scanner-status').textContent = 'Permission granted! Click "Start Scanner" to begin.';
                document.getElementById('scanner-status').classList.remove('text-red-400');
                document.getElementById('scanner-status').classList.add('text-green-400');
            } catch (err) {
                console.error("Permission request failed:", err);
                document.getElementById('scanner-status').textContent = 'Permission request failed. Please check browser settings.';
                document.getElementById('scanner-status').classList.add('text-red-400');
            }
        }

        function handleCameraError(err) {
            console.error("Camera error details:", err);
            
            if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                document.getElementById('scanner-status').textContent = 'Camera permission denied. Click the lock icon in your browser address bar to allow camera access.';
                document.getElementById('scanner-status').classList.add('text-red-400');
                document.getElementById('permission-error').classList.remove('hidden');
            } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                document.getElementById('scanner-status').textContent = 'No camera device found. Please check if your camera is connected.';
                document.getElementById('scanner-status').classList.add('text-red-400');
                document.getElementById('start-scanner-btn').classList.remove('hidden');
            } else if (err.name === 'NotReadableError' || err.name === 'TrackStartError') {
                document.getElementById('scanner-status').textContent = 'Camera is already in use by another application.';
                document.getElementById('scanner-status').classList.add('text-red-400');
                document.getElementById('start-scanner-btn').classList.remove('hidden');
            } else if (err.name === 'OverconstrainedError' || err.name === 'ConstraintNotSatisfiedError') {
                document.getElementById('scanner-status').textContent = 'Camera does not support the required settings.';
                document.getElementById('scanner-status').classList.add('text-red-400');
                document.getElementById('start-scanner-btn').classList.remove('hidden');
            } else if (err.name === 'SecurityError') {
                document.getElementById('scanner-status').textContent = 'Camera access blocked by browser security. Make sure you are using HTTPS or localhost.';
                document.getElementById('scanner-status').classList.add('text-red-400');
                document.getElementById('start-scanner-btn').classList.remove('hidden');
            } else {
                document.getElementById('scanner-status').textContent = 'Camera error: ' + err.message;
                document.getElementById('scanner-status').classList.add('text-red-400');
                document.getElementById('start-scanner-btn').classList.remove('hidden');
            }
        }

=======
            ).catch(err => {
                console.error("Scanner error:", err);
                document.getElementById('scanner-status').textContent = 'Camera access denied or not available';
                document.getElementById('scanner-status').classList.add('text-red-400');
            });
        }

>>>>>>> 55261fdf8c0856ecab9d26ddd532e9ca004a9774
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
