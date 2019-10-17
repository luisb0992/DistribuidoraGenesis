@extends('layouts.app')
@section('title','Asignacion de modelos - '.config('app.name'))
@section('header','Asignacion de modelos')
@section('breadcrumb')
	<ol class="breadcrumb">
	  <li><a href="{{route('dashboard')}}"><i class="fa fa-home" aria-hidden="true"></i> Inicio</a></li>
	  <li class="active"> Asignacion de modelos </li>
	</ol>
@endsection
@section('content')
	@include('partials.flash')
	  <div class="row">
	  	<div class="col-md-3 col-sm-6 col-xs-12">
	      <div class="info-box">
	        <span class="info-box-icon bg-red"><i class="fa fa-user"></i></span>
	        
	        <div class="info-box-content">
	          <span class="info-box-text">Asignacion de modelos</span>
	          <span class="info-box-number">{{ count($asignaciones) }}</span>
	        </div>
	        <!-- /.info-box-content -->
	      </div>
	      <!-- /.info-box -->
	    </div>
	  </div><!--row-->

	<div class="row">
  		<div class="col-md-12">
    		<div class="box box-danger box-solid">
	      		<div class="box-header with-border">
			        <span>
			        	<button type="button" data-toggle="modal" data-target="#busqueda_avanzada_asig" class="btn bg-navy">
			        		<i class="fa fa-search"></i> Busqueda avanzada
			        	</button>
			        </span>
			        <span class="pull-right">
						<a href="{{ route('asignaciones.create') }}" class="btn btn-danger">
							<i class="fa fa-plus" aria-hidden="true"></i> Nueva asignacion
						</a>
					</span>
			    </div>
      			<div class="box-body">
					<table class="table data-table table-bordered table-hover">
						<thead class="label-danger">
							<tr>
								<th class="text-center">Vendedor (Usuario)</th>
								<th class="text-center">Modelo - [Codigo]</th>
								<th class="text-center">Monturas</th>
                                <th class="text-center">Estuches</th>
								<th class="text-center">Fecha asignacion</th>
                                <th class="text-center bg-navy">Estado</th>
								<th class="text-center bg-navy"><i class="fa fa-cogs"></i></th>
							</tr>
						</thead>
						<tbody class="text-center">
							@foreach($asignaciones as $d)
								<tr>
									<td class="text-capitalize"><strong>{{ $d->user->name }} {{ $d->user->ape }}</strong></td>
									<td>{{ $d->modelo->name.' - ['.$d->modelo->id.']' }}</td>
									<td>{{ $d->monturas }}</td>
                                    <td>{{ $d->estuches }}</td>
                                    <td>{{ $d->fecha }}</td>
									<td @if($d->status == 1) class='warning' @elseif($d->status == 2) class='info' @else class='success' @endif>
                                        {{ $d->status() }}
                                    </td>
									<td>
                                        @if($d->status == "Asignado")										<form action="{{ route('asignaciones.destroy', $d->id) }}" method="POST">
												{{ method_field( 'DELETE' ) }}
                      							{{ csrf_field() }}
                      							<button class="btn btn-xs btn-danger confirmar" type="submit" onclick="return confirm('Desea eliminar la asignacion con todas sus dependencias S/N?');"><i class="fa fa-trash"></i>
                      							</button>
											</form>
                                        @else
                                            ---    
                                        @endif
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	@include("asignaciones.modals.busqueda_avanzada_asig",[
		"color_header" => "bg-navy",
		"icon" => "search",
		"titulo" => "Busqueda avanzada",
		"modal_type" => "modal-lg"
	])
@endsection

@section("script")
<script>

	// cargar modelos en la tabla
	$("#btn_cargar_modelos").click(function(e) {
		var user = $("#user").val();
		var fecha = $("#fecha_asig").val();

		if (fecha) {
			var userDate = fecha;
		    var from = userDate.split("/");
		    var f = new Date(from[2], from[1], from[0]);
		    var fec = f.getFullYear() + "-" + f.getMonth() + "-" + f.getDate();
		}else{
			var fec = null;
		}

		if (user) {
			$("#data_modelos").empty();

			$.get("buscarModelosAsignados/"+user+"/"+fec+"",function(response, status){

					$('.data-table .table-model').DataTable().destroy();
				    $("#data_modelos").html(response.data);
				    $('.data-table .table-model').DataTable({
				    	responsive: true,
					    language: {
					      	url:'{{asset("plugins/datatables/spanish.json")}}'
					    }
				    });
			});
		}else{
			mensajes("Alerta!", "Nada para mostrar", "fa-warning", "red");
		}
	});

</script>
@endsection