@extends('layouts.app')
@section('title','Nota de Credito - '.config('app.name'))
@section('header','Nueva Nota de Credito')
@section('breadcrumb')
    <ol class="breadcrumb">
      <li><a href="{{ route('dashboard') }}"><i class="fa fa-home" aria-hidden="true"></i> Inicio</a></li>
      <li class="active"> Nota de Credito / Nueva </li>
    </ol>
@endsection
@section('content')

<div class="row">

    {{-- panel - datos --}}
    <div class="col-lg-3">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3><i class="fa fa-arrow-right"></i> Facturas</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label>Codigo + Fecha factura</label>
                    <select class="select2" name="factura_id" id="factura_id" required="" style="width: 100%">
                        @foreach($facturas as $m)
                        <option value="{{ $m->id }}">{{ '['.$m->num_factura.'] - '.$m->createF() }}</option>
                        @endforeach
                    </select><hr>

                    <span class="pull-right">
                        <button type="button" class="btn btn-success" id="btn_buscar_factura">
                            <i class="fa fa-spinner fa-pulse" id="icon-buscar-factura" style="display: none"></i> Buscar
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-9">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3><i class="fa fa-arrow-right"></i> Datos</h3>
            </div>
            <div class="panel-body">
                @include("devoluciones.partials.form_create_devolucion")
            </div>
        </div>
    </div>
           
</div>
@include('direcciones.modals.modal_create')
@endsection
@section("script")
<script>

    var totalFacturar = 0, btnGuardarTodo = $("#btn_guardar_all"), contar_det_guia = 2;
    $("#select_coleccion").val('').prop('selected', true);
    $("#checkbox_guia").prop('checked', false);
    $(".total_venta, .subtotal, #abono, #restante, #total_facturar, #user_id, #cliente_id, #direccion_id").val('');


    $("#checkbox_guia").click(function(e) {
        var bool = this.checked;
        if(bool === true){
            $("#section_guia").animate({height: "toggle"}, 400);
            $("#checkbox_guia").val(1);
            $("#serial, #guia, #dir_salida, #dir_llegada, #item, #cantidad, #peso").prop('required', true);
        }else{
            $("#section_guia").animate({height: "toggle"}, 400);
            $("#checkbox_guia").val(0);
            $("#serial, #guia, #dir_salida, #dir_llegada, #item, #cantidad, #peso").prop('required', false);
        }
    });

    // añadir mas detalles a la guia
    $("#btn_añadir_detalle_guia").click(function(e){
        e.preventDefault();
        
        contar = contar_det_guia++;
        
        $("#section_replicar_detalle_guia").append("<section id='section_detalle_guia_"+contar+"'></section>");
        $("#section_detalle_guia_"+contar+"").html($("#section_detalle_guia_1").html());

        $("#section_detalle_guia_"+contar+"").append(
            "<div class='form-group col-lg-1'>"+
                "<label>---</label><br>"+
                "<button class='btn btn-danger' type='button' id='btn_delete_det_guia"+contar+"'>"+
                    "<i class='fa fa-remove'></i>"+
                "</button>"+
            "</div>");

        $('#btn_delete_det_guia'+contar+'').click(function(e){
          e.preventDefault();
          $('#section_detalle_guia_'+contar+'').remove();
          contar--;
        });
    });

    // buscar y cargar la factura y venta
    $("#btn_buscar_factura").click(function(e){

        if ($("#factura_id").val() == null) {
            mensajes("Alerta!", "El campo de seleccion esta vacio, seleccione una factura", "fa-remove", "red");
        }else{
            $("#icon-buscar-factura").show();
            $("#btn_buscar_factura").attr("disabled", "disabled");

            $.get('../cargarTablaDesdeFactura/'+$("#factura_id").val()+'', function(data) {

                $('.data-table').DataTable().destroy();

                $("#data_modelos_venta").empty().append(data.data);
                $("#user_id").val(data.user.name+' '+data.user.ape);
                $("#user").val(data.user.id);
                $("#cliente_id").val(data.cliente.nombre_full);
                $("#cliente").val(data.cliente.id);
                $("#direccion_id").val(data.dir);
                $("#direccion").val(data.direccion);
                $("#venta_id").val($("#venta").val());
                $("#total_facturar").val(0);
                
                $('.data-table').DataTable({responsive: true});
                
                $("#icon-buscar-factura").hide();
                $("#btn_buscar_factura").removeAttr('disabled');
            }); 
        }
    });

    // busqueda de marcas en la coleccion
    $('#select_coleccion').change(function(event) {
        $("#select_marca").empty();
        $.get("../marcasAll/"+event.target.value+"",function(response, dep){
            if (response.length > 0) {
                for (i = 0; i<response.length; i++) {
                    $("#select_marca").append(
                        "<option value='"+response[i].marca.id+"'>"
                        +response[i].marca.material.name+' | '+response[i].marca.name+
                        "</option>"
                    );
                }
            }else{
                mensajes("Alerta!", "No posee marcas asociadas", "fa-warning", "red");
            }
            $("#data_modelos_venta_directa").empty();
        });
    });

    // escuchar el evento cuando cambie la monturas
    $('#section_venta').on("change", ".venta_montura_modelo", function(e) {
        montura         = parseInt($(this).parents("tr").find('.venta_montura_modelo').val());
        precio_montura  = parseFloat($(this).parents("tr").find('.venta_precio_montura').val());
        precio_total    = parseFloat($(this).parents("tr").find('.venta_preciototal').val());

        if (!Number(montura)) {
            totalFacturar = parseFloat($("#total_facturar").val()) + precio_total;
            $(this).parents("tr").find('.venta_preciototal').val(0);
        }else{
            totalFacturar = parseFloat($("#total_facturar").val()) + (precio_total - (montura * precio_montura));
            parseFloat($(this).parents("tr").find('.venta_preciototal').val(montura * precio_montura));
        }

        if (!Number(totalFacturar) || !totalFacturar > 0) {
            mensajes("Alerta!", "El total a facturar es incorrecto, verifique", "fa-remove", "red");
            btnGuardarTodo.prop("disabled", true);
        }else{
            btnGuardarTodo.removeAttr("disabled");
        }

        $("#total_facturar").val(totalFacturar).animate({opacity: "0.2"}, 400).animate({opacity: "1"}, 800);
    });

    // evitar el siguiente si se cambia cualquier valor en la carga de modelos
    $('#section_coleccion_marca').on("change", "#select_marca, #select_coleccion", function(e) {
        btnGuardarTodo.attr("disabled", "disabled");
        $("#data_modelos_venta_directa").empty();
        reiniciarMontoTotal();
    });

    //--------------------------------------------------------funciones ---------------------------------------------------------------------

    // cargar modelos en la tabla para calcular
    function cargarModelos(){
        $("#btn_cargar_modelos").attr('disabled', 'disabled');
        $("#icon-cargar-modelos").show();
        if ($("#select_coleccion").val() && $("#select_marca").val()) {
            $.get("../cargarTabla/"+$("#select_coleccion").val()+"/"+$("#select_marca").val()+"",function(response, dep){

                $('.data-table').DataTable().destroy();
                $("#data_modelos_venta_directa").empty().html(response);
                $('.data-table').DataTable({responsive: true});

                $("#btn_cargar_modelos").removeAttr("disabled");
                $("#icon-cargar-modelos").hide();
            });
        }else{
            mensajes("Alerta!", "Nada para mostrar, debe llenar todos los campos", "fa-warning", "red");
            $("#btn_cargar_modelos").removeAttr("disabled");
            $("#icon-cargar-modelos").hide();
        }
    }

    // Calcular monto y total
    function calcularMontoTotal(){
        total = 0; error = false;
        $.each($("#data_modelos_venta_directa > tr"), function(index, val) {
            montura = parseInt($(this).find('.montura_modelo').val());
            precio  = parseFloat($(this).find('.costo_modelo').val());

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
        });

        if (error) {
            mensajes("Alerta!", "El precio o la montura es incorrecta, deben ser solo numeros, verifique", "fa-remove", "red");
            $("#btn_guardar_all").prop("disabled", true);
            return false;
        }else{
            if (Number(total) || total > 0) {
                $("#btn_guardar_all").removeAttr("disabled");
            }else{
                mensajes("Alerta!", "El total es incorrecto, verifique", "fa-remove", "red");
                $("#btn_guardar_all").prop("disabled", true);
            }
        }

        $(".total_venta, .subtotal").val(total).animate({opacity: "0.5"}, 400).animate({opacity: "1"}, 400);
    }

    // guardar direccion
    $("#form_save_devolucion").on('submit', function(e) {
        
        if ($('#total_facturar').val() <= 0) {
            mensajes("Alerta!", "El total a facturar debe ser mayor a 0, verifique", "fa-warning", "red");
            return false;
        }

        e.preventDefault();
        btnGuardarTodo.attr("disabled", 'disabled');
        $("#icon-guardar-all").removeClass("fa-save").addClass('fa-spinner fa-pulse');
        var form = $(this);

        $.ajax({
            url: "{{ route('devoluciones.store') }}",
            headers: {'X-CSRF-TOKEN': $("input[name=_token]").val()},
            type: 'POST',
            dataType: 'JSON',
            data: form.serialize(),
        })
        .done(function(data) {
            $("#icon-guardar-all").removeClass("fa-spinner fa-pulse").addClass('fa-save');
            if (data == 1) {
                mensajes('Listo!', 'Venta procesada, espere mientras es redireccionado...', 'fa-check', 'green');
                setTimeout(window.location = "../devoluciones", 2000);
            }else if(data == 2){
                mensajes('Alerta!', 'Serial de la guia repetido, verifique', 'fa-warning', 'red');
            }
        })
        .fail(function(data) {
            btnGuardarTodo.removeAttr("disabled");
            $("#icon-guardar-all").removeClass("fa-spinner fa-pulse").addClass('fa-save');
            mensajes('Alerta!', eachErrors(data), 'fa-warning', 'red');
        })
        .always(function() {
            console.log("complete");
        });
        
    });
</script>
@endsection