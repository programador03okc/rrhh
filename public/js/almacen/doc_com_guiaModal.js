function doc_guia(){
    var id_tp_doc = 2;
    $('[name=id_tp_doc]').val(id_tp_doc).trigger('change.select2');
    $('[name=fecha_emision_doc]').val(fecha_actual());
}
function guardar_doc_guia(){
    var id_guia = $('[name=id_guia]').val();
    var id_tp_doc = $('[name=id_tp_doc]').val();
    var serie_doc = $('[name=serie_doc]').val();
    var numero_doc = $('[name=numero_doc]').val();
    var fecha_emision_doc = $('[name=fecha_emision_doc]').val();
    var token = $('#token').val();

    data =  'id_guia='+id_guia+
            '&id_tp_doc='+id_tp_doc+
            '&serie='+serie_doc+
            '&numero='+numero_doc+
            '&fecha_emision='+fecha_emision_doc;
    console.log('data='+data);

    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: 'guardar_doc_guia',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log('response'+response);
            if (response > 0){
                alert('Comprobante registrado con éxito');
                localStorage.setItem("id_doc_com",response);
                $('#modal-doc_guia').modal('hide');
                location.assign("doc_compra");
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function ceros_numero_doc(){
    var num = $('[name=numero_doc]').val();
    $('[name=numero_doc]').val(leftZero(6,num));
}