function nuevo_doc_venta(){
    $('#form-doc_venta')[0].reset();
    $('[name=usuario]').val(auth_user.id_usuario);
    $('[name=id_tp_doc]').val(2).trigger('change.select2');
    $('#nombre_usuario label').text(auth_user.nombres);
	$('#listaDetalle tbody').html('');
    $('#guias tbody').html('');
}
function mostrar_doc_venta(id_doc_ven){
    if (id_doc_ven !== null){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: 'mostrar_doc_venta/'+id_doc_ven,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                $('[name=id_doc_ven]').val(response['doc'][0].id_doc_ven);
                $('[name=serie]').val(response['doc'][0].serie);
                $('#serie').text(response['doc'][0].serie);
                $('[name=numero]').val(response['doc'][0].numero);
                $('#numero').text(response['doc'][0].numero);
                $('[name=id_tp_doc]').val(response['doc'][0].id_tp_doc).trigger('change.select2');
                $('[name=fecha_emision]').val(response['doc'][0].fecha_emision);
                $('[name=fecha_vcmto]').val(response['doc'][0].fecha_vcmto);
                $('[name=id_condicion]').val(response['doc'][0].id_condicion).trigger('change.select2');
                $('[name=id_empresa]').val(response['doc'][0].id_empresa).trigger('change.select2');
                $('[name=moneda]').val(response['doc'][0].moneda).trigger('change.select2');
                $('[name=cod_estado]').val(response['doc'][0].estado);
                $('#estado label').text(response['doc'][0].estado_doc);
                $('#nombre_usuario label').text(response['doc'][0].nombre_usuario);
                $('#fecha_registro label').text(response['doc'][0].fecha_registro);

                listar_guias_emp(response['doc'][0].id_empresa);
                listar_docven_guias(response['doc'][0].id_doc_ven);
                listar_docven_items(response['doc'][0].id_doc_ven);

                localStorage.removeItem("id_doc_ven");
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });   
    }
}

function listar_docven_guias(id_doc){
    $('#guias tbody').html('');
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'listar_docven_guias/'+id_doc,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('#guias tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function listar_docven_items(id_doc){
    $('#listaDetalle tbody').html('');
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'listar_docven_items/'+id_doc,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('#listaDetalle tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function save_doc_venta(data, action){
    console.log(data);
    if (action == 'register'){
        baseUrl = 'guardar_doc_venta';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_doc_venta';
    }
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response['id_doc'] > 0){
                alert('Documento registrado con éxito');
                // listar_guias_prov(response['id_proveedor']);
                
                $('[name=id_doc_ven]').val(response['id_doc']);
                
                if (action == 'register'){
                    $('[name=cod_estado]').val('1');
                    $('#estado label').text('Elaborado');
                }
                changeStateButton('guardar');
                $('#form-doc_venta').attr('type', 'register');
				changeStateInput('form-doc_venta', true);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function listar_guias_emp(id_empresa){
    console.log('id_empresa'+id_empresa);
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'listar_guias_emp/'+id_empresa,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var option = '';
            for (var i=0;i<response.length;i++){
                option +='<option value="'+response[i].id_guia_ven+'">'+'GR-'+
                    response[i].serie+'-'+response[i].numero+' - '+response[i].razon_social+' - '+
                    response[i].estado_doc+'</option>';
            }
            $('[name=id_guia]').html('<option value="0" disabled selected>Elija una opción</option>'+option);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function agrega_guia(){
    var id_guia = $('[name=id_guia]').val();
    var id_doc_ven = $('[name=id_doc_ven]').val();
    var id_empresa = $('[name=id_empresa]').val();
    console.log('id_guia'+id_guia+' id_doc_ven'+id_doc_ven+' id_empresa'+id_empresa);
    
    if (id_guia !== null){
        var rspta = confirm('¿Esta seguro que desea agregar los items de ésta guía?');
        if (rspta){
            var token = $('#token').val();
            $.ajax({
                type: 'GET',
                headers: {'X-CSRF-TOKEN': token},
                url: 'guardar_docven_items_guia/'+id_guia+'/'+id_doc_ven,
                dataType: 'JSON',
                success: function(response){
                    console.log('response'+response);
                    if (response > 0){
                        alert('Items registrados con éxito');
                        listar_docven_items(id_doc_ven);
                        listar_docven_guias(id_doc_ven);
                        // listar_guias_prov(id_empresa);
                        $('[name=id_guia]').val('0').trigger('change.select2');
                    }
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }
    } else {
        alert('Debe seleccionar una Guía');
    }
}

function anular_doc_venta(ids){
    baseUrl = 'anular_doc_venta/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Comprobante anulado con éxito');
                changeStateButton('anular');
                $('#estado label').text('Anulado');
                $('[name=cod_estado]').val('7');
                // clearForm('form-doc_venta');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function anular_guia(id_guia,id_doc_ven_guia){
    var id_doc = $('[name=id_doc_ven]').val();
    console.log('id_guia'+id_guia+'id_doc'+id_doc);
    var anula = confirm("¿Esta seguro que desea anular ésta OC?\nSe quitará también la relación de sus Items");
    if (anula){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: 'anular_guiaven/'+id_doc+'/'+id_guia,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Guía anulada con éxito');
                    $("#doc-"+id_doc_ven_guia).remove();
                    listar_docven_items(id_doc);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function ceros_numero(){
    var num = $('[name=numero]').val();
    $('[name=numero]').val(leftZero(6,num));
}