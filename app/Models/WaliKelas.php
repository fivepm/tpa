<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WaliKelas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'wali_kelas';
    protected $fillable = ['kelas_id', 'user_id'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'id_kelas');
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
