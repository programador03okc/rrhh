function listarAcus(tp){
    var vardataTables = funcDatatables();
    var tabla = $('#listaAcu').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'retrieve': true,
        'ajax': 'listar_acus',
        'columns': [
            {'data': 'id_cu'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'abreviatura'},
            {'data': 'rendimiento'},
            {'data': 'total'}
        ]
    });
    $('#listaAcu tbody').on("click","tr", function(){
        var data = tabla.row($(this)).data();
        console.log(data);
        console.log(tp);
        if (tp=='cd'){
            $('[name=id_cu]').val(data.id_cu);
            $('[name=cod_acu]').val(data.codigo);
            $('[name=des_acu]').val(data.descripcion);
            $('[name=precio_unitario]').val(data.total);
            $('[name=unid_medida]').val(data.unid_medida);
        } 
        else if (tp=='ci'){
            $('[name=id_cu_ci]').val(data.id_cu);
            $('[name=cod_acu_ci]').val(data.codigo);
            $('[name=des_acu_ci]').val(data.descripcion);
            $('[name=precio_unitario_ci]').val(data.total);
            $('[name=unid_medida_ci]').val(data.unid_medida);
        } 
        else if (tp=='gg'){
            $('[name=id_cu_gg]').val(data.id_cu);
            $('[name=cod_acu_gg]').val(data.codigo);
            $('[name=des_acu_gg]').val(data.descripcion);
            $('[name=precio_unitario_gg]').val(data.total);
            $('[name=unid_medida_gg]').val(data.unid_medida);
        }
        $('#modal-acu').modal('hide');
    });
}
function acuModal(tp){
    $('#modal-acu').modal({
        show: true
    });
    // $("#listaAcu").dataTable().fnDestroy();
    // clearDataTable();
    listarAcus(tp);
}
