function listarDetalleOC(id){
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'listar_oc_det/'+id,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            $('#listaDetalleOC tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function guia_compra_detModal(id_oc){
    $('#modal-guia_detalle').modal({
        show: true
    });
    // clearDataTable();
    listarDetalleOC(id_oc);
}
