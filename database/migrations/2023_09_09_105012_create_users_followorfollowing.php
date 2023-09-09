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
        Schema::create('followorfollowing', function (Blueprint $table) {
            $table->id()->length(11);
            $table->integer('id_users')->length(11);
            $table->integer('id_follow')->length(11);
            $table->string('kategori')->length(20);
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
        Schema::dropIfExists('followorfollowing');
    }
};
