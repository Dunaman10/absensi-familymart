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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 50)->unique()->comment('Digunakan untuk Login');
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('position')->nullable()->comment('Jabatan / Tim Karyawan');
            $table->string('password');
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->enum('account_status', ['Aktif', 'Non-Aktif', 'Pending Biometrik'])->default('Pending Biometrik');
            $table->text('face_descriptor')->nullable()->comment('Data Float32Array (JSON) dari face-api.js');
            $table->string('registered_face_photo')->nullable()->comment('Path foto wajah acuan saat registrasi');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('nip')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
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
