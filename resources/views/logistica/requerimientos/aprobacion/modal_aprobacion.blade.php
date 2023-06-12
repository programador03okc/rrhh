<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-aprobacion-docs">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <form id="form-aprobacion">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Proceso de Aprobación</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" name="id_documento">
                            <input type="hidden" name="doc_aprobacion">
                            <input type="hidden" name="flujo">
                            <h5>Motivo/Justificación</h5>
                            <textarea class="form-control input-sm" name="motivo" id="motivo" rows="5"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-success">Grabar</button>
                </div>
            </form>
        </div>
    </div>
</div>