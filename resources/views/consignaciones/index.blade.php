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
                    <span>
                        <button type="button" data-toggle="modal" data-target="#busqueda_avanzada_consig" class="btn bg-navy">
                            <i class="fa fa-search"></i> Busqueda avanzada
                        </button>
                    </span>
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
                                <th class="text-center">Total S/</th>
                                <th class="text-center bg-navy">Estado</th>
  								<th class="text-center bg-navy"><i class="fa fa-cogs"><i></th>
  							</tr>
  						</thead>
  						<tbody class="text-center">
  							@foreach($consignaciones as $d)
  								<tr>
  									<td>[{{ $d->id }}]</td>
                                    <td>{{ $d->cliente->nombre_full }}</td>
  									<td class="text-nowrap">
                                        {{ $d->fecha_envio }}
                                        <span data-toggle="tooltip" title="Editar fecha de envio" class="pull-right">
                                            <button type="button" class="btn bg-orange btn-xs bec" data-toggle="modal" data-target="#editar_consig" data-fechaenvio="{{ $d->fecha_envio }}" data-id="{{ $d->id }}">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </span>
                                    </td>
                                    <td class="text-nowrap">
                                        {{ $d->detalleConsignacion->count() }}
                                        @if($d->status === 1)
                                        <span data-toggle="tooltip" title="Añadir mas modelos a la consignacion">
                                            <a href="#añadir_modelos_consig" class="btn btn-default btn-sm btn_añadir_modelos_consig" data-toggle="modal" data-target="#añadir_modelos_consig" data-id="{{ $d->id }}">
                                                <i class="fa fa-plus"></i>
                                            </a>
                                        </span>
                                        @endif
                                    </td>
                                    <td>{{ $d->modelosConsignados($d->id) }}</td>
                                    <td>{{ $d->total ?? '---' }} </td>
                                    <td class="{{ $d->status == 1 ? 'warning' : 'success' }}">
                                        {{ $d->status == 1 ? 'En espera' : 'Consignada' }}
                                    </td>
  									<td class="text-nowrap">
                                        <span data-toggle="tooltip" title="Detalles de la consignacion">
                                            <a href="#detalle_consig" class="btn bg-navy btn-xs btn_detalle_consig" data-toggle="modal" data-target="#detalle_consig" data-id="{{ $d->id }}">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </span>
                                        @if($d->notapedido)
                                        <span data-toggle="tooltip" title="Detalles de la nota de pedido">
                                            <a href="#detalle_notapedido_{{ $d->id }}" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#detalle_notapedido_{{ $d->id }}">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </span>
                                        @endif
                                        @if($d->status == 1)
                                        <span data-toggle="tooltip" title="Procesar venta">
                                            <a href="{{ route('procesarVentaConsig', $d->id) }}" class="btn btn-success btn-xs">
                                                <i class="fa fa-arrow-right"></i> venta
                                            </a>
                                        </span>
                                        @endif
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
    @include("consignaciones.modals.mas_modelos_consig")
    @include("consignaciones.modals.editar_consig")
    @include("consignaciones.modals.detalle_consig")
    @foreach($consignaciones as $d)
        @if($d->notapedido)
            @include("consignaciones.modals.detalle_notapedido")
        @endif
    @endforeach
    @include("partials.modals.busqueda_avanzada",[
        "color_header"  => "bg-navy",
        "icon"          => "search",
        "titulo"        => "Busqueda avanzada",
        "modal_type"    => "modal-lg",
        "id_modal"      => "busqueda_avanzada_consig",
        "body"          => "consignaciones.partials.body_busqueda_avanzada",
        "footer"        => "",
    ])
@endsection
@section("script")
<script>

    $(".bec").click(function(e) {
        ruta = '{{ route("consignacion.update",":value") }}';
        $("#form_edit_consig").attr("action", ruta.replace(':value', $(this).data("id")));
        $("#fe_en").val("");
        $("#fe_en").val($(this).data("fechaenvio"));
    });

    $(".btn_añadir_modelos_consig").click(function(e) {
        ruta = '{{ route("añadirModelos",":value") }}';
        $("#form_añadir_modelos_consig").attr("action", ruta.replace(':value', $(this).data("id")));
    });

    // mostrar y validar campos en consignacion y guia
    $(".btn_detalle_consig").click(function(e){
        $("#icon-loading-consig").show();
        $.get('detalleConsig/'+$(this).data("id"), {view: 1}, function(data) {
            
            $('.data-table').DataTable().destroy();
            cargarDataConsignacionYModelos(data);
            $('.data-table').DataTable();

            if (data.consig.guia == null) {
                $("#section_guia").fadeOut(400);
                $("#guia").empty().append("<i class='fa fa-remove text-danger'></i> Guia de remision");
            }else{
                $("#section_guia").fadeIn(400);
                $("#guia").empty().append("<i class='fa fa-check text-success'></i> Guia de remision");
                cargarGuia(data);
            }
            
            $("#icon-loading-consig").hide();
        });
    });

    // busqueda de marcas en la coleccion
    $('#añadir_coleccion').change(function(event) {
        $(".btn_save_añadir").prop("disabled", true);
        $("#data_añadir_modelos, #añadir_marcas, #añadir_name_modelos").empty();
        $.get("marcasAll/"+event.target.value+"",function(response, dep){
            if (response.length > 0) {
                for (i = 0; i<response.length; i++) {
                    $("#añadir_marcas").append(
                        "<option value='"+response[i].marca.id+"'>"
                        +response[i].marca.name+' | '+response[i].marca.material.name+
                        "</option>"
                    );
                }
            }else{
                mensajes("Alerta!", "No posee marcas asociadas", "fa-warning", "red");
            }
        });
    });

    // cargar modelos
    $("#btn_cargar_modelos").click(function(e) {
        e.preventDefault();
        var nombre_modelo, montura_modelo, precio_montura_modelo = "";
        $("#btn_cargar_modelos").attr('disabled', 'disabled');
        $(".total_consig").val("");
        $(".btn_save_añadir").prop("disabled", true);
        if ($("#añadir_coleccion").val() && $("#añadir_marcas").val()) {
            $.get("modelosAll/"+$("#añadir_coleccion").val()+"/"+$("#añadir_marcas").val()+"",function(response, dep){
                    $('.data-table').DataTable().destroy();
                    $("#data_añadir_modelos").empty().html(response.data);
                    $("#añadir_name_modelos").empty().html(response.model);
                    $('.data-table').DataTable();
            });
        }else{
            mensajes("Alerta!", "Nada para mostrar, debe llenar todos los campos", "fa-warning", "red");
        }
        $("#btn_cargar_modelos").removeAttr("disabled");
    });

    // copiar y pegar modelo en buscador de la tabla y aplicar la busqueda
    $(".div_tablas_modelos").on("click", ".btn_nm", function(e) {
        e.preventDefault();
        $("table.data-table.ok input[type='search']").empty().val($(this).val());
        $('table.data-table.ok').DataTable().search($(this).val()).draw();    
    });

    // buscar consignacion y cargar en el select
    $("#btn_buscar_consignacion").click(function(e){
        var cliente = $("#cliente_bus").val();
        var fecha = $("#fecha_env").val();
        var notap = $("#nota_bus").val();
        var guiar = $("#guia_bus").val();

        fecha = (fecha) ? (
                from = fecha.split("/"),
                f = new Date(from[2], from[1], from[0]),
                f.getFullYear() + "-" + f.getMonth() + "-" + f.getDate()
            ) : null;

        notap = (notap) ? notap : null;
        guiar = (guiar) ? guiar : null;

        if (cliente == null) {

            mensajes("Alerta!", "Nada para mostrar", "fa-remove", "red");

        }else{

            $("#icon-buscar-consig").show();
            $("#btn_buscar_consignacion").attr("disabled", "disabled");
            var url ="cargarConsigSelect/"+cliente+"/"+fecha+"/"+notap+"/"+guiar+"";

            $.get(url, {view: 1},function(data) {
                $("#id_consignacion").empty().html(data);
                $("#icon-buscar-consig").hide();
                $("#btn_buscar_consignacion").removeAttr('disabled');
                mensajes("Listo!", data.length+" resultados", "fa-remove", "green");
            }); 
        }
    });

    // ocultar carga cuando cambie algo en la busqueda
    $('#cliente_bus, #nota_bus, #guia_bus, #fecha_env').on("change",  function(e) {
        $("#id_consignacion").empty();
        $("#section_guia_remision, #section_detalle_consig, #section_nota_pedido").hide(400);
    });

    // cargar consignacion, guia, nota de pedido en modal - tabla
    $("#btn_cargar_consignacion").click(function(e){

        var valor = $("#id_consignacion").val();

        if (valor == null) {
            mensajes("Alerta!", "El campo de seleccion esta vacio, seleccione un codigo", "fa-remove", "red");
        }else{
            $("#icon-cargar-consig").show();
            $("#btn_cargar_consignacion").attr("disabled", "disabled");

            $.get('detalleConsigGuiaNotaHtml/'+valor+'', function(data) {
                $("#section_detalle_consig").fadeIn(400);

                $('.data-table.search_consig').DataTable().destroy();

                $("#cliente_consig").text(data.consig.cliente.nombre_full);
                $("#fecha_envio_consig").text(data.consig.fecha_envio);
                $("#data_modelos_consig").empty().append(data.modelos);

                $('.data-table.search_consig').DataTable({responsive: true});

                cargarGuiaRemision(data, $("#section_guia_remision"), $("#guia_consig"), $("#serie_consig"), $("#dir_salida_consig"), $("#dir_llegada_consig"), $("#data_detalles_guia_consig"));

                cargarNotaPedido(data, $("#section_nota_pedido"), $("#n_pedido_consig, #dir_nota_consig, #total_nota_consig"), $("#n_pedido_consig"), $("#dir_nota_consig"), $("#total_nota_consig"));
                
                
                $("#icon-cargar-consig").hide();
                $("#btn_cargar_consignacion").removeAttr('disabled');
            }); 
        }
    });

    // FUNCIONES ----------------------------------------------------------------------------------------
    function cargarNotaPedido(data, section, ele, npedido, dir, total){
        if (data.consig.notapedido == null) { 
            section.hide(); 
            ele.text('');
        }else{
            section.show();
            npedido.text(data.consig.notapedido.n_pedido);
            total.text(data.consig.notapedido.total);
            dir.text(data.dir_nota);
        }
    }

    function cargarGuiaRemision(data, section, status, serie = null, dir_salida = null, dir_llegada = null, det_guia = null){
        if (data.consig.guia == null) {
            section.hide();
            status.empty().append("<i class='fa fa-remove text-danger'></i> N/A");
        }else{
            section.show();
            status.empty().append("<i class='fa fa-check text-success'></i> Posee");
            (serie != null) ? serie.text(data.consig.guia.serial) : false;
            (dir_salida != null) ? dir_salida.text(data.dir_salida) : false;
            (dir_llegada != null) ?  dir_llegada.text(data.dir_llegada) : false;
            (det_guia != null) ? det_guia.empty().append(data.data_det_guia) : false;
        }
    }

    // Calcular monto y total
    function calcularMontoTotal(){
        total = 0; error = false;
        if (comprobarCheckModelo() === true) {
            $.each($("#data_añadir_modelos > tr"), function(index, val) {
                montura = parseInt($(this).find('.montura_modelo').val());
                precio  = parseFloat($(this).find('.costo_modelo').val());
                check   = $(this).find('.hidden_model').val();

                if (check == 1) {
                    if (!Number(montura)) {
                        costo = 0;
                        $(this).find('.costo_modelo').val(0);
                        $(this).find('.preciototal').val(0);
                    }else{
                        costo = montura * precio;
                        if (!Number(costo)) { 
                            error = true;
                        }else{
                            $(this).find('.preciototal').val(costo);
                        }
                    }

                    total += costo;
                }else{
                    $(this).find('.preciototal').val('');
                }
            });

            if (error) {
                mensajes("Alerta!", "El precio o la montura es incorrecta, deben ser solo numeros, verifique", "fa-remove", "red");
                $(".btn_save_añadir").prop("disabled", true);
                return false;
            }else{
                if (Number(total) || total > 0) {
                    $(".btn_save_añadir").removeAttr("disabled");
                }else{
                    mensajes("Alerta!", "El total es incorrecto, verifique", "fa-remove", "red");
                    $(".btn_save_añadir").prop("disabled", true);
                }
            }

            $(".total_consig").val(total).animate({opacity: "0.5"}, 400).animate({opacity: "1"}, 400);
        }
    }

</script>    
@endsection
