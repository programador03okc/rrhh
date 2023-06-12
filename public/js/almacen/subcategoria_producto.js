function mostrar_subcategoria(id){
    baseUrl = 'mostrar_subcategoria/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response[0]);
            $('[name=id_subcategoria]').val(response[0].id_subcategoria);
            $('[name=codigo]').val(response[0].codigo);
            $('[name=id_categoria]').val(response[0].id_categoria);
            $('[name=tipo_descripcion]').val(response[0].tipo_descripcion);
            $('[name=cat_descripcion]').val(response[0].cat_descripcion);
            $('[name=descripcion]').val(response[0].descripcion);
            $('[name=estado]').val(response[0].estado);
            $('[id=fecha_registro] label').text('');
            $('[id=fecha_registro] label').append(formatDateHour(response[0].fecha_registro));
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_subcategoria(data, action){
    if (action == 'register'){
        baseUrl = 'guardar_subcategoria';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_subcategoria';
    }
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('SubCategoria registrado con exito');
                $('#listaSubCategoria').DataTable().ajax.reload();
                changeStateButton('guardar');
                $('#form-subcategoria').attr('type', 'register');
                changeStateInput('form-subcategoria', true);

            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_subcategoria(ids){
    baseUrl = 'anular_subcategoria/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'revisarSubCat/'+ids,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response >= 1){
                alert('No es posible anular. \nLa subcategoria seleccionada está relacionada con '
                +response+' producto(s).');
            }
            else {
                $.ajax({
                    type: 'GET',
                    headers: {'X-CSRF-TOKEN': token},
                    url: baseUrl,
                    dataType: 'JSON',
                    success: function(response){
                        console.log(response);
                        if (response > 0){
                            alert('SubCategoria anulada con exito');
                            $('#listaSubCategoria').DataTable().ajax.reload();
                            changeStateButton('anular');
                            clearForm('form-subcategoria');
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

