<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\WaliKelas;
use App\Models\WebAuthnCredential;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, Auditable;

    protected $fillable = [
        'nama',
        'username',
        'email',
        'no_hp',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function kelas()
    {
        return $this->belongsToMany(
            Kelas::class,
            'kelas_user',
            'user_id',
            'kelas_id',
            'id',
            'id_kelas'
        );
    }

    public function waliKelas()
    {
        return $this->hasMany(WaliKelas::class, 'user_id', 'id');
    }

    public function webAuthnCredentials()
    {
        return $this->hasMany(WebAuthnCredential::class, 'user_id', 'id');
    }
}
