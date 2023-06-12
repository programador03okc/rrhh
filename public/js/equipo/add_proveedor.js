$(function(){
    $("#form-proveedor").on("submit", function(e){
        e.preventDefault();
        guardar_proveedor();
    });
});
function agregar_proveedor(){
    $('#modal-proveedor').modal({
        show: true
    });
}
function guardar_proveedor(){
    var formData = new FormData($('#form-proveedor')[0]);
    console.log(formData);
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: 'guardar_proveedor',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            alert('Proveedor registrado con éxito');
            $('#modal-proveedor').modal('hide');
            $('[name=id_proveedor]').html('');
            var html = '<option value="0" disabled>Elija una opción</option>'+response;
            $('[name=id_proveedor]').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });        
}