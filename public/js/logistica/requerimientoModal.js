$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaRequerimientoPendientes tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaRequerimientoPendientes').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        // console.log(idTr);
        $('.modal-footer #id_requerimiento').text(idTr);
    });
});

function listar_requerimientos(){
    var vardataTables = funcDatatables();
    $('#listaRequerimientoPendientes').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': '/listar_requerimientos_pendientes',
        'columns': [
            {'data': 'id_requerimiento'},
            {'data': 'codigo'},
            {'data': 'concepto'},
            {'data': 'des_area'},
            {'data': 'fecha_requerimiento'},
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function requerimientoModal(){
    $('#modal-requerimiento').modal({
        show: true
    });
    clearDataTable();
    listar_requerimientos();
}

function selectRequerimiento(){
    var myId = $('.modal-footer #id_requerimiento').text();
    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');

    if (page == "cotizacion"){
        // console.log('requerimiento'+myId);
        mostrar_detalle_requerimiento(myId);
    }
    
    $('#modal-requerimiento').modal('hide');
}