function listarItems(id){
    var vardataTables = funcDatatables();
    var tabla = $('#listaItems').DataTable({
        'language' : vardataTables[0],
        'bDestroy': true,
        'retrieve': true,
        'ajax': 'listar_items_req/'+id,
        'columns': [
            {'data': 'codigo'},
            {'defaultContent':'<input type="checkbox"/>'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'cod_posicion'},
            {'data': 'cantidad'},
            {'data': 'abreviatura'},
            {'data': 'partida'},
        ]
    });
}
function req_atencionModal(data){
    $('#modal-req_atencion').modal({
        show: true
    });
    console.log(data);
    $('#cod_req').text(data.codigo);
    $('#des_prioridad').text(data.des_prioridad);
    $('#concepto').text(data.concepto);
    $('#fecha_requerimiento').text(formatDate(data.fecha_requerimiento));
    $('#responsable').text(data.responsable);
    $('#des_grupo').text(data.des_grupo);
    $('#id_requerimiento').text(data.id_requerimiento);
    
    if (data.id_proyecto !== null){
        $('#area_proy').text(data.proy_descripcion);
    } else if (data.id_area !== null){
        if (data.id_area == 5)
            $('#area_proy').text('GASTOS ADMINISTRATIVOS');
        else
            $('#area_proy').text(data.area_descripcion);
    }
    listarItems(data.id_requerimiento);
}
function atender_req(){
    var rspta = confirm('Desea generar Guía de Venta');

    if (rspta){
        open_guia_ven_create();
    }
}
function open_guia_ven_create(){
    $('#modal-guia_ven_create').modal({
        show: true
    });
}
function ceros_numero_guia(){
    var num = $('[name=numero_guia]').val();
    $('[name=numero_guia]').val(leftZero(num,6));
}
function guardar_guia_ven_create(){
    var serie = $('[name=serie_guia]').val();
    var numero = $('[name=numero_guia]').val();
    var fecha_emision = $('[name=fecha_emision_guia]').val();
    var id_motivo = $('[name=id_motivo]').val();
    var id_guia_clas = $('[name=id_guia_clas]').val();

    var tabla = $('#listaItems').DataTable();
    var json = [];
    var id_req;
    $("input[type=checkbox]:checked").each(function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
        json.push(data);
        id_req = data.id_requerimiento;
    });
    console.log(json);
    var det = JSON.stringify(json);
    var token = $('#token').val();

    var data = 'serie='+serie+
            '&numero='+numero+
            '&fecha_emision='+fecha_emision+
            '&id_motivo='+id_motivo+
            '&id_guia_clas='+id_guia_clas+
            '&id_empresa='+auth_user.id_empresa+
            '&usuario='+auth_user.id_usuario+
            '&detalle='+det;

    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: 'guardar_guia_ven',
        data: data,
        dataType: 'JSON',
        success: function(id_guia){
            console.log(id_guia);
            if (id_guia > 0){
                alert('Guia de Venta generada con éxito');
                var token_guia = $('#token_guia').val();
                var salida = 'id_req='+id_req+'&id_guia='+id_guia+'&detalle='+det;
                console.log(salida);
                $.ajax({
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': token_guia},
                    url: 'generar_salida',
                    data: salida,
                    dataType: 'JSON',
                    success: function(id_salida){
                        console.log(id_salida);
                        if (id_salida > 0){
                            alert('Salida de Almacén generada con éxito');
                            $('#modal-guia_ven_create').modal('hide');
                            $('#modal-req_atencion').modal('hide');
                            window.open('imprimir_salida/'+id_salida);
                        }
                    }
                }).fail( function( jqXHR, textStatus, errorThrown ){
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}