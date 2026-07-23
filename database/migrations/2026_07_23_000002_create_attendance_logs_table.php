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
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('geofence_id')->nullable()->constrained('geofence_settings')->nullOnDelete();
            $table->date('date');
            $table->time('time');
            $table->enum('type', ['MASUK', 'PULANG']);
            $table->enum('status', ['TEPAT_WAKTU', 'TERLAMBAT', 'PULANG_CEPAT', 'LUAR_RADIUS']);
            $table->decimal('lat', 10, 8);
            $table->decimal('lng', 11, 8);
            $table->integer('distance_from_office');
            $table->decimal('face_confidence_score', 5, 2)->nullable();
            $table->string('photo_path')->nullable();
            $table->text('notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
