<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = "marcas";

    protected $fillable = ["name", "codigo", "observacion", "material_id", "precio"];

    // muchos mc
    public function marcaColeccion(){
        return $this->hasMany('App\ColeccionMarca');
    }

    // muchos a muchos mc
    public function colecciones(){
      return $this->belongsToMany('App\Coleccion','colecciones_marcas');
    }

    public function material(){
      return $this->belongsTo('App\Material','material_id');
    }

    public function fcreated(){
        $created = $this->created_at;
        $newcreated = date('d-m-Y',strtotime(str_replace('/', '-', $created)));
        return $newcreated;
    }

    public function fupdated(){
        $created = $this->updated_at;
        $newcreated = date('d-m-Y',strtotime(str_replace('/', '-', $created)));
        return $newcreated;
    }

    public function modelos(){
        return $this->hasMany('App\Modelo');
    }

    public function mc($coleccion, $marca){
        $query = Modelo::where("coleccion_id", $coleccion)->where("marca_id", $marca)->where("status_id", "<>", 5)->get()->groupBy("name");
        $data = $query->count();
        return $data;
    }

}