<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModeloGuiasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modelo_guias', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('guia_remision_id')->nullable();
            $table->unsignedInteger('modelo_id')->nullable();
            $table->unsignedInteger('montura')->nullable();
            $table->unsignedInteger('estuche')->nullable();

            $table->foreign('guia_remision_id')->references('id')
                                        ->on('guia_remision')
                                        ->onDelete('cascade');

            $table->foreign('modelo_id')->references('id')
                                        ->on('modelos')
                                        ->onDelete('cascade');


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
        Schema::dropIfExists('modelo_guias');
    }
}
