<x-layouts.app :title="__('Import Data')">
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Import Data</h1>
                <p class="text-gray-600 text-sm mt-1">Import offline transactions from CSV or Excel files</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ Route::has('offline.reconciliation') ? route('offline.reconciliation') : url('/offline-reconciliation') }}" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 focus:outline-none transition-all duration-200">
                    <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Back to Offline Home</span>
                </a>
            </div>
        </div>

        @include('partials.offline-submenu')

        <!-- Import Form -->
        <div class="rounded-[15px] border border-slate-200 bg-white p-5 shadow-sm">
            <form action="{{ route('offline.import.store') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <h2 class="text-sm font-bold text-slate-900">Upload File</h2>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-900 bg-[#0f172a] px-4 py-2 text-sm font-medium text-[#6EC1D1] shadow-sm hover:bg-slate-800 focus:outline-none transition-all duration-200 cursor-pointer">
                            <svg class="h-4 w-4 text-[#6EC1D1]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            <span>Import Data</span>
                        </button>
                        <button type="button" onclick="validateFile()" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 focus:outline-none transition-all duration-200 cursor-pointer">
                            <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Validate First</span>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Select File</label>
                    <label for="fileUploadInput" id="uploadDropZone" class="flex flex-col items-center justify-center w-full rounded-[12px] border-2 border-dashed border-slate-300 bg-slate-50 cursor-pointer hover:border-[#6EC1D1] hover:bg-[rgba(110,193,209,0.05)] transition-all duration-200 py-8 px-4 group">
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
                        <input id="fileUploadInput" type="file" name="file" accept=".csv,.xlsx,.xls" class="hidden" required onchange="updateFileName(this)">
                    </label>
                </div>
            </form>
        </div>

        <!-- Validation Results -->
        @if(session('validation_results'))
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h2 class="text-sm font-semibold text-slate-900">Validation Results</h2>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                        <p class="text-xs text-green-600 font-medium">Valid Records</p>
                        <p class="text-2xl font-bold text-green-700">{{ count(session('validation_results.valid')) }}</p>
                    </div>
                    <div class="p-3 bg-red-50 rounded-lg border border-red-200">
                        <p class="text-xs text-red-600 font-medium">Invalid Records</p>
                        <p class="text-2xl font-bold text-red-700">{{ count(session('validation_results.invalid')) }}</p>
                    </div>
                    <div class="p-3 bg-amber-50 rounded-lg border border-amber-200">
                        <p class="text-xs text-amber-600 font-medium">Duplicate Records</p>
                        <p class="text-2xl font-bold text-amber-700">{{ count(session('validation_results.duplicates')) }}</p>
                    </div>
                </div>
                @if(!empty(session('validation_results.invalid')))
                <div class="mt-4">
                    <h3 class="text-xs font-semibold text-slate-900 mb-2">Invalid Records Details</h3>
                    <div class="max-h-60 overflow-y-auto">
                        @foreach(session('validation_results.invalid') as $invalid)
                        <div class="p-2 bg-red-50 rounded mb-2">
                            <p class="text-xs text-red-700">Row {{ $invalid['index'] + 1 }}: {{ implode(', ', $invalid['errors']) }}</p>
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
                <h2 class="text-base font-semibold text-slate-900">Pending Imports ({{ $pendingImports->count() }})</h2>
                <a href="{{ route('offline.pending.imports') }}" class="text-xs text-cyan-600 hover:text-cyan-700 font-medium">View All</a>
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
                            <tr>
                                <td class="px-3 py-2 text-slate-900 font-medium">{{ $pending->file_name }}</td>
                                <td class="px-3 py-2 text-slate-600">{{ $pending->uploadedBy?->name ?? '-' }}</td>
                                <td class="px-3 py-2 text-right text-slate-900 font-medium">{{ $pending->total_records }}</td>
                                <td class="px-3 py-2 text-right text-green-600 font-medium">{{ $pending->valid_records }}</td>
                                <td class="px-3 py-2 text-right text-red-600 font-medium">{{ $pending->invalid_records }}</td>
                                <td class="px-3 py-2">
                                    <a href="{{ route('offline.pending.imports') }}" class="text-cyan-600 hover:text-cyan-700 font-medium">Review</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-3 py-2 text-xs">
                    <p class="text-slate-600">
                        Showing {{ $pendingImports->total() > 0 ? $pendingImports->firstItem() : 0 }} - {{ $pendingImports->total() > 0 ? $pendingImports->lastItem() : 0 }} of {{ $pendingImports->total() }} items
                    </p>
                    @if($pendingImports->hasPages())
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
                    @else
                        <div class="flex gap-1">
                            <button disabled class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 opacity-50 cursor-not-allowed">← Prev</button>
                            <button class="rounded-[10px] bg-slate-200 border border-slate-300 px-2.5 py-1 text-xs font-bold text-slate-900">1</button>
                            <button disabled class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 opacity-50 cursor-not-allowed">Next →</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Imports -->
        <div class="rounded-[15px] border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-base font-semibold text-slate-900">Recent Imports</h2>
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
                            <tr>
                                <td class="px-3 py-2 text-slate-900 font-medium">{{ $import->file_name }}</td>
                                <td class="px-3 py-2 text-slate-600">{{ $import->import_date ? $import->import_date->format('M d, Y H:i') : '-' }}</td>
                                <td class="px-3 py-2 text-slate-600">{{ $import->importedBy?->name ?? '-' }}</td>
                                <td class="px-3 py-2 text-right text-slate-900 font-medium">{{ $import->total_records }}</td>
                                <td class="px-3 py-2 text-right text-green-600 font-medium">{{ $import->imported_records }}</td>
                                <td class="px-3 py-2 text-right text-amber-600 font-medium">{{ $import->duplicate_records }}</td>
                                <td class="px-3 py-2 text-right text-red-600 font-medium">{{ $import->failed_records }}</td>
                                <td class="px-3 py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium @if($import->synchronization_status == 'completed') bg-green-100 text-green-700 @elseif($import->synchronization_status == 'failed') bg-red-100 text-red-700 @else bg-slate-100 text-slate-700 @endif">
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
                <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-3 py-2 text-xs">
                    <p class="text-slate-600">
                        Showing {{ $recentImports->total() > 0 ? $recentImports->firstItem() : 0 }} - {{ $recentImports->total() > 0 ? $recentImports->lastItem() : 0 }} of {{ $recentImports->total() }} items
                    </p>
                    @if($recentImports->hasPages())
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
                    @else
                        <div class="flex gap-1">
                            <button disabled class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 opacity-50 cursor-not-allowed">← Prev</button>
                            <button class="rounded-[10px] bg-slate-200 border border-slate-300 px-2.5 py-1 text-xs font-bold text-slate-900">1</button>
                            <button disabled class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 opacity-50 cursor-not-allowed">Next →</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Import Information -->
        <div class="rounded-[15px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-[#0f172a]">
                <h2 class="text-sm font-semibold text-white">Import Information</h2>
            </div>
            <div class="p-4">
                <div class="space-y-3">
                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-slate-900">Validation Process</p>
                            <p class="text-xs text-slate-600 mt-1">All records are validated before import. Invalid records are skipped and reported.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-slate-900">Duplicate Detection</p>
                            <p class="text-xs text-slate-600 mt-1">Duplicate purchase orders and inventory movements are automatically detected and skipped.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-slate-900">Sync Status Update</p>
                            <p class="text-xs text-slate-600 mt-1">Successfully imported records are marked as "Synchronized" automatically.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function validateFile() {
            const fileInput = document.querySelector('input[name="file"]');
            if (fileInput.files.length === 0) {
                alert('Please select a file first.');
                return;
            }

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);

            fetch('{{ route('offline.import.validate') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Validation successful!\n\nTotal Records: ${data.total_records}\nValid: ${data.valid_records}\nInvalid: ${data.invalid_records}\nDuplicates: ${data.duplicate_records}`);
                } else {
                    alert('Validation failed: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error validating file: ' + error.message);
            });
        }

        function updateFileName(input) {
            const label = document.getElementById('uploadFileName');
            const zone = document.getElementById('uploadDropZone');
            if (input.files && input.files[0]) {
                label.textContent = input.files[0].name;
                label.classList.add('text-[#145a66]');
                label.classList.remove('text-slate-700');
                zone.classList.add('border-[#6EC1D1]', 'bg-[rgba(110,193,209,0.05)]');
                zone.classList.remove('border-slate-300');
            } else {
                label.textContent = 'Click to browse or drag & drop';
                label.classList.remove('text-[#145a66]');
                label.classList.add('text-slate-700');
                zone.classList.remove('border-[#6EC1D1]', 'bg-[rgba(110,193,209,0.05)]');
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
                if (e.dataTransfer.files.length) {
                    fileInput.files = e.dataTransfer.files;
                    updateFileName(fileInput);
                }
            });
        }
    </script>
</x-layouts.app>
