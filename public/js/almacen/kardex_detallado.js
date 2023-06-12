$(function(){
    $('[name=fecha_inicio]').val(fecha_actual());
    $('[name=fecha_fin]').val(fecha_actual());
});
function generar_kardex(){
    var id_producto = $('[name=id_producto]').val();
    var finicio = $('[name=fecha_inicio]').val();
    var ffin = $('[name=fecha_fin]').val();

    if (id_producto == ''){
        alert('Debe seleccionar un producto..!');
    } else if (finicio > ffin){
        alert('La fecha inicio no puede ser mayor a la fecha fin');
    } else {
        baseUrl = 'kardex_producto/'+id_producto+'/'+finicio+'/'+ffin;
        $.ajax({
            type: 'GET',
            // headers: {'X-CSRF-TOKEN': token},
            url: baseUrl,
            dataType: 'JSON',
            success: function(response){
                $('#resultado').html(response);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function download_kardex_excel(){
    var prod = $('[name=id_producto]').val();
    var fini = $('[name=fecha_inicio]').val();
    var ffin = $('[name=fecha_fin]').val();
    window.open('kardex_detallado/'+prod+'/'+fini+'/'+ffin);
}