<div class="modal fade" tabindex="-1" role="dialog" id="detalle_consig">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-navy">
                <buttton class="close" type="button" data-dismiss="modal">&times;</buttton>
                <h3>
                    <i class="fa fa-list-alt"></i> Datos de la consignacion 
                    <i class='fa fa-spinner fa-pulse' id="icon-loading-consig" style="display: none;"></i>
                </h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <label>Cliente</label>
                        <p class="list-group-item"><strong id="cliente"></strong></p>
                    </div>
                                
                    <div class="col-lg-3">
                        <label>Fecha de envio</label>
                        <p id="fecha_envio" class="list-group-item"></p>
                    </div>

                    <div class="col-lg-3">
                        <label>---</label>
                        <p id="guia" class="list-group-item"></p>
                        <br>
                    </div>

                    <div class="col-lg-12">
                        <table class="table table-bordered table-striped table-hover data-table" width="100%">
                            <caption>Modelos de la consignacion</caption>
                            <thead class="bg-navy">
                                <tr>
                                    <th>[Codigo]</th>
                                    <th>Nombre</th>
                                    <th>Monturas</th>
                                    <th>Estuches</th>
                                    <th class="text-nowrap">Precio <strong data-toggle="tooltip" title="Precio de venta establecido en la marca y coleccion">(PVE)</strong></th>
                                    <th class="bg-primary">Estado</th>
                                </tr>
                            </thead>
                            <tbody id="data_modelos"></tbody>
                        </table>
                    </div> 
                </div>

                <div class="row" id="section_guia" style="display: none;">
                    <h4 class="padding_1em bg-navy"><i class="fa fa-list-alt"></i> Guia de Remision</h4>

                    <div class="col-lg-4">
                        <label>Nº Serie - Codigo</label> 
                        <p class="list-group-item" id="serie"></p>
                    </div>
                    <div class="col-lg-4">
                        <label>Direccion de salida</label> 
                        <p class="list-group-item" id="dir_salida"></p>
                    </div>
                    <div class="col-lg-4">
                        <label>Direccion de llegada</label> 
                        <p class="list-group-item" id="dir_llegada"></p>
                        <br>
                    </div>
                    <div class="col-lg-12">
                        <table class="table table-hover table-bordered">
                            <caption>Detalles de la guia</caption>
                            <thead class="bg-primary">
                                <tr>
                                    <th>ITEM</th>
                                    <th>CANTIDAD</th>
                                    <th>PESO TOTAL (Kg)</th>
                                    <th>DESCRIPCION</th>
                                </tr>
                            </thead>
                            <tbody id="data_detalles_guia">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>      
            <div class="modal-footer">
                <div class="text-right">
                    <input type="button" class="btn btn-danger cerrar" data-dismiss="modal" value="Cerrar">
                </div>
            </div>
        </div>
    </div>
</div>