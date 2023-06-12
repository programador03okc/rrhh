$(function(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaProyecto').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_proyectos',
        'columns': [
            {'data': 'id_proyecto'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'razon_social'},
            {'data': 'nombre_tp_proyecto'},
            {'data': 'nombre_modalidad'},
            {'data': 'nombre_sis_contrato'},
            {'data': 'simbolo'},
            {'data': 'importe'},
            {'data': 'usuario'},
            {'render': 
                function (data, type, row){
                    return (row['plazo_ejecucion']+' '+row['des_unid_prog']);
                }
            },
            // {'render':
            //     function (data, type, row){
            //         return (formatDate(row['fecha_fin']));
            //     }
            // },
            {'defaultContent': 
            '<button type="button" class="editar btn btn-primary boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Editar" >'+
                '<i class="fas fa-edit"></i></button>'+
            '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Anular" >'+
                '<i class="fas fa-trash"></i></button>'+
            '<button type="button" class="contrato btn btn-warning boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Ver Contratos" >'+
                '<i class="fas fa-file-upload"></i></button>'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    botones('#listaProyecto tbody',tabla);
});
function botones(tbody, tabla){
    console.log("editar");
    $(tbody).on("click","button.editar", function(){
        var data = tabla.row($(this).parents("tr")).data();
        open_proyecto_create(data);
    });
    $(tbody).on("click","button.anular", function(){
        var data = tabla.row($(this).parents("tr")).data();
        anular_proyecto(data.id_proyecto);
    });
    $(tbody).on("click","button.contrato", function(){
        var data = tabla.row($(this).parents("tr")).data();
        open_proyecto_contrato(data);
    });
}
function open_proyecto_create(data){
    $('#modal-proyecto_create').modal({
        show: true
    });
    if (data !== undefined){
        $('[name=id_proyecto]').val(data.id_proyecto);
        $('[name=codigo]').val(data.codigo);
        $('[name=id_op_com]').val(data.id_op_com);
        $('[name=nombre_opcion]').val(data.descripcion);
        $('[name=tp_proyecto]').val(data.tp_proyecto);
        $('[name=cliente]').val(data.cliente);
        $('[name=moneda]').val(data.moneda);
        $('[name=simbolo]').val(data.simbolo);
        $('[name=importe]').val(data.importe);
        $('[name=sis_contrato]').val(data.sis_contrato);
        $('[name=modalidad]').val(data.modalidad);
        $('[name=plazo_ejecucion]').val(data.plazo_ejecucion);
        $('[name=unid_program]').val(data.unid_program);
        $('[name=fecha_inicio]').val(data.fecha_inicio);
        $('[name=fecha_fin]').val(data.fecha_fin);
        // $('[name=iu]').val(data.iu).trigger('change.select2');
    } else {
        $('[name=id_proyecto]').val('');
        $('[name=codigo]').val('');
        $('[name=id_op_com]').val('');
        $('[name=nombre_opcion]').val('');
        $('[name=tp_proyecto]').val('');
        $('[name=cliente]').val('');
        $('[name=moneda]').val('');
        $('[name=simbolo]').val('');
        $('[name=importe]').val('');
        $('[name=sis_contrato]').val('');
        $('[name=modalidad]').val('');
        $('[name=plazo_ejecucion]').val('');
        $('[name=unid_program]').val('');
        $('[name=fecha_inicio]').val('');
        $('[name=fecha_fin]').val('');
    }

}
function guardar_proyecto(){
    var id = $('[name=id_proyecto]').val();
    var id_op = $('[name=id_op_com]').val();
    var des = $('[name=nombre_opcion]').val();
    var tp = $('[name=tp_proyecto]').val();
    var cli = $('[name=cliente]').val();
    var mon = $('[name=moneda]').val();
    var imp = $('[name=importe]').val();
    var sis = $('[name=sis_contrato]').val();
    var mod = $('[name=modalidad]').val();
    var plz = $('[name=plazo_ejecucion]').val();
    var prog = $('[name=unid_program]').val();
    var fec_ini = $('[name=fecha_inicio]').val();
    var fec_fin = $('[name=fecha_fin]').val();
    var jornal = $('[name=jornal]').val();

    var data = 'id_proyecto='+id+
            '&id_op_com='+id_op+
            '&id_empresa='+auth_user.id_empresa+
            '&tp_proyecto='+tp+
            '&descripcion='+des+
            '&cliente='+cli+
            '&elaborado_por='+auth_user.id_usuario+
            '&moneda='+mon+
            '&importe='+imp+
            '&sis_contrato='+sis+
            '&modalidad='+mod+
            '&plazo_ejecucion='+plz+
            '&unid_program='+prog+
            '&fecha_inicio='+fec_ini+
            '&fecha_fin='+fec_fin+
            '&jornal='+jornal;

    console.log(data);

    var token = $('#token').val();
    var baseUrl;
    if (id !== ''){
        baseUrl = 'actualizar_proyecto';
    } else {
        baseUrl = 'guardar_proyecto';
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
                alert('Proyecto registrado con éxito');
                $('#modal-proyecto_create').modal('hide');
                $('#listaProyecto').DataTable().ajax.reload();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function anular_proyecto(ids){
    if (ids !== ''){
        var rspta = confirm("¿Está seguro que desea anular éste Proyecto?")
        if (rspta){
            baseUrl = 'anular_proyecto/'+ids;
            $.ajax({
                type: 'GET',
                headers: {'X-CSRF-TOKEN': token},
                url: baseUrl,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0){
                        alert('Proyecto anulado con éxito');
                        $('#listaProyecto').DataTable().ajax.reload();
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