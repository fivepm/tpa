<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->insert([
            'nama'       => 'Admin TPA',
            'username'   => 'admin',
            'password'   => Hash::make('admin123'),
            'role'       => 'pengurus',
            'no_hp'      => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('users')->where('username', 'admin')->where('role', 'pengurus')->delete();
    }
};
