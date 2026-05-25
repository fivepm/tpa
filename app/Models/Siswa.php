<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'siswa';
    protected $fillable = [
        'nama',
        'nis',
        'kelas_id',
        'orangtua_id'
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'id_kelas');
    }

    public function orangtua()
    {
        return $this->belongsTo(User::class, 'orangtua_id', 'id');
    }

    public function perkembangan()
    {
        return $this->hasMany(Perkembangan::class, 'siswa_id', 'id');
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'siswa_id', 'id');
    }
}
