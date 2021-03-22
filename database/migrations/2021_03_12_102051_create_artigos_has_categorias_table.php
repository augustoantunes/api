<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArtigosHasCategoriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('artigos_has_categorias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('categorias_id');
            $table->unsignedBigInteger('artigos_id');
            $table->timestamps();

            $table->foreign('categorias_id')->references('id')->on('categorias')->onDelete('CASCADE');
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
        Schema::dropIfExists('artigos_has_categorias');
    }
}
