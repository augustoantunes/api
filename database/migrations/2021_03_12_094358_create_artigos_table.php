<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArtigosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('artigos', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('SUBMETIDO');
            $table->string('lang')->default('pt');
            $table->string('titulo');
            $table->string('subtitulo')->nullable();
            $table->string('imagem')->nullable();
            $table->string('tipoartigo')->nullable();
            $table->string('slug')->nullable();
            $table->longText('resumo');
            $table->longText('referencias')->nullable();
            $table->string('file');
            $table->bigInteger('visualizacoes')->default(0);
            $table->unsignedBigInteger('edicoes_id')->nullable();
            $table->timestamps();

            $table->foreign('edicoes_id')->references('id')->on('edicoes')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('artigos');
    }
}
