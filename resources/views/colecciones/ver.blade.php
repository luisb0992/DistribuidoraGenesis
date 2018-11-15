@extends('layouts.app')
@section('title','Coleccion - '.config('app.name'))

@section('content')
@include('partials.flash')
<div class="row">
<div class="col-sm-12 col-xs-12">
	<div class="box box-danger box-solid">
  		<div class="box-header with-border">
	        <h3 class="box-title"><i class="fa fa-database"></i> Listado de colecciones</h3>
	        <span class="pull-right">
	        	<a href="{{ route('colecciones.index') }}" class="btn btn-sm btn-danger"><i class="fa fa-plus"></i> Nuevo</a>
	        </span>
	    </div>
			<div class="box-body">
			<table class="table data-table table-bordered table-hover">
				<thead class="label-danger">
					<tr>
						<th class="text-center">Codigo</th>
						<th class="text-center">Nombre</th>
						<th class="text-center">Fecha de coleccion</th>
						<th class="text-center">Marcas registradas</th>
						<th class="text-center">Modelos</th>
						<th class="text-center">Proveedor</th>
					</tr>
				</thead>
				<tbody class="text-center">
					@foreach($colecciones as $d)
						<tr>
							<td>{{ $d->codigo }}</td>
							<td>{{ $d->name }}</td>
							<td>{{ $d->fecha_coleccion }}</td>
							<td>{{ $d->cmCount() }}</td>
							<td>
								{{ $d->modelos($d->id)->count() }}
								<span class="pull-right">
									<a href="{{ route('colecciones.show',[$d->id]) }}" class="btn btn-default"
										data-toggle="tooltip" data-placement="top" title="Añadir mas modelos">
										<i class="fa fa-plus-circle"></i>
									</a>
								</span>
							</td>
							<td>{{ $d->proveedor->nombre }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
@endsection