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
        Schema::table('comments', function (Blueprint $table) {
            // Add parent_id for replies (self-referencing relationship)
            // NULL = root comment
            // non-null = reply to another comment
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('comments')
                ->cascadeOnDelete();

            // Index for efficient querying replies by parent
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeignKeyIfExists('comments_parent_id_foreign');
            $table->dropIndex('comments_parent_id_index');
            $table->dropColumn('parent_id');
        });
    }
};
