$(function(){
    listar_mtto_realizados();
});
function listar_mtto_realizados(){
    var vardataTables = funcDatatables();
    var actual = fecha_actual();
    console.log(actual);

    $('#listaMttoRealizados').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        ajax:{url:"listar_mttos_detalle",dataSrc:""},
        'columns': [
            {'data': 'id_mtto'},
            {'render':
                function (data, type, row){
                    return ((row['id_programacion'] !== null) ? '<i class="fas fa-tasks purple"></i>' : '');
                }
            },
            {'data': 'cod_equipo'},
            {'data': 'des_equipo'},
            {'render':
                function (data, type, row){
                    return ((row['tp_mantenimiento'] == 1) ? 'Mtto. Preventivo' : 'Mtto. Correctivo');
                }
            },
            {'data': 'descripcion'},
            {'data': 'precio_total'},
            {'data': 'resultado'},
            {'data': 'estado_doc'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    // botones('#listaMttoPendientes tbody',tabla);
}
// function botones(tbody, tabla){
//     console.log("editar");
//     $(tbody).on("click","button.editar", function(){
//         var data = tabla.row($(this).parents("tr")).data();
//         equipo_create(data);
//     });
//     $(tbody).on("click","button.anular", function(){
//         var data = tabla.row($(this).parents("tr")).data();
//         anular_equipo(data.id_equipo);
//     });
//     $(tbody).on("click","button.seguro", function(){
//         var data = tabla.row($(this).parents("tr")).data();
//         open_seguro(data);
//     });
//     $(tbody).on("click","button.programacion", function(){
//         var data = tabla.row($(this).parents("tr")).data();
//         open_programacion(data);
//     });
// }
