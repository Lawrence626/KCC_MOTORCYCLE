<x-layouts.app :title="__('Import Data')">
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between pt-2 pb-1 pl-1">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Import Data</h1>
                <p class="text-gray-600 text-xs mt-1">Import offline transactions from CSV or Excel files</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
            </div>
        </div>

        @include('partials.offline-submenu')

        <!-- Flash Alerts & Notifications -->
        @if(session('success'))
            <div id="importSuccessAlert" class="rounded-[15px] border border-emerald-300 bg-emerald-50 p-4 shadow-sm flex items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <div class="p-1.5 rounded-full bg-emerald-100 text-emerald-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-emerald-900">Import Successful!</h3>
                        <p class="text-xs text-emerald-700 mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('importSuccessAlert').remove()" class="text-emerald-700 hover:text-emerald-900 p-1 rounded-md transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const el = document.getElementById('importSuccessAlert');
                    if (el) {
                        el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                        setTimeout(function() {
                            el.style.opacity = '0';
                            el.style.transform = 'translateY(-10px)';
                            setTimeout(function() { el.remove(); }, 400);
                        }, 3500);
                    }
                });
            </script>
        @endif

        @if(session('error'))
            <div id="importErrorAlert" class="rounded-[15px] border border-red-300 bg-red-50 p-4 shadow-sm flex items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <div class="p-1.5 rounded-full bg-red-100 text-red-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-red-900">Import Failed</h3>
                        <p class="text-xs text-red-700 mt-0.5">{{ session('error') }}</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('importErrorAlert').remove()" class="text-red-700 hover:text-red-900 p-1 rounded-md transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div id="importValidationErrors" class="rounded-[15px] border border-red-300 bg-red-50 p-4 shadow-sm flex items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <div class="p-1.5 rounded-full bg-red-100 text-red-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-red-900">Upload Validation Errors</h3>
                        <ul class="text-xs text-red-700 mt-1 list-disc pl-4 space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('importValidationErrors').remove()" class="text-red-700 hover:text-red-900 p-1 rounded-md transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        <!-- Dynamic Client Alert Container -->
        <div id="clientAlertContainer" class="hidden"></div>

        <!-- Import Form -->
        <div class="rounded-[15px] border border-slate-200 bg-white p-5 shadow-sm">
            <form action="{{ route('offline.import.store') }}" method="POST" enctype="multipart/form-data" id="importForm" onsubmit="return handleImportSubmit(event)">
                @csrf
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Upload Offline Data File</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Select a CSV or Excel file exported from an offline terminal</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" id="btnImportData" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-900 bg-[#0f172a] px-4 py-2 text-sm font-medium text-[#6EC1D1] shadow-sm hover:bg-slate-800 focus:outline-none transition-all duration-200 cursor-pointer">
                            <svg class="h-4 w-4 text-[#6EC1D1]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            <span>Import Data</span>
                        </button>
                        <button type="button" id="btnValidateFirst" onclick="validateFile()" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 focus:outline-none transition-all duration-200 cursor-pointer">
                            <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Validate First</span>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Select File</label>
                    <label for="fileUploadInput" id="uploadDropZone" class="flex flex-col items-center justify-center w-full rounded-[12px] border-2 border-dashed border-slate-300 bg-slate-50 cursor-pointer hover:border-[#6EC1D1] hover:bg-[rgba(110,193,209,0.05)] transition-all duration-200 py-8 px-4 group relative">
                        <div class="flex flex-col items-center gap-2 pointer-events-none">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-[rgba(110,193,209,0.15)] group-hover:bg-[rgba(110,193,209,0.25)] transition-colors">
                                <svg class="w-6 h-6 text-[#145a66]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                            </div>
                            <div class="text-center">
                                <p id="uploadFileName" class="text-sm font-semibold text-slate-700">Click to browse or drag & drop</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Supported formats: CSV, Excel (.xlsx, .xls)</p>
                            </div>
                        </div>
                        <input id="fileUploadInput" type="file" name="file" accept=".csv,.xlsx,.xls,.txt" class="sr-only" onchange="updateFileName(this)">
                    </label>
                </div>
            </form>
        </div>

        <!-- Live Validation Results Container -->
        <div id="liveValidationContainer" class="hidden"></div>

        <!-- Server-side Validation Results (If rendered from redirect) -->
        @if(session('validation_results'))
        <div class="bg-white rounded-[15px] border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900">Validation Results</h2>
                <span class="text-xs font-semibold text-slate-500">Total Records: {{ (count(session('validation_results.valid', [])) + count(session('validation_results.invalid', [])) + count(session('validation_results.duplicates', []))) }}</span>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                    <div class="p-3 bg-emerald-50 rounded-[12px] border border-emerald-200">
                        <p class="text-xs text-emerald-700 font-bold">Valid Records</p>
                        <p class="text-2xl font-bold text-emerald-900 mt-1">{{ count(session('validation_results.valid', [])) }}</p>
                    </div>
                    <div class="p-3 bg-red-50 rounded-[12px] border border-red-200">
                        <p class="text-xs text-red-700 font-bold">Invalid Records</p>
                        <p class="text-2xl font-bold text-red-900 mt-1">{{ count(session('validation_results.invalid', [])) }}</p>
                    </div>
                    <div class="p-3 bg-amber-50 rounded-[12px] border border-amber-200">
                        <p class="text-xs text-amber-700 font-bold">Duplicate Records</p>
                        <p class="text-2xl font-bold text-amber-900 mt-1">{{ count(session('validation_results.duplicates', [])) }}</p>
                    </div>
                </div>

                @if(count(session('validation_results.valid', [])) > 0)
                <div class="mb-4 p-4 rounded-[14px] bg-gradient-to-r from-emerald-500/10 via-[#6EC1D1]/15 to-emerald-500/10 border border-emerald-300 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-extrabold text-emerald-950 uppercase tracking-wide flex items-center gap-2">
                                <span>All Set! Ready for Import</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-200 text-emerald-950">{{ count(session('validation_results.valid', [])) }} Verified</span>
                            </h4>
                            <p class="text-xs text-emerald-900 mt-0.5 font-medium">
                                Your records have passed validation. You can now proceed to import these transactions into the system.
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                @if(!empty(session('validation_results.invalid')))
                <div class="mt-4">
                    <h3 class="text-xs font-bold text-slate-900 mb-2">Invalid Records Details:</h3>
                    <div class="max-h-60 overflow-y-auto space-y-2">
                        @foreach(session('validation_results.invalid') as $invalid)
                        <div class="p-2.5 bg-red-50/80 rounded-[10px] border border-red-200">
                            <p class="text-xs text-red-900 font-medium">Record #{{ $invalid['index'] + 1 }}: {{ is_array($invalid['errors']) ? implode(', ', $invalid['errors']) : $invalid['errors'] }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Pending Imports -->
        @if($pendingImports->count() > 0)
        <div class="rounded-[15px] border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-base font-bold text-slate-900">Pending Imports for Review ({{ $pendingImports->total() }})</h2>
                <a href="{{ route('offline.pending.imports') }}" class="text-xs text-cyan-600 hover:text-cyan-700 font-bold">View Full List →</a>
            </div>
            <div class="overflow-hidden rounded-[10px] border border-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs text-slate-700">
                        <thead class="border-b border-slate-200 bg-[#0f172a] text-xs uppercase tracking-wider text-white">
                            <tr>
                                <th class="px-3 py-3 text-left font-semibold text-white">File Name</th>
                                <th class="px-3 py-3 text-left font-semibold text-white">Uploaded By</th>
                                <th class="px-3 py-3 text-right font-semibold text-white">Total</th>
                                <th class="px-3 py-3 text-right font-semibold text-white">Valid</th>
                                <th class="px-3 py-3 text-right font-semibold text-white">Invalid</th>
                                <th class="px-3 py-3 text-left font-semibold text-white">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach($pendingImports as $pending)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-3 py-2.5 text-slate-900 font-medium font-mono text-[11px]">{{ $pending->file_name }}</td>
                                <td class="px-3 py-2.5 text-slate-600">{{ $pending->uploadedBy?->name ?? '-' }}</td>
                                <td class="px-3 py-2.5 text-right text-slate-900 font-bold">{{ $pending->total_records }}</td>
                                <td class="px-3 py-2.5 text-right text-emerald-600 font-bold">{{ $pending->valid_records }}</td>
                                <td class="px-3 py-2.5 text-right text-rose-600 font-bold">{{ $pending->invalid_records }}</td>
                                <td class="px-3 py-2.5">
                                    <a href="{{ route('offline.pending.imports') }}" class="inline-flex items-center gap-1 text-cyan-700 hover:text-cyan-900 font-bold">
                                        <span>Review & Approve</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($pendingImports->hasPages())
                <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-3 py-2 text-xs">
                    <p class="text-slate-600">
                        Showing {{ $pendingImports->firstItem() }} - {{ $pendingImports->lastItem() }} of {{ $pendingImports->total() }} items
                    </p>
                    <div class="flex gap-1">
                        @if ($pendingImports->onFirstPage())
                            <button disabled class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 opacity-50 cursor-not-allowed">← Prev</button>
                        @else
                            <a href="{{ $pendingImports->previousPageUrl() }}" class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">← Prev</a>
                        @endif

                        @foreach ($pendingImports->getUrlRange(1, $pendingImports->lastPage()) as $page => $url)
                            @if ($page == $pendingImports->currentPage())
                                <button class="rounded-[10px] bg-slate-200 border border-slate-300 px-2.5 py-1 text-xs font-bold text-slate-900">{{ $page }}</button>
                            @else
                                <a href="{{ $url }}" class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($pendingImports->hasMorePages())
                            <a href="{{ $pendingImports->nextPageUrl() }}" class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">Next →</a>
                        @else
                            <button disabled class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 opacity-50 cursor-not-allowed">Next →</button>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Recent Imports -->
        <div class="rounded-[15px] border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-base font-bold text-slate-900">Recent Completed Imports</h2>
            </div>
            <div class="overflow-hidden rounded-[10px] border border-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs text-slate-700">
                        <thead class="border-b border-slate-200 bg-[#0f172a] text-xs uppercase tracking-wider text-white">
                            <tr>
                                <th class="px-3 py-3 text-left font-semibold text-white">File Name</th>
                                <th class="px-3 py-3 text-left font-semibold text-white">Import Date</th>
                                <th class="px-3 py-3 text-left font-semibold text-white">Imported By</th>
                                <th class="px-3 py-3 text-right font-semibold text-white">Total</th>
                                <th class="px-3 py-3 text-right font-semibold text-white">Imported</th>
                                <th class="px-3 py-3 text-right font-semibold text-white">Duplicates</th>
                                <th class="px-3 py-3 text-right font-semibold text-white">Failed</th>
                                <th class="px-3 py-3 text-left font-semibold text-white">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($recentImports as $import)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-3 py-2.5 text-slate-900 font-medium font-mono text-[11px]">{{ $import->file_name }}</td>
                                <td class="px-3 py-2.5 text-slate-600">{{ $import->import_date ? $import->import_date->format('M d, Y H:i') : '-' }}</td>
                                <td class="px-3 py-2.5 text-slate-600">{{ $import->importedBy?->name ?? '-' }}</td>
                                <td class="px-3 py-2.5 text-right text-slate-900 font-medium">{{ $import->total_records }}</td>
                                <td class="px-3 py-2.5 text-right text-emerald-600 font-bold">{{ $import->imported_records }}</td>
                                <td class="px-3 py-2.5 text-right text-amber-600 font-medium">{{ $import->duplicate_records }}</td>
                                <td class="px-3 py-2.5 text-right text-rose-600 font-medium">{{ $import->failed_records }}</td>
                                <td class="px-3 py-2.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold @if($import->synchronization_status == 'completed') bg-emerald-100 text-emerald-800 @elseif($import->synchronization_status == 'failed') bg-red-100 text-red-800 @else bg-slate-100 text-slate-800 @endif">
                                        {{ ucfirst($import->synchronization_status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-3 py-8 text-center text-slate-500">No recent imports found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($recentImports->hasPages())
                <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-3 py-2 text-xs">
                    <p class="text-slate-600">
                        Showing {{ $recentImports->firstItem() }} - {{ $recentImports->lastItem() }} of {{ $recentImports->total() }} items
                    </p>
                    <div class="flex gap-1">
                        @if ($recentImports->onFirstPage())
                            <button disabled class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 opacity-50 cursor-not-allowed">← Prev</button>
                        @else
                            <a href="{{ $recentImports->previousPageUrl() }}" class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">← Prev</a>
                        @endif

                        @foreach ($recentImports->getUrlRange(1, $recentImports->lastPage()) as $page => $url)
                            @if ($page == $recentImports->currentPage())
                                <button class="rounded-[10px] bg-slate-200 border border-slate-300 px-2.5 py-1 text-xs font-bold text-slate-900">{{ $page }}</button>
                            @else
                                <a href="{{ $url }}" class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($recentImports->hasMorePages())
                            <a href="{{ $recentImports->nextPageUrl() }}" class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">Next →</a>
                        @else
                            <button disabled class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 opacity-50 cursor-not-allowed">Next →</button>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Import Information -->
        <div class="rounded-[15px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-[#0f172a]">
                <h2 class="text-sm font-semibold text-white">Import Information & Rules</h2>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-[12px] border border-slate-200">
                        <svg class="w-5 h-5 text-cyan-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-xs font-bold text-slate-900">Validation & Staging</p>
                            <p class="text-[11px] text-slate-600 mt-0.5 leading-tight">All purchase orders are validated first before staging into pending imports for admin review.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-[12px] border border-slate-200">
                        <svg class="w-5 h-5 text-amber-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <p class="text-xs font-bold text-slate-900">Duplicate Detection</p>
                            <p class="text-[11px] text-slate-600 mt-0.5 leading-tight">Existing purchase order numbers in the database or within the file are automatically flagged.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-[12px] border border-slate-200">
                        <svg class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-xs font-bold text-slate-900">Safe Syncing</p>
                            <p class="text-[11px] text-slate-600 mt-0.5 leading-tight">Approved imports are converted to official Purchase Orders and marked as Synchronized.</p>
                        </div>
                    </div>
                </div>
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

    <script>
        // Client Alert Helper
        function showClientAlert(message, type = 'error', title = null) {
            const container = document.getElementById('clientAlertContainer');
            if (!container) return;

            const isError = type === 'error';
            const bgClass = isError ? 'bg-red-50 border-red-300 text-red-900' : 'bg-emerald-50 border-emerald-300 text-emerald-950';
            const iconBg = isError ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700';
            const headerTitle = title || (isError ? 'Notice' : 'Success');

            container.classList.remove('hidden');
            container.style.opacity = '1';
            container.style.transform = 'translateY(0)';
            container.style.transition = 'opacity 0.4s ease, transform 0.4s ease';

            container.innerHTML = `
                <div class="rounded-[15px] border ${bgClass} p-4 shadow-sm flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <div class="p-1.5 rounded-full ${iconBg} shrink-0 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                ${isError 
                                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'
                                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
                                }
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold">${headerTitle}</h3>
                            <p class="text-xs mt-0.5 ${isError ? 'text-red-700' : 'text-emerald-800'}">${message}</p>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('clientAlertContainer').classList.add('hidden')" class="text-slate-500 hover:text-slate-800 p-1 rounded-md transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            `;

            container.scrollIntoView({ behavior: 'smooth', block: 'center' });

            if (window._clientAlertTimer) clearTimeout(window._clientAlertTimer);
            window._clientAlertTimer = setTimeout(() => {
                container.style.opacity = '0';
                container.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    container.classList.add('hidden');
                    container.style.opacity = '1';
                    container.style.transform = 'translateY(0)';
                }, 400);
            }, 3500);
        }

        // Handle Import Form Submit
        function handleImportSubmit(e) {
            const fileInput = document.getElementById('fileUploadInput');
            if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                if (e) e.preventDefault();
                showClientAlert('Please select a CSV or Excel file before clicking Import Data.', 'error', 'File Required');
                return false;
            }

            const btn = document.getElementById('btnImportData');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `<svg class="w-4 h-4 animate-spin text-[#6EC1D1]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Importing...</span>`;
            }
            return true;
        }

        function submitImportFromValidation() {
            const form = document.getElementById('importForm');
            if (form) {
                const btn = document.getElementById('btnImportData');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = `<svg class="w-4 h-4 animate-spin text-[#6EC1D1]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Importing...</span>`;
                }
                form.submit();
            }
        }

        // Validate File Function
        async function validateFile() {
            const fileInput = document.getElementById('fileUploadInput');
            if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                showClientAlert('Please select a CSV or Excel file before validating.', 'error', 'File Required');
                return;
            }

            const btn = document.getElementById('btnValidateFirst');
            const originalHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `<svg class="w-4 h-4 animate-spin text-slate-700" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Validating...</span>`;
            }

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);

            try {
                const response = await fetch('{{ route('offline.import.validate') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    renderLiveValidationResults(data);

                    const valid = data.valid_records || 0;
                    const invalid = data.invalid_records || 0;

                    if (valid > 0 && invalid === 0) {
                        showClientAlert(
                            `All set! All <strong>${valid}</strong> order(s) have been verified successfully. You can now proceed to click <strong>Import Data</strong>.`,
                            'success',
                            'Validation Passed — All Set!'
                        );
                    } else if (valid > 0) {
                        showClientAlert(
                            `Validation completed: <strong>${valid}</strong> valid order(s) are ready for import (${invalid} item(s) flagged with issues). You may now proceed to click <strong>Import Data</strong> or review the details below.`,
                            'success',
                            'Validation Completed'
                        );
                    } else {
                        showClientAlert(
                            `Validation completed, but no valid records were found in this file. Please review the errors below before importing.`,
                            'error',
                            'No Valid Records Found'
                        );
                    }
                } else {
                    const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Validation failed. Please check file format.');
                    showClientAlert(msg, 'error', 'Validation Failed');
                }
            } catch (error) {
                showClientAlert('Error connecting to server for validation: ' + error.message, 'error', 'Network Error');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            }
        }

        // Render Live Validation Results
        function renderLiveValidationResults(data) {
            const container = document.getElementById('liveValidationContainer');
            if (!container) return;

            const validCount = data.valid_records || 0;
            const invalidCount = data.invalid_records || 0;
            const duplicateCount = data.duplicate_records || 0;
            const totalCount = data.total_records || 0;
            const invalids = (data.validation_results && data.validation_results.invalid) || [];
            const duplicates = (data.validation_results && data.validation_results.duplicates) || [];

            let invalidsHtml = '';
            if (invalids.length > 0) {
                invalidsHtml = `
                    <div class="mt-4 border-t border-slate-100 pt-3">
                        <h3 class="text-xs font-bold text-slate-900 mb-2">Invalid Records Details (${invalids.length}):</h3>
                        <div class="max-h-56 overflow-y-auto space-y-2">
                            ${invalids.map(inv => `
                                <div class="p-2.5 bg-red-50 rounded-[10px] border border-red-200 text-xs text-red-900">
                                    <span class="font-bold">Record #${(inv.index || 0) + 1} (${(inv.record && inv.record.order_number) || 'N/A'}):</span>
                                    <span>${Array.isArray(inv.errors) ? inv.errors.join(', ') : inv.errors}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }

            let duplicatesHtml = '';
            if (duplicates.length > 0) {
                duplicatesHtml = `
                    <div class="mt-3 border-t border-slate-100 pt-3">
                        <h3 class="text-xs font-bold text-slate-900 mb-2">Duplicate Records Flagged (${duplicates.length}):</h3>
                        <div class="max-h-40 overflow-y-auto space-y-2">
                            ${duplicates.map(dup => `
                                <div class="p-2 bg-amber-50 rounded-[10px] border border-amber-200 text-xs text-amber-900">
                                    <span class="font-bold">${(dup.record && dup.record.order_number) || 'PO'}:</span>
                                    <span>${dup.reason || 'Already exists in database or batch.'}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }

            container.classList.remove('hidden');
            container.innerHTML = `
                <div class="bg-white rounded-[15px] border border-slate-200 shadow-sm overflow-hidden mb-4">
                    <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span>Validation Check Completed</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${invalidCount === 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'}">
                                    ${invalidCount === 0 ? 'All Valid' : `${invalidCount} Issues Found`}
                                </span>
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Summary of orders inspected in the uploaded file</p>
                        </div>
                        <button type="button" onclick="document.getElementById('liveValidationContainer').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 p-1 rounded-md transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="p-3 bg-emerald-50 rounded-[12px] border border-emerald-200">
                                <p class="text-xs text-emerald-700 font-bold">Valid Orders Ready</p>
                                <p class="text-2xl font-bold text-emerald-900 mt-1">${validCount}</p>
                                <p class="text-[10px] text-emerald-700 mt-0.5">Orders ready to be imported</p>
                            </div>
                            <div class="p-3 bg-red-50 rounded-[12px] border border-red-200">
                                <p class="text-xs text-red-700 font-bold">Invalid Orders</p>
                                <p class="text-2xl font-bold text-red-900 mt-1">${invalidCount}</p>
                                <p class="text-[10px] text-red-700 mt-0.5">Orders with missing data/errors</p>
                            </div>
                            <div class="p-3 bg-amber-50 rounded-[12px] border border-amber-200">
                                <p class="text-xs text-amber-700 font-bold">Duplicate Orders</p>
                                <p class="text-2xl font-bold text-amber-900 mt-1">${duplicateCount}</p>
                                <p class="text-[10px] text-amber-700 mt-0.5">Already in DB or repeated in file</p>
                            </div>
                        </div>

                        ${validCount > 0 ? `
                        <div class="mt-4 p-4 rounded-[14px] bg-gradient-to-r from-emerald-500/10 via-[#6EC1D1]/15 to-emerald-500/10 border border-emerald-300 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-extrabold text-emerald-950 uppercase tracking-wide flex items-center gap-2">
                                        <span>All Set! Ready for Import</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-200 text-emerald-950">${validCount} Order${validCount === 1 ? '' : 's'} Verified</span>
                                    </h4>
                                    <p class="text-xs text-emerald-900 mt-0.5 font-medium">
                                        Your file has passed validation checks. You can now proceed to import these transactions into the system.
                                    </p>
                                </div>
                            </div>
                            <button type="button" onclick="submitImportFromValidation()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-[10px] bg-[#0f172a] hover:bg-slate-800 text-[#6EC1D1] text-xs font-bold shadow-md hover:shadow-lg transition-all duration-200 cursor-pointer shrink-0 border border-slate-900">
                                <svg class="w-4 h-4 text-[#6EC1D1]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                <span>Proceed to Import Data</span>
                            </button>
                        </div>
                        ` : ''}

                        ${invalidsHtml}
                        ${duplicatesHtml}
                    </div>
                </div>
            `;

            container.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatFileSize(bytes) {
            if (!bytes || bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        }

        function updateFileName(input) {
            const label = document.getElementById('uploadFileName');
            const zone = document.getElementById('uploadDropZone');
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const sizeStr = formatFileSize(file.size);

                label.innerHTML = `<span class="text-slate-900 font-bold">${escapeHtml(file.name)}</span> <span class="text-[11px] font-bold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full ml-1.5 inline-flex items-center gap-1"><svg class="w-3 h-3 text-emerald-600 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>${sizeStr}</span>`;
                label.classList.add('text-[#145a66]');
                label.classList.remove('text-slate-700');
                zone.classList.add('border-emerald-400', 'bg-emerald-50/40');
                zone.classList.remove('border-slate-300');

                // Trigger clear success notification when file is chosen
                showClientAlert(
                    `File <strong>${escapeHtml(file.name)}</strong> (${sizeStr}) loaded successfully. You can now click <strong>Validate First</strong> to check records or <strong>Import Data</strong> to upload.`,
                    'success',
                    'File Attached Successfully'
                );
            } else {
                label.textContent = 'Click to browse or drag & drop';
                label.classList.remove('text-[#145a66]', 'font-bold');
                label.classList.add('text-slate-700');
                zone.classList.remove('border-emerald-400', 'bg-emerald-50/40', 'border-[#6EC1D1]', 'bg-[rgba(110,193,209,0.05)]');
                zone.classList.add('border-slate-300');
            }
        }

        // Drag and drop support
        const dropZone = document.getElementById('uploadDropZone');
        if (dropZone) {
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('border-[#6EC1D1]', 'bg-[rgba(110,193,209,0.08)]');
            });
            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('bg-[rgba(110,193,209,0.08)]');
            });
            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                const fileInput = document.getElementById('fileUploadInput');
                if (e.dataTransfer.files && e.dataTransfer.files.length) {
                    fileInput.files = e.dataTransfer.files;
                    updateFileName(fileInput);
                }
            });
        }
    </script>
</x-layouts.app>
