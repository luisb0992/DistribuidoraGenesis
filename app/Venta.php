<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class Venta extends Model
{
	protected $table = "ventas";
    protected $fillable = [
		"user_id", "cliente_id", 
		"direccion_id", "total", "fecha", "estado_entrega_estuche"
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

    public function movimientoVenta(){
        return $this->hasMany("App\MovimientoVenta");
    }

    public function adicionalVenta(){
        return $this->hasOne("App\AdicionalVenta");
    }

    public function pagos(){
        return $this->hasMany("App\Pago");
    }

    public function createF(){
        return $this->created_at->format("d-m-Y");
    }

    public function updateF(){
        return $this->updated_at->format("d-m-Y");
    }

     // comprobar el estado del estuche
    public static function estadoEstuche($request){
        return isset($request->status_estuche) ? $request->status_estuche : $request->status_estuche = null;
    }

    // setear el status de los estuches
    public function estatusEstuche(){
        if ($this->estado_entrega_estuche == "1") {
            $this->estado_entrega_estuche = "Entregados";
        }elseif ($this->estado_entrega_estuche == "0"){
            $this->estado_entrega_estuche = "No entregados";
        }elseif ($this->estado_entrega_estuche == null){
            $this->estado_entrega_estuche = "No posee estuches";
        }

        return $this->estado_entrega_estuche;
    }

    // totals restante
    public function totaldeuda(){
        return $this->pagos()->orderBy("id", "DESC")->value("restante");
    }

    // fecha cancelacion de la deuda
    public function fechaCancelacion(){
        return $this->pagos()->orderBy("id", "DESC")->value("fecha_cancelacion");
    }

    public function scopeFventa($query, $fecha)
    {   
        if ($fecha != "null") {
            $f = date('d-m-Y',strtotime(str_replace('/', '-', $fecha)));
            return $query->where('fecha', $f);
        }
    }

    // public static function ventasAdeudadas(){
    //     $venta = Venta::whereIn("id", Pago::all(["venta_id"]))->get();
    //     dd($venta->pagos());
    //     return $venta;
    // }
    // guardar datos de la venta
    public static function saveVenta($request){

        $v = Venta::create([
            'user_id'                   => Auth::id(),
            'cliente_id'                => $request->cliente_id,
            'direccion_id'              => $request->direccion_id,
            'total'                     => $request->total,
            'estado_entrega_estuche'    => Venta::estadoEstuche($request),
            'fecha'                     => date("d-m-Y"),
        ]);

        for ($i = 0; $i < count($request->check_model) ; $i++) {
            if ($request->check_model[$i] == 1 && $request->montura[$i] > 0) {    
                $v->movimientoVenta()->create([
                    'modelo_id'         => $request->modelo_id[$i],
                    'monturas'          => $request->montura[$i],
                    'estuches'          => $request->estuche[$i],
                    'precio_montura'    => $request->precio_montura[$i],
                    'precio_modelo'     => $request->precio_modelo[$i]
                ]);
            }
        }

        BitacoraUser::saveBitacora("Nueva venta registrada [".$v->id."] correctamente");

        return $v;

    }

    // logica para guardar venta directa
    public static function storeVentaDirecta($request){

        $db = DB::transaction(function() use ($request) {

            $v = Venta::saveVenta($request); // guardar venta
            $n = NotaPedido::saveNotaPedido($request, $motivo = 1); // guardar nota pedido

            for ($i = 0; $i < count($request->check_model) ; $i++) {
                Modelo::actualizarMonturasEnModelo($request->modelo_id[$i], $request->montura[$i], 4, $proced = "resta"); 
            }
            
            if ($request->checkbox_factura) {
                if ($request->checkbox_factura == 1) {
                    $factura = Factura::saveFactura($request); // guardar factura
                    AdicionalVenta::saVeAV($v->id, $factura->id, $request);
                }
            }

            if ($request->checkbox_guia) {
                if ($request->checkbox_guia == 1) {
                    if (GuiaRemision::where("serial", $request->serial.'-'.$request->guia)->count() > 0) {
                        return response()->json(2);
                    }else{
                        GuiaRemision::guiaStore($request, $motivo = 1); // guardar guia
                    }
                }
            }

            if ($request->checkbox_pago) {
                if ($request->checkbox_pago == 1) {
                    $factura = Pago::savePago($request, $v->id); // guardar pago
                }
            }
            
        });

        if (is_null($db)) { // fue todo correcto
            if ($request->ajax()) {
                return response()->json("1");
            }
        }else{ // fallo la operacion en algun sitio
            if ($request->ajax()) {
                return response()->json("0");
            }
        }
    }

    // logica para guardar ventas consignadas
    public static function storeVentaConsignacion($request){

        $db = DB::transaction(function() use ($request) {
            
            $v = Venta::saveVenta($request); // guardar venta
            $n = NotaPedido::saveNotaPedido($request, $motivo = 3); // guardar nota pedido

            if ($request->checkbox_factura) {
                if ($request->checkbox_factura == 1) {
                    $factura = Factura::saveFactura($request); // guardar factura
                    AdicionalVenta::saVeAV($v->id, $factura->id, $request);
                }
            }

            if ($request->checkbox_pago) {
                if ($request->checkbox_pago == 1) {
                    $factura = Pago::savePago($request, $v->id); // guardar pago
                }
            }

            Consignacion::updateStatusConsignacion($request->id_consig, 2); // status 2 = consignacion procesada

            DetalleConsignacion::modeloRetornadoOrConsignado($request); // sumar y retornar el modelo al almacen

            if ($request->id_guia != null) {
                GuiaRemision::guiaMotivo($request->id_guia, $motivo = 4); // actualizar motivo guia
            }
        });

        if (is_null($db)) { // fue todo correcto
            if ($request->ajax()) {
                return response()->json("1");
            }
        }else{ // fallo la operacion en algun sitio
            if ($request->ajax()) {
                return response()->json("0");
            }
        }
    }

    public static function storeVentaAsignacion($request){
        
        $db = DB::transaction(function() use ($request) {
            $venta  = Venta::saveVenta($request); // guardar venta
            $n      = NotaPedido::saveNotaPedido($request, $motivo = 1); // guardar nota pedido
            Asignacion::modeloRetornadoOrAsignados($request); // descontar modelos vendidos asignados
        });

        if (is_null($db)) { // fue todo correcto
            if ($request->ajax()) {
                return response()->json("1");
            }
        }else{ // fallo la operacion en algun sitio
            if ($request->ajax()) {
                return response()->json("0");
            }
        }
    }

    // logica para actualizar estuches
    public static function updateEstadoEstuche($request){
        
        $db = DB::transaction(function() use ($request) {
            $venta = Venta::findOrFail($request->venta_id);
            $venta->estado_entrega_estuche = $request->estado_entrega_estuche;
            $venta->save();
        });

        if (is_null($db)) { // fue todo correcto
            if ($request->ajax()) {
                return response()->json("1");
            }
        }else{ // fallo la operacion en algun sitio
            if ($request->ajax()) {
                return response()->json("0");
            }
        }
    }

     // cargar tabla para manipular los datos
    public static function cargarTablaVenta($venta){
        
        $data = array();
        $user = $cliente = $direccion = $dir = $tf = $factura = $adicional = "";

        $user       = $venta->user;
        $cliente    = $venta->cliente;
        $direccion  = $venta->direccion_id;
        $dir        = $venta->direccion->full_dir();
        $tf         = array_sum($venta->movimientoVenta->pluck("precio_modelo")->toArray());
        $factura    = $venta->adicionalVenta->factura;
        $adicional  = $venta->adicionalVenta;

        foreach ($venta->movimientoVenta as $m) {

            $data [] = "<tr>
                <td>
                    ".$m->modelo_id."
                    <input type='hidden' value='".$m->modelo_id."' name='venta_modelo_id[]'>
                    <input type='hidden' value='".$m->id."' name='mov_venta_id[]'>
                </td>
                <td>
                	<button type='button' class='btn-link btn_nm' value='".$m->modelo->name."'>
                        ".$m->modelo->name."
                    </button>
                </td>
                <td>
                    <select class='form-control venta_montura_modelo' name='venta_montura_modelo[]'>
                        <option value=''>...</option>
                        ".Asignacion::Monturas($m->monturas)."
                    </select>
                </td>
                <td>".$m->estuches."<input type='hidden' value='".$m->estuches."' class='venta_estuches'></td>
                <td>
                    <input type='number' step='0.01' max='999999999999' min='0' value='".$m->precio_montura."'  class='form-control venta_precio_montura' readonly='' data-valor='".$m->precio_montura."'>
                </td>
                <td>
                    <input type='number' class='form-control venta_preciototal' readonly='' value='".$m->precio_modelo."' step='0.01' max='999999999999' min='0'>
                </td>
                <td>
                    <input type='hidden' name='check_model[]' value='0' class='hidden_model' id='hidden_".$m->modelo_id."'>
                    <input type='checkbox' onclick='checkModelo(this)' class='check_model' value='".$m->modelo_id."'>
                </td>
            </tr>"; 
        }         

        return response()->json([
            "data"      => $data,
            "user"      => $user,
            "cliente"   => $cliente,
            "direccion" => $direccion,
            "dir"       => $dir,
            "tf"        => $tf,
            "factura"   => $factura,
            "adicional" => $adicional,
        ]);
    }

     // cargar datos de venta
    public static function showVentaJson($id){
        
        $venta          = Venta::findOrFail($id);
        $data           = array();

        foreach ($venta->movimientoVenta as $m) 
        {
            $data [] = "<tr>
                <td>".$m->modelo_id."</td>
                <td>".$m->monturas."</td>
                <td>".$m->estuches."</td>
                <td>".$m->precio_montura."</td>
                <td>".$m->precio_modelo."</td>
            </tr>"; 
        }               
        
        return response()->json([
            "data"              => $data,
            "user"              => $venta->user->fullName(),
            "cliente"           => $venta->cliente->nombre_full,
            "direccion"         => $venta->direccion->full_dir(),
            "fecha_venta"       => $venta->created_at->format("d-m-Y"),
            "status_estuche"    => $venta->estatusEstuche(),
            "total"             => array_sum($venta->movimientoVenta->pluck("precio_modelo")->toArray()),
        ]);
    }

     // busqueda avanzada de ventas
    public static function buscarVentas($cliente, $fecha){
        
        $ventas  = Venta::where("cliente_id", $cliente)
                        ->fventa($fecha)->get();
        $data   = array();

        foreach ($ventas as $v) 
        {
            $data [] = "
                <tr>
                    <td>".$v->id."</td>
                    <td>".$v->cliente->nombre_full."</td>
                    <td>S/ ".$v->total."</td>
                    <td>".$v->fecha."</td>
                    <td>".$v->estadoFacturaHtml()."</td>
                    <td>".$v->estadoEstucheHtml()."</td>
                    <td>".$v->estadoPagoHtml()."</td>
                    <td>
                        <a href=".route('ventas.show', $v->id)." class='btn bg-navy btn-xs' data-toggle='tooltip' title='Detalles de la venta'>
                            <i class='fa fa-eye'></i> 
                        </a>
                    </td>
                </tr>"; 
        }               
        
        return response()->json([
            "data" => $data,
        ]);
    }

    // pintar estado factura
    public function estadoFacturaHtml(){
        $data = "";

        if ($this->adicionalVenta) {
            if ($this->adicionalVenta->ref_estadic_id == 3) {
                $data = "<span>Entregada</span>";
            }else{
                $data = "<span>".$this->adicionalVenta->statusAdicional->nombre."</span>";
            }
        }else{
            $data = "<span>Factura no generada</span>";
        }

        return $data;
    }

    // pintar estado estuche
    public function estadoEstucheHtml(){

        $data = "";

        if ($this->estado_entrega_estuche == "1") {
            $data = "<span class='text-success'>".$this->estatusEstuche()."<i class='fa fa-check'></i></span>";
        }elseif ($this->estado_entrega_estuche == "0") {
            $data = "<span class='text-info'>".$this->estatusEstuche()."<i class='fa fa-info-circle'></i></span>";
        }else{
            $data = "<span class='text-danger'>".$this->estatusEstuche()."<i class='fa fa-remove'></i></span>";
        }

        return $data;
    }

    // pintar pagos
    public function estadoPagoHtml(){

        $data = "";

        if ($this->totaldeuda() == "0") {
            $data = "<span>Deuda cancelada <i class='fa fa-check text-success'></i></span>";
        }elseif ($this->totaldeuda() == "") {
            $data = "<span>Ningun pago registrado <i class='fa fa-info-circle text-info'></i></span>";
        }else{
            $data = "<span>Deuda por cancelar <i class='fa fa-warning text-warning'></i></span>";
        }

        return $data;
    }

    // cargar factura relacionada con la venta (en desarrollo...)
    public static function cargarFacturaVenta($venta){

    }

}
