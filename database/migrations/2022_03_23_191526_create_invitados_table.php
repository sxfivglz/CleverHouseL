<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvitadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invitados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_invitado');
            $table->unsignedBigInteger('usuario_fk');
            $table->foreign('usuario_fk')->references('id')->on('users');
            $table->string('columna_1')->nullable();
            $table->string('columna_2')->nullable();
            $table->string('columna_3')->nullable();
            $table->string('columna_4')->nullable();
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
        Schema::dropIfExists('invitados');
    }
}
