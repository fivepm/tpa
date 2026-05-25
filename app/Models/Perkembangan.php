<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Perkembangan extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'perkembangan';
    protected $fillable = ['siswa_id', 'guru_id', 'tanggal', 'penilaian', 'catatan', 'jadwal_id'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }
}
