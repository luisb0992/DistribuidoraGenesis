{{-- Busqueda de consignacion --}}
<div class="row">
    <div class="form-group col-md-4">
        <label for="cliente_bus">Cliente</label>
        <select class="select2" id="cliente_bus" style="width: 100%">
            @foreach($clientes as $c)
            <option value="{{$c->id}}">{{$c->nombre_full}}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group col-md-4">
        <label for="nota_bus">Nota de Pedido</label>
        <select class="select2" id="nota_bus" style="width: 100%">
            <option value="">...</option>
            @foreach($consignaciones as $c)
            <option value="{{$c->notapedido_id}}">{{$c->notapedido->n_pedido}}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group col-md-4">
        <label for="guia_bus">Guia de Remision</label>
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
        <label for="fecha_env">Fecha envio</label>
        <input type="text" name="fecha_env" class="form-control fecha" id="fecha_env">
    </div>

    <div class="form-group col-md-4 text-left">
        <label>-</label><br>
        <button type="button" class="btn btn-danger" id="btn_buscar_consignacion"> 
            <i class="fa fa-spinner fa-pulse" style="display: none;" id="icon-buscar-consig"></i> buscar consignacion
        </button>   
    </div>

    <div class="form-group col-md-12"><hr></div>

    <div class="form-group col-md-4">
            <label for="id_consignacion"><i class="fa fa-arrow-right"></i> Consignacion </label>
            <select name="id" class="select2" id="id_consignacion" style="width: 100%"></select>
    </div>
    <div class="form-group col-md-4">
        <label></label><br>
        <button type="button" class="btn btn-primary" id="btn_cargar_consignacion"> 
            <i class="fa fa-spinner fa-pulse" style="display: none;" id="icon-cargar-consig"></i> cargar
        </button>   
    </div>
</div>

<!-- ------------------------------------------------------------------------------------------ -->

{{-- DETALLES DE LA CONSIGNACION - MODELOS --}}
<div class="row" id="section_detalle_consig" style="display: none;">
    <h4 class="padding_1em bg-navy">
        <i class="fa fa-arrow-right"></i> Consignacion
    </h4>
    <div class="form-group col-lg-6">
        <label class="">Cliente</label>
        <p class="list-group-item"><strong id="cliente_consig"></strong></p>
    </div>
                
    <div class="form-group col-lg-3">
        <label class="">Fecha de envio</label>
        <p id="fecha_envio_consig" class="list-group-item"></p>
    </div>

    <div class="form-group col-lg-3">
        <label>---</label>
        <p id="guia_consig" class="list-group-item"></p>
    </div>
    
    <div class="form-group col-lg-12">
        <table class="table data-table table-bordered table-striped table-hover search_consig" style="width: 100%">
            <caption>Detalles de la consignacion</caption>
            <thead class="bg-navy disabled">
                <tr>
                    <th>[Codigo]</th>
                    <th>Nombre</th>
                    <th>Monturas</th>
                    <th>Estuches</th>
                    <th class="text-nowrap">
                        Precio <strong data-toggle="tooltip" title="Precio de venta establecido en la marca y coleccion">(PVE)</strong>
                    </th>
                    <th class="bg-primary">Total (S/)</th>
                </tr>
            </thead>
            <tbody id="data_modelos_consig"></tbody>
        </table>
        <br>
    </div>
</div>

{{-- GUIA DE REMISION --}}
<div class="row" id="section_guia_remision" style="display: none;">
    <h4 class="padding_1em bg-navy">
        <i class="fa fa-arrow-right"></i> Guia de remision
    </h4>
    <div class="form-group col-lg-4">
        <label>Nº Serie - Codigo</label> 
        <p class="list-group-item" id="serie_consig"></p>
    </div>
    <div class="form-group col-lg-4">
        <label>Direccion de salida</label> 
        <p class="list-group-item" id="dir_salida_consig"></p>
    </div>
    <div class="form-group col-lg-4">
        <label>Direccion de llegada</label> 
        <p class="list-group-item" id="dir_llegada_consig"></p>
    </div>
    <div class="form-group col-lg-12">
        <table class="table table-bordered table-striped" style="width: 100%">
            <caption>Detalles de la guia</caption>
            <thead class="bg-primary">
                <tr>
                    <th>ITEM</th>
                    <th>CANTIDAD</th>
                    <th>PESO TOTAL (Kg)</th>
                    <th>DESCRIPCION</th>
                </tr>
            </thead>
            <tbody id="data_detalles_guia_consig"></tbody>
        </table>
    </div>
    
</div>

{{-- NOTA DE PEDIDO --}}
<div class="row" id="section_nota_pedido" style="display: none;">
    <h4 class="padding_1em bg-navy">
        <i class="fa fa-arrow-right"></i> Nota de Pedido
    </h4>
    <div class="form-group col-lg-3">
        <label>Nº Pedido</label>
        <p class="list-group-item" id="n_pedido_consig"></p>
    </div>
    <div class="form-group col-lg-5">
        <label>Direccion</label> 
        <p class="list-group-item" id="dir_nota_consig"></p>
    </div>

    <div class="form-group col-lg-4">
        <label>Total (S /)</label> 
        <p class="list-group-item" id="total_nota_consig"></p>
    </div>
</div>
