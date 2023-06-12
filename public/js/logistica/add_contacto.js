$(function(){
    $("#form-contacto").on("submit", function(e){
        e.preventDefault();
        guardar_contacto();
    });
});
function agregar_contacto(){
    $('#modal-contacto').modal({
        show: true
    });
    var id_contribuyente = $('[name=id_contrib]').val();
    $('[name=id_contribuyente]').val(id_contribuyente);
}
function guardar_contacto(){
    var formData = new FormData($('#form-contacto')[0]);
    // console.log(formData);
    $.ajax({
        type: 'POST',
        url: '/guardar_contacto',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            alert('Contacto registrado con éxito');
            $('#modal-contacto').modal('hide');
            $('[name=id_contacto]').html('');
            var html = '<option value="0" disabled>Elija una opción</option>'+response;
            $('[name=id_contacto]').html(html);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });        
}