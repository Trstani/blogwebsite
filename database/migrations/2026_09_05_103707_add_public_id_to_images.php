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
        // Add public_id to article_sections for image deletion
        if (Schema::hasTable('article_sections') && ! Schema::hasColumn('article_sections', 'image_public_id')) {
            Schema::table('article_sections', function (Blueprint $table) {
                $table->string('image_public_id')->nullable()->after('content')->comment('Cloudinary public_id for image sections');
            });
        }

        // Add public_id to articles for cover image deletion
        if (Schema::hasTable('articles') && ! Schema::hasColumn('articles', 'cover_image_public_id')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->string('cover_image_public_id')->nullable()->after('cover_image')->comment('Cloudinary public_id for cover image');
            });
        }

        // Add avatar_public_id to users for profile image deletion
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'avatar_public_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('avatar_public_id')->nullable()->after('avatar')->comment('Cloudinary public_id for avatar');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove public_id columns
        if (Schema::hasTable('article_sections') && Schema::hasColumn('article_sections', 'image_public_id')) {
            Schema::table('article_sections', function (Blueprint $table) {
                $table->dropColumn('image_public_id');
            });
        }

        if (Schema::hasTable('articles') && Schema::hasColumn('articles', 'cover_image_public_id')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->dropColumn('cover_image_public_id');
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'avatar_public_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('avatar_public_id');
            });
        }
    }
};
