<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Modelo extends Model
{
    protected $table = "modelos";

    protected $fillable = [
      "codigo", "name", "descripcion_modelo", "coleccion_id", 
      "marca_id", "montura", "status_id", "estuche"
    ];

    // relaciones
    public function coleccion(){
    	return $this->belongsTo("App\Coleccion", "coleccion_id");
    }

	public function marca(){
    	return $this->belongsTo("App\Marca", "marca_id");
    }

    public function status(){
        return $this->belongsTo("App\Status", "status_id");
    }

    // muchos users - modelos
    public function modeloUsers(){
        return $this->hasMany('App\Asignacion');
    }

    public function users(){
      return $this->belongsToMany('App\User','asignaciones');
    }

    // modelos - guias
    public function modeloGuias(){
        return $this->hasMany('App\ModeloGuia');
    }

    public function guias(){
      return $this->belongsToMany('App\GuiaRemision','modelo_guias');
    }
    
    public function asignaciones(){
      return $this->hasMany('App\Asignacion');
    }
    
    //----------------------- funciones personalizadas --------------------------------------------
    //
    // modelos a mostrar dependiendo del rol
    public static function modelosToUser(){
      if (\Auth::user()->rol_id == 1) {
        $data = Modelo::where("montura", ">", 0)->get();
      }else{
        $data = $this->asignaciones()->where("user_id", \Auth::id())->where("monturas", ">", 0)->get();
      }
      return $data;
    }

    // descontar monturas de los modelos
    public static function descontarMonturaToModelos($id, $montura){
        $data = Modelo::findOrFail($id);
        if (($data->montura - $montura)  == 0) {
            $data->status_id = 2;
        }else{
            $data->status_id = 1;
        }
        $data->montura = $data->montura - $montura;
        return $data->save();
    }

    // descontar consignacion - modelos
    public static function descontarMonturaToModelosToConsignacion($id, $montura){
        $data = Modelo::findOrFail($id);
        if (($data->montura + $montura) == 0) {
            $data->status_id = 2;
        }else{
            $data->status_id = 1;
        }
        $data->montura = $data->montura + $montura;
        return $data->save();
    }

    //---------------- metodos propios ------------------------

    // eliminar varios modelo a la vez
    public static function deleteAll($request){
        $modelos = Modelo::with("marca","status")
                        ->where("coleccion_id", $request->col_del)
                        ->where("marca_id", $request->mar_del)
                        ->get();

        foreach ($modelos as $mod) {
            $mod->status_id = 5;
            $mod->name = $mod->name." (Eliminado ".date("h:i:s").")";
            $mod->save();

            BitacoraUser::saveBitacora("Eliminacion de modelo (".$mod->name.")");
        }

        if ($request->ajax()) {
            return response()->json($mod);
        }
    } 

    // actualizar varios modelos
    public static function updateAll($request){
        
        // recorremos la cantidad de modelos
        for ($m = 0 ; $m < count($request->id); $m++) {
            $modelo = Modelo::find($request->id[$m]);
            $modelo->name = $request->name[$m];
            $modelo->descripcion_modelo = $request->descripcion_modelo[$m];
            
            if($request->montura[$m] == ''){
                $modelo->montura = $modelo->montura;
            }else{
                $modelo->montura = $request->montura[$m];
                if ($request->montura[$m] > 0) {
                    $modelo->status_id = 1;
                }
            }
            
            $modelo->save();
            BitacoraUser::saveBitacora("Actualizacion de modelo(s) (".$modelo->name.")");
        }

        if ($request->ajax()) {
            return response()->json($modelo);
        }
    }

    // almacenar varios modelos 
    public static function store($request){

         // obtenemos la coleccion
        $coleccion = Coleccion::findOrFail($request->id_coleccion);

        // recorremos la cantidad de modelos
        for ($m = 0 ; $m < count($request->name); $m++) {

            // y los multiplicamos por la cantidad de ruedas o cajas sea el caso
            if ($request->caja[$m]) {
                $cajas = $request->caja[$m];
            }else{
                $cajas = $request->rueda;
            }
            
            for ($i = 0; $i < $cajas; $i++) {
                $modelo = new Modelo();
                $modelo->codigo = $i + 1;
                $modelo->name = $request->name[$m];
                $modelo->descripcion_modelo = $request->descripcion_modelo[$m];
                $modelo->coleccion_id = $request->id_coleccion;
                $modelo->marca_id = $request->marca_id;
                $modelo->montura = $request->montura[$m];
                $modelo->estuche = $request->estuche[$m];
                $modelo->status_id = 1;
                $modelo->save();
            }
        }

        BitacoraUser::saveBitacora("Creacion de nuevo(s) modelo(s) para la coleccion (".$coleccion->name.")");

        if ($request->ajax()) {
            return response()->json($modelo);
        }
    }

}
