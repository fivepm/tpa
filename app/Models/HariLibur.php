<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HariLibur extends Model
{
    use HasFactory, SoftDeletes, Auditable;
    protected $table = 'hari_libur';
    protected $fillable = ['tanggal', 'keterangan'];
}
