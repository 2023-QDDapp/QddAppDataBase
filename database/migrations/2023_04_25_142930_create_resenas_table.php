<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResenasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resenas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario_emisor');
            $table->foreign('id_usuario_emisor')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('id_usuario_receptor');
            $table->foreign('id_usuario_receptor')->references('id')->on('users')->onDelete('cascade');
            $table->string('mensaje', 500);
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
        Schema::dropIfExists('resenas');
    }
}
