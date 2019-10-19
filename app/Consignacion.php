<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class Consignacion extends Model
{
    protected $table    = "consignaciones";
    protected $fillable = [
        "cliente_id", "user_id", "fecha_envio", "total", 
        "guia_id", "notapedido_id", "status"
    ];
    
    public function cliente(){
      return $this->belongsTo("App\Cliente", "cliente_id");
    }

    public function guia(){
      return $this->belongsTo("App\GuiaRemision", "guia_id");
    }

    public function notapedido(){
      return $this->belongsTo("App\NotaPedido", "notapedido_id");
    }
    
    public function user(){
      return $this->belongsTo("App\User", "user_id");
    }

    public function detalleConsignacion(){
      return $this->hasMany("App\DetalleConsignacion");
    }
    
    public function createF(){
      return $this->created_at->format("d-m-Y");
    }

    public function modelosConsignados($id){
      return DetalleConsignacion::where("consignacion_id", $id)->where("status", 3)->count();
    }

    // scopes querys
    public function scopeFechaenvio($query, $fecha)
    {   
        if ($fecha != "null") {
            $f = date('d/m/Y',strtotime(str_replace('/', '-', $fecha)));
            return $query->where('fecha_envio', $f);
        }
    }

    public function scopeNotap($query, $nota)
    {   
        if ($nota != "null") {
            return $query->where('notapedido_id', $nota);
        }
    }

    public function scopeGuiar($query, $guia)
    {   
        if ($guia != "null") {
            return $query->where('guia_id', $guia);
        }
    }

    // guardar consignacion
    public static function saveConsignacionAndDetalle($request, $guia, $nota){

        $consignacion = Consignacion::create([
            'cliente_id'    => $request->cliente_id,
            'fecha_envio'   => $request->fecha_envio,
            'user_id'       => Auth::id(),
            'guia_id'       => $guia != null ? $guia->id : null,
            'notapedido_id' => $nota,
            'total'         => $request->total,
            'status'        => 1,
        ]);

        for ($i = 0; $i < count($request->check_model) ; $i++) {
            if ($request->check_model[$i] == 1 && $request->montura[$i] > 0) {
                $consignacion->detalleConsignacion()->create([
                    'modelo_id'   => $request->modelo_id[$i],
                    'montura'     => $request->montura[$i],
                    'estuche'     => $request->estuche[$i],
                    'status'      => 1,
                ]);
                Modelo::descontarMonturaToModelos($request->modelo_id[$i], $request->montura[$i]);
            }
        }

        BitacoraUser::saveBitacora("Consignacion creada");
    }

    // validar si la consignacion tiene o no guia
    public static function consigStore($request){
        $guia = '';
        if ($request->checkbox == 1) {
            if (GuiaRemision::where("serial", $request->serial.'-'.$request->guia)->count() > 0) {
                return response()->json(1);
            }else{
                $guia = GuiaRemision::guiaStore($request, $motivo = 3); // guardar guia
            }
        }
        $nota = NotaPedido::saveNotaPedido($request, $motivo = 3); // guardar nota pedido
        Consignacion::saveConsignacionAndDetalle($request, $guia, $nota->id); // guardar consignacion y detalle

        return response()->json("ok");
    }

    // validar si la consignacion tiene o no guia
    public static function updateConsig($request, $id){

        $consig = Consignacion::findOrFail($id);
        $consig->fill($request->all());
        $consig->save();

        BitacoraUser::saveBitacora("Consignacion [".$id."] actualizada  correctamente");
        
        if ($request->ajax()) {
            return response()->json(1);
        }else{
            return back()->with([
                'flash_message' => 'Consignacion actualizada.',
                'flash_class' => 'alert-success'
            ]);
        }
    }

    // añadir mas modelos a la consignacion
    public static function añadirModelos($request, $id){

        $db = DB::transaction(function() use ($request, $id) {
            $consig             = Consignacion::findOrFail($id);
            $consig->total      = $request->total + $consig->total;
            $consig->user_id    = Auth::id();
            $consig->save();

            for ($i = 0; $i < count($request->check_model) ; $i++) {
                if ($request->check_model[$i] == 1 && $request->montura[$i] > 0) {
                    $consig->detalleConsignacion()->create([
                        'modelo_id'   => $request->modelo_id[$i],
                        'montura'     => $request->montura[$i],
                        'estuche'     => $request->estuche[$i],
                        'status'      => 1,
                    ]);
                    Modelo::descontarMonturaToModelos($request->modelo_id[$i], $request->montura[$i]);
                }   
            }

            if ($consig->notapedido_id) {
                $nt             = NotaPedido::findorFail($consig->notapedido_id);
                $nt->total      = $request->total + $nt->total;
                $nt->user_id    = Auth::id();
                $nt->save();

                for ($j = 0; $j < count($request->check_model) ; $j++) {
                    if ($request->check_model[$j] == 1 && $request->montura[$j] > 0) {
                        $nt->movNotaPedido()->create([
                            'modelo_id' => $request->modelo_id[$j],
                            'monturas'  => $request->montura[$j],
                            'estuches'  => $request->estuche[$j]
                        ]);
                    }
                }
            }

            BitacoraUser::saveBitacora("Consignacion [".$id."] actualizada  correctamente");
        });

        if (is_null($db)) { // fue todo correcto
            if ($request->ajax()) {
                return response()->json("1");
            }else{
                return back()->with([
                    'flash_message' => 'Consignacion actualizada.',
                    'flash_class'   => 'alert-success'
                ]);
            }
        }
    }

    //actualizar status en consig;
    public static function updateStatusConsignacion($id, $status){
        $consig = Consignacion::findOrFail($id);
        $consig->status = $status;
        $consig->save();
    }

     // cargar datos de la consig - json
    public static function showConsig($id, $request){
        
        $data_det_guia = $names = $data = "";

        $consig = Consignacion::with("cliente", "guia.detalleGuia.item", "guia.motivo_guia", "guia.cliente")->findOrFail($id);

        if (!$request->view) {
            $data = Consignacion::cargarDataConsigJson($consig);
        }else{
            $data = Consignacion::cargarDataConsigJsonView($consig);
        }

        $names = Consignacion::cargarNameModelosJson($consig);
        
        if ($consig->guia) {
            $data_det_guia = Consignacion::cargarGuiaJson($consig);
        }

        return response()->json([
            "data"          => $data,
            "names"         => $names,
            "consig"        => $consig,
            "data_det_guia" => $data_det_guia,
            "dir_llegada"   => ($consig->guia) ? $consig->guia->dirLLegada->full_dir() : 'vacio',
            "dir_salida"    => ($consig->guia) ? $consig->guia->dirSalida->full_dir()  : 'vacio',
        ]);
    }

     // cargar datos de la consig - pintar html
    public static function detalleConsigGuiaNotaHtml($id){
        
        $data_det_guia = $modelos = "";

        $consig = Consignacion::with("cliente", "guia.detalleGuia.item", "guia.motivo_guia", "guia.cliente", "notapedido.direccion")->findOrFail($id);

        $modelos = Consignacion::cargarDataConsigJsonView($consig);
        
        if ($consig->guia) {
            $data_det_guia = Consignacion::cargarGuiaJson($consig);
        }

        return response()->json([
            "consig"        => $consig,
            "modelos"       => $modelos,
            "data_det_guia" => $data_det_guia,
            "dir_llegada"   => ($consig->guia) ? $consig->guia->dirLLegada->full_dir() : 'vacio',
            "dir_salida"    => ($consig->guia) ? $consig->guia->dirSalida->full_dir()  : 'vacio',
            "dir_nota"      => ($consig->notapedido) ? $consig->notapedido->direccion->full_dir()  : 'vacio',
        ]);
    }

    // cargar la guia mediante html
    public static function cargarGuiaJson($consig){
        $data = array();
        foreach ($consig->guia->detalleGuia as $dg) {
            $data [] = "
                <tr>
                    <td>".$dg->item->nombre."</td>
                    <td>".$dg->cantidad."</td>
                    <td>".$dg->peso."</td>
                    <td>".$dg->descripcion."</td>
                </tr>"; 
        }
        return $data;
    }

    // cargar los nombres de modelos para ser buscados
    public static function cargarNameModelosJson($consig){
        $names = array();
        foreach ($consig->detalleConsignacion->unique("modelo.name") as $val) {
            $names [] = "<button type='button' class='btn-link btn_nm' value='".$val->modelo->name."'>
                            ".$val->modelo->name."
                        </button>"; 
        }

        return $names;
    }

    // cargar datos completos de la consignacion html
    public static function cargarDataConsigJson($consig){
        $data = array();
        foreach ($consig->detalleConsignacion as $dc) {
            $data [] = "<tr>
                            <td>".$dc->modelo_id."<input type='hidden' value='".$dc->modelo_id."' id='modelo_id_".$dc->modelo_id."' name='modelo_id[]'></td>
                            <td>".$dc->modelo->name."</td>
                            <td>
                                <select class='form-control montura_modelo' name='montura[]' id='montura_".$dc->id."'>
                                    <option value=''>...</option>
                                    ".Asignacion::Monturas($dc->montura)."
                                </select>
                            </td>
                            <td>".$dc->estuche."<input type='hidden' value='".$dc->estuche."' name='estuche[]' class='estuches'></td>
                            <td id='td_precio'>
                                <input type='number' step='0.01' max='999999999999' min='0' value='".ColeccionMarca::cargarPrecios($dc->modelo->coleccion_id, $dc->modelo->marca_id)->precio_venta_establecido."' name='precio_montura[]' class='form-control numero costo_modelo' id='costo_".$dc->id."'>
                            </td>
                            <td><input type='text' name='precio_modelo[]' class='preciototal' readonly=''></td>
                            <td>".Consignacion::validarStatusDeModeloEnConsignacion($dc)."</td>
                            <td>
                                <input type='hidden' name='check_model[]' value='0' class='hidden_model' id='hidden_".$dc->modelo_id."'>
                                <input type='checkbox' onclick='checkModelo(this)' class='check_model' value='".$dc->modelo_id."'>
                            </td>
                        </tr>"; 
        }

        return $data;
    }

    // cargar datos completos de la consignacion html (solo vista)
    public static function cargarDataConsigJsonView($consig){
        $data = array();
        foreach ($consig->detalleConsignacion as $dc) {
            $data [] = "<tr>
                            <td>".$dc->modelo_id."</td>
                            <td>".$dc->modelo->name."</td>
                            <td>".$dc->montura."</td>
                            <td>".$dc->estuche."</td>
                            <td>
                                ".ColeccionMarca::cargarPrecios($dc->modelo->coleccion_id, $dc->modelo->marca_id)->precio_venta_establecido."
                            </td>
                            <td>".Consignacion::validarStatusDeModeloEnConsignacion($dc)."</td>
                        </tr>"; 
        }

        return $data;
    }

    // validar status de los modelos en la consignacion
    public static function validarStatusDeModeloEnConsignacion($data){
        if ($data->status == 1) {
            $status = "Enviado a cliente";
        }elseif ($data->status == 2) {
            $status = "En almacen";
        }elseif ($data->status == 3) {
            $status = "Consignado";
        }

        return $status;
    }

    // cargar cosnignacion en un select filtrado
    public static function cargarConsigSelect($request, $cliente, $fecha, $nota, $guia){

        if ($request->view) {

            $consig = Consignacion::where("cliente_id", $cliente)
                                    ->fechaenvio($fecha)
                                    ->notap($nota)
                                    ->guiar($guia)
                                    ->get();
        }else{

            $consig = Consignacion::where("cliente_id", $cliente)
                                    ->where("status", 1)
                                    ->fechaenvio($fecha)
                                    ->notap($nota)
                                    ->guiar($guia)
                                    ->get();
        }

        $data = array();

        for ($i = 0; $i < count($consig); $i++) { 
            
            $data [] = "<option value=".$consig[$i]->id.">[".$consig[$i]->id."] - ".$consig[$i]->fecha_envio."</option>";
        
        }

        return response()->json($data);

    }

}
