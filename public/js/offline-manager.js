// Offline Manager - Handles offline detection, local storage, and synchronization for Purchase Orders
class OfflineManager {
    constructor() {
        this.isOnline = typeof navigator !== 'undefined' ? navigator.onLine : true;
        this.dbName = 'KCC_OfflineDB';
        this.dbVersion = 4;
        this.db = null;
        this.queue = {
            orders: []
        };
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
        
        // Update UI status, sidebar, and alert banner
        this.updateStatusIndicator();
        await this.updateOfflineReconSidebar();
        await this.updatePendingOfflineSyncAlert();
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

                // Delete deprecated pending_movements store if it exists
                if (db.objectStoreNames.contains('pending_movements')) {
                    db.deleteObjectStore('pending_movements');
                }

                // Create stores for offline operations
                if (!db.objectStoreNames.contains('pending_orders')) {
                    const orderStore = db.createObjectStore('pending_orders', { keyPath: 'id', autoIncrement: true });
                    orderStore.createIndex('timestamp', 'timestamp');
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

                if (!db.objectStoreNames.contains('master_products')) {
                    db.createObjectStore('master_products', { keyPath: 'id' });
                }

                if (!db.objectStoreNames.contains('master_suppliers')) {
                    db.createObjectStore('master_suppliers', { keyPath: 'id' });
                }
            };
        });
    }

    calculateWorkingDays(days = 7, startDate = new Date()) {
        let date = new Date(startDate);
        let workingDays = 0;
        while (workingDays < days) {
            date.setDate(date.getDate() + 1);
            const dayOfWeek = date.getDay(); // 0 is Sunday, 6 is Saturday
            if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                workingDays++;
            }
        }
        return date.toISOString().split('T')[0];
    }

    async handleOnline() {
        this.isOnline = true;
        this.updateStatusIndicator();
        await this.loadPendingOperations();
        await this.updateOfflineReconSidebar();
        await this.updatePendingOfflineSyncAlert();

        const pendingOrders = await this.getPendingOrders();
        if (pendingOrders && pendingOrders.length > 0) {
            this.showNotification(`Internet connection restored! You have ${pendingOrders.length} locally saved order(s). Please export and import them for final synchronization before placing new orders.`, 'warning');
        } else {
            this.showNotification('You are back online.', 'success');
        }
    }

    async handleOffline() {
        this.isOnline = false;
        this.updateStatusIndicator();
        await this.updateOfflineReconSidebar();
        await this.updatePendingOfflineSyncAlert();
        this.showNotification('You are currently offline. Orders will be saved locally to your device.', 'warning');
    }

    updateStatusIndicator() {
        const isOnline = (typeof navigator !== 'undefined' && typeof navigator.onLine === 'boolean') ? navigator.onLine : true;
        this.isOnline = isOnline;

        const indicator = document.getElementById('offline-indicator');
        if (indicator) {
            if (isOnline) {
                indicator.className = 'hidden';
            } else {
                indicator.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-100 border border-amber-300 rounded-[10px] text-xs font-semibold text-amber-900 shadow-sm';
                indicator.innerHTML = `
                    <svg class="w-4 h-4 text-amber-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                    <span>Offline Mode</span>
                `;
            }
        }

        const banner = document.getElementById('offline-banner');
        if (banner) {
            if (isOnline) {
                banner.classList.add('hidden');
            } else {
                banner.classList.remove('hidden');
            }
        }
    }

    async updateOfflineReconSidebar() {
        const group = document.getElementById('sidebar-offline-recon-group');
        const badge = document.getElementById('sidebar-offline-pending-badge');
        if (!group) return;

        const isOffline = (typeof navigator !== 'undefined' && typeof navigator.onLine === 'boolean') ? !navigator.onLine : false;
        const currentPath = (typeof window !== 'undefined' && window.location) ? window.location.pathname : '';
        const isOfflineRoute = currentPath.includes('offline-reconciliation') || currentPath.includes('offline');
        
        let count = 0;
        try {
            const pendingOrders = await this.getPendingOrders();
            count = Array.isArray(pendingOrders) ? pendingOrders.length : 0;
        } catch (e) {
            count = this.queue?.orders?.length || 0;
        }

        if (badge) {
            if (count > 0) {
                badge.textContent = count;
                badge.classList.remove('hidden');
                badge.classList.add('inline-flex');
            } else {
                badge.classList.add('hidden');
                badge.classList.remove('inline-flex');
            }
        }

        // Hide offline recon module when online with 0 local pending orders,
        // but keep visible when offline OR when pending local orders exist OR when actively browsing offline pages
        if (isOffline || count > 0 || isOfflineRoute) {
            group.style.display = 'block';
        } else {
            group.style.display = 'none';
        }
    }

    async updatePendingOfflineSyncAlert() {
        const alertContainer = document.getElementById('pending-offline-sync-alert');
        const countBadge = document.getElementById('pending-offline-orders-count-badge');
        const previewEl = document.getElementById('pending-offline-orders-preview');

        const isOnline = (typeof navigator !== 'undefined' && typeof navigator.onLine === 'boolean') ? navigator.onLine : true;
        let pendingOrders = [];
        try {
            pendingOrders = await this.getPendingOrders();
        } catch(e) {
            pendingOrders = this.queue?.orders || [];
        }
        const count = Array.isArray(pendingOrders) ? pendingOrders.length : 0;

        if (!alertContainer) return;

        if (isOnline && count > 0) {
            alertContainer.classList.remove('hidden');
            if (countBadge) {
                countBadge.textContent = `${count} Local Order${count === 1 ? '' : 's'} Pending`;
            }
            if (previewEl) {
                const poList = pendingOrders.slice(0, 4).map(o => {
                    const num = o.order_number || o.po_number || 'PO';
                    const supp = o.supplier_name ? `(${o.supplier_name})` : '';
                    const itemsCount = Array.isArray(o.items) ? `${o.items.length} item(s)` : '';
                    return `<span class="inline-flex items-center gap-1 bg-amber-100 text-amber-950 px-2 py-0.5 rounded-[6px] border border-amber-300 font-mono text-[10px]"><strong>${num}</strong> ${supp} ${itemsCount}</span>`;
                }).join(' ');
                const more = count > 4 ? `<span class="text-amber-800 text-[10px] font-bold">+${count - 4} more</span>` : '';
                previewEl.innerHTML = `<span><strong>Local Orders:</strong> ${poList} ${more}</span>`;
            }
        } else {
            alertContainer.classList.add('hidden');
        }
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed bottom-4 right-4 px-4 py-3 rounded-[12px] shadow-2xl z-[99999] text-xs font-semibold flex items-center gap-2.5 max-w-md ${
            type === 'success' ? 'bg-green-700 text-white' :
            type === 'warning' ? 'bg-amber-500 text-slate-950' :
            type === 'error' ? 'bg-red-600 text-white' :
            'bg-[#0f172a] text-white'
        }`;
        notification.innerHTML = `
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>${message}</span>
        `;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(10px)';
            setTimeout(() => notification.remove(), 400);
        }, 4500);
    }

    async cacheMasterData(products = [], suppliers = []) {
        if (!this.db) return;

        try {
            if (Array.isArray(products) && products.length > 0) {
                const txP = this.db.transaction(['master_products'], 'readwrite');
                const storeP = txP.objectStore('master_products');
                for (const p of products) {
                    storeP.put(p);
                }
            }

            if (Array.isArray(suppliers) && suppliers.length > 0) {
                const txS = this.db.transaction(['master_suppliers'], 'readwrite');
                const storeS = txS.objectStore('master_suppliers');
                for (const s of suppliers) {
                    storeS.put(s);
                }
            }
        } catch (e) {
            console.warn('Could not cache master data to IndexedDB:', e);
        }
    }

    async getCachedProducts() {
        return this.getAllFromStore('master_products');
    }

    async getCachedSuppliers() {
        return this.getAllFromStore('master_suppliers');
    }

    async savePendingOrder(orderData) {
        return new Promise((resolve, reject) => {
            if (!this.db) {
                reject(new Error('IndexedDB not initialized'));
                return;
            }

            const transaction = this.db.transaction(['pending_orders'], 'readwrite');
            const store = transaction.objectStore('pending_orders');

            const expectedDelivery = orderData.expected_delivery_date || this.calculateWorkingDays(7);

            const order = {
                ...orderData,
                expected_delivery_date: expectedDelivery,
                timestamp: orderData.timestamp || new Date().toISOString(),
                synced: false
            };

            const addRequest = store.add(order);
            addRequest.onsuccess = () => {
                this.loadPendingOperations().then(async () => {
                    await this.updateOfflineReconSidebar();
                    await this.updatePendingOfflineSyncAlert();
                    resolve(addRequest.result);
                });
            };
            addRequest.onerror = () => reject(addRequest.error);
        });
    }

    async loadPendingOperations() {
        const orders = await this.getAllFromStore('pending_orders');
        
        this.queue = {
            orders: orders.filter(o => !o.synced)
        };

        this.updateQueueCount();
        await this.updateOfflineReconSidebar();
        await this.updatePendingOfflineSyncAlert();
    }

    async getAllFromStore(storeName) {
        return new Promise((resolve, reject) => {
            if (!this.db) {
                resolve([]);
                return;
            }

            if (!this.db.objectStoreNames.contains(storeName)) {
                resolve([]);
                return;
            }

            const transaction = this.db.transaction([storeName], 'readonly');
            const store = transaction.objectStore(storeName);
            const request = store.getAll();

            request.onsuccess = () => resolve(request.result || []);
            request.onerror = () => resolve([]);
        });
    }

    async getOrderById(storeName, id) {
        return new Promise((resolve, reject) => {
            if (!this.db || !this.db.objectStoreNames.contains(storeName)) {
                reject(new Error(`Store ${storeName} not available`));
                return;
            }

            const transaction = this.db.transaction([storeName], 'readonly');
            const store = transaction.objectStore(storeName);
            const numKey = (!isNaN(id) && id !== '' && id !== null) ? Number(id) : id;
            const request = store.get(numKey);

            request.onsuccess = () => {
                if (request.result !== undefined) {
                    resolve(request.result);
                } else if (numKey !== id) {
                    const fallbackReq = store.get(id);
                    fallbackReq.onsuccess = () => resolve(fallbackReq.result);
                    fallbackReq.onerror = () => reject(fallbackReq.error);
                } else {
                    resolve(undefined);
                }
            };
            request.onerror = () => reject(request.error);
        });
    }

    async archiveOrder(id) {
        return new Promise(async (resolve, reject) => {
            if (!this.db) {
                reject(new Error('IndexedDB not initialized'));
                return;
            }

            try {
                const order = await this.getOrderById('pending_orders', id);
                if (!order) {
                    reject(new Error(`Order #${id} not found in local orders`));
                    return;
                }

                const transaction = this.db.transaction(['pending_orders', 'archived_orders'], 'readwrite');
                const pendingStore = transaction.objectStore('pending_orders');
                const archiveStore = transaction.objectStore('archived_orders');

                // Omit the pending_orders id so archived_orders creates its own autoIncrement key without conflict
                const { id: originalId, ...orderData } = order;
                const recordToArchive = {
                    ...orderData,
                    original_id: originalId,
                    archived_at: new Date().toISOString()
                };

                const addReq = archiveStore.add(recordToArchive);
                addReq.onsuccess = () => {
                    const delKey = (order.id !== undefined) ? order.id : ((!isNaN(id) && id !== '') ? Number(id) : id);
                    const delReq = pendingStore.delete(delKey);
                    delReq.onsuccess = () => {
                        this.loadPendingOperations().then(() => resolve(addReq.result));
                    };
                    delReq.onerror = () => reject(delReq.error);
                };
                addReq.onerror = () => reject(addReq.error);
                transaction.onerror = () => reject(transaction.error);
            } catch (err) {
                reject(err);
            }
        });
    }

    async restoreOrder(id) {
        return new Promise(async (resolve, reject) => {
            if (!this.db) {
                reject(new Error('IndexedDB not initialized'));
                return;
            }

            try {
                const order = await this.getOrderById('archived_orders', id);
                if (!order) {
                    reject(new Error(`Archived order #${id} not found`));
                    return;
                }

                const transaction = this.db.transaction(['pending_orders', 'archived_orders'], 'readwrite');
                const pendingStore = transaction.objectStore('pending_orders');
                const archiveStore = transaction.objectStore('archived_orders');

                const { id: archiveId, archived_at, ...orderData } = order;
                const recordToRestore = {
                    ...orderData,
                    restored_at: new Date().toISOString()
                };

                const addReq = pendingStore.add(recordToRestore);
                addReq.onsuccess = () => {
                    const delKey = (order.id !== undefined) ? order.id : ((!isNaN(id) && id !== '') ? Number(id) : id);
                    const delReq = archiveStore.delete(delKey);
                    delReq.onsuccess = () => {
                        this.loadPendingOperations().then(() => resolve(addReq.result));
                    };
                    delReq.onerror = () => reject(delReq.error);
                };
                addReq.onerror = () => reject(addReq.error);
                transaction.onerror = () => reject(transaction.error);
            } catch (err) {
                reject(err);
            }
        });
    }

    async deleteOrder(storeName, id) {
        return new Promise((resolve, reject) => {
            if (!this.db || !this.db.objectStoreNames.contains(storeName)) {
                reject(new Error(`Store ${storeName} not available`));
                return;
            }

            const transaction = this.db.transaction([storeName], 'readwrite');
            const store = transaction.objectStore(storeName);
            const key = (!isNaN(id) && id !== '' && id !== null) ? Number(id) : id;
            const request = store.delete(key);

            request.onsuccess = () => {
                this.loadPendingOperations().then(() => resolve());
            };
            request.onerror = () => reject(request.error);
        });
    }

    updateQueueCount() {
        const countElement = document.getElementById('pending-sync-count');
        if (countElement) {
            const total = this.queue.orders?.length || 0;
            countElement.textContent = total;
        }
    }

    async syncPendingOperations() {
        if (!this.isOnline) return;

        try {
            await this.loadPendingOperations();

            for (const order of this.queue.orders) {
                await this.syncOrder(order);
            }

            await this.loadPendingOperations();
        } catch (error) {
            console.error('Sync error:', error);
        }
    }

    async syncOrder(order) {
        const response = await fetch('/offline-reconciliation/sync-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify(order)
        });

        if (response.ok) {
            await this.markAsSynced('pending_orders', order.id);
        }
    }

    async markAsSynced(storeName, id) {
        return new Promise((resolve, reject) => {
            if (!this.db) return resolve();
            const transaction = this.db.transaction([storeName], 'readwrite');
            const store = transaction.objectStore(storeName);
            const request = store.get(id);

            request.onsuccess = () => {
                const data = request.result;
                if (data) {
                    data.synced = true;
                    data.sync_status = 'synchronized';
                    const updateRequest = store.put(data);
                    updateRequest.onsuccess = () => {
                        this.loadPendingOperations().then(() => resolve());
                    };
                    updateRequest.onerror = () => reject(updateRequest.error);
                } else {
                    resolve();
                }
            };
            request.onerror = () => reject(request.error);
        });
    }

    async clearOfflineData() {
        return new Promise((resolve, reject) => {
            if (!this.db) return resolve();
            const storesToClear = ['pending_orders', 'archived_orders', 'sync_queue'].filter(
                s => this.db.objectStoreNames.contains(s)
            );
            if (storesToClear.length === 0) return resolve();

            const tx = this.db.transaction(storesToClear, 'readwrite');
            for (const storeName of storesToClear) {
                tx.objectStore(storeName).clear();
            }
            tx.oncomplete = () => {
                this.queue = { orders: [] };
                this.updateQueueCount();
                this.updateOfflineReconSidebar();
                this.updatePendingOfflineSyncAlert();
                resolve();
            };
            tx.onerror = () => reject(tx.error);
        });
    }

    exportOrdersToCsv(orders = []) {
        if (!orders || orders.length === 0) {
            alert('No local orders to export.');
            return;
        }

        const headers = [
            'Type',
            'Order Number',
            'Product ID',
            'Product Name',
            'SKU',
            'Supplier ID',
            'Supplier Name',
            'Quantity',
            'Unit Price',
            'Subtotal',
            'Total Amount',
            'Status',
            'Sync Status',
            'Notes',
            'Created At',
            'Updated At'
        ];

        const rows = [headers.join(',')];

        for (const order of orders) {
            const safeNotes = (order.notes || '').replace(/"/g, '""');
            const poNum = order.order_number || '';
            const suppId = order.supplier_id || '';
            const suppName = (order.supplier_name || '').replace(/"/g, '""');
            const totalAmt = (order.total_amount || 0).toFixed(2);
            const status = order.status || 'pending';
            const syncStatus = 'exported';
            const createdAt = order.timestamp || new Date().toISOString();

            if (Array.isArray(order.items) && order.items.length > 0) {
                for (const item of order.items) {
                    const pId = item.product_id || '';
                    const pName = (item.product_name || '').replace(/"/g, '""');
                    const pSku = (item.sku || '').replace(/"/g, '""');
                    const qty = item.quantity || 1;
                    const unitPrice = (item.unit_price || 0).toFixed(2);
                    const subtotal = (item.subtotal || (qty * item.unit_price)).toFixed(2);

                    rows.push([
                        'purchase_order',
                        `"${poNum}"`,
                        `"${pId}"`,
                        `"${pName}"`,
                        `"${pSku}"`,
                        `"${suppId}"`,
                        `"${suppName}"`,
                        qty,
                        unitPrice,
                        subtotal,
                        totalAmt,
                        `"${status}"`,
                        `"${syncStatus}"`,
                        `"${safeNotes}"`,
                        `"${createdAt}"`,
                        `"${createdAt}"`
                    ].join(','));
                }
            } else {
                rows.push([
                    'purchase_order',
                    `"${poNum}"`,
                    '',
                    '',
                    '',
                    `"${suppId}"`,
                    `"${suppName}"`,
                    '',
                    '',
                    '',
                    totalAmt,
                    `"${status}"`,
                    `"${syncStatus}"`,
                    `"${safeNotes}"`,
                    `"${createdAt}"`,
                    `"${createdAt}"`
                ].join(','));
            }
        }

        // Generate filename corresponding to PO numbers
        const poNumbers = orders.map(o => o.order_number || o.po_number || '').filter(Boolean);
        let poPart = '';
        if (poNumbers.length === 1) {
            poPart = `_${String(poNumbers[0]).replace(/[^a-zA-Z0-9_-]/g, '_')}`;
        } else if (poNumbers.length > 1 && poNumbers.length <= 3) {
            poPart = `_${poNumbers.map(p => String(p).replace(/[^a-zA-Z0-9_-]/g, '_')).join('_')}`;
        } else if (poNumbers.length > 3) {
            const first = String(poNumbers[0]).replace(/[^a-zA-Z0-9_-]/g, '_');
            const last = String(poNumbers[poNumbers.length - 1]).replace(/[^a-zA-Z0-9_-]/g, '_');
            poPart = `_${first}_to_${last}_(${poNumbers.length}_orders)`;
        } else {
            const ymd = new Date().toISOString().slice(0, 10).replace(/-/g, '_');
            poPart = `_ORDERS_${ymd}`;
        }

        const fileName = `KCC_MOTORCYCLE${poPart}.csv`;

        const csvContent = rows.join('\r\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = fileName;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        return { count: orders.length, fileName: fileName };
    }

    getPendingOrders() {
        return this.queue.orders || [];
    }

    isOffline() {
        return !this.isOnline;
    }
}

// Initialize offline manager
const offlineManager = new OfflineManager();
window.offlineManager = offlineManager;
