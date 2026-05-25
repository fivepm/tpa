<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jadwal extends Model
{
    use HasFactory, SoftDeletes, Auditable;
    protected $table = 'jadwal';
    protected $fillable = ['kelas_id', 'guru_id', 'materi_id', 'hari', 'jam_mulai', 'jam_selesai'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'id_kelas');
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id', 'id');
    }

    public function materi()
    {
        return $this->belongsTo(Materi::class, 'materi_id', 'id');
    }
}
