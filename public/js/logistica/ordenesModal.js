$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaOrdenes tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaOrdenes').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        // var idCo = $(this)[0].childNodes[1].innerHTML;
        // var idPr = $(this)[0].childNodes[2].innerHTML;
        // var idCn = $(this)[0].childNodes[3].innerHTML;
        // var des = $(this)[0].childNodes[6].innerHTML;
        
        $('.modal-footer #id_orden_compra').text(idTr);
        // $('.modal-footer #cot_razon_social').text(des);
        // $('.modal-footer #id_cotizacion').text(idCo);
        // $('.modal-footer #id_prov').text(idPr);
        // $('.modal-footer #id_contri').text(idCn);
    });
});

function listar_ordenes(){
    var vardataTables = funcDatatables();
    $('#listaOrdenes').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': '/listar_ordenes',
        'columns': [
            {'data': 'id_orden_compra'},
            {'data': 'codigo'},
            {'data': 'nro_documento'},
            {'data': 'razon_social'},
            {'data': 'monto_total'},
            // {'render':
            //     function (data, type, row){
            //         var req = '';
            //         for (i=0;i<row['requerimiento'].length;i++){
            //             if (req !== ''){
            //                 req += ', '+row['requerimiento'][0].codigo_requerimiento;
            //             } else {
            //                 req += row['requerimiento'][0].codigo_requerimiento;
            //             }
            //         }
            //         return (req);
            //     }
            // },
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
}

function ordenModal(){
    $('#modal-ordenes').modal({
        show: true
    });
    clearDataTable();
    listar_ordenes();
}

function selectOrden(){
    var myId = $('.modal-footer #id_orden_compra').text();
    // var idCo = $('.modal-footer #id_cotizacion').text();
    // var idPr = $('.modal-footer #id_prov').text();
    // var idCn = $('.modal-footer #id_contri').text();
    // var des = $('.modal-footer #cot_razon_social').text();
    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');

    if (page == "orden"){
        $('[name=id_orden_compra]').val(myId);
        // console.log(myId);
        // $('[name=id_cotizacion]').val(idCo);
        // $('[name=id_proveedor]').val(idPr);
        // $('[name=id_contrib]').val(idCn);
        // $('[name=razon_social]').val(des);
        listar_detalle_orden(myId);
        mostrar_orden(myId);
    }
    
    $('#modal-ordenes').modal('hide');
}