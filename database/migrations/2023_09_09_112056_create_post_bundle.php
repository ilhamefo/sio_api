<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->length(11);
            $table->text('caption');
            $table->timestamps();
        });
        
        Schema::create('post_images', function (Blueprint $table) {
            $table->id();
            $table->integer('post_id')->length(11);
            $table->string('image_path');
            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->integer('post_id')->length(11);
            $table->integer('user_id')->length(11);
            $table->text('comment_text');
            $table->timestamps();
        });

        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->integer('post_id')->length(11);
            $table->integer('user_id')->length(11);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('post_images');
    }
};
