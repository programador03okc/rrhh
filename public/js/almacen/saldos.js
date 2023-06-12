function listarSaldos(){
    var almacen = $('[name=almacen]').val();
    console.log(almacen);
    var vardataTables = funcDatatables();
    $('#listaSaldos').dataTable({
        'destroy':true,
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': {
            url:'saldos/'+almacen,
            dataSrc:''
        },
        'columns': [
            {'data': 'id_prod_ubi'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            // {'data': 'fecha_emision'},
            {'data': 'cod_posicion'},
            {'data': 'abreviatura'},
            {'data': 'stock'},
            {'data': 'costo_promedio'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        "order": [[2, "asc"]]
    });
}
