<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;

class NotaPedido extends Model
{
    protected $table = "nota_pedidos";

    protected $fillable = [
        "n_pedido", "motivo_nota_id", "user_id", 
        "cliente_id", "direccion_id", "total"
    ];

    // relaciones
    public function user(){
        return $this->belongsTo("App\User", "user_id");
    }

    public function cliente(){
        return $this->belongsTo("App\Cliente", "cliente_id");
    }

    public function direccion(){
        return $this->belongsTo("App\Direccion", "direccion_id");
    }

    public function motivo(){
        return $this->belongsTo("App\MotivoGuia", "motivo_nota_id");
    }

    public function movNotaPedido(){
        return $this->hasMany("App\MovNotaPedido", "notapedido_id");
    }

    public function createF(){
        return $this->created_at->format("d-m-Y");
    }

    public function updateF(){
        return $this->updated_at->format("d-m-Y");
    }

    // guardar datos de la nota pedido
    public static function saveNotaPedido($request, $motivo){

        $n = NotaPedido::create([
            'n_pedido'          => $request->n_pedido,
            'motivo_nota_id'    => $motivo,
            'user_id'           => Auth::id(),
            'cliente_id'        => $request->cliente_id,
            'direccion_id'      => $request->direccion_id,
            'total'             => $request->total,
        ]);

        for ($i = 0; $i < count($request->modelo_id) ; $i++) {
            if ($request->montura[$i] != 0 || $request->montura[$i] != null) {
                $n->movNotaPedido()->create([
                    'modelo_id' => $request->modelo_id[$i],
                    'monturas'  => $request->montura[$i],
                    'estuches'  => $request->estuche[$i]
                ]);
            }
        }

        BitacoraUser::saveBitacora("Nueva Nota registrada [".$n->id."] correctamente");

        return $n;

    }
}
