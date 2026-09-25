<<<<<<< HEAD
<x-layouts.app :title="__('Reconciliation Report')">
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="text-lg font-bold text-slate-900 leading-tight">Reconciliation Report</h1>
=======
﻿<x-layouts.app :title="__('Reconciliation Report')">
    <div class="space-y-3">
        <!-- Header -->
        <div class="flex items-center justify-between pt-2 pb-1 pl-1">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Reconciliation Report</h1>
>>>>>>> 594490397ebecd1f37adadd252bb79d7a67298f2
                <p class="text-xs text-slate-500 mt-0.5">Detailed synchronization report for {{ $syncHistory->file_name }}</p>
            </div>
            <div class="flex gap-2">
                <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[10px] border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-sm">
                    <svg class="w-3.5 h-3.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print Report
                </button>
                <a href="{{ route('offline.history') }}" class="inline-flex items-center px-3 py-1.5 rounded-[10px] border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-sm">
                    Back to History
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">

        <!-- Report Summary -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-2">
            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Total Records</p>
                <p class="text-2xl font-bold text-slate-900">{{ $report['total_records'] }}</p>
                <p class="text-xs text-slate-500 mt-0.5">In file</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Imported</p>
                <p class="text-2xl font-bold text-green-600">{{ $report['imported_records'] }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Successfully</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Duplicates</p>
                <p class="text-2xl font-bold text-amber-600">{{ $report['duplicate_records'] }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Skipped</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Failed</p>
                <p class="text-2xl font-bold text-red-600">{{ $report['failed_records'] }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Errors</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Skipped</p>
                <p class="text-2xl font-bold text-slate-600">{{ $report['skipped_records'] }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Invalid</p>
            </div>
        </div>

        <!-- Report Details -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h2 class="text-sm font-semibold text-slate-900">Synchronization Details</h2>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-slate-600 font-medium">File Name</p>
                        <p class="text-sm text-slate-900 mt-0.5">{{ $report['file_name'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-600 font-medium">Synchronization Status</p>
                        <p class="text-sm mt-0.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium @if($report['synchronization_status'] == 'completed') bg-green-100 text-green-700 @elseif($report['synchronization_status'] == 'failed') bg-red-100 text-red-700 @else bg-slate-100 text-slate-700 @endif">
                                {{ ucfirst($report['synchronization_status']) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-600 font-medium">Export Date</p>
                        <p class="text-sm text-slate-900 mt-0.5">{{ $report['export_date'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-600 font-medium">Import Date</p>
                        <p class="text-sm text-slate-900 mt-0.5">{{ $report['import_date'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-600 font-medium">Exported By</p>
                        <p class="text-sm text-slate-900 mt-0.5">{{ $report['exported_by'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-600 font-medium">Imported By</p>
                        <p class="text-sm text-slate-900 mt-0.5">{{ $report['imported_by'] ?? 'N/A' }}</p>
                    </div>
                </div>
                @if($report['notes'])
                <div class="mt-4">
                    <p class="text-xs text-slate-600 font-medium">Notes</p>
                    <p class="text-sm text-slate-900 mt-0.5">{{ $report['notes'] }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Success Rate -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h2 class="text-sm font-semibold text-slate-900">Success Rate</h2>
            </div>
            <div class="p-4">
                @if($report['total_records'] > 0)
                @php
                $successRate = round(($report['imported_records'] / $report['total_records']) * 100, 2);
                @endphp
                <div class="mb-2">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-slate-600">Imported Records</span>
                        <span class="font-medium text-slate-900">{{ $successRate }}%</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $successRate }}%"></div>
                    </div>
                </div>
                @if($report['duplicate_records'] > 0)
                @php
                $duplicateRate = round(($report['duplicate_records'] / $report['total_records']) * 100, 2);
                @endphp
                <div class="mb-2">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-slate-600">Duplicate Records</span>
                        <span class="font-medium text-slate-900">{{ $duplicateRate }}%</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2">
                        <div class="bg-amber-600 h-2 rounded-full" style="width: {{ $duplicateRate }}%"></div>
                    </div>
                </div>
                @endif
                @if($report['failed_records'] > 0)
                @php
                $failedRate = round(($report['failed_records'] / $report['total_records']) * 100, 2);
                @endphp
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-slate-600">Failed Records</span>
                        <span class="font-medium text-slate-900">{{ $failedRate }}%</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2">
                        <div class="bg-red-600 h-2 rounded-full" style="width: {{ $failedRate }}%"></div>
                    </div>
                </div>
                @endif
                @else
                <p class="text-sm text-slate-500">No records to calculate success rate.</p>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Notification Panel Toggle
        function toggleNotificationPanel(event) {
            event.stopPropagation();
            const panel = document.getElementById('notification-panel');
            const profileDropdown = document.getElementById('dashboardProfileDropdown');
            if (panel) {
                const isHidden = panel.classList.contains('hidden');
                if (isHidden) {
                    panel.classList.remove('hidden');
                    if (profileDropdown) {
                        profileDropdown.classList.add('hidden');
                        profileDropdown.classList.remove('opacity-100', 'scale-100');
                        profileDropdown.classList.add('opacity-0', 'scale-95');
                    }
                } else {
                    panel.classList.add('hidden');
                }
            }
        }

        // Helper functions
        function markAllNotificationsRead() {
            // Placeholder for marking all notifications as read
            console.log('Mark all notifications as read');
        }

        function openAllNotificationsModal() {
            // Placeholder for opening all notifications modal
            console.log('Open all notifications modal');
        }

        // Close dropdowns when clicking outside
        window.addEventListener('click', function(event) {
            const panel = document.getElementById('notification-panel');
            const profileDropdown = document.getElementById('dashboardProfileDropdown');
            const notificationBell = document.getElementById('notification-bell-btn');
            const profileButton = document.getElementById('dashboardProfileButton');

            if (panel && !panel.contains(event.target) && notificationBell && !notificationBell.contains(event.target)) {
                panel.classList.add('hidden');
            }

            if (profileDropdown && !profileDropdown.contains(event.target) && profileButton && !profileButton.contains(event.target)) {
                profileDropdown.classList.add('hidden');
                profileDropdown.classList.remove('opacity-100', 'scale-100');
                profileDropdown.classList.add('opacity-0', 'scale-95');
            }
        });
    </script>
</x-layouts.app>
