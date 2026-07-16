<x-layouts.app :title="__('DSS Configuration Settings')">
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <h1 class="h3 mb-0">
                <i class="fas fa-cog text-primary"></i> DSS Configuration
            </h1>
            <p class="text-muted mt-2">Configure Dead Stock Detection & Recommendation Engine settings</p>
        </div>
        <div class="col-lg-4 text-end">
            <a href="{{ route('dss.dead-stock.index') }}" class="btn btn-secondary">
                <i class="fas fa-cube"></i> Back to Dead Stock
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Configuration Settings</h5>
                </div>
                <form action="{{ route('dss.settings.update') }}" method="POST" id="settingsForm">
                    @csrf
                    <div class="card-body">
                        <!-- Dead Stock Threshold -->
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2">Dead Stock Classification</h6>
                            <div class="mb-3">
                                <label for="dead_stock_threshold_days" class="form-label">
                                    Days Without Sale (Dead Stock Threshold)
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('dead_stock_threshold_days') is-invalid @enderror" 
                                           id="dead_stock_threshold_days"
                                           name="dead_stock_threshold_days"
                                           value="{{ old('dead_stock_threshold_days', $settings->where('key', 'dead_stock_threshold_days')->first()->value ?? 90) }}"
                                           min="30" max="365" step="1">
                                    <span class="input-group-text">days</span>
                                </div>
                                <small class="form-text text-muted">
                                    Products with no sales for this duration are classified as Dead Stock. 
                                    Recommended: 90 days (3 months)
                                </small>
                                @error('dead_stock_threshold_days')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Slow Moving Threshold -->
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2">Slow Moving Classification</h6>
                            <div class="mb-3">
                                <label for="slow_moving_threshold_days" class="form-label">
                                    Days Without Sale (Slow Moving Threshold)
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('slow_moving_threshold_days') is-invalid @enderror" 
                                           id="slow_moving_threshold_days"
                                           name="slow_moving_threshold_days"
                                           value="{{ old('slow_moving_threshold_days', $settings->where('key', 'slow_moving_threshold_days')->first()->value ?? 60) }}"
                                           min="30" max="365" step="1">
                                    <span class="input-group-text">days</span>
                                </div>
                                <small class="form-text text-muted">
                                    Products with no sales for this duration are classified as Slow Moving. 
                                    Recommended: 60 days (2 months)
                                </small>
                                @error('slow_moving_threshold_days')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Fast Moving Threshold -->
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2">Fast Moving Classification</h6>
                            <div class="mb-3">
                                <label for="fast_moving_threshold_units" class="form-label">
                                    Minimum Units Sold in 30 Days
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('fast_moving_threshold_units') is-invalid @enderror" 
                                           id="fast_moving_threshold_units"
                                           name="fast_moving_threshold_units"
                                           value="{{ old('fast_moving_threshold_units', $settings->where('key', 'fast_moving_threshold_units')->first()->value ?? 50) }}"
                                           min="10" max="1000" step="5">
                                    <span class="input-group-text">units</span>
                                </div>
                                <small class="form-text text-muted">
                                    Products selling at least this many units per month are classified as Fast Moving. 
                                    Used for bundle recommendations.
                                </small>
                                @error('fast_moving_threshold_units')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Feature Toggles -->
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2">Recommendation Types</h6>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" 
                                       id="promotion_recommendation_enabled"
                                       name="promotion_recommendation_enabled" value="1"
                                       {{ old('promotion_recommendation_enabled', $settings->where('key', 'promotion_recommendation_enabled')->first()->value ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="promotion_recommendation_enabled">
                                    <strong>Enable Promotional Campaign Recommendations</strong>
                                    <br><small class="text-muted">Suggest promotional campaigns for dead stock items</small>
                                </label>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" 
                                       id="discount_recommendation_enabled"
                                       name="discount_recommendation_enabled" value="1"
                                       {{ old('discount_recommendation_enabled', $settings->where('key', 'discount_recommendation_enabled')->first()->value ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="discount_recommendation_enabled">
                                    <strong>Enable Price Reduction Recommendations</strong>
                                    <br><small class="text-muted">Suggest price reductions and discounts</small>
                                </label>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" 
                                       id="bundle_recommendation_enabled"
                                       name="bundle_recommendation_enabled" value="1"
                                       {{ old('bundle_recommendation_enabled', $settings->where('key', 'bundle_recommendation_enabled')->first()->value ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="bundle_recommendation_enabled">
                                    <strong>Enable Bundle Offer Recommendations</strong>
                                    <br><small class="text-muted">Suggest bundling with fast-moving products</small>
                                </label>
                            </div>
                        </div>

                        <!-- DSS Toggle -->
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2">System Status</h6>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" 
                                       id="dss_analysis_enabled"
                                       name="dss_analysis_enabled" value="1"
                                       {{ old('dss_analysis_enabled', $settings->where('key', 'dss_analysis_enabled')->first()->value ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="dss_analysis_enabled">
                                    <strong>Enable Automatic DSS Analysis</strong>
                                    <br><small class="text-muted">Automatically recalculate dead stock analysis when inventory changes</small>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <div>
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> Reset to Current
                            </button>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Configuration
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Info Cards -->
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Priority Levels</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6><span class="badge bg-danger">Critical</span></h6>
                        <small>Not sold for 180+ days</small>
                    </div>
                    <div class="mb-3">
                        <h6><span class="badge bg-warning text-dark">High</span></h6>
                        <small>Not sold for 120-179 days</small>
                    </div>
                    <div class="mb-3">
                        <h6><span class="badge bg-info">Medium</span></h6>
                        <small>Not sold for 90-119 days</small>
                    </div>
                    <div class="mb-3">
                        <h6><span class="badge bg-primary">Low</span></h6>
                        <small>Not sold for 60-89 days</small>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Recommendation Types</h5>
                </div>
                <div class="card-body small">
                    <ul class="list-unstyled">
                        <li><i class="fas fa-bullhorn text-warning"></i> <strong>Promotion</strong> - Marketing campaigns</li>
                        <li><i class="fas fa-percentage text-danger"></i> <strong>Discount</strong> - Price reduction</li>
                        <li><i class="fas fa-box text-info"></i> <strong>Bundle</strong> - Bundle with fast movers</li>
                        <li><i class="fas fa-warehouse text-secondary"></i> <strong>Relocate</strong> - Move to high-demand location</li>
                        <li><i class="fas fa-star text-warning"></i> <strong>Featured</strong> - Display prominence</li>
                        <li><i class="fas fa-share-alt text-primary"></i> <strong>Social Media</strong> - Online promotion</li>
                        <li><i class="fas fa-undo text-muted"></i> <strong>Supplier Return</strong> - Return to supplier</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#settingsForm').on('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    toastr.success('Settings saved successfully!');
                    submitBtn.prop('disabled', false).html(originalText);
                },
                error: function(xhr) {
                    toastr.error('Error saving settings. Please try again.');
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    });
</script>
@endpush
