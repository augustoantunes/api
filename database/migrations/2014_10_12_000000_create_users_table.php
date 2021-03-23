<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(1);
            $table->string('name');
            $table->string('email')->unique();
            $table->string('telefone')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('imagem')->nullable();
            $table->enum('sexo', ['M', 'F'])->nullable();
            $table->string('data_nascimento')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
