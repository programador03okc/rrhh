$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaProveedor tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaProveedor').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        var idCo = $(this)[0].childNodes[1].innerHTML;
        var des = $(this)[0].childNodes[3].innerHTML;
        $('.modal-footer #id_proveedor').text(idTr);
        $('.modal-footer #id_contribuyente').text(idCo);
        $('.modal-footer #razon_social').text(des);
    });
});

function listar_proveedores(){
    var vardataTables = funcDatatables();
    $('#listaProveedor').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': '/mostrar_proveedores',
        'columns': [
            {'data': 'id_proveedor'},
            {'data': 'id_contribuyente'},
            {'data': 'nro_documento'},
            {'data': 'razon_social'},
        ],
        'columnDefs': [{ 'aTargets': [0,1], 'sClass': 'invisible'}],
    });
}

function proveedorModal(){
    $('#modal-proveedores').modal({
        show: true
    });
    // clearDataTable();
    listar_proveedores();
}

function selectProveedor(){
    var myId = $('.modal-footer #id_proveedor').text();
    var idCo = $('.modal-footer #id_contribuyente').text();
    var des = $('.modal-footer #razon_social').text();
    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');

    // console.log('proveedor'+myId+' razon_social'+des);
    $('[name=id_proveedor]').val(myId);
    $('[name=id_contrib]').val(idCo);
    $('[name=razon_social]').val(des);
    if (page == "cotizacion"){
        change_proveedor(myId);
    }
    
    $('#modal-proveedores').modal('hide');
}