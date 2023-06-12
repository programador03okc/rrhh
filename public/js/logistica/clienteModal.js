$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaCliente tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaCliente').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        var idCo = $(this)[0].childNodes[1].innerHTML;
        var des = $(this)[0].childNodes[3].innerHTML;
        $('.modal-footer #id_cliente').text(idTr);
        $('.modal-footer #id_contribuyente').text(idCo);
        $('.modal-footer #razon_social').text(des);
    });
});

function listar_clientes(){
    var vardataTables = funcDatatables();
    $('#listaCliente').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'mostrar_clientes',
        'columns': [
            {'data': 'id_cliente'},
            {'data': 'id_contribuyente'},
            {'data': 'nro_documento'},
            {'data': 'razon_social'},
        ],
        'columnDefs': [{ 'aTargets': [0,1], 'sClass': 'invisible'}],
    });
}

function clienteModal(){
    $('#modal-clientes').modal({
        show: true
    });
    // clearDataTable();
    listar_clientes();
}

function selectCliente(){
    var myId = $('.modal-footer #id_cliente').text();
    var idCo = $('.modal-footer #id_contribuyente').text();
    var des = $('.modal-footer #razon_social').text();
    // var page = $('.page-main').attr('type');
    // var form = $('.page-main form[type=register]').attr('id');

    console.log('cliente'+myId+' razon_social'+des);
    $('[name=id_cliente]').val(myId);
    $('[name=id_contrib]').val(idCo);
    $('[name=cliente_razon_social]').val(des);
    
    
    $('#modal-clientes').modal('hide');
}