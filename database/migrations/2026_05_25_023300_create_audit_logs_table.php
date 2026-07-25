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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable()->comment('Snapshot nama user saat aksi terjadi');

            $table->string('event')->comment('created | updated | deleted | restored');

            $table->string('auditable_type')->comment('Nama class model, contoh: App\\Models\\Siswa');
            $table->unsignedBigInteger('auditable_id')->comment('ID record yang berubah');
            $table->index(['auditable_type', 'auditable_id']);

            $table->json('old_values')->nullable()->comment('Data sebelum perubahan (null untuk created)');
            $table->json('new_values')->nullable()->comment('Data setelah perubahan (null untuk deleted)');

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
