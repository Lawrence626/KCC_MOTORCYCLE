<x-layouts.app :title="__('Warehouse Mobile Scanner')">
    <div class="min-h-screen bg-slate-900 flex flex-col">
        <!-- Header -->
        <div class="bg-slate-800 px-4 py-3 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold text-white">Warehouse Scanner</h1>
                <p class="text-xs text-slate-400">Scan QR codes to add new stock</p>
            </div>
            <button onclick="window.location.href='{{ route('warehouse.management') }}'" class="rounded-full bg-slate-700 px-2.5 py-1 text-[11px] font-semibold text-white hover:bg-slate-600">
                Back to Warehouse
            </button>
        </div>

        <!-- Scanner Area -->
        <div class="flex-1 flex flex-col items-center justify-center p-4">
            <div id="reader" class="w-full max-w-md bg-black rounded-2xl overflow-hidden"></div>
            <div id="status" class="mt-4 text-center text-sm text-slate-400">
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
            <button id="start-scanner-btn" onclick="initScanner()" class="hidden mt-4 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                Start Scanner
            </button>
=======
>>>>>>> 55261fdf8c0856ecab9d26ddd532e9ca004a9774
        </div>

        <!-- Scanned Items -->
        <div class="bg-slate-800 px-4 py-3">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-sm font-semibold text-white">Scanned Items (<span id="scan-count">0</span>)</h2>
                <button onclick="clearScannedItems()" class="text-xs text-red-400 hover:text-red-300">Clear All</button>
            </div>
            <div id="scan-list" class="space-y-2 max-h-40 overflow-y-auto">
                <div class="text-center text-xs text-slate-500">No items scanned yet</div>
            </div>
            
            <!-- Pagination -->
            <div id="pagination" class="hidden flex items-center justify-between mt-3 pt-3 border-t border-slate-700">
                <button onclick="prevPage()" class="px-2.5 py-1 rounded-lg border border-slate-600 bg-slate-700 text-[11px] font-semibold text-white hover:bg-slate-600 disabled:opacity-50" id="prev-page">Previous</button>
                <span id="page-info" class="text-[11px] text-slate-400">Page 1 of 1</span>
                <button onclick="nextPage()" class="px-2.5 py-1 rounded-lg border border-slate-600 bg-slate-700 text-[11px] font-semibold text-white hover:bg-slate-600 disabled:opacity-50" id="next-page">Next</button>
            </div>

            <button onclick="sendToWarehouse()" class="w-full mt-4 px-3 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-sm font-semibold hover:from-emerald-700 hover:to-emerald-800 transition" id="send-btn" disabled>Send to Warehouse</button>
        </div>

        <!-- Connection Status -->
        <div class="bg-slate-900 px-4 py-2 border-t border-slate-700">
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-400">Connection:</span>
                <span id="connection-status" class="text-green-400 font-medium">Connected</span>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    
    <script>
        let html5QrcodeScanner = null;
        let scannedItems = [];
        let currentPage = 0;
        const ITEMS_PER_PAGE = 4;

        document.addEventListener('DOMContentLoaded', function() {
<<<<<<< HEAD
            checkCameraSupport();
            checkConnection();
        });

        async function checkCameraSupport() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                document.getElementById('status').textContent = 'Camera not supported in this browser';
                document.getElementById('status').classList.add('text-red-400');
                document.getElementById('start-scanner-btn').classList.add('hidden');
                return;
            }

            // Check if we can access cameras
            try {
                const devices = await navigator.mediaDevices.enumerateDevices();
                const cameras = devices.filter(device => device.kind === 'videoinput');
                
                if (cameras.length === 0) {
                    document.getElementById('status').textContent = 'No camera found on this device';
                    document.getElementById('status').classList.add('text-red-400');
                    document.getElementById('start-scanner-btn').classList.add('hidden');
                } else {
                    // Camera available, show start button
                    document.getElementById('status').textContent = `Camera detected (${cameras.length} available)`;
                    document.getElementById('status').classList.add('text-emerald-400');
                    document.getElementById('start-scanner-btn').classList.remove('hidden');
                }
            } catch (err) {
                console.error('Error checking cameras:', err);
                document.getElementById('status').textContent = 'Unable to check camera availability';
                document.getElementById('status').classList.add('text-red-400');
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
                document.getElementById('status').textContent = 'Position QR code within the frame';
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
                document.getElementById('status').textContent = 'Permission granted! Click "Start Scanner" to begin.';
                document.getElementById('status').classList.remove('text-red-400');
                document.getElementById('status').classList.add('text-emerald-400');
            } catch (err) {
                console.error("Permission request failed:", err);
                document.getElementById('status').textContent = 'Permission request failed. Please check browser settings.';
                document.getElementById('status').classList.add('text-red-400');
            }
        }

        function handleCameraError(err) {
            console.error("Camera error details:", err);
            
            if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                document.getElementById('status').textContent = 'Camera permission denied. Click the lock icon in your browser address bar to allow camera access.';
                document.getElementById('status').classList.add('text-red-400');
                document.getElementById('permission-error').classList.remove('hidden');
            } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                document.getElementById('status').textContent = 'No camera device found. Please check if your camera is connected.';
                document.getElementById('status').classList.add('text-red-400');
                document.getElementById('start-scanner-btn').classList.remove('hidden');
            } else if (err.name === 'NotReadableError' || err.name === 'TrackStartError') {
                document.getElementById('status').textContent = 'Camera is already in use by another application.';
                document.getElementById('status').classList.add('text-red-400');
                document.getElementById('start-scanner-btn').classList.remove('hidden');
            } else if (err.name === 'OverconstrainedError' || err.name === 'ConstraintNotSatisfiedError') {
                document.getElementById('status').textContent = 'Camera does not support the required settings.';
                document.getElementById('status').classList.add('text-red-400');
                document.getElementById('start-scanner-btn').classList.remove('hidden');
            } else if (err.name === 'SecurityError') {
                document.getElementById('status').textContent = 'Camera access blocked by browser security. Make sure you are using HTTPS or localhost.';
                document.getElementById('status').classList.add('text-red-400');
                document.getElementById('start-scanner-btn').classList.remove('hidden');
            } else {
                document.getElementById('status').textContent = 'Camera error: ' + err.message;
                document.getElementById('status').classList.add('text-red-400');
                document.getElementById('start-scanner-btn').classList.remove('hidden');
            }
        }

=======
            ).catch(err => {
                console.error("Scanner error:", err);
                document.getElementById('status').textContent = 'Camera access denied or not available';
                document.getElementById('status').classList.add('text-red-400');
            });
        }

>>>>>>> 55261fdf8c0856ecab9d26ddd532e9ca004a9774
        function onScanSuccess(decodedText, decodedResult) {
            try {
                const parsed = JSON.parse(decodedText);
                
                // Check if it's a new stock QR code
                if (parsed.type === 'new_stock' && parsed.product_name && parsed.sku) {
                    // Check for duplicates
                    const alreadyScanned = scannedItems.find(item => item.sku === parsed.sku);
                    if (alreadyScanned) {
                        showToast('This item is already scanned', 'error');
                        return;
                    }
                    
                    scannedItems.push({
                        product_name: parsed.product_name,
                        sku: parsed.sku,
                        restock_date: parsed.restock_date,
                        timestamp: new Date().toLocaleTimeString()
                    });
                    
                    updateScanList();
                    showToast(`Scanned: ${parsed.product_name}`, 'success');
                    
                    // Play beep sound
                    playBeep();
                } else {
                    showToast('Invalid new stock QR code', 'error');
                }
            } catch (e) {
                showToast('Invalid QR code format', 'error');
            }
        }

        function onScanFailure(error) {
            // Ignore scan failures
        }

        function updateScanList() {
            const scanList = document.getElementById('scan-list');
            const scanCount = document.getElementById('scan-count');
            const sendBtn = document.getElementById('send-btn');
            const pagination = document.getElementById('pagination');
            
            scanCount.textContent = scannedItems.length;
            
            if (scannedItems.length > 0) {
                sendBtn.disabled = false;
                
                // Show pagination if needed
                if (scannedItems.length > ITEMS_PER_PAGE) {
                    pagination.classList.remove('hidden');
                    pagination.classList.add('flex');
                } else {
                    pagination.classList.add('hidden');
                    pagination.classList.remove('flex');
                }
                
                // Render current page
                const start = currentPage * ITEMS_PER_PAGE;
                const end = Math.min(start + ITEMS_PER_PAGE, scannedItems.length);
                const pageItems = scannedItems.slice(start, end);
                
                scanList.innerHTML = pageItems.map((item, index) => {
                    const actualIndex = start + index;
                    return `
                        <div class="flex items-center justify-between bg-slate-700 rounded-lg px-3 py-2">
                            <div>
                                <div class="text-xs font-medium text-white">${item.product_name}</div>
                                <div class="text-[10px] text-slate-400">SKU: ${item.sku} • ${item.timestamp}</div>
                            </div>
                            <button onclick="removeScannedItem(${actualIndex})" class="text-red-400 hover:text-red-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    `;
                }).join('');
                
                // Update pagination controls
                const totalPages = Math.ceil(scannedItems.length / ITEMS_PER_PAGE);
                document.getElementById('page-info').textContent = `Page ${currentPage + 1} of ${totalPages}`;
                document.getElementById('prev-page').disabled = currentPage === 0;
                document.getElementById('next-page').disabled = currentPage >= totalPages - 1;
            } else {
                sendBtn.disabled = true;
                scanList.innerHTML = '<div class="text-center text-xs text-slate-500">No items scanned yet</div>';
                pagination.classList.add('hidden');
                pagination.classList.remove('flex');
                currentPage = 0;
            }
        }

        function prevPage() {
            if (currentPage > 0) {
                currentPage--;
                updateScanList();
            }
        }

        function nextPage() {
            const totalPages = Math.ceil(scannedItems.length / ITEMS_PER_PAGE);
            if (currentPage < totalPages - 1) {
                currentPage++;
                updateScanList();
            }
        }

        function removeScannedItem(index) {
            scannedItems.splice(index, 1);
            const totalPages = Math.ceil(scannedItems.length / ITEMS_PER_PAGE);
            if (currentPage >= totalPages && currentPage > 0) {
                currentPage = totalPages - 1;
            }
            updateScanList();
        }

        function clearScannedItems() {
            scannedItems = [];
            currentPage = 0;
            updateScanList();
        }

        async function sendToWarehouse() {
            if (scannedItems.length === 0) return;
            
            try {
                // Save to localStorage for warehouse page to pick up
                localStorage.setItem('warehouseScannedItems', JSON.stringify(scannedItems));
                localStorage.setItem('warehouseScanTimestamp', Date.now());
                
                showToast('Items sent to warehouse!', 'success');
                clearScannedItems();
            } catch (error) {
                console.error('Error sending to warehouse:', error);
                showToast('Error sending to warehouse', 'error');
            }
        }

        function showToast(message, type) {
            const status = document.getElementById('status');
            status.textContent = message;
            status.className = type === 'success' ? 'text-emerald-400 text-sm mt-4 text-center' : 'text-red-400 text-sm mt-4 text-center';
            
            setTimeout(() => {
                status.textContent = 'Position QR code within the frame';
                status.className = 'text-slate-400 text-sm mt-4 text-center';
            }, 2000);
        }

        function playBeep() {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = 1000;
            oscillator.type = 'sine';
            gainNode.gain.value = 0.1;
            
            oscillator.start();
            oscillator.stop(audioContext.currentTime + 0.1);
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
