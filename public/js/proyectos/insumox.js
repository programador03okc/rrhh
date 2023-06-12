$(function(){
    listarInsumos();
});
function listarInsumos(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaInsumo').DataTable({
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
            {'data': 'precio'},
            {'data': 'flete'},
            {'data': 'peso_unitario'},
            {'data': 'iu_descripcion'},
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
    botones('#listaInsumo tbody',tabla)
}
function botones(tbody, tabla){
    console.log("editar");
    $(tbody).on("click","button.editar", function(){
        var data = tabla.row($(this).parents("tr")).data();
        open_insumo_create(data);
    });
    $(tbody).on("click","button.anular", function(){
        var data = tabla.row($(this).parents("tr")).data();
        anular_insumo(data.id_insumo);
    });
}
function open_insumo_create(data){
    $('#modal-insumo_create').modal({
        show: true
    });
    if (data !== undefined){
        $('[name=id_insumo]').val(data.id_insumo);
        $('[name=codigo]').val(data.codigo);
        $('[name=descripcion]').val(data.descripcion);
        $('[name=tp_insumo]').val(data.tp_insumo);
        $('[name=unid_medida]').val(data.unid_medida);
        $('[name=precio]').val(data.precio);
        $('[name=flete]').val(data.flete);
        $('[name=peso_unitario]').val(data.peso_unitario);
        $('[name=iu]').val(data.iu).trigger('change.select2');
    } else {
        $('[name=id_insumo]').val('');
        $('[name=codigo]').val('');
        $('[name=descripcion]').val('');
        $('[name=tp_insumo]').val('');
        $('[name=unid_medida]').val('');
        $('[name=precio]').val('');
        $('[name=flete]').val('');
        $('[name=peso_unitario]').val('');
        $('[name=iu]').val('').trigger('change.select2');
    }
}
function guardar_insumo(){
    var id = $('[name=id_insumo]').val();
    var des = $('[name=descripcion]').val();
    var tp = $('[name=tp_insumo]').val();
    var und = $('[name=unid_medida]').val();
    var pre = $('[name=precio]').val();
    var fle = $('[name=flete]').val();
    var pes = $('[name=peso_unitario]').val();
    var iu = $('[name=iu]').val();

    var data = 'id_insumo='+id+
            '&descripcion='+des+
            '&tp_insumo='+tp+
            '&unid_medida='+und+
            '&precio='+pre+
            '&flete='+fle+
            '&peso_unitario='+pes+
            '&iu='+iu;
    var token = $('#token').val();
    console.log(data);
    var baseUrl;
    if (id !== ''){
        baseUrl = 'actualizar_insumo';
    } else {
        baseUrl = 'guardar_insumo';
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
                alert('Insumo registrado con exito');
                $('#listaInsumo').DataTable().ajax.reload();
                $('#modal-insumo_create').modal('hide');
                changeStateButton('guardar');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function anular_insumo(id){
    console.log(id);
    var anula = confirm('¿Esta seguro que desea Anular éste Insumo?');
    
    if (anula){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: 'anular_insumo/'+id,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Insumo anulado con exito');
                    $('#listaInsumo').DataTable().ajax.reload();
                    changeStateButton('anular');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}