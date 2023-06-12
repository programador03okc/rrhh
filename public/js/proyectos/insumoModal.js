$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaInsumo tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaInsumo').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        var cod = $(this)[0].childNodes[1].innerHTML;
        var des = $(this)[0].childNodes[2].innerHTML;
        var tp = $(this)[0].childNodes[3].innerHTML;
        var unid = $(this)[0].childNodes[4].innerHTML;
        var prec = $(this)[0].childNodes[5].innerHTML;
        $('.modal-footer #id_insumo').text(idTr);
        $('.modal-footer #cod_insumo').text(cod);
        $('.modal-footer #des_insumo').text(des);
        $('.modal-footer #tp_insumo').text(tp);
        $('.modal-footer #unid_medida').text(unid);
        $('.modal-footer #precio').text(prec);
    });
});
function listarInsumos(){
    var vardataTables = funcDatatables();
    $('#listaInsumo').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        // 'processing': true,
        'ajax': 'listar_insumos',
        'columns': [
            {'data': 'id_insumo'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'cod_tp_insumo'},
            {'data': 'abreviatura'},
            {'data': 'precio'}
        ]
    });
}
function insumoModal(){
    $('#modal-insumo').modal({
        show: true
    });
    // clearDataTable();
    listarInsumos();
}
function selectInsumo(){
    var myId = $('.modal-footer #id_insumo').text();
    var cod = $('.modal-footer #cod_insumo').text();
    var des = $('.modal-footer #des_insumo').text();
    var tp = $('.modal-footer #tp_insumo').text();
    var unid = $('.modal-footer #unid_medida').text();
    var prec = $('.modal-footer #precio').text();

    // if (page == "insumo"){
    //     clearForm(form);
    //     mostrar_insumo(myId);
    //     changeStateButton('historial');
    //     console.log($(":file").filestyle('disabled'));
    // }
    // else if (page == "acu"){
        $('[name=id_insumo]').val(myId);
        $('[name=cod_insumo]').val(cod);
        $('[name=des_insumo]').val(des);
        $('[name=tp_insumo]').val(tp);
        $('[name=unidad]').val(unid);
        $('[name=precio_unitario]').val(prec);
        console.log(myId+' '+cod+' '+des+' '+tp+' '+unid);
        // $('[name=precio_unitario]').val((myId == 326) ? this.state.objeto.mo : res.data[0].precio);
    // }
    $('#modal-insumo').modal('hide');
}
