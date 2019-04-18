@extends('layouts.app')
@section('title','Consignaciones - '.config('app.name'))
@section('header','Consignaciones')
@section('breadcrumb')
	<ol class="breadcrumb">
	  <li><a href="{{route('dashboard')}}"><i class="fa fa-home" aria-hidden="true"></i> Inicio</a></li>
	  <li class="active"> Consignaciones </li>
	</ol>
@endsection
@section('content')
	@include('partials.flash')

	<div class="row">
	  	<div class="col-md-3 col-sm-6 col-xs-12">
	       <div class="info-box">
	           <span class="info-box-icon bg-red"><i class="fa fa-user"></i></span>
	           <div class="info-box-content">
	               <span class="info-box-text">Consignaciones</span>
	               <span class="info-box-number">{{ count($consignaciones) }}</span>
	           </div>
	       </div>
	    </div>
	</div>

	<div class="row">
  		<div class="col-lg-12">
    		<div class="box box-danger box-solid">
	      		<div class="box-header with-border">
			        <h3 class="box-title"><i class="fa fa-list-alt"></i> Consignaciones</h3>
    		        <span class="pull-right">
                        <a href="{{ route('consignacion.create') }}" class="btn btn-danger">
       				        <i class="fa fa-plus" aria-hidden="true"></i> Nueva consignacion
       					</a>
    				</span>
			     </div>
      		    <div class="box-body">
  					<table class="table data-table table-striped table-hover">
  						<thead class="label-danger">
  							<tr>
  								<th class="text-center">Cod consignacion</th>
                                <th class="text-center">Cliente</th>
  								<th class="text-center">Fecha de envio</th>
                                <th class="text-center">Modelos enviados</th>
                                <th class="text-center">Modelos consignados</th>
  								<th class="text-center"><i class="fa fa-cogs"><i></th>
  							</tr>
  						</thead>
  						<tbody class="text-center">
  							@foreach($consignaciones as $d)
  								<tr>
  									<td>{{ $d->id }}</td>
                                    <td>{{ $d->cliente->nombre_full }}</td>
  									<td>{{ $d->fecha_envio }}</td>
                                    <td>{{ $d->detalleConsignacion->count() }}</td>
                                    <td>{{ $d->modelosConsignados($d->id) }}</td>
  									<td>
                                        <a href="#detalle_consig" class="btn bg-navy btn-xs btn_detalle_consig" data-toggle="modal" data-target="#detalle_consig" title="Datos completos de la consignacion" id="" data-id="{{ $d->id }}">
                                            <i class="fa fa-eye"></i> detalle
                                        </a>
                                        {{-- <a href="{{ route('consignacion.edit', $d->id) }}" class="btn bg-orange btn-xs">
                                            <i class="fa fa-edit"></i> editar
                                        </a> --}}
                                        <input type="hidden" data-v="{{ ($d->guia != null) ? $d->guia->dirSalida->full_dir() : 'vacio' }}" id="val_salida{{ $d->id }}"> 
                                        <input type="hidden" data-v="{{ ($d->guia != null) ? $d->guia->dirLLegada->full_dir() : 'vacio' }}" id="val_llegada{{ $d->id }}">                         
                                    </td>
  								</tr>
  							@endforeach
  						</tbody>
  					</table>
				</div>
			</div>
		</div>
	</div>
    @include("consignaciones.modals.detalle_consig")
@endsection
@section("script")
<script>

    // mostrar y validar campos en consignacion y guia
    $(".btn_detalle_consig").click(function(e){
        $("#icon-loading").show();
        $("#data_modelos").empty();
        id = $(this).data("id");
        status = "";

        $.get('detalleConsig/'+$(this).data("id"), function(res) {

            $("#cliente").text(res.cliente.nombre_full);
            $("#fecha_envio").text(res.fecha_envio);
            $.each(res.detalle_consignacion, function(index, val) {

                if (val.status == 1) {
                    status = "Enviado";
                }else if(val.status == 2){
                    status = "Recibido";
                }else if(val.status == 3){
                    status = "Consignado";
                }

                $("#data_modelos").append(
                    "<tr>"+
                          "<td>"+val.id+"</td>"+
                          "<td>"+val.modelo.name+"</td>"+
                          "<td>"+val.montura+"</td>"+
                          "<td>"+val.estuche+"</td>"+
                          "<td>"+status+"</td>"+
                    "</tr>"
                );
            });
            if (res.guia == null) {
                $("#section_guia").fadeOut(400);
                $("#guia").empty().append("<i class='fa fa-remove text-danger'></i> Guia de remision");
            }else{
                $("#section_guia").fadeIn(400);
                $("#guia").empty().append("<i class='fa fa-check text-success'></i> Guia de remision");
                $("#serie").text(res.guia.serial);
                $("#dir_salida").text($("#val_salida"+id).data("v"));
                $("#dir_llegada").text($("#val_llegada"+id).data("v"));
                $("#ref_item_id").text(res.guia.detalle_guia.item.nombre);
                $("#cantidad").text(res.guia.detalle_guia.cantidad);
                $("#peso").text(res.guia.detalle_guia.peso);
                $("#descripcion").text(res.guia.detalle_guia.descripcion);
            }

            $("#icon-loading").hide();
        });
    });
</script>    
@endsection