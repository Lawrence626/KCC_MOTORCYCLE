<x-layouts.app :title="__('DSS Recommendations')">
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <h1 class="h3 mb-0">
                <i class="fas fa-lightbulb text-warning"></i> DSS Recommendations
            </h1>
            <p class="text-muted mt-2">Intelligent recommendations to move dead stock inventory</p>
        </div>
        <div class="col-lg-4 text-end">
            <a href="{{ route('dss.dead-stock.index') }}" class="btn btn-secondary">
                <i class="fas fa-cube"></i> Dead Stock List
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted small">Total Active</h6>
                    <h3>{{ $stats['total'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted small">Pending Action</h6>
                    <h3 class="text-warning">{{ $stats['pending'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted small">Actioned</h6>
                    <h3 class="text-success">{{ $stats['actioned'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted small">Completion Rate</h6>
                    <h3>{{ $stats['total'] > 0 ? round(($stats['actioned'] / $stats['total']) * 100) : 0 }}%</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="actioned" {{ request('status') === 'actioned' ? 'selected' : '' }}>Actioned</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="promotion" {{ request('type') === 'promotion' ? 'selected' : '' }}>Promotional Campaign</option>
                        <option value="discount" {{ request('type') === 'discount' ? 'selected' : '' }}>Price Reduction</option>
                        <option value="bundle" {{ request('type') === 'bundle' ? 'selected' : '' }}>Bundle Offer</option>
                        <option value="relocate" {{ request('type') === 'relocate' ? 'selected' : '' }}>Warehouse Relocation</option>
                        <option value="featured_display" {{ request('type') === 'featured_display' ? 'selected' : '' }}>Featured Display</option>
                        <option value="social_media" {{ request('type') === 'social_media' ? 'selected' : '' }}>Social Media</option>
                        <option value="supplier_return" {{ request('type') === 'supplier_return' ? 'selected' : '' }}>Supplier Return</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Priority</label>
                    <select name="priority" class="form-select">
                        <option value="">All Priorities</option>
                        <option value="Critical" {{ request('priority') === 'Critical' ? 'selected' : '' }}>Critical</option>
                        <option value="High" {{ request('priority') === 'High' ? 'selected' : '' }}>High</option>
                        <option value="Medium" {{ request('priority') === 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="Low" {{ request('priority') === 'Low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Recommendations Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Recommendations List</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Type</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Generated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recommendations as $recommendation)
                    <tr>
                        <td>
                            <a href="{{ route('dss.recommendations.show', $recommendation->id) }}">
                                {{ $recommendation->product->name ?? 'N/A' }}
                            </a>
                        </td>
                        <td>{{ $recommendation->product->sku ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-secondary">
                                {{ $recommendation->getTypeLabel() }}
                            </span>
                        </td>
                        <td>
                            <span class="badge" style="background-color: {{ match($recommendation->priority) {
                                'Critical' => '#dc3545',
                                'High' => '#fd7e14',
                                'Medium' => '#ffc107',
                                'Low' => '#0dcaf0',
                                default => '#6c757d',
                            } }}">
                                {{ $recommendation->priority }}
                            </span>
                        </td>
                        <td>
                            @if($recommendation->action_taken_at)
                                <span class="badge bg-success">Actioned</span>
                                <br><small class="text-muted">{{ $recommendation->action_taken_at->format('M d, Y') }}</small>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>
                        <td>
                            {{ $recommendation->generated_at->format('M d, Y') }}
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('dss.recommendations.show', $recommendation->id) }}" 
                                   class="btn btn-outline-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(!$recommendation->action_taken_at)
                                <button type="button" class="btn btn-outline-success actionBtn" 
                                        data-id="{{ $recommendation->id }}" title="Mark as Actioned">
                                    <i class="fas fa-check"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No recommendations found matching your filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($recommendations->hasPages())
        <div class="card-footer">
            {{ $recommendations->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Action Modal -->
<div class="modal fade" id="actionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark Recommendation as Actioned</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="actionForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="action_notes" class="form-label">Action Notes (Optional)</label>
                        <textarea class="form-control" id="action_notes" name="action_notes" rows="3" 
                                  placeholder="Document what action was taken..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Confirm Action
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentRecommendationId = null;
    const actionModal = new bootstrap.Modal(document.getElementById('actionModal'));

    $(document).on('click', '.actionBtn', function() {
        currentRecommendationId = $(this).data('id');
        $('#actionForm')[0].reset();
        actionModal.show();
    });

    $('#actionForm').on('submit', function(e) {
        e.preventDefault();
        
        const actionNotes = $('#action_notes').val();
        
        $.ajax({
            url: '{{ route("dss.recommendations.action", ":id") }}'.replace(':id', currentRecommendationId),
            type: 'POST',
            data: {
                action_notes: actionNotes,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                actionModal.hide();
                toastr.success('Recommendation marked as actioned!');
                setTimeout(() => location.reload(), 1000);
            },
            error: function() {
                toastr.error('Error marking recommendation as actioned. Please try again.');
            }
        });
    });
</script>
</x-layouts.app>
