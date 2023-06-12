function onChangeTipo(){
    var tipo = $('[name=tipo]').val();
    var id_guia_ven = $('[name=id_guia_ven]').val();
    console.log('tipo'+tipo);
    if (id_guia_ven !== ''){
        if (tipo == 1){
            var id_almacen = $('[name=id_almacen]').val();
            listar_guias_compra(id_almacen);
        } else if (tipo == 2){
            var id_empresa = $('[name=id_empresa]').val();
            listar_req(id_empresa);
        } else if (tipo == 3){
            $('[name=docs_sustento]').html('<option value="0" disabled selected>Elija una opción</option>');
            $('[name=docs_sustento]').val(0).trigger('change.select2');
        }
    // } else {
    //     alert('Debe seleccionar una Guía!');
    }
}
function agrega_sustento(){
    console.log('agregar_sustento');
    var tipo = $('[name=tipo]').val();
    console.log('sustento:'+tipo);
    $('#modal-guia_detalle_ing').modal({
        show: true
    });
    listarDetalleIng(tipo);
}
function listar_guias_compra(id_almacen){
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'listar_guias_compra/'+id_almacen,
        dataType: 'JSON',
        success: function(response){
            var option = '';
            for (var i=0;i<response.length;i++){
                option+='<option value="'+response[i].id_guia+'"> GR-'+response[i].serie+'-'+response[i].numero+' '+response[i].razon_social+'</option>';
            }
            $('[name=docs_sustento]').html('<option value="0" disabled selected>Elija una opción</option>'+option);
            $('[name=docs_sustento]').val(0).trigger('change.select2');
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function listar_req(id_empresa){
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'listar_req/'+id_empresa,
        dataType: 'JSON',
        success: function(response){
            var option = '';
            for (var i=0;i<response.length;i++){
                option+='<option value="'+response[i].id_requerimiento+'">'+response[i].codigo+' - '+response[i].concepto+'</option>';
            }
            $('[name=docs_sustento]').html('<option value="0" disabled selected>Elija una opción</option>'+option);
            $('[name=docs_sustento]').val(0).trigger('change.select2');
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function listarDetalleIng(tipo){
    var id = $('[name=docs_sustento]').val();
    //Guia de Compra 
    if (id !== null){
        console.log(id);
        console.log(tipo);
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: 'listar_ing_det/'+id+'/'+tipo,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                $('#listaDetalleIng tbody').html(response);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        }); 
    } else {
        alert('Debe seleccionar un Documento!');
    }
}
function guardar_detalle_ing(){
    var id_mov_alm_det = [];
    var r = 0;

    $("input[type=checkbox]:checked").each(function(){
        id_mov_alm_det[r] = $(this).closest('td').siblings().find("input[name=id]").val();
        ++r;
    });
    
    if (r == 0){
        alert('Debe seleccionar por lo menos un item');
    } else {
        console.log(id_mov_alm_det);
        var token = $('#token').val();
        var id_guia_ven = $("[name=id_guia_ven]").val();
        
        var data =  'id_guia_ven='+id_guia_ven+
                    '&id_mov_alm_det='+id_mov_alm_det;
        console.log(data);
        $.ajax({
            type: 'POST',
            headers: {'X-CSRF-TOKEN': token},
            url: 'guardar_detalle_ing',
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Detalle registrado con éxito');
                    $('#listaDetalle tbody tr').remove();
                    listar_detalle(id_guia_ven);
                    $('#modal-guia_detalle_ing').modal('hide');
                    // guia_ocs(id_guia);
                    $('[name=docs_sustento]').val('0').trigger('change.select2');
                    changeStateButton('guardar');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });    
    }
}
