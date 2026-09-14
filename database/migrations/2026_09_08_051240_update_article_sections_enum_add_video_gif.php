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
        Schema::table('article_sections', function (Blueprint $table): void {
            // Change enum from ['text', 'image'] to ['text', 'image', 'video', 'gif']
            $table->enum('type', ['text', 'image', 'video', 'gif'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('article_sections', function (Blueprint $table): void {
            // Revert to original enum
            $table->enum('type', ['text', 'image'])->change();
        });
    }
};
