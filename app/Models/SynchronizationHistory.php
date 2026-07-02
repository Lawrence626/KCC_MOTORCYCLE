<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SynchronizationHistory extends Model
{
    protected $table = 'synchronization_history';

    protected $fillable = [
        'file_name',
        'export_date',
        'import_date',
        'exported_by',
        'imported_by',
        'total_records',
        'imported_records',
        'duplicate_records',
        'failed_records',
        'skipped_records',
        'synchronization_status',
        'notes',
    ];

    protected $casts = [
        'export_date' => 'datetime',
        'import_date' => 'datetime',
        'total_records' => 'integer',
        'imported_records' => 'integer',
        'duplicate_records' => 'integer',
        'failed_records' => 'integer',
        'skipped_records' => 'integer',
    ];

    public function exportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'exported_by');
    }

    public function importedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }
}
