<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAvaliacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('avaliacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('artigos_id');
            $table->unsignedBigInteger('users_id');
            $table->enum('titulo', ['S', 'AS','N','NA']);
            $table->enum('resumo', ['S', 'AS','N','NA']);
            $table->enum('contexto', ['S', 'AS','N','NA']);
            $table->enum('objectivo', ['S', 'AS','N','NA']);
            $table->enum('tema_original', ['S', 'AS','N','NA']);
            $table->enum('fundamento', ['S', 'AS','N','NA']);
            $table->enum('conhecimento', ['S', 'AS','N','NA']);
            $table->enum('problema', ['S', 'AS','N','NA']);
            $table->enum('procedimento', ['S', 'AS','N','NA']);
            $table->enum('resultado', ['S', 'AS','N','NA']);
            $table->enum('discucao', ['S', 'AS','N','NA']);
            $table->enum('objectivo_alcansado', ['S', 'AS','N','NA']);
            $table->enum('contribuicao', ['S', 'AS','N','NA']);
            $table->enum('limitacao', ['S', 'AS','N','NA']);
            $table->enum('nova_direcao', ['S', 'AS','N','NA']);
            $table->enum('consideracao', ['S', 'AS','N','NA']);
            $table->enum('conclusao', ['S', 'AS','N','NA']);
            $table->enum('linguagem_cientifica', ['S', 'AS','N','NA']);
            $table->enum('tabela_figura', ['S', 'AS','N','NA']);
            $table->enum('referencia', ['S', 'AS','N','NA']);
            $table->enum('biografia_referencia', ['S', 'AS','N','NA']);
            $table->enum('normas', ['S', 'AS','N','NA']);
            $table->enum('ditame', ['APTO', 'PORROGAR','REGEITADO']);
            $table->enum('flcorrecoes', ['S', 'N']);
            $table->longText('descricao');
            $table->timestamps();

            $table->foreign('artigos_id')->references('id')->on('artigos')->onDelete('cascade');
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('avaliacoes');
    }
}
