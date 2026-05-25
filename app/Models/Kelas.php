<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\WaliKelas;

class Kelas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kelas';

    protected $primaryKey = 'id_kelas';

    protected $fillable = ['nama_kelas'];

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'kelas_id', 'id_kelas');
    }

    public function guru()
    {
        return $this->belongsToMany(
            User::class,
            'kelas_user',
            'kelas_id',
            'user_id',
            'id_kelas',
            'id'
        );
    }

    public function waliKelas()
    {
        return $this->hasOne(WaliKelas::class, 'kelas_id', 'id_kelas');
    }
}
