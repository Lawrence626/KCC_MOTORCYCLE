<x-layouts.app :title="__('Item Disposal')">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Item Disposal List</h1>
                <p class="text-xs text-slate-500 mt-1">Identify items for removal to reduce business waste. Track disposal status and reasons.</p>
            </div>
            <button id="openAddDisposal" class="px-4 py-2 rounded-lg bg-gradient-to-r from-cyan-600 to-cyan-500 text-white font-semibold">+ Add Item for Disposal</button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-lg p-4">
                <p class="text-xs text-slate-500 font-medium">Total Items</p>
                <p id="totalItems" class="text-2xl font-bold text-slate-900 mt-1">0</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-4">
                <p class="text-xs text-slate-500 font-medium">Pending Review</p>
                <p id="pendingItems" class="text-2xl font-bold text-yellow-600 mt-1">0</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-4">
                <p class="text-xs text-slate-500 font-medium">Approved</p>
                <p id="approvedItems" class="text-2xl font-bold text-cyan-600 mt-1">0</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-4">
                <p class="text-xs text-slate-500 font-medium">Disposed</p>
                <p id="disposedItems" class="text-2xl font-bold text-green-600 mt-1">0</p>
            </div>
        </div>

        <!-- Disposal Items Table -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-slate-900">Item Name</th>
                            <th class="px-6 py-3 text-left font-semibold text-slate-900">Reason</th>
                            <th class="px-6 py-3 text-left font-semibold text-slate-900">Quantity</th>
                            <th class="px-6 py-3 text-left font-semibold text-slate-900">Status</th>
                            <th class="px-6 py-3 text-left font-semibold text-slate-900">Date Identified</th>
                            <th class="px-6 py-3 text-center font-semibold text-slate-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="disposalTableBody" class="divide-y divide-slate-200">
                        <!-- Items will be loaded here -->
                        <tr class="hover:bg-slate-50">
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">No items identified for disposal yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Disposal Modal -->
    <div id="disposalModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div id="disposalOverlay" class="absolute inset-0 bg-black/40"></div>

        <div class="relative w-full max-w-2xl bg-white rounded-xl shadow-xl border border-slate-200 p-6 z-10">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h3 id="modalTitle" class="text-lg font-semibold text-slate-900">Add Item for Disposal</h3>
                    <p class="text-xs text-slate-500">Identify items to be removed and track their disposal status</p>
                </div>
                <button id="closeDisposalModal" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form id="disposalForm">
                <input type="hidden" id="disposalId" />
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-slate-500 font-medium">Item Name</label>
                        <input type="text" id="itemName" placeholder="e.g., Damaged Clutch Cable" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm mt-1" required />
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 font-medium">Quantity</label>
                        <input type="number" id="itemQuantity" placeholder="1" min="1" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm mt-1" required />
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs text-slate-500 font-medium">Reason for Disposal</label>
                        <select id="disposalReason" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm mt-1" required>
                            <option value="">Select reason</option>
                            <option value="Damaged/Defective">Damaged/Defective</option>
                            <option value="Expired">Expired</option>
                            <option value="Obsolete">Obsolete</option>
                            <option value="Excess Stock">Excess Stock</option>
                            <option value="Poor Condition">Poor Condition</option>
                            <option value="Incompatible">Incompatible</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs text-slate-500 font-medium">Description/Notes</label>
                        <textarea id="itemNotes" placeholder="Additional details about why this item needs disposal..." rows="3" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm mt-1"></textarea>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 font-medium">Status</label>
                        <select id="itemStatus" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm mt-1" required>
                            <option value="Pending">Pending Review</option>
                            <option value="Approved">Approved</option>
                            <option value="Disposed">Disposed</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 font-medium">Date Identified</label>
                        <input type="date" id="dateIdentified" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm mt-1" required />
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-gradient-to-r from-cyan-600 to-cyan-500 text-white font-semibold">Save Item</button>
                    <button type="button" id="cancelDisposalModal" class="px-4 py-2 rounded-lg border border-slate-200 text-sm text-slate-700">Cancel</button>
                    <button type="button" id="deleteDisposalBtn" class="ml-auto px-4 py-2 rounded-lg border border-red-200 text-sm text-red-600 hover:bg-red-50 hidden">Delete Item</button>
                </div>
            </form>
        </div>
    </div>
    @vite('resources/js/item-disposal.js')
</x-layouts.app>
