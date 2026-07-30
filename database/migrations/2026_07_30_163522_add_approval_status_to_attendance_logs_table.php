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
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->enum('approval_status', ['APPROVED', 'PENDING', 'REJECTED'])->default('APPROVED')->after('status');
            $table->text('approval_notes')->nullable()->after('approval_status');
            $table->foreignId('approved_by')->nullable()->after('approval_notes')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['approval_status', 'approval_notes', 'approved_by']);
        });
    }
};
