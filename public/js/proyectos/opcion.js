$(function(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaOpcion').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_opciones',
        'columns': [
            {'data': 'id_op_com'},
            {'data': 'codigo'},
            {'render': 
                function (data, type, row){
                    return (formatDate(row['fecha_emision']));
                }
            },
            {'data': 'descripcion'},
            {'data': 'razon_social'},
            {'data': 'des_tp_proyecto'},
            {'data': 'simbolo'},
            {'data': 'importe'},
            {'data': 'usuario'},
            {'render':
                function (data, type, row){
                    return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
                }
            },
            {'defaultContent': 
            '<button type="button" class="editar btn btn-primary boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Editar" >'+
                '<i class="fas fa-edit"></i></button>'+
            '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Anular" >'+
                '<i class="fas fa-trash"></i></button>'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    botones('#listaOpcion tbody',tabla)
});
function botones(tbody, tabla){
    console.log("editar");
    $(tbody).on("click","button.editar", function(){
        var data = tabla.row($(this).parents("tr")).data();
        open_opcion_create(data);
    });
    $(tbody).on("click","button.anular", function(){
        var data = tabla.row($(this).parents("tr")).data();
        anular_opcion(data.id_op_com);
    });
}
function open_opcion_create(data){
    $('#modal-opcion_create').modal({
        show: true
    });
    if (data !== undefined){
        $('[name=id_op_com]').val(data.id_op_com);
        $('[name=codigo]').val(data.codigo);
        $('[name=descripcion]').val(data.descripcion);
        $('[name=tp_proyecto]').val(data.tp_proyecto);
        $('[name=cliente]').val(data.cliente);
        $('[name=moneda]').val(data.moneda);
        $('[name=simbolo]').val(data.simbolo);
        $('[name=importe]').val(data.importe);
        $('[name=fecha_emision]').val(data.fecha_emision);
        // $('[name=iu]').val(data.iu).trigger('change.select2');
    } else {
        $('[name=id_op_com]').val('');
        $('[name=codigo]').val('');
        $('[name=descripcion]').val('');
        $('[name=tp_proyecto]').val('');
        $('[name=cliente]').val('');
        $('[name=moneda]').val('');
        $('[name=importe]').val('');
        $('[name=fecha_emision]').val('');
    }

}
function guardar_opcion(){
    var id = $('[name=id_op_com]').val();
    // var cod = $('[name=codigo]').val();
    var des = $('[name=descripcion]').val();
    var tp = $('[name=tp_proyecto]').val();
    var cli = $('[name=cliente]').val();
    var mon = $('[name=moneda]').val();
    var imp = $('[name=importe]').val();
    var fech = $('[name=fecha_emision]').val();

    var data = 'id_op_com='+id+
            '&id_empresa='+auth_user.id_empresa+
            '&tp_proyecto='+tp+
            '&descripcion='+des+
            '&cliente='+cli+
            '&elaborado_por='+auth_user.id_usuario+
            '&moneda='+mon+
            '&importe='+imp+
            '&fecha_emision='+fech;

    console.log(data);

    var token = $('#token').val();
    var baseUrl;
    if (id !== ''){
        baseUrl = 'actualizar_opcion';
    } else {
        baseUrl = 'guardar_opcion';
    }
    console.log(baseUrl);
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Opcion Comercial registrada con éxito');
                $('#modal-opcion_create').modal('hide');
                $('#listaOpcion').DataTable().ajax.reload();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function anular_opcion(ids){
    if (ids !== ''){
        var rspta = confirm("¿Está seguro que desea anular ésta Opción?")
        if (rspta){
            baseUrl = 'anular_opcion/'+ids;
            $.ajax({
                type: 'GET',
                headers: {'X-CSRF-TOKEN': token},
                url: baseUrl,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0){
                        alert('Opción Comercial anulada con éxito');
                        $('#listaOpcion').DataTable().ajax.reload();
                    }
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });        
        }
    }
}

function moneda(){
    $moneda = $('select[name="moneda"] option:selected').text();
    console.log($moneda);
    $simbolo = $moneda.split(" - ");
    if ($simbolo.length > 0){
        console.log($simbolo[1]);
        $('[name=simbolo]').val($simbolo[1]);
    } else {
        $('[name=simbolo]').val("");
    }
}