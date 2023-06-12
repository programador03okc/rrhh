@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<fieldset>
    <legend><h2>Busqueda Avanzada de Postulantes</h2></legend>
    <div class="row">
        <div class="col-md-3">
            <button class="btn btn-sm btn-info btn-flat" onclick="crearReportePostu();">
                <i class="fa fa-cogs"></i> Filtro de busqueda
            </button>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="group-table">
                <table class="table table-striped table-bordered table-okc-view" id="my-report-table" width="100%"></table>
            </div>
        </div>
    </div>
</fieldset>

<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-informacion-reporte">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Información del Postulante</h3>
            </div>
            <div class="modal-body" id="info-detail"></div>
        </div>
    </div>
</div>

<!-- Modal Filtro -->
<div class="modal fade" tabindex="-1" role="dialog" id="modalFiltros">
	<div class="modal-dialog" style="width: 550px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">Filtros</h3>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-5">
								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="checkCarrera">
									<label class="text-muted" for="checkCarrera">Carrera prof.</label>
								</div>
							</div>
							<div class="col-md-7">
								<input type="text" class="form-control input-sm" name="fil_carrera" id="fil_carrera">
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-5">
								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="checkEmpresa">
									<label class="text-muted" for="checkEmpresa">Empresa</label>
								</div>
							</div>
							<div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="fil_empresa" id="fil_empresa">
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-5">
								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="checkCargo">
									<label class="text-muted" for="checkCargo">Cargo ocupado</label>
								</div>
							</div>
							<div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="fil_cargo" id="fil_cargo">
							</div>
						</div>
                        <br>
                        <div class="row">
							<div class="col-md-5">
								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="checkFuncion">
									<label class="text-muted" for="checkFuncion">Funciones realizadas</label>
								</div>
							</div>
							<div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="fil_funciones" id="fil_funciones">
							</div>
						</div>
                        <br>
                        <div class="row">
							<div class="col-md-5">
								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="checkProvincia">
									<label class="text-muted" for="checkProvincia">Provincia</label>
								</div>
							</div>
							<div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="fil_provincia" id="fil_provincia">
							</div>
						</div>
						<br>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-sm btn-flat btn-primary" onclick="filterView();"> Procesar </button>
				<button type="button" class="btn btn-sm btn-flat btn-success" onclick="reportView();"> Reporte </button>
			</div>
		</div>
	</div>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/reportes/reporte_rrhh.js')}}"></script>
@include('layout.fin_html')