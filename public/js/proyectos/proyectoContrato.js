$(function(){
    $('[name=fecha_contrato]').val(fecha_actual());
    // $('[name=elaborado_por]').val(JSON.parse(sessionStorage.getItem('userSession')).id_usuario);
    $('[name=id_tp_contrato]').val(1).trigger('change.select2');
    $('[name=moneda]').val(1).trigger('change.select2');
    $('#listaContratos tbody').html('');
    
    $("#form-contrato").on("submit", function(e){
        e.preventDefault();
        guardar_contrato();
    });

});
function open_proyecto_contrato(data){
    $('#modal-proyecto_contrato').modal({
        show: true
    });
    console.log(data.id_proyecto);
    $('[name=id_proyecto]').val(data.id_proyecto);
    $('#cod_proyecto').text(data.codigo);
    $('#des_proyecto').text(data.descripcion);
    listar_contratos_proy(data.id_proyecto);
}
function listar_contratos_proy(id_proyecto){
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'listar_contratos_proy/'+id_proyecto,
        dataType: 'JSON',
        success: function(response){
            $('#listaContratos tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function guardar_contrato(){
    var id_pro = $('[name=id_proyecto]').val();
    var formData = new FormData($('#form-contrato')[0]);
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: 'guardar_contrato',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Contrato registrado con éxito');
                listar_contratos_proy(id_pro);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function anular_contrato(id_contrato){
    if (id_contrato !== ''){
        var rspta = confirm("¿Está seguro que desea anular el contrato?")
        if (rspta){
            $.ajax({
                type: 'GET',
                headers: {'X-CSRF-TOKEN': token},
                url: 'anular_contrato/'+id_contrato,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0){
                        alert('Contrato anulado con éxito');
                        var id = $('[name=id_proyecto]').val();
                        listar_contratos_proy(id);
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

