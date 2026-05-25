<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JurnalMengajar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jurnal_mengajar';

    protected $fillable = [
        'jadwal_id',
        'guru_id',
        'tanggal',
        'topik',
        'metode',
        'ringkasan',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
}
