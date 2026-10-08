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
        try {
            // Initialize IndexedDB
            await this.initIndexedDB();
            
            // Setup event listeners for online/offline status
            window.addEventListener('online', () => this.handleOnline());
            window.addEventListener('offline', () => this.handleOffline());
            
            // Clean up any orders that are already synced
            if (this.isOnline) {
                await this.cleanupSyncedOrders();
            }

            // Load pending operations from IndexedDB
            await this.loadPendingOperations();
            
            // Update UI status, sidebar, and notification bell
            this.updateStatusIndicator();
            await this.updateOfflineReconSidebar();
            await this.updatePendingOfflineSyncAlert();

            // Remind admin upon re-login / page load if pending local orders exist
            const pendingCount = Array.isArray(this.queue?.orders) ? this.queue.orders.length : 0;
            if (pendingCount > 0) {
                const isDashboard = window.location.pathname.includes('/dashboard') || window.location.pathname === '/';
                const lastShown = parseInt(sessionStorage.getItem('kcc_offline_po_reminder_last_ts') || '0', 10);
                const now = Date.now();
                if (isDashboard || (now - lastShown > 120000)) {
                    sessionStorage.setItem('kcc_offline_po_reminder_last_ts', String(now));
                    setTimeout(() => {
                        this.showNotification(`🔔 Paalala: May ${pendingCount} offline purchase order(s) na naka-save locally. I-export at i-import ito sa Offline Reconciliation para ma-proceed sa supplier.`, 'warning');
                    }, 800);
                }
            }
        } catch (err) {
            console.error('OfflineManager init error:', err);
        }
    }

    async cleanupSyncedOrders() {
        try {
            if (!this.db || !this.db.objectStoreNames.contains('pending_orders')) return;
            const orders = await this.getAllFromStore('pending_orders');
            const synced = orders.filter(o => o.synced === true);
            if (synced.length > 0) {
                const tx = this.db.transaction(['pending_orders'], 'readwrite');
                const store = tx.objectStore('pending_orders');
                for (const item of synced) {
                    if (item.id !== undefined) store.delete(item.id);
                }
            }
        } catch (e) {
            console.warn('cleanupSyncedOrders warning:', e);
        }
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
        date.setDate(date.getDate() + days);
        return date.toISOString().split('T')[0];
    }

    async handleOnline() {
        this.isOnline = true;
        this.updateStatusIndicator();
        await this.cleanupSyncedOrders();
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

        // Strictly hide offline recon module when online with 0 local pending orders.
        // It will only be shown if currently offline OR when there are pending local orders (count > 0).
        if (isOffline || count > 0) {
            group.style.display = 'block';
        } else {
            group.style.display = 'none';
        }
    }

    async cleanupSyncedOrders() {
        if (!this.db || !this.isOnline) return [];

        try {
            const rawPending = await this.getAllFromStore('pending_orders');
            if (!rawPending || rawPending.length === 0) return [];

            const orderNumbers = rawPending
                .map(o => o.order_number || o.po_number)
                .filter(Boolean);

            if (orderNumbers.length === 0) return [];

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const response = await fetch('/offline-reconciliation/check-synced-orders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ order_numbers: orderNumbers })
            });

            if (!response.ok) return [];

            const data = await response.json();
            const syncedNumbers = Array.isArray(data.synced_order_numbers) ? data.synced_order_numbers : [];

            if (syncedNumbers.length > 0) {
                const syncedSet = new Set(syncedNumbers);
                const tx = this.db.transaction(['pending_orders'], 'readwrite');
                const store = tx.objectStore('pending_orders');

                for (const order of rawPending) {
                    const poNum = order.order_number || order.po_number;
                    if (poNum && syncedSet.has(poNum)) {
                        store.delete(order.id);
                    }
                }

                await new Promise((resolve) => {
                    tx.oncomplete = () => resolve();
                    tx.onerror = () => resolve();
                });
            }

            return syncedNumbers;
        } catch (e) {
            console.warn('Auto cleanup of synced orders skipped:', e);
            return [];
        }
    }

    injectOfflineNotificationIntoBell(serverUnreadCount = null) {
        let pendingOrders = [];
        try {
            pendingOrders = (this.queue?.orders || []).filter(o => !o.synced);
        } catch (e) {
            pendingOrders = [];
        }

        const count = pendingOrders.length;
        const list = document.getElementById('notification-list') || document.getElementById('headerNotificationList');
        const empty = document.getElementById('notification-empty');
        const bellBadge = document.getElementById('notification-badge') || document.getElementById('headerNotificationBadge');
        const centerBadge = document.getElementById('notif-center-unread-badge');

        // Always remove old offline notification elements first to prevent duplication
        document.querySelectorAll('#offline-notif-bell-item').forEach(el => el.remove());
        document.querySelectorAll('#modal-offline-notif-item').forEach(el => el.remove());

        if (count > 0) {
            if (list) {
                if (empty) empty.classList.add('hidden');

                const poNumbers = pendingOrders.slice(0, 3).map(o => o.order_number || o.po_number || 'PO').join(', ');
                const moreCount = count > 3 ? ` +${count - 3} more` : '';

                const item = document.createElement('div');
                item.id = 'offline-notif-bell-item';
                item.className = 'border-b border-amber-200 bg-amber-50/95 hover:bg-amber-100/90 p-3.5 transition-colors';
                item.innerHTML = `
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-[10px] bg-amber-100 border border-amber-300 text-amber-800 shadow-2xs">
                            <svg class="w-4 h-4 text-amber-700 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-900 font-mono flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 inline-block animate-ping"></span>
                                    OFFLINE ORDERS PENDING SYNC
                                </span>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-amber-200 text-amber-950">${count} order${count > 1 ? 's' : ''}</span>
                            </div>
                            <p class="text-xs font-semibold text-slate-900 leading-snug mb-1">
                                May <strong>${count} locally saved purchase order(s)</strong> ka na kailangan i-export at i-import sa Offline Reconciliation para ma-proceed sa supplier.
                            </p>
                            <p class="text-[11px] font-mono text-amber-800 truncate mb-2">
                                ${poNumbers}${moreCount}
                            </p>
                            <div class="flex items-center justify-between pt-1">
                                <span class="text-[10px] text-slate-500">Saved in browser storage</span>
                                <a href="/offline-reconciliation/export" class="inline-flex items-center gap-1 rounded-lg bg-[#0f172a] hover:bg-slate-800 px-3 py-1.5 text-[11px] font-bold text-white shadow-xs transition cursor-pointer">
                                    <span>Export & Proceed</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                `;

                list.insertBefore(item, list.firstChild);
            }

            // Also inject into "View All Notifications" modal list if present in DOM
            const modalList = document.getElementById('modal-notification-list');
            if (modalList) {
                const modalItem = document.createElement('div');
                modalItem.id = 'modal-offline-notif-item';
                modalItem.className = 'rounded-2xl border border-amber-200 bg-amber-50/95 p-4 mb-3';
                modalItem.innerHTML = `
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-100 border border-amber-300 flex items-center justify-center text-amber-800 shrink-0">
                            <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-amber-900 font-mono">Offline Purchase Orders Awaiting Sync</h4>
                                <span class="text-xs font-bold text-amber-900 bg-amber-200 px-2 py-0.5 rounded-full">${count} pending</span>
                            </div>
                            <p class="text-sm font-semibold text-slate-900 mb-2">May ${count} locally saved purchase order(s) na kailangan i-export at i-import sa Offline Reconciliation para ma-proceed sa supplier.</p>
                            <a href="/offline-reconciliation/export" class="inline-flex items-center gap-1.5 rounded-xl bg-[#0f172a] text-white px-4 py-2 text-xs font-bold hover:bg-slate-800 transition shadow-xs">
                                <span>Export & Sync Locally Saved Orders</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                `;
                modalList.insertBefore(modalItem, modalList.firstChild);
            }

            const serverCount = typeof serverUnreadCount === 'number' 
                ? serverUnreadCount 
                : (parseInt(bellBadge?.dataset?.serverCount) || 0);

            const total = serverCount + count;

            if (bellBadge) {
                bellBadge.textContent = total > 9 ? '9+' : total;
                bellBadge.classList.remove('hidden');
                bellBadge.style.display = 'inline-flex';
            }
            if (centerBadge) {
                centerBadge.textContent = total;
                centerBadge.classList.remove('hidden');
                centerBadge.style.display = 'inline-flex';
            }
        }
    }

    async updatePendingOfflineSyncAlert() {
        this.injectOfflineNotificationIntoBell();
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
        if (!this.db) {
            try {
                await this.initIndexedDB();
            } catch (e) {
                console.warn('DB init error in savePendingOrder:', e);
            }
        }

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
                this.loadPendingOperations().catch(e => console.warn(e)).finally(async () => {
                    try {
                        await this.updateOfflineReconSidebar();
                    } catch (e) {}
                    try {
                        await this.updatePendingOfflineSyncAlert();
                    } catch (e) {}
                    resolve(addRequest.result);
                });
            };
            addRequest.onerror = () => reject(addRequest.error);
        });
    }

    updateQueueCount() {
        const count = Array.isArray(this.queue?.orders) ? this.queue.orders.length : 0;
        const badge = document.getElementById('sidebar-offline-pending-badge');
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
    }

    async loadPendingOperations() {
        if (this.isOnline) {
            await this.cleanupSyncedOrders().catch(e => console.warn(e));
        }

        const orders = await this.getAllFromStore('pending_orders');
        
        this.queue = {
            orders: orders.filter(o => !o.synced)
        };

        this.updateQueueCount();
        await this.updateOfflineReconSidebar().catch(e => console.warn(e));
        this.injectOfflineNotificationIntoBell();
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
