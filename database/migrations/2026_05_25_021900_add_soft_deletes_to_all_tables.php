<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'siswa',
            'kelas',
            'materi',
            'jadwal',
            'hari_libur',
            'users',
            'wali_kelas',
            'jurnal_mengajar',
            'perkembangan',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'siswa',
            'kelas',
            'materi',
            'jadwal',
            'hari_libur',
            'users',
            'wali_kelas',
            'jurnal_mengajar',
            'perkembangan',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
