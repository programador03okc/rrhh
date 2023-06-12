function listarOpcion(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaOpcion').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_opciones',//1 Presupuesto Interno
        'bDestroy': true,
        'retrieve': true,
        'columns': [
            {'data': 'id_op_com'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_emision']));
                }
            },
            {'data': 'simbolo'},
            {'data': 'importe'}
        ]
    });
    $('#listaOpcion tbody').on("click","tr", function(){
        var data = tabla.row($(this)).data();
        console.log(data);
        $('[name=id_op_com]').val(data.id_op_com);
        $('[name=nombre_opcion]').val(data.descripcion);
        $('[name=simbolo]').val(data.simbolo);
        $('[name=importe]').val(data.importe);
        $('[name=moneda]').val(data.moneda);
        $('[name=tp_proyecto]').val(data.tp_proyecto);
        $('[name=cliente]').val(data.cliente);
        $('#modal-opcion').modal('hide');
});
}
function open_opcion_modal(){
    $('#modal-opcion').modal({
        show: true
    });
    listarOpcion();
}
