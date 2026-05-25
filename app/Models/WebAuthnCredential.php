<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebAuthnCredential extends Model
{
    protected $table = 'webauthn_credentials';

    protected $fillable = [
        'user_id',
        'credential_id',
        'public_key',
        'counter',
        'device_name',
        'last_used_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'counter'      => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
