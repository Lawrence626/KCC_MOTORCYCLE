/**
 * Offline Manager - Handles offline detection, storage, and sync
 */
class OfflineManager {
    constructor() {
        this.isOnline = navigator.onLine;
        this.pendingTransactions = [];
        this.pendingPurchaseOrders = [];
        this.pendingInventoryMovements = [];
        this.storageKey = 'kcc_offline_data';
        
        this.init();
    }

    init() {
        // Load existing offline data
        this.loadOfflineData();
        
        // Setup event listeners
        window.addEventListener('online', () => this.handleOnline());
        window.addEventListener('offline', () => this.handleOffline());
        
        // Initial status check
        this.updateOfflineIndicator();
        
        // Periodic sync check (every 30 seconds)
        setInterval(() => this.checkSyncStatus(), 30000);
    }

    handleOnline() {
        this.isOnline = true;
        console.log('Connection restored');
        this.updateOfflineIndicator();
        this.showNotification('Back Online - Syncing data...', 'success');
        
        // Attempt to sync pending data
        this.syncPendingData();
    }

    handleOffline() {
        this.isOnline = false;
        console.log('Connection lost - Offline mode activated');
        this.updateOfflineIndicator();
        this.showNotification('Offline Mode - Data will be saved locally', 'warning');
    }

    updateOfflineIndicator() {
        const indicator = document.getElementById('offline-indicator');
        if (indicator) {
            if (this.isOnline) {
                indicator.classList.add('hidden');
            } else {
                indicator.classList.remove('hidden');
                indicator.innerHTML = `
                    <div class="flex items-center gap-2 px-3 py-2 bg-amber-100 border border-amber-300 rounded-lg">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L2.168 5.636"/>
                        </svg>
                        <span class="text-xs font-medium text-amber-800">Offline Mode</span>
                    </div>
                `;
            }
        }
    }

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'warning' ? 'bg-amber-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    saveTransactionLocally(transaction) {
        const pendingTransaction = {
            ...transaction,
            id: 'local_' + Date.now(),
            sync_status: 'pending_sync',
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString()
        };
        
        this.pendingTransactions.push(pendingTransaction);
        this.saveOfflineData();
        console.log('Transaction saved locally:', pendingTransaction);
        
        return pendingTransaction.id;
    }

    savePurchaseOrderLocally(order) {
        const pendingOrder = {
            ...order,
            id: 'local_po_' + Date.now(),
            order_number: order.order_number || 'PO-' + Date.now(),
            sync_status: 'pending_sync',
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString()
        };
        
        this.pendingPurchaseOrders.push(pendingOrder);
        this.saveOfflineData();
        console.log('Purchase order saved locally:', pendingOrder);
        
        return pendingOrder.id;
    }

    saveInventoryMovementLocally(movement) {
        const pendingMovement = {
            ...movement,
            id: 'local_mv_' + Date.now(),
            sync_status: 'pending_sync',
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString()
        };
        
        this.pendingInventoryMovements.push(pendingMovement);
        this.saveOfflineData();
        console.log('Inventory movement saved locally:', pendingMovement);
        
        return pendingMovement.id;
    }

    saveOfflineData() {
        const offlineData = {
            pendingTransactions: this.pendingTransactions,
            pendingPurchaseOrders: this.pendingPurchaseOrders,
            pendingInventoryMovements: this.pendingInventoryMovements,
            lastUpdated: new Date().toISOString()
        };
        
        localStorage.setItem(this.storageKey, JSON.stringify(offlineData));
        this.updatePendingCounts();
    }

    loadOfflineData() {
        const storedData = localStorage.getItem(this.storageKey);
        if (storedData) {
            try {
                const offlineData = JSON.parse(storedData);
                this.pendingTransactions = offlineData.pendingTransactions || [];
                this.pendingPurchaseOrders = offlineData.pendingPurchaseOrders || [];
                this.pendingInventoryMovements = offlineData.pendingInventoryMovements || [];
                console.log('Loaded offline data:', offlineData);
                this.updatePendingCounts();
            } catch (e) {
                console.error('Error loading offline data:', e);
            }
        }
    }

    updatePendingCounts() {
        const totalPending = this.pendingTransactions.length + 
                           this.pendingPurchaseOrders.length + 
                           this.pendingInventoryMovements.length;
        
        // Update dashboard widgets if they exist
        const pendingSyncEl = document.getElementById('pending-sync-count');
        if (pendingSyncEl) {
            pendingSyncEl.textContent = totalPending;
        }
    }

    async syncPendingData() {
        if (!this.isOnline) {
            console.log('Cannot sync - offline');
            return;
        }

        const totalPending = this.pendingTransactions.length + 
                           this.pendingPurchaseOrders.length + 
                           this.pendingInventoryMovements.length;
        
        if (totalPending === 0) {
            console.log('No pending data to sync');
            return;
        }

        console.log('Syncing pending data...');
        this.showNotification(`Syncing ${totalPending} records...`, 'info');

        try {
            // Sync transactions
            for (const transaction of this.pendingTransactions) {
                await this.syncTransaction(transaction);
            }

            // Sync purchase orders
            for (const order of this.pendingPurchaseOrders) {
                await this.syncPurchaseOrder(order);
            }

            // Sync inventory movements
            for (const movement of this.pendingInventoryMovements) {
                await this.syncInventoryMovement(movement);
            }

            // Clear synced data
            this.pendingTransactions = [];
            this.pendingPurchaseOrders = [];
            this.pendingInventoryMovements = [];
            this.saveOfflineData();

            this.showNotification('All data synced successfully!', 'success');
            this.updatePendingCounts();

        } catch (error) {
            console.error('Sync failed:', error);
            this.showNotification('Sync failed - data saved locally', 'error');
        }
    }

    async syncTransaction(transaction) {
        try {
            const response = await fetch('/offline-reconciliation/sync-order', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify(transaction)
            });

            if (response.ok) {
                console.log('Transaction synced:', transaction.id);
            } else {
                throw new Error('Failed to sync transaction');
            }
        } catch (error) {
            console.error('Error syncing transaction:', error);
            throw error;
        }
    }

    async syncPurchaseOrder(order) {
        try {
            const response = await fetch('/offline-reconciliation/sync-order', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify(order)
            });

            if (response.ok) {
                console.log('Purchase order synced:', order.id);
            } else {
                throw new Error('Failed to sync purchase order');
            }
        } catch (error) {
            console.error('Error syncing purchase order:', error);
            throw error;
        }
    }

    async syncInventoryMovement(movement) {
        try {
            const response = await fetch('/offline-reconciliation/sync-movement', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify(movement)
            });

            if (response.ok) {
                console.log('Inventory movement synced:', movement.id);
            } else {
                throw new Error('Failed to sync inventory movement');
            }
        } catch (error) {
            console.error('Error syncing inventory movement:', error);
            throw error;
        }
    }

    checkSyncStatus() {
        if (this.isOnline) {
            const totalPending = this.pendingTransactions.length + 
                               this.pendingPurchaseOrders.length + 
                               this.pendingInventoryMovements.length;
            
            if (totalPending > 0) {
                console.log(`Found ${totalPending} pending records - attempting sync`);
                this.syncPendingData();
            }
        }
    }

    getPendingCount() {
        return this.pendingTransactions.length + 
               this.pendingPurchaseOrders.length + 
               this.pendingInventoryMovements.length;
    }

    // Manual sync trigger
    manualSync() {
        if (this.isOnline) {
            this.syncPendingData();
        } else {
            this.showNotification('Cannot sync - offline mode', 'warning');
        }
    }

    // Clear all offline data (for testing)
    clearOfflineData() {
        if (confirm('Are you sure you want to clear all offline data? This cannot be undone.')) {
            this.pendingTransactions = [];
            this.pendingPurchaseOrders = [];
            this.pendingInventoryMovements = [];
            this.saveOfflineData();
            this.showNotification('Offline data cleared', 'info');
        }
    }
}

// Initialize offline manager when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (!window.offlineManager) {
        window.offlineManager = new OfflineManager();
    }
});
