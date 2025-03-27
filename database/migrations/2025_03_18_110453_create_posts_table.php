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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('content');
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('post_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('post_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('post_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['post_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_likes');
        Schema::dropIfExists('post_comments');
        Schema::dropIfExists('post_images');
        Schema::dropIfExists('posts');
    }
};
