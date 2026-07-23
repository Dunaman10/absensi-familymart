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
        Schema::create('geofence_settings', function (Blueprint $table) {
            $table->id();
            $table->string('office_name');
            $table->decimal('lat', 10, 8);
            $table->decimal('lng', 11, 8);
            $table->integer('radius_in_meters')->default(100);
            $table->time('shift_start')->nullable()->comment('Batas jam masuk (misal 08:00)');
            $table->time('shift_end')->nullable()->comment('Batas jam pulang (misal 17:00)');
            $table->integer('late_tolerance_minutes')->default(15)->comment('Toleransi keterlambatan dalam menit');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geofence_settings');
    }
};
