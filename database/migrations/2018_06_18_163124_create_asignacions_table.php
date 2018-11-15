<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAsignacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asignaciones', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('modelo_id')->unsigned();
            $table->string('monturas')->nullable();
            $table->string('fecha')->nullable();

            $table->foreign('user_id')->references('id')
                                       ->on('users')
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
        Schema::dropIfExists('asignaciones');
    }
}