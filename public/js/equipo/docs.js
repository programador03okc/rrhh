$(function(){
    listar_docs();
});
function listar_docs(){
    var vardataTables = funcDatatables();

    $('#listaDocs').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        ajax:{url:"listar_docs",dataSrc:""},
        'columns': [
            {'data': 'id_seguro'},
            {'render': 
                function (data, type, row){
                    return ('<i class="fas fa-exclamation-triangle '+row['warning']+'"></i>');
                }
            },
            {'data': 'cod_equipo'},
            {'data': 'des_equipo'},
            {'data': 'tipo_seguro'},
            {'data': 'nro_poliza'},
            {'data': 'razon_social'},
            {'data': 'fecha_inicio'},
            {'data': 'fecha_fin'},
            {'data': 'importe'},
            {'render':
                function (data, type, row){
                    return ((row['archivo_adjunto'] !== null) ? ('<a href="abrir_adjunto_seguro/'+row['archivo_adjunto']+'">'+row['archivo_adjunto']+'</a>') : '');
                }
            },
            // {'defaultContent': 
            // '<button type="button" class="editar btn btn-primary boton" data-toggle="tooltip" '+
            //     'data-placement="bottom" title="Editar" >'+
            //     '<i class="fas fa-edit"></i></button>'+
            // '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
            //     'data-placement="bottom" title="Anular" >'+
            //     '<i class="fas fa-trash"></i></button>'+
            // '<button type="button" class="seguro btn btn-warning boton" data-toggle="tooltip" '+
            //     'data-placement="bottom" title="Ver Seguros" >'+
            //     '<i class="fas fa-file-upload"></i></button>'+
            // '<button type="button" class="programacion btn btn-info boton" data-toggle="tooltip" '+
            //     'data-placement="bottom" title="Ver Program. Mtto." >'+
            //     '<i class="fas fa-clock"></i></button>'}
        ]
    });
    // botones('#listaMttoPendientes tbody',tabla);
}
