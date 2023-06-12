$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaCotizacion tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaCotizacion').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        var idCo = $(this)[0].childNodes[1].innerHTML;
        var idPr = $(this)[0].childNodes[2].innerHTML;
        var idCn = $(this)[0].childNodes[3].innerHTML;
        var des = $(this)[0].childNodes[6].innerHTML;
        var id_req = $(this)[0].childNodes[9].innerHTML;
        // console.log(id_req);
        $('.modal-footer #id_grupo_cotizacion').text(idTr);
        $('.modal-footer #cot_razon_social').text(des);
        $('.modal-footer #id_cotizacion').text(idCo);
        $('.modal-footer #id_prov').text(idPr);
        $('.modal-footer #id_contri').text(idCn);
        $('.modal-footer #id_req').text(id_req);
    });
});

function listar_grupo_cotizaciones(){
    var vardataTables = funcDatatables();
    $('#listaCotizacion').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        'ajax': '/listar_grupo_cotizaciones',
        'columns': [
            {'data': 'id_grupo_cotizacion'},
            {'data': 'id_cotizacion'},
            {'data': 'id_proveedor'},
            {'data': 'id_contribuyente'},
            {'data': 'codigo_grupo'},
            {'data': 'nro_documento'},
            {'data': 'razon_social'},
            {'data': 'codigo_cotizacion'},
            {'render':
                function (data, type, row){
                    var req = '';
                    for (i=0;i<row['requerimiento'].length;i++){
                        if (req !== ''){
                            req += ', '+row['requerimiento'][0].codigo_requerimiento;
                        } else {
                            req += row['requerimiento'][0].codigo_requerimiento;
                        }
                    }
                    return (req);
                }
            },
            {'render':
            function (data, type, row){
                var id_req = '';
                for (i=0;i<row['requerimiento'].length;i++){
                    if (id_req !== ''){
                        id_req += ', '+row['requerimiento'][0].id_requerimiento;
                    } else {
                        id_req += row['requerimiento'][0].id_requerimiento;
                    }
                }
                return (id_req);
            }
        },

         ],
        'columnDefs': [{ 'aTargets': [0,1,2,3,9], 'sClass': 'invisible'}],
    });
}

function cotizacionModal(){
    $('#modal-cotizacion').modal({
        show: true
    });
    clearDataTable();
    listar_grupo_cotizaciones();
}

function selectGrupoCotizacion(){
    var idReq = $('.modal-footer #id_req').text(); //llevar este '93, 93' a un array
    let arrIdReq = idReq.split(", ");
    console.log(arrIdReq); // enviar en page === orden para al guardar la orden enviar los id_requeimientos para ser actualizados su estado a  = 5 (atendidos) 
    
    var myId = $('.modal-footer #id_grupo_cotizacion').text();
    var idCo = $('.modal-footer #id_cotizacion').text();
    var idPr = $('.modal-footer #id_prov').text();
    var idCn = $('.modal-footer #id_contri').text();
    var des = $('.modal-footer #cot_razon_social').text();
    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');
    // console.log(des);
    // console.log(idCn);

    if (page == "cotizacion"){
        mostrar_grupo_cotizacion(myId);
        listar_cotizaciones(myId);
        listar_items_cotizaciones(myId);
    } 
    else if (page == "orden"){
        $('[name=id_grupo_cotizacion]').val(myId);
        $('[name=id_cotizacion]').val(idCo);
        $('[name=id_proveedor]').val(idPr);
        $('[name=id_contrib]').val(idCn);
        $('[name=razon_social]').val(des);
        detalle_cotizacion(idCo);
        mostrar_cuentas_bco();
    }
    else if (page == "cuadro_comparativo"){
        var myId = $('.modal-footer #id_grupo_cotizacion').text();
        grupoCotizaciones(myId,'3');


        
    }

    
    $('#modal-cotizacion').modal('hide');
}