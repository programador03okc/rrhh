function cargarEstOrg(id){
    // limpiar();
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: '/cargar_estructura_org/' + id,
        dataType: 'JSON',
        success: function(response){
            $('#mostrar-arbol').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function showEfectOkc(id){
    $('#detalle-'+id).toggle();
}

function areaSelectModal(sede, grupo, area, text){
    // alert('sede:'+sede+' grupo:'+grupo+' area:'+area);
    $('[name=id_grupo]').val(grupo);
    $('[name=id_area]').val(area);
    $('[name=nombre_area]').val(text);
    $('#modal-empresa-area').modal('hide');

    if (page == 'requerimiento'){
        if (grupo == 3){
            document.getElementById('section-proyectos').setAttribute('class', 'row')
        }
    }
}