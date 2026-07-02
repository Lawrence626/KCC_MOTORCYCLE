// Offline Manager - Handles offline detection, local storage, and synchronization
class OfflineManager {
    constructor() {
        this.isOnline = navigator.onLine;
        this.dbName = 'KCC_OfflineDB';
        this.dbVersion = 2;
        this.db = null;
        this.queue = [];
        this.init();
    }

    async init() {
        // Initialize IndexedDB
        await this.initIndexedDB();
        
        // Setup event listeners for online/offline status
        window.addEventListener('online', () => this.handleOnline());
        window.addEventListener('offline', () => this.handleOffline());
        
        // Load pending operations from IndexedDB
        await this.loadPendingOperations();
        
        // Update UI status
        this.updateStatusIndicator();
    }

    async initIndexedDB() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.dbVersion);

            request.onerror = () => reject(request.error);
            request.onsuccess = () => {
                this.db = request.result;
                resolve();
            };

            request.onupgradeneeded = (event) => {
                const db = event.target.result;

                // Create stores for offline operations
                if (!db.objectStoreNames.contains('pending_orders')) {
                    const orderStore = db.createObjectStore('pending_orders', { keyPath: 'id', autoIncrement: true });
                    orderStore.createIndex('timestamp', 'timestamp');
                }

                if (!db.objectStoreNames.contains('pending_movements')) {
                    const movementStore = db.createObjectStore('pending_movements', { keyPath: 'id', autoIncrement: true });
                    movementStore.createIndex('timestamp', 'timestamp');
                }

                if (!db.objectStoreNames.contains('sync_queue')) {
                    const queueStore = db.createObjectStore('sync_queue', { keyPath: 'id', autoIncrement: true });
                    queueStore.createIndex('timestamp', 'timestamp');
                    queueStore.createIndex('status', 'status');
                }

                if (!db.objectStoreNames.contains('archived_orders')) {
                    const archiveStore = db.createObjectStore('archived_orders', { keyPath: 'id', autoIncrement: true });
                    archiveStore.createIndex('archived_at', 'archived_at');
                }
            };
        });
    }

    handleOnline() {
        this.isOnline = true;
        this.updateStatusIndicator();
        this.showNotification('You are back online. Syncing...', 'success');
        
        // Attempt to sync pending operations
        this.syncPendingOperations();
    }

    handleOffline() {
        this.isOnline = false;
        this.updateStatusIndicator();
        this.showNotification('You are offline. Orders will be saved locally.', 'warning');
    }

    updateStatusIndicator() {
        const indicator = document.getElementById('offline-indicator');
        if (indicator) {
            if (this.isOnline) {
                indicator.className = 'hidden';
            } else {
                indicator.className = 'fixed top-4 right-4 bg-amber-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 flex items-center gap-2';
                indicator.innerHTML = `
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                    <span>Offline Mode</span>
                `;
            }
        }
    }

    showNotification(message, type = 'info') {
        // Simple notification system
        const notification = document.createElement('div');
        notification.className = `fixed bottom-4 right-4 px-4 py-2 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'warning' ? 'bg-amber-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    async savePendingOrder(orderData) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['pending_orders'], 'readwrite');
            const store = transaction.objectStore('pending_orders');

            // Check if order with same order_number already exists
            const index = store.index('timestamp');
            const request = index.openCursor(null, 'prev');
            
            request.onsuccess = (event) => {
                const cursor = event.target.result;
                if (cursor) {
                    const existingOrder = cursor.value;
                    if (existingOrder.order_number === orderData.order_number) {
                        reject(new Error('Order with this number already exists'));
                        return;
                    }
                    cursor.continue();
                } else {
                    // No duplicate found, add the order
                    const order = {
                        ...orderData,
                        timestamp: new Date().toISOString(),
                        synced: false
                    };

                    const addRequest = store.add(order);
                    addRequest.onsuccess = () => resolve(addRequest.result);
                    addRequest.onerror = () => reject(addRequest.error);
                }
            };

            request.onerror = () => {
                // If index doesn't exist, just add without checking
                const order = {
                    ...orderData,
                    timestamp: new Date().toISOString(),
                    synced: false
                };

                const addRequest = store.add(order);
                addRequest.onsuccess = () => resolve(addRequest.result);
                addRequest.onerror = () => reject(addRequest.error);
            };
        });
    }

    async savePendingMovement(movementData) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['pending_movements'], 'readwrite');
            const store = transaction.objectStore('pending_movements');

            const movement = {
                ...movementData,
                timestamp: new Date().toISOString(),
                synced: false
            };

            const request = store.add(movement);
            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    async loadPendingOperations() {
        // Load pending orders
        const orders = await this.getAllFromStore('pending_orders');
        const movements = await this.getAllFromStore('pending_movements');
        
        this.queue = {
            orders: orders.filter(o => !o.synced),
            movements: movements.filter(m => !m.synced)
        };

        this.updateQueueCount();
    }

    async getAllFromStore(storeName) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([storeName], 'readonly');
            const store = transaction.objectStore(storeName);
            const request = store.getAll();

            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    async getOrderById(storeName, id) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([storeName], 'readonly');
            const store = transaction.objectStore(storeName);
            const request = store.get(id);

            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    updateQueueCount() {
        const countElement = document.getElementById('pending-sync-count');
        if (countElement) {
            const total = (this.queue.orders?.length || 0) + (this.queue.movements?.length || 0);
            countElement.textContent = total;
        }
    }

    async syncPendingOperations() {
        if (!this.isOnline) return;

        try {
            // Sync pending orders
            for (const order of this.queue.orders) {
                await this.syncOrder(order);
            }

            // Sync pending movements
            for (const movement of this.queue.movements) {
                await this.syncMovement(movement);
            }

            this.showNotification('Sync completed successfully!', 'success');
            this.queue = { orders: [], movements: [] };
            this.updateQueueCount();

        } catch (error) {
            console.error('Sync failed:', error);
            this.showNotification('Sync failed. Will retry later.', 'error');
        }
    }

    async syncOrder(order) {
        // Send order to server
        const response = await fetch('/offline-reconciliation/sync-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify(order)
        });

        if (response.ok) {
            // Mark as synced in IndexedDB
            await this.markAsSynced('pending_orders', order.id);
        }
    }

    async syncMovement(movement) {
        // Send movement to server
        const response = await fetch('/offline-reconciliation/sync-movement', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify(movement)
        });

        if (response.ok) {
            // Mark as synced in IndexedDB
            await this.markAsSynced('pending_movements', movement.id);
        }
    }

    async markAsSynced(storeName, id) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([storeName], 'readwrite');
            const store = transaction.objectStore(storeName);
            const request = store.get(id);

            request.onsuccess = () => {
                const data = request.result;
                data.synced = true;
                const updateRequest = store.put(data);
                updateRequest.onsuccess = () => resolve();
                updateRequest.onerror = () => reject(updateRequest.error);
            };
            request.onerror = () => reject(request.error);
        });
    }

    getPendingOrders() {
        return this.queue.orders || [];
    }

    getPendingMovements() {
        return this.queue.movements || [];
    }

    isOffline() {
        return !this.isOnline;
    }
}

// Initialize offline manager
const offlineManager = new OfflineManager();
