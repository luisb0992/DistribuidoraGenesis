<div class="form-group col-sm-2 text-left">
	<a href="{{ route("colecciones.ver") }}" class="btn btn-primary btn-lg">
		<i class="fa fa-arrow-right"></i> Listado de colecciones
	</a>
</div>
<div class="col-sm-12">
	<div class="box box-danger box-solid">
		<div class="box-body" id="box-body">

					{{-- seccion de coleccion --}}
					<section id="section_col">
						<div class="col-sm-12">
							<h3 class="label-danger padding_1em"><i class="fa fa-database"></i> <i class="fa fa-plus"></i> Nueva Coleccion</h3>
						</div>

						<div class="form-group col-sm-4">
							<label for="">Nombre </label>
							<input type="text" class="form-control" name="name_col" required="" id="name">
						</div>

						<div class="form-group col-sm-3">
							<label for="">Fecha </label>
							<input type="text" class="form-control fecha" name="fecha_coleccion" required="" id="fecha">
						</div>

						<div class="">
							<input type="hidden" name="codigo" value="{{ $col }}" class="form-control" readonly="" id="codigo">
						</div>

						<div class="form-group col-sm-3">
							<label for="">
								Proveedor
								[<a href="#cp" class="btn-link" data-toggle="modal" data-target="#cp" id="btn_prove">
									<span class="text-primary"><i class="fa fa-plus"></i> Nuevo</span>
								</a>]
								@include('colecciones.modals.cp')
							</label>
							<select name="proveedor_id" class="form-control" required="" id="s_p">
								<option value="">Seleccione</option>
								@foreach($proveedores as $prov)
								<option value="{{ $prov->id }}">{{ $prov->nombre }} / {{ $prov->empresa }}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group col-sm-2" style="margin-top:2em">
							<button class="btn btn-danger btn-sm" type="button" id="btn_save_col">
								<i class="fa fa-save"></i> Guardar
							</button>
						</div>
					</section>

					<div id="msj_col" style="display: none;" class="col-sm-12">
						<div class="alert alert-success">
					      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					      <strong class="text-center">Coleccion creada exitosamente!... proceda con las marcas</strong>
					  	</div>
					</div>

					{{-- seccion de marcas --}}
					<form id="form_mc" action="{{ route('colecciones.store') }}" method="POST">
					<input type="hidden" name="id_coleccion" id="id_col">
					<section id="section_marcas" style="display: none;">
						<div class="col-sm-12">
							<h3 class="label-danger padding_1em"><i class="fa fa-plus"></i> Añadir marcas</h3>
						</div>

						<div class="col-sm-12 text-left" style="border-bottom: solid 0px #D4D4D4; ">
							[<a href="#cm" class="btn-link" data-toggle="modal" data-target="#modal_marca">
								<span class="text-primary"><i class="fa fa-plus"></i> Nueva</span>
							</a>]
						</div>

						<section id="section_marca">

							<div class="div_total_marcas">
								<div class='form-group col-sm-5'>
									<label>Marcas</label>
									<select name='marca_id[]' class='form-control s_m' required='' id="s_m_0">
										<option value=''>Seleccione</option>
										@foreach($marcas as $m)
										<option value='{{ $m->id }}'>{{ $m->name }} | ({{ $m->material->name }})</option>
										@endforeach
									</select>
								</div>

								<div class='form-group col-sm-3'>
									<label>Ruedas</label>
									<select name='rueda[]' class='form-control ru' required=''>
										@for($r = 1; $r < 21; $r++)
										<option value='{{ $r }}'>{{ $r }}</option>
										@endfor
									</select>
								</div>
							</div>

						</section>

						<div class="form-group col-sm-2 text-left" style="padding: 0.4em;">
							<br>
							<button class="btn btn-primary" type="button" id="btn_añadir_marca">
								<i class="fa fa-plus"></i>
							</button>

						</div>

						<div class="form-group col-sm-12 text-right">
							<button class="btn btn-danger" type="submit" id="btn_save_mar">
								<i class="fa fa-save"></i> Guardar Marcas
							</button>
						</div>

					</section>
					</form>

					<div id="msj_mar" style="display: none;" class="col-sm-12">
						<div class="alert alert-success">
					      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					      <strong class="text-center">Marcas añadidas a la coleccion... ingresar modelos</strong>
					  	</div>
					</div>

					{{-- seccion de modelos --}}
					<section id="section_modelos" style="display:none">
					 	<div class="col-sm-12">
							<h3 class="label-danger padding_1em"><i class="fa fa-database"></i> <i class="fa fa-plus"></i> Añadir Modelos (Caja)</h3>
						</div>

						<div id="msj_mod" style="display: none;" class="col-sm-12">
							<div class="alert alert-success">
						      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						      <strong class="text-center">Modelos añadidos a la coleccion</strong>
						  	</div>
						</div>

						<div id="msj_mod" style="display: none;" class="col-sm-12">
							<div class="alert alert-info">
						      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						      <strong class="text-center" id="msj_ajax"></strong>
						  	</div>
						</div>

						<div class="form-group col-sm-4">
							<label for="">Seleccione marca</label>
							<select name="mar_mod" class="form-control" required="" id="col_mar">
							</select>
						</div>

						<div class="form-group col-sm-4" style="margin-top:2em">
							<button class="btn btn-primary btn-sm" type="button" id="btn_carga_mar">
								Cargar
							</button>
						</div>

						<div class="form-group col-sm-4">
							<label for="">Ruedas</label>
							<input type="text" class="form-control" name="rueda" readonly="" id="mar_rueda">
						</div>

						<hr>

					</section>


					<!-- formulario de modelos -->
					<form id="form_modelos" method="POST">
						{{ csrf_field() }}
						<input type='hidden' name='marca_id' id="marca_id">
						<input type="hidden" name="rueda" id="cant_ruedas">
						<input type="hidden" name="id_coleccion" id="id_col2">

						<section id="sm" style="display: none;">
			    			<div class="div_total">
				    			<div class='form-group col-sm-3'>
									<label class='control-label' for='name'>Nombre modelo: *</label>
										<input type='text' name='name[]' class='form-control nombre_modelo' id="nombre_modelo_0" required=''>
								</div>
								<div class='form-group col-sm-2'>
									<label class='control-label'>Cantidad Monturas: *</label>
										<select name='montura[]' class='form-control' required=''>
												<option value='1'>1</option>
												<option value='2'>2</option>
												<option value='3'>3</option>
												<option value='4'>4</option>
												<option value='5'>5</option>
												<option value='6'>6</option>
												<option value='7'>7</option>
												<option value='8'>8</option>
												<option value='9'>9</option>
												<option value='10'>10</option>
												<option value='11'>11</option>
												<option value='12' selected>12</option>
										</select>
								</div>
								<div class='form-group col-sm-5'>
									<label>Descripcion </label>
										<textarea name='descripcion_modelo[]' class='form-control'></textarea>
								</div>
							</div>
						</section>

							<div class='form-group col-sm-1 text-left' style='padding: 1.8em;'>
								<button class='btn btn-primary' type='button' id='btn_añadir_modelo' style="display:none;">
									<i class='fa fa-plus'></i>
								</button>
							</div>

						<div class="form-group col-sm-12 text-right">
							<br>
							<button class="btn btn-danger" type="submit" id="btn_save_mod" style="display: none;">
								<i class="fa fa-save"></i> Guardar Modelos
							</button>
						</div>
					</form>

					<div class="col-sm-2">
			          	<button class="btn btn-success btn-lg" type="button" id="btn_new_col" style="display: none;">
							<i class="fa fa-plus"></i> Nueva Coleccion
						</button>
			        </div>

			</div>
		</div>
	</div>
	@include('marcas.modals.modal_create')