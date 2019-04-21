<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleConsignacion extends Model
{
    protected $table = "detalle_consignaciones";
    protected $fillable = [
        "consignacion_id", "modelo_id", "montura", 
        "estuche", "costo", "status"
    ];
  
   public function consignacion(){
       return $this->belongsTo("App\Consignacion", "consignacion_id");
   }
  
   public function modelo(){
       return $this->belongsTo("App\Modelo", "modelo_id");
   }

   public static function modeloRetornadoOrConsignado($request){
        
        for ($i = 0; $i < count($request->modelo_id) ; $i++) {
            $id = DetalleConsignacion::where("consignacion_id", $request->id_consig)
                                        ->where("modelo_id", $request->modelo_id[$i])->value("id");

            $data = DetalleConsignacion::findOrFail($id);                            
            
            if ($request->montura[$i] != 0 || $request->montura[$i] != null) {
                $total_montura  = $data->montura - $request->montura[$i]; // calcular modelos restantes para ser devueltos
                $data->montura  = $request->montura[$i];
                $data->status   = 3; // consignado
                Modelo::descontarMonturaToModelosToConsignacion($request->modelo_id[$i], $total_montura);
            }else{
                $data->status    = 2; // recibido y devuelto a almacen
                $data->montura   = 0;
                Modelo::descontarMonturaToModelosToConsignacion($request->modelo_id[$i], $data->montura);
            }

            $data->save();
        }
        
        $dc = DetalleConsignacion::where("consignacion_id", $request->id_consig)
                                    ->whereNotIn("modelo_id", $request->modelo_id)->get();
        if (count($dc) > 0) {
            for ($m = 0; $m < count($dc); $m++) { 
                $data2 = DetalleConsignacion::findOrFail($dc[$m]->id);  
                Modelo::descontarMonturaToModelosToConsignacion($dc[$m]->modelo_id, $data2->montura);
                $data2->status   = 2; // recibido y devuelto a almacen
                $data2->montura  = 0;
                $data2->save();
            }
        }
   }
}
