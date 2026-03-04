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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete(); // Who wrote it

            $table->string('title');
            $table->text('content');
            $table->string('target_role')->nullable(); // e.g., 'Student', 'Teacher'. Null = Everyone.

            $table->dateTime('publish_at')->nullable(); // When it goes live
            $table->dateTime('expires_at')->nullable(); // When it disappears
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
