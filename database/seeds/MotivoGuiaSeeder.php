<?php

use Illuminate\Database\Seeder;

class MotivoGuiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $status = array(
	        array('id' => '1','nombre' => 'Venta'),
	        array('id' => '2','nombre' => 'Emisor_itinerante'),
	        array('id' => '3','nombre' => 'Consignacion'),
	        array('id' => '4','nombre' => 'Devolucion')
	    );
      //insert manual a una base de datos con array
      \DB::table('motivo_guias')->insert($status);
    }
}