<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('Replacing Items')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Replacing Items'))]); ?>
    <div class="space-y-6">
        <!-- Header Section -->
         <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="pl-3 lg:pl-1">
                <h1 class="text-3xl font-bold text-slate-900">Replacing Items</h1>
                <p class="text-slate-600 text-sm mt-1">Manage returned products and issue replacements.</p>
            </div>
        </div>

        <!-- Controls Section -->
        <div class="flex items-center gap-3 w-full">
            <button onclick="openNewReplacementModal()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#105f68] text-white rounded-[10px] font-semibold text-sm hover:bg-[#0c474e] transition-all ring-1 ring-[#105f68]/10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Replacement
            </button>

            <div class="flex-1 flex items-center gap-3">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                        <input
                            id="searchInput"
                            type="text" placeholder="Search receipt no. / product"
                                class="w-full pl-10 pr-10 py-2.5 rounded-[10px] border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#105f68] focus:border-transparent"
                        />
                </div>
                <!-- Custom Dropdown -->
                <div class="relative">
                    <button type="button" onclick="toggleStatusDropdown()" class="appearance-none pl-4 pr-10 py-2.5 rounded-[10px] border border-slate-300 bg-white text-sm font-medium text-slate-700 text-center focus:outline-none focus:ring-2 focus:ring-[#105f68] flex items-center gap-2 whitespace-nowrap w-40"
                        style="background-image:url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 20 20\' fill=\'none\' stroke=\'%2338445d\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3E%3Cpath d=\'M6 8l4 4 4-4\'/%3E%3C/svg%3E'); background-repeat:no-repeat; background-position:right 0.85rem center; background-size:1.2em; line-height:1.25rem;">
                        <span id="statusLabel">Status: All</span>
                    </button>
                    <div id="statusDropdown" class="hidden absolute top-full mt-2 -right-0 w-40 bg-white border border-slate-300 rounded-lg shadow-xl z-50 p-3 space-y-1">
                        <button type="button" onclick="selectStatus('Status: All')" class="w-full px-4 py-2 text-center text-sm text-slate-700 hover:bg-slate-100 rounded-[10px]">All</button>
                        <button type="button" onclick="selectStatus('Pending')" class="w-full px-4 py-2 text-center text-sm text-slate-700 hover:bg-slate-100 rounded-[10px]">Pending</button>
                        <button type="button" onclick="selectStatus('Approved')" class="w-full px-4 py-2 text-center text-sm text-slate-700 hover:bg-slate-100 rounded-[10px]">Approved</button>
                        <button type="button" onclick="selectStatus('Completed')" class="w-full px-4 py-2 text-center text-sm text-slate-700 hover:bg-slate-100 rounded-[10px]">Completed</button>
                    </div>
                </div>
                <!-- Date Filter -->
                <div class="relative">
                    <select id="dateFilter" class="appearance-none pl-4 pr-10 py-2.5 rounded-[10px] border border-slate-300 bg-white text-sm font-medium text-slate-700 text-center focus:outline-none focus:ring-2 focus:ring-[#105f68] w-40 cursor-pointer"
                        style="background-image:url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 20 20\' fill=\'none\' stroke=\'%2338445d\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3E%3Cpath d=\'M6 8l4 4 4-4\'/%3E%3C/svg%3E'); background-repeat:no-repeat; background-position:right 0.85rem center; background-size:1.2em; line-height:1.25rem;">
                        <option value="today">Today</option>
                        <option value="this_week">This Week</option>
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden flex flex-col" style="min-height: calc(100vh - 220px);">
            <div class="overflow-x-auto flex-1">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-900">Receipt No.</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-900">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-900">Returned Item</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-slate-900">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-900">Replacement Item</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-900">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-slate-900">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                <svg class="w-12 h-12 mx-auto mb-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="font-medium">No replacements yet</p>
                                <p class="text-sm mt-1">Start by clicking "New Replacement" to create your first replacement request</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="border-t border-slate-200 px-6 py-4 flex items-center justify-between">
                <p class="text-sm text-slate-600">Showing 0 of 0 entries</p>
                <div class="flex gap-1">
                    <button class="px-3 py-1 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>← Prev</button>
                    <button class="px-3 py-1 rounded-lg bg-cyan-600 text-sm font-medium text-white">1</button>
                    <button class="px-3 py-1 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>Next →</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Process Replacement Modal -->
    <div id="processModal" class="hidden fixed inset-0 bg-slate-950/40 backdrop-blur-xl flex items-center justify-center z-50 px-4 py-6">
        <div class="bg-white/95 backdrop-blur-sm rounded-[28px] shadow-[0_30px_80px_rgba(15,23,42,0.15)] border border-slate-200/80 max-w-2xl w-full overflow-hidden">
            <div class="flex items-center justify-between px-8 py-5 border-b border-slate-200/80">
                <h2 class="text-2xl font-semibold text-slate-900">Process Replacement</h2>
                <button onclick="closeProcessModal()" class="text-slate-500 hover:text-slate-700 transition-colors p-2 rounded-full hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-8 space-y-5">
                <!-- Receipt No & Returned Item (Side by Side) -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Receipt No.</label>
                        <input id="receiptNo" type="text" readonly class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-slate-50 text-slate-600 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Returned Item</label>
                        <input id="returnedItem" type="text" readonly class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-slate-50 text-slate-600 text-sm" />
                    </div>
                </div>

                <!-- Reason -->
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Reason</label>
                    <select class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#105f68] focus:border-transparent">
                        <option>Defective Item</option>
                        <option>Wrong Item Sent</option>
                        <option>Customer Request</option>
                        <option>Quality Issue</option>
                    </select>
                </div>

                <!-- Replacement Product & Quantity (Side by Side) -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Replacement Product</label>
                        <select class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#105f68] focus:border-transparent">
                            <option>Search product...</option>
                            <option>Brembo Brake Pad</option>
                            <option>NGK Spark Plug</option>
                            <option>Motul 4T 10W40 Oil</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-900 mb-1">Quantity</label>
                        <div class="flex items-center gap-2">
                            <button class="p-2 rounded-lg border border-slate-300 hover:bg-slate-50">
                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            <input type="text" value="1" readonly class="flex-1 px-3 py-2 rounded-lg border border-slate-300 text-center text-slate-900 font-medium text-sm" />
                            <button class="p-2 rounded-lg border border-slate-300 hover:bg-slate-50">
                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="px-6 py-4 border-t border-slate-200 flex gap-3 justify-end bg-slate-50">
                <button onclick="closeProcessModal()" class="px-5 py-3 rounded-2xl border border-slate-300 bg-white text-slate-900 font-semibold text-sm hover:bg-slate-100 transition-all">Cancel</button>
                <button class="px-5 py-3 rounded-2xl bg-[#105f68] text-white font-semibold text-sm hover:bg-[#0c474e] transition-all shadow-lg shadow-[#105f68]/20">Confirm Replacement</button>
            </div>
        </div>
    </div>

    <!-- New Replacement Modal -->
    <div id="newReplacementModal" class="hidden fixed inset-0 bg-slate-950/40 backdrop-blur-xl flex items-center justify-center z-50 px-4 py-6">
        <div class="bg-white/95 backdrop-blur-sm rounded-[28px] shadow-[0_30px_100px_rgba(15,23,42,0.18)]  max-w-2xl w-full overflow-hidden">
            <div class="flex items-center justify-between px-8 py-5 border-b border-transparent bg-[#105f68] rounded-t-[28px]">
                <h2 class="text-2xl font-semibold text-white">New Replacement</h2>
                <button onclick="closeNewReplacementModal()" class="text-white transition-colors p-2 rounded-[10px] hover:bg-white/15">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-8 space-y-6">
                <!-- Receipt Number -->
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-3">Receipt No.</label>
                    <input id="newReceiptNo" type="text" placeholder="Enter receipt number" class="w-full px-5 py-3 rounded-[10px] border border-slate-200 bg-slate-50 text-slate-600 placeholder:text-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-[#105f68] focus:border-transparent shadow-sm" />
                </div>

                <!-- Returned Item -->
                <div class="relative">
                    <label class="block text-sm font-semibold text-slate-900 mb-3">Returned Item</label>
                    <input id="newReturnedItem" type="hidden" value="" />
                    <button type="button" id="returnedItemButton" onclick="toggleDropdown('returnedItemDropdown')" class="w-full px-5 py-3 rounded-[10px] border border-[#105f68]/20 bg-white text-left text-slate-700 text-sm font-semibold flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-[#105f68] focus:border-transparent ring-1 ring-[#105f68]/10 shadow-sm hover:bg-[#105f68]/5">
                        <span id="returnedItemLabel">Select returned item...</span>
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                        </svg>
                    </button>
                    <div id="returnedItemDropdown" class="hidden absolute z-50 w-full mt-2 bg-white border border-slate-200 rounded-[10px] shadow-xl p-1.5 space-y-0.5 max-h-40 overflow-y-auto">
                        <button type="button" onclick="selectDropdown('newReturnedItem', 'Brembo Brake Pad', 'returnedItemLabel', 'returnedItemDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Brembo Brake Pad</button>
                        <button type="button" onclick="selectDropdown('newReturnedItem', 'NGK Spark Plug', 'returnedItemLabel', 'returnedItemDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">NGK Spark Plug</button>
                        <button type="button" onclick="selectDropdown('newReturnedItem', 'Motul 4T 10W40 Oil', 'returnedItemLabel', 'returnedItemDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Motul 4T 10W40 Oil</button>
                    </div>
                </div>

                <!-- Reason -->
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-3">Reason</label>
                    <input id="newReason" type="hidden" value="Defective Item" />
                    <button type="button" id="newReasonButton" onclick="toggleDropdown('reasonDropdown')" class="w-full px-5 py-3 rounded-[10px] border border-[#105f68]/20 bg-white text-left text-slate-700 text-sm font-semibold flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-[#105f68] focus:border-transparent ring-1 ring-[#105f68]/10 shadow-sm hover:bg-[#105f68]/5">
                        <span id="newReasonLabel">Defective Item</span>
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                        </svg>
                    </button>
                    <div id="reasonDropdown" class="hidden absolute z-50 w-full mt-2 bg-white border border-slate-200 rounded-[10px] shadow-xl p-1.5 space-y-0.5 max-h-40 overflow-y-auto">
                        <button type="button" onclick="selectDropdown('newReason', 'Defective Item', 'newReasonLabel', 'reasonDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Defective Item</button>
                        <button type="button" onclick="selectDropdown('newReason', 'Wrong Item Sent', 'newReasonLabel', 'reasonDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Wrong Item Sent</button>
                        <button type="button" onclick="selectDropdown('newReason', 'Customer Request', 'newReasonLabel', 'reasonDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Customer Request</button>
                        <button type="button" onclick="selectDropdown('newReason', 'Quality Issue', 'newReasonLabel', 'reasonDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Quality Issue</button>
                    </div>
                </div>

                <!-- Replacement Product & Quantity (Side by Side) -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-2 relative">
                        <label class="block text-sm font-semibold text-slate-900 mb-3">Replacement Product</label>
                        <input id="newReplacementProduct" type="hidden" value="" />
                        <button type="button" id="replacementProductButton" onclick="toggleDropdown('replacementProductDropdown')" class="w-full px-5 py-3 rounded-[10px] border border-[#105f68]/20 bg-white text-left text-slate-700 text-sm font-semibold flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-[#105f68] focus:border-transparent ring-1 ring-[#105f68]/10 shadow-sm hover:bg-[#105f68]/5">
                            <span id="newReplacementProductLabel">Select product...</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="replacementProductDropdown" class="hidden absolute z-50 w-full mt-2 bg-white border border-slate-200 rounded-[10px] shadow-xl p-1.5 space-y-0.5 max-h-40 overflow-y-auto">
                            <button type="button" onclick="selectDropdown('newReplacementProduct', 'Brembo Brake Pad', 'newReplacementProductLabel', 'replacementProductDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Brembo Brake Pad</button>
                            <button type="button" onclick="selectDropdown('newReplacementProduct', 'NGK Spark Plug', 'newReplacementProductLabel', 'replacementProductDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">NGK Spark Plug</button>
                            <button type="button" onclick="selectDropdown('newReplacementProduct', 'Motul 4T 10W40 Oil', 'newReplacementProductLabel', 'replacementProductDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Motul 4T 10W40 Oil</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Quantity</label>
                        <div class="flex items-center gap-2">
                            <button onclick="decreaseNewQuantity()" class="p-2.5 rounded-lg border border-slate-300 hover:bg-slate-100 transition-colors">
                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            <input id="newQuantity" type="text" value="1" readonly class="flex-1 px-3 py-2.5 rounded-lg border border-slate-300 text-center text-slate-900 font-semibold text-sm" />
                            <button onclick="increaseNewQuantity()" class="p-2.5 rounded-lg border border-slate-300 hover:bg-slate-100 transition-colors">
                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="px-8 py-5 border-t border-slate-200 flex gap-3 justify-end bg-slate-50 rounded-b-[10px]">
                <button onclick="closeNewReplacementModal()" class="px-6 py-3 rounded-[10px] border border-slate-300 bg-white text-slate-900 font-semibold text-sm hover:bg-slate-100 transition-all">Cancel</button>
                <button onclick="submitNewReplacement()" class="px-6 py-3 rounded-[10px] bg-[#105f68] text-white font-semibold text-sm hover:bg-[#0c474e] transition-all shadow-lg shadow-[#105f68]/20">Create Replacement</button>
            </div>
        </div>
    </div>

    <?php echo app('Illuminate\Foundation\Vite')('resources/js/replacing-items.js'); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?><?php /**PATH C:\Users\Admin\Desktop\WEQW\KCC_MOTORCYCLE\resources\views/point_of_sales/replacing-items.blade.php ENDPATH**/ ?>