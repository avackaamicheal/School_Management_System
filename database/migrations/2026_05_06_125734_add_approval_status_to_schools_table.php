<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            Schema::table('schools', function (Blueprint $table) {
                $table->enum('approval_status', ['pending', 'approved', 'rejected'])
                    ->default('approved') // existing schools stay approved
                    ->after('is_active');
                $table->text('rejection_reason')->nullable()->after('approval_status');
                $table->timestamp('approved_at')->nullable()->after('rejection_reason');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            Schema::table('schools', function (Blueprint $table) {
                $table->dropColumn(['approval_status', 'rejection_reason', 'approved_at']);
            });
        });
    }
};
