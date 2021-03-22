<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntervenientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intervenientes', function (Blueprint $table) {
            $table->id();
            $table->enum('role', ['AUTOR', 'REVISOR']);
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('artigos_id');
            $table->timestamps();

            $table->foreign('users_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->foreign('artigos_id')->references('id')->on('artigos')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('intervenientes');
    }
}
