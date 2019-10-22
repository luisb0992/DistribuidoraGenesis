<div class="modal fade" tabindex="-1" role="dialog" id="{{$id_modal}}">
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
				@includeIf($body)
			</div>
			<div class="modal-footer">
				<input type="button" class="btn btn-danger" data-dismiss="modal" value="Cerrar">
				@includeIf($footer)
			</div>
		</div>
	</div>
</div>