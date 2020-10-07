<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reserva', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idCliente');
            $table->foreignId('idSalon');
            
            $table->date('fecha');
            $table->double('pago',8,2);
            $table->string('estado',50);
            $table->datetime('fechaInicio',0);
            $table->datetime('fechaFin',0);
            $table->foreign('idCliente')->references('id')->on('cliente');
            $table->foreign('idSalon')->references('id')->on('salon');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reserva');
    }
}
