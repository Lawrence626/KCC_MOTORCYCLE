<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendingImport extends Model
{
    protected $fillable = [
        'file_name',
        'uploaded_by',
        'data',
        'total_records',
        'valid_records',
        'invalid_records',
        'duplicate_records',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'data' => 'array',
        'total_records' => 'integer',
        'valid_records' => 'integer',
        'invalid_records' => 'integer',
        'duplicate_records' => 'integer',
        'reviewed_at' => 'datetime',
    ];

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
