<x-layouts.app :title="__('Test Offline Mode')">
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Test Offline Mode</h1>
                <p class="text-xs text-slate-500 mt-0.5">Test offline functionality without going offline</p>
            </div>
            <div class="flex items-center gap-2">
                <div id="offline-indicator" class="hidden"></div>
                <a href="{{ route('offline.reconciliation') }}" class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Connection Status -->
        <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900 mb-3">Connection Status</h2>
            <div class="flex items-center gap-3">
                <div id="connection-status" class="w-3 h-3 rounded-full bg-green-500"></div>
                <span id="connection-text" class="text-sm text-slate-700">Online</span>
            </div>
        </div>

        <!-- Test Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Test Transaction -->
            <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
                <h2 class="text-sm font-semibold text-slate-900 mb-3">Test Transaction</h2>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Product Name</label>
                        <input type="text" id="test-product" value="Test Product" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Quantity</label>
                        <input type="number" id="test-quantity" value="1" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Amount</label>
                        <input type="number" id="test-amount" value="100" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                    </div>
                    <button onclick="testTransaction()" class="w-full px-4 py-2 rounded-lg bg-blue-500 text-white text-sm font-medium hover:bg-blue-600 transition">
                        Save Test Transaction
                    </button>
                </div>
            </div>

            <!-- Test Purchase Order -->
            <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
                <h2 class="text-sm font-semibold text-slate-900 mb-3">Test Purchase Order</h2>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Supplier Name</label>
                        <input type="text" id="test-supplier" value="Test Supplier" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Order Number</label>
                        <input type="text" id="test-order-number" value="PO-TEST-001" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Total Amount</label>
                        <input type="number" id="test-total" value="500" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm">
                    </div>
                    <button onclick="testPurchaseOrder()" class="w-full px-4 py-2 rounded-lg bg-purple-500 text-white text-sm font-medium hover:bg-purple-600 transition">
                        Save Test Purchase Order
                    </button>
                </div>
            </div>

            <!-- Sync Controls -->
            <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
                <h2 class="text-sm font-semibold text-slate-900 mb-3">Sync Controls</h2>
                <div class="space-y-3">
                    <button onclick="window.offlineManager.manualSync()" class="w-full px-4 py-2 rounded-lg bg-green-500 text-white text-sm font-medium hover:bg-green-600 transition">
                        Sync Now
                    </button>
                    <button onclick="window.offlineManager.clearOfflineData()" class="w-full px-4 py-2 rounded-lg bg-red-500 text-white text-sm font-medium hover:bg-red-600 transition">
                        Clear All Offline Data
                    </button>
                    <button onclick="viewOfflineData()" class="w-full px-4 py-2 rounded-lg bg-slate-500 text-white text-sm font-medium hover:bg-slate-600 transition">
                        View Offline Data
                    </button>
                </div>
            </div>
        </div>

        <!-- Pending Data Display -->
        <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900 mb-3">Pending Data Summary</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-3 bg-blue-50 rounded-lg">
                    <p class="text-xs text-blue-600">Pending Transactions</p>
                    <p id="pending-transactions-count" class="text-2xl font-bold text-blue-700">0</p>
                </div>
                <div class="p-3 bg-purple-50 rounded-lg">
                    <p class="text-xs text-purple-600">Pending Purchase Orders</p>
                    <p id="pending-purchase-orders-count" class="text-2xl font-bold text-purple-700">0</p>
                </div>
            </div>
        </div>

        <!-- Offline Data Details -->
        <div id="offline-data-details" class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm hidden">
            <h2 class="text-sm font-semibold text-slate-900 mb-3">Offline Data Details</h2>
            <pre id="offline-data-content" class="text-xs bg-slate-50 p-3 rounded-lg overflow-auto max-h-96"></pre>
        </div>
    </div>

    <script src="{{ asset('js/offline-manager.js') }}"></script>
    <script>
        function testTransaction() {
            const product = document.getElementById('test-product').value;
            const quantity = parseInt(document.getElementById('test-quantity').value);
            const amount = parseFloat(document.getElementById('test-amount').value);

            const transaction = {
                product_name: product,
                quantity: quantity,
                amount: amount,
                total: quantity * amount,
                type: 'sale'
            };

            const id = window.offlineManager.saveTransactionLocally(transaction);
            alert('Transaction saved locally! ID: ' + id);
            updatePendingCounts();
        }

        function testPurchaseOrder() {
            const supplier = document.getElementById('test-supplier').value;
            const orderNumber = document.getElementById('test-order-number').value;
            const total = parseFloat(document.getElementById('test-total').value);

            const order = {
                supplier_name: supplier,
                order_number: orderNumber,
                total_amount: total,
                status: 'pending',
                items: []
            };

            const id = window.offlineManager.savePurchaseOrderLocally(order);
            alert('Purchase order saved locally! ID: ' + id);
            updatePendingCounts();
        }

        function updatePendingCounts() {
            if (document.getElementById('pending-transactions-count')) {
                document.getElementById('pending-transactions-count').textContent = 
                    (window.offlineManager.pendingTransactions || []).length;
            }
            if (document.getElementById('pending-purchase-orders-count')) {
                document.getElementById('pending-purchase-orders-count').textContent = 
                    (window.offlineManager.pendingPurchaseOrders || []).length;
            }
        }

        function viewOfflineData() {
            const data = {
                pendingTransactions: window.offlineManager.pendingTransactions || [],
                pendingPurchaseOrders: window.offlineManager.pendingPurchaseOrders || []
            };

            document.getElementById('offline-data-content').textContent = JSON.stringify(data, null, 2);
            document.getElementById('offline-data-details').classList.remove('hidden');
        }

        // Update connection status
        function updateConnectionStatus() {
            const isOnline = navigator.onLine;
            const statusDot = document.getElementById('connection-status');
            const statusText = document.getElementById('connection-text');

            if (isOnline) {
                statusDot.classList.remove('bg-red-500');
                statusDot.classList.add('bg-green-500');
                statusText.textContent = 'Online';
            } else {
                statusDot.classList.remove('bg-green-500');
                statusDot.classList.add('bg-red-500');
                statusText.textContent = 'Offline';
            }
        }

        // Listen for connection changes
        window.addEventListener('online', updateConnectionStatus);
        window.addEventListener('offline', updateConnectionStatus);

        // Initial update
        document.addEventListener('DOMContentLoaded', () => {
            updateConnectionStatus();
            updatePendingCounts();
        });
    </script>
</x-layouts.app>
