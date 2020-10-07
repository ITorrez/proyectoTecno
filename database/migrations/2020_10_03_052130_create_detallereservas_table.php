<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetallereservasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detallereserva', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idReserva');
            $table->foreignId('idPaqueteitem');
            
            $table->foreign('idReserva')->references('id')->on('reserva');
            $table->foreign('idPaqueteitem')->references('id')->on('paqueteitem');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detallereservas');
    }
}
