<?php

namespace App\Http\Controllers;

use App\{
    Venta, Consignacion, Direccion, Departamento, RefItem, 
    StatusAdicionalVenta, Coleccion, Cliente, User, Factura, 
    TipoAbono, Pago, Letra, StatusLetra, ProtestoLetra, AdicionalVenta,
    NotaPedido
};
use Illuminate\Http\Request;
use App\Http\Requests\CreateVentaRequest;
use App\Http\Requests\CreateFacturaRequest;

class VentaController extends Controller
{

    public function index()
    {
        return view("ventas.index",[
            "ventas"         => Venta::all(),
            "items"          => RefItem::all(),
            "status_av"      => StatusAdicionalVenta::all(),
            "tipo_abono"     => TipoAbono::all(),
            "status_letra"   => StatusLetra::all(),
            "protesto_letra" => ProtestoLetra::all(),
            "clientes"       => Cliente::all(),
        ]);
    }

    public function createConsignacion()
    {
        return view("ventas.create_venta_consignacion",[
            "consignaciones" => Consignacion::where("status", 1)->get(["id", "created_at", "notapedido_id", "guia_id"]),
            "direcciones"    => Direccion::all(),
            "departamentos"  => Departamento::all(),
            "items"          => RefItem::all(),
            "status_av"      => StatusAdicionalVenta::all(),
            "tipo_abono"     => TipoAbono::all(),
            "status_letra"   => StatusLetra::all(),
            "protesto_letra" => ProtestoLetra::all(),
            "clientes"       => Cliente::all()
        ]);
    }

    public function createAsignacion()
    {
        return view("ventas.create_venta_asignacion",[
            "consignaciones" => Consignacion::where("status", 1)->get(["id"]),
            "direcciones"    => Direccion::all(),
            "departamentos"  => Departamento::all(),
            "items"          => RefItem::all(),
            "status_av"      => StatusAdicionalVenta::all(),
            "clientes"       => Cliente::all(),
            "users"          => User::all(),
            "tipo_abono"     => TipoAbono::all(),
        ]);
    }

    public function createDirecta()
    {
        return view("ventas.create_venta_directa",[
            "consignaciones" => Consignacion::where("status", 1)->get(["id"]),
            "direcciones"    => Direccion::all(),
            "colecciones"    => Coleccion::all(),
            "departamentos"  => Departamento::all(),
            "items"          => RefItem::all(),
            "status_av"      => StatusAdicionalVenta::all(),
            "clientes"       => Cliente::all(),
            "tipo_abono"     => TipoAbono::all(),
            "status_letra"   => StatusLetra::all(),
            "protesto_letra" => ProtestoLetra::all(),
        ]);
    }

    /**
     * @param  CreateVentaRequest peticion
     * @return [objeto]
     * Guardar la venta directa y sus subprocesos
     */
    public function storeVentaDirecta(CreateVentaRequest $request)
    {   
        return Venta::storeVentaDirecta($request);
    }

    /**
     * @param  CreateVentaRequest peticion
     * @return [objeto]
     * Guardar la venta por asignacion y sus subprocesos
     */
    public function storeVentaAsignacion(CreateVentaRequest $request)
    {   
        return Venta::storeVentaAsignacion($request);
    }

    /**
     * @param  CreateVentaRequest peticion
     * @return [objeto]
     * Guardar la venta por consignacion y sus subprocesos
     */
    public function storeVentaConsignacion(CreateVentaRequest $request)
    {   
        return Venta::storeVentaConsignacion($request);
    }

    public function generarFactura(CreateFacturaRequest $request)
    {   
        return Factura::generarFactura($request);
    }

    public function updateEstadoFactura(Request $request) // actualizar factura
    {   
        $this->validate($request, [
            'num_factura'           => '',
            'adicional_id'          => 'required',
            'ref_estadic_id'        => 'required|in:1,2,3',
        ]);

        return AdicionalVenta::updateEstadoFactura($request);
    }

    public function updateEstadoEstuche(Request $request) // actualizar estuche
    {   
        $this->validate($request, [
            'venta_id'                => '',
            'estado_entrega_estuche'  => 'required',
        ]);
        
        return Venta::updateEstadoEstuche($request);
    }

    public function show($id)
    {
        return view("ventas.show",[
            "venta" => Venta::findOrFail($id),
        ]);
    }

    public function cargarVenta($id)
    {
        $venta = Venta::with("cliente")->findOrFail($id);
        return response()->json($venta);
    }

    // busqueda avanzada de ventas
    public function buscarVentas($cliente, $fecha)
    {
        return Venta::buscarVentas($cliente, $fecha);
    }

    public function showVentaJson($id)
    {
        return Venta::showVentaJson($id);
    }

    public function cargarTablaVenta($id)
    {
        return Venta::cargarTablaVenta(Venta::findOrFail($id));
    }

    public function totalDeuda($id)
    {
        if (Venta::findOrFail($id)->totaldeuda()) {
            $total = Venta::findOrFail($id)->totaldeuda();
        }else{
            $total = Venta::findOrFail($id)->total;
        }
        return response()->json($total);
    }

    public function edit(Venta $id)
    {
        //
    }

    public function update(Request $request, Venta $venta)
    {
        //
    }

    public function destroy(Venta $venta)
    {
        //
    }
}
