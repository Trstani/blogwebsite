<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('article_sections', function (Blueprint $table) {
            $table->unsignedTinyInteger('image_position_x')
                ->default(50);

            $table->unsignedTinyInteger('image_position_y')
                ->default(50);
        });
    }

    public function down(): void
    {
        Schema::table('article_sections', function (Blueprint $table) {
            $table->dropColumn([
                'image_position_x',
                'image_position_y',
            ]);
        });
    }
};