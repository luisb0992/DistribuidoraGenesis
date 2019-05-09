<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\{GuiaRemision, MotivoGuia, ModeloGuia, Direccion, User, Departamento, Cliente, Modelo, Asignacion};

class GuiaRemisionController extends Controller
{

    public function index()
    {
        return view("guia_remision.index", [
            "guias"          => GuiaRemision::where("user_id", \Auth::id())->get(),
            "motivo"         => MotivoGuia::all(),
            "direcciones"    => Direccion::all(),
            "users"          => User::all(),
            "departamentos"  => Departamento::all(),
            "clientes"       => Cliente::all(),
            "modelos"        => Asignacion::where("user_id", \Auth::user()->id)->where("monturas", ">", 0)->get(),
        ]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        // $this->validate($request, [
        //     'serial'         => 'required',
        //     'guia'           => 'required',
        //     'cliente_id'     => '',
        //     'dir_salida'     => 'required',
        //     'dir_llegada'    => 'required',
        //     'motivo_guia_id' => 'required',
        //     'modelo_id'      => 'required',
        //     'montura'        => 'required',
        // ]);

        // return GuiaRemision::guiaStore($request);
    }

    public function show($id)
    {
        $query = GuiaRemision::findOrFail($id)->modeloGuias;
        $modelo = array();
        $montura = array();
        foreach ($query as $m) {
            $modelo []  = $m->modelo->name.' / ['.$m->modelo->id.']<br>';
            $montura [] = $m->montura.'<br>';
        }

        return response()->json([
            "modelo"    => $modelo,
            "montura"   => $montura,
        ]);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'cantidad'    => 'required',
            'peso'        => 'required',
            'descripcion' => 'required',
        ]);

        return GuiaRemision::guiaUpdate($request, $id);
    }

    public function destroy($id)
    {
        return GuiaRemision::guiaDestroy($id);
    }
}
