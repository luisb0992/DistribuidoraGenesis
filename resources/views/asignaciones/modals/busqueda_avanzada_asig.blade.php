<div class="modal fade" tabindex="-1" role="dialog" id="busqueda_avanzada_asig">
	<div class="modal-dialog {{$modal_type}}" role="document">
		<div class="modal-content">
			<div class="modal-header {{$color_header}}">
				<buttton class="close" type="button" data-dismiss="modal">&times;</buttton>
				<h3>
					<i class="fa fa-{{$icon}}"></i> 
			 		{{$titulo}}
				</h3>
			</div>
			<div class="modal-body">
				<div class="form-group col-sm-4">
					<label for="">Vendedor (usuario) </label>
					<select class="select2" name="user_id" required="" style="width: 100%;" id="user">
						@foreach($users as $user)
						<option value="{{ $user->id }}">{{ $user->name }}</option>
						@endforeach
					</select>
				</div>

				<div class="form-group col-sm-4">
					<label for="">Fecha asig.</label>
					<input class="form-control fecha" name="fecha" id="fecha_asig">
				</div>
				
				<div class="form-group col-sm-12 text-right">
					<button type="button" class="btn btn-primary" id="btn_cargar_modelos">
						<i class="fa fa-save"></i> Buscar
					</button>
				</div>

				<div class="col-sm-12 div_tablas_modelos">
				    <table class="table data-table table-bordered table-hover table-model" width="100%">
				        <thead class="bg-primary">
				            <tr>
				                <th class="text-center">Vendedor</th>
								<th class="text-center">Modelo - [Codigo]</th>
								<th class="text-center">Monturas</th>
                                <th class="text-center">Estuches</th>
								<th class="text-center">Fecha asignacion</th>
                                <th class="text-center bg-navy">Estado</th>
                                <th class="text-center bg-navy"><i class="fa fa-cogs"></i></th>
				            </tr>
				        </thead>
				        <tbody id="data_modelos"></tbody>
				    </table>
				    <hr>
				</div>
			</div>
			<div class="modal-footer">
			</div>
		</div>
	</div>
</div>