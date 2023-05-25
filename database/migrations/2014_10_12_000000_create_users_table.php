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
        //los atributos: token_sesion, creacion_token_sesion 
        //se guardan en la tabla personal_access_tokens
       
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamp('ultimo_cambio_password')->nullable();
            $table->boolean('verificacion_email')->nullable();
            $table->timestamp('ultimo_inicio_sesion')->nullable();
        //nuevos atributos   
            $table->unsignedBigInteger('codigo_de_verificacion')->nullable();
            $table->boolean('estado')->nullable();


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
};
