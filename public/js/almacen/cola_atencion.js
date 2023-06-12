$(function(){
    // clearDataTable();
    $('[name=id_almacen]').val(1).trigger('change.select2');
    listarRequerimientos();
});
function listarRequerimientos(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaPendientes').DataTable({
        'destroy':true,
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_requerimientos',
        'columns': [
            {'data': 'codigo'},
            {'render':
                function (data, type, row){
                    if (row['id_prioridad'] == 1){
                        return ('<i class="fas fa-exclamation-circle icon-tabla blue"></i>');
                    } else if (row['id_prioridad'] == 2){
                        return ('<i class="fas fa-exclamation-circle icon-tabla yellow"></i>');
                    } else if (row['id_prioridad'] == 3){
                        return ('<i class="fas fa-exclamation-circle icon-tabla red"></i>');
                    } else {
                        return ('<i class="fas fa-exclamation-circle icon-tabla "></i>');
                    }
                }
            },
            // {'defaultContent':'<input type="checkbox"/>'},
            // {'render': 
            //     function(data, type, row, meta){
            //         return (meta.row + 1);
            //     }
            // },
            {'data': 'codigo'},
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_requerimiento']));
                }
            },
            {'data': 'responsable'},
            {'data': 'des_grupo'},
            {'data': 'concepto'},
            {'render':
                function (data, type, row){
                    return ((row['id_proyecto'] !== null) ? row['proy_descripcion'] : row['area_descripcion']);
                }
            },
            {'defaultContent': 
                '<button type="button" class="ver btn btn-primary boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Ver Documento" >'+
                    '<i class="fas fa-search-plus"></i></button>'+
                '<button type="button" class="atender btn btn-success boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Atender" >'+
                    '<i class="fas fa-share"></i></button>'+
                '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
                    'data-placement="bottom" title="Anular" >'+
                    '<i class="fas fa-trash"></i></button>'},
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    ver("#listaPendientes tbody", tabla);
    atender("#listaPendientes tbody", tabla);
    anular("#listaPendientes tbody", tabla);
}
function ver(tbody, tabla){
    console.log("ver");
    $(tbody).on("click","button.ver", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
    });
}
function atender(tbody, tabla){
    console.log("atender");
    $(tbody).on("click","button.atender", function(){
        var data = tabla.row($(this).parents("tr")).data();
        req_atencionModal(data);
    });
}
function anular(tbody, tabla){
    console.log("anular");
    $(tbody).on("click","button.anular", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
    });
}