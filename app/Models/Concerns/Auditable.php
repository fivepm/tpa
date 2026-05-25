<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    /**
     * Field yang tidak perlu direkam ke audit log.
     * Model bisa override ini dengan mendefinisikan property $auditExclude.
     */
    protected function getAuditExcludeFields(): array
    {
        $default = ['updated_at', 'deleted_at', 'remember_token', 'password'];

        // Izinkan model untuk menambah pengecualian sendiri
        if (property_exists($this, 'auditExclude')) {
            return array_merge($default, $this->auditExclude);
        }

        return $default;
    }

    /**
     * Daftarkan listener event Eloquent setelah trait di-boot.
     */
    public static function bootAuditable(): void
    {
        // --- CREATED ---
        static::created(function ($model) {
            $newValues = $model->filterAuditFields($model->getAttributes());

            if (!empty($newValues)) {
                $model->createAuditLog('created', null, $newValues);
            }
        });

        // --- UPDATED ---
        static::updated(function ($model) {
            $exclude = $model->getAuditExcludeFields();

            // Ambil perubahan sebelum & sesudah, kecualikan field yang tidak penting
            $dirty = collect($model->getChanges())
                ->except($exclude)
                ->toArray();

            if (empty($dirty)) {
                return; // Tidak ada field penting yang berubah
            }

            $newValues = $dirty;
            $oldValues = collect($model->getOriginal())
                ->only(array_keys($dirty))
                ->toArray();

            $model->createAuditLog('updated', $oldValues, $newValues);
        });

        // --- DELETED (termasuk soft delete) ---
        static::deleted(function ($model) {
            // Bedakan soft delete vs hard delete
            $isSoftDelete = method_exists($model, 'trashed') && $model->trashed();

            $oldValues = $model->filterAuditFields($model->getAttributes());
            $model->createAuditLog('deleted', $oldValues, null);
        });

        // --- RESTORED (dari soft delete) ---
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                $newValues = $model->filterAuditFields($model->getAttributes());
                $model->createAuditLog('restored', null, $newValues);
            });
        }
    }

    /**
     * Hilangkan field yang dikecualikan dari array attribute.
     */
    protected function filterAuditFields(array $attributes): array
    {
        $exclude = $this->getAuditExcludeFields();

        return collect($attributes)
            ->except($exclude)
            ->filter(fn($value) => $value !== null)
            ->toArray();
    }

    /**
     * Tulis satu baris ke tabel audit_logs.
     */
    protected function createAuditLog(string $event, ?array $oldValues, ?array $newValues): void
    {
        try {
            $user     = Auth::user();
            $request  = app('request');

            AuditLog::create([
                'user_id'        => $user?->id,
                'user_name'      => $user?->nama ?? $user?->name,
                'event'          => $event,
                'auditable_type' => static::class,
                'auditable_id'   => $this->getKey(),
                'old_values'     => $oldValues,
                'new_values'     => $newValues,
                'ip_address'     => $request?->ip(),
                'user_agent'     => $request?->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Jangan sampai audit trail menggagalkan operasi utama
            logger()->error('AuditLog gagal ditulis: ' . $e->getMessage(), [
                'event'          => $event,
                'auditable_type' => static::class,
                'auditable_id'   => $this->getKey(),
            ]);
        }
    }
}
