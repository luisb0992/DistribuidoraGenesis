<div class="form-group col-sm-3">
	<label for="cliente">Cliente</label>
	<select name="cliente" class="select2" id="cliente_bus" style="width: 100%">
		@foreach($clientes as $c)
		<option value="{{$c->id}}">{{$c->nombre_full}}</option>
		@endforeach
	</select>
</div>

<div class="form-group col-sm-3">
	<label for="fecha_venta">Fecha venta</label>
	<input class="form-control fecha" name="fecha_venta" id="fecha_venta">
</div>

<div class="form-group col-sm-6 text-right">
	<label for="">---</label><br>
	<button type="button" class="btn btn-primary" id="btn_cargar_modelos">
		<i class="fa fa-save"></i> Buscar
	</button>
</div>

<div class="col-sm-12 div_tablas_modelos">
    <table class="table data-table table-bordered table-hover table-model" width="100%">
        <thead class="bg-primary">
            <tr>
                <th class="text-center">Codigo</th>
                <th class="text-center">Cliente</th>
                <th class="text-center">Total</th>
                <th class="text-center">Fecha</th>
                <th class="text-center bg-navy" width="140px">Estado Factura</th>
                <th class="text-center bg-navy">Estado Estuche</th>
                <th class="text-center bg-navy">Pagos</th>
                <th class="text-center bg-navy"><i class="fa fa-cogs"></i></th>
            </tr>
        </thead>
        <tbody id="data_modelos"></tbody>
    </table>
    <hr>
</div>