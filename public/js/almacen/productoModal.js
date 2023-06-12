$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaProducto tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaProducto').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        var code = $(this)[0].childNodes[1].innerHTML;
        var desc = $(this)[0].childNodes[2].innerHTML;
        var unid = $(this)[0].childNodes[3].innerHTML;
        $('.modal-footer #id_producto').text(idTr);
        $('.modal-footer #codigo').text(code);
        $('.modal-footer #descripcion').text(desc);
        $('.modal-footer #unid_med').text(unid);
    });
});
function listarProductos(){
    var vardataTables = funcDatatables();
    $('#listaProducto').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        // 'processing': true,
        'ajax': 'mostrar_prods',
        'columns': [
            {'data': 'id_producto'},
            {'data': 'codigo'},
            {'data': 'cod_antiguo'},
            {'data': 'descripcion'},
            {'data': 'id_unidad_medida'},
        ],
        'columnDefs': [{ 'aTargets': [0,4], 'sClass': 'invisible'}],
    });
}
function productoModal(){
    $('#modal-producto').modal({
        show: true
    });
    clearDataTable();
    listarProductos();
}
function selectProducto(){
    var myId = $('.modal-footer #id_producto').text();
    var code = $('.modal-footer #codigo').text();
    var desc = $('.modal-footer #descripcion').text();
    var unid = $('.modal-footer #unid_med').text();
    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');
    if (form == undefined){
        var form = $('.page-main form[type=edition]').attr('id');
    }
    console.log('form:'+form);
    if (page == "producto"){
        if (form == "form-general"){
            clearForm(form);
            mostrar_producto(myId);
            changeStateButton('historial');
        } 
        else if (form == "form-ubicacion"){
            // clearDataTable();
            listar_ubicaciones(myId);
            var abr = $('[name=abr_id_unidad_medida]').text();
            $('[name=id_producto]').val(myId);
            $('[name=abreviatura]').text(abr);
        } 
        else if (form == "form-serie"){
            // clearDataTable();
            listar_series(myId);
            $('[name=id_producto]').val(myId);
        }
    }
    else if (page == "guia_compra"){
        guardar_guia_detalle(myId,unid);
    }
    else if (page == "guia_venta"){
        guardar_guia_detalle(myId,unid);
    }
    else if (page == "kardex_detallado"){
        $('[name=id_producto]').val(myId);
        $('[name=descripcion]').val(code+' - '+desc);
    }
    $('#modal-producto').modal('hide');
}
