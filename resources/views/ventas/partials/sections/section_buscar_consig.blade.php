<h4 class="padding_1em bg-navy">
    <i class="fa fa-arrow-right"></i> Datos asociados a la consignacion
</h4>
<div class="">
    <div class="form-group col-md-4">
        <label for="cliente">Cliente</label>
        <select class="select2" id="cliente_bus" style="width: 100%">
            @foreach($clientes as $c)
            <option value="{{$c->id}}">{{$c->nombre_full}}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group col-md-4">
        <label for="cliente">Nota de Pedido</label>
        <select class="select2" id="nota_bus" style="width: 100%">
            <option value="">...</option>
            @foreach($consignaciones as $c)
            <option value="{{$c->notapedido_id}}">{{$c->notapedido->n_pedido}}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group col-md-4">
        <label for="cliente">Guia de Remision</label>
        <select class="select2" id="guia_bus" style="width: 100%">
            <option value="">...</option>
            @foreach($consignaciones as $c)
                @if($c->guia_id)
                <option value="{{$c->guia_id}}">{{$c->guia->serial}}</option>
                @endif
            @endforeach
        </select>
    </div>

    <div class="form-group col-md-4">
        <br>
        <label for="fecha_venta">Fecha envio</label>
        <input class="form-control fecha" id="fecha_envio" style="width: 100%">
    </div>


    <div class="form-group col-md-2">
        <br>
        <label for="">---</label><br>
        <button type="button" class="btn btn-danger" id="btn_buscar_consignacion"> 
            <i class="fa fa-spinner fa-pulse" style="display: none;" id="icon-buscar-consig"></i> buscar consignacion
        </button>   
    </div>

    <div class="form-group col-md-12"><hr></div>

    <div class="form-group col-md-4">
        <label for="id_consignacion"><i class="fa fa-arrow-right"></i> Consignacion </label>
        <select name="id" class="select2" id="id_consignacion" style="width: 100%"></select>
    </div>
    <div class="form-group col-md-3">
        <label for=""></label><br>
        <button type="button" class="btn btn-primary" id="btn_cargar_consignacion"> 
            <i class="fa fa-spinner fa-pulse" style="display: none;" id="icon-cargar-consig"></i> cargar
        </button>   
    </div>    
</div>