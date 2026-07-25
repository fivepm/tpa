<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    const UPDATED_AT = null;

    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'user_name',
        'event',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getEventLabelAttribute(): string
    {
        return match ($this->event) {
            'created'  => 'Dibuat',
            'updated'  => 'Diubah',
            'deleted'  => 'Dihapus',
            'restored' => 'Dipulihkan',
            default    => ucfirst($this->event),
        };
    }

    public function getModelNameAttribute(): string
    {
        return class_basename($this->auditable_type);
    }
}
