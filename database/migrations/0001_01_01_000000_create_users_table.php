<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('kode_user')->unique(); // U001, dst
            $table->string('name');
            $table->string('username')->unique();  // Sesuai navigasi sidebar
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif'); // Sesuai wireframe
            
            // RELASI: Pastikan menggunakan foreignId agar Laravel mengenalinya sebagai angka, bukan teks
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            
            $table->rememberToken();
            $table->timestamps();

        });
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
