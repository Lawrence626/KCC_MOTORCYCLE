<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('Mobile Scanner')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Mobile Scanner'))]); ?>
    <div class="min-h-screen bg-slate-900 flex flex-col">
        <!-- Header -->
        <div class="bg-slate-800 px-4 py-3 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold text-white">POS Scanner</h1>
                <p class="text-xs text-slate-400">Scan QR codes to add items to cart</p>
            </div>
            <button onclick="window.location.href='<?php echo e(route('pos.terminal')); ?>'" class="rounded-full bg-slate-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-600">
                Back to Terminal
            </button>
        </div>

        <!-- Scanner Area -->
        <div class="flex-1 flex flex-col items-center justify-center p-4">
            <div id="reader" class="w-full max-w-md bg-black rounded-2xl overflow-hidden"></div>
            <div id="scanner-status" class="mt-4 text-center text-sm text-slate-400">
                Position QR code within the frame
            </div>
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
                const response = await fetch('<?php echo e(route('pos.scan')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
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
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Admin\Desktop\WEQW\KCC_MOTORCYCLE\resources\views/point_of_sales/mobile-scanner.blade.php ENDPATH**/ ?>