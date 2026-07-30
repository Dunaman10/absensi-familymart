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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. Shift Pagi, Shift Siang, Shift Malam
            $table->time('start_time'); // e.g. 06:00:00
            $table->time('end_time'); // e.g. 14:00:00
            $table->integer('late_tolerance_minutes')->default(15);
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
