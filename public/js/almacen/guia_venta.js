function nuevo_guia_venta(){
    console.log('auth_user');
    console.log(auth_user);
    console.log(auth_user.nombres);
    $('#form-general')[0].reset();
    $('[name=usuario]').val(auth_user.id_usuario);
    $('#nombre_usuario label').text(auth_user.nombres);
    $('#listaDetalle tbody').html('');
    $('#oc tbody').html('');
}
$(function(){
    $("#tab-guia_venta section:first form").attr('form', 'formulario');

    /* Efecto para los tabs */
    $('ul.nav-tabs li a').click(function(){
        $('ul.nav-tabs li').removeClass('active');
        $(this).parent().addClass('active');
        $('.content-tabs section').attr('hidden', true);
        $('.content-tabs section form').removeAttr('type');
        $('.content-tabs section form').removeAttr('form');

        var activeTab = $(this).attr('type');
        var activeForm = "form-"+activeTab.substring(1);

        $("#"+activeForm).attr('type', 'register');
        $("#"+activeForm).attr('form', 'formulario');
        changeStateInput(activeForm, true);

        $("[name=usuario]").val(3);
        $('[name=nombre_usuario]').val('Rocio Condori Palomino');
        var id = $('[name=id_guia_ven]').val();
        if (activeForm == "form-detalle" || activeForm == "form-transportista"){
            clearDataTable();
        }
        actualizar_tab(activeForm, id);
        $(activeTab).attr('hidden', false);//inicio botones (estados)
    });

});
function actualizar_tab(activeForm, id){
    if (id !== null){
        if (activeForm == "form-general"){
            mostrar_guia_ven(id);
        } 
        else if (activeForm == "form-transportista"){
            // listar_transportista(id);
            $('[name=id_guia_ven]').val(id);
        }
    }
}
function mostrar_guia_ven(id){
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'mostrar_guia_venta/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('[name=id_guia_ven]').val(response[0].id_guia_ven);
            $('[name=id_tp_doc_almacen]').val(response[0].id_tp_doc_almacen).trigger('change.select2');
            $('[name=serie]').val(response[0].serie);
            $('#serie').text(response[0].serie);
            $('[name=numero]').val(response[0].numero);
            $('#numero').text(response[0].numero);
            $('[name=id_empresa]').val(response[0].id_empresa).trigger('change.select2');
            $('[name=id_almacen]').val(response[0].id_almacen).trigger('change.select2');
            $('[name=id_motivo]').val(response[0].id_motivo).trigger('change.select2');
            $('[name=fecha_emision]').val(response[0].fecha_emision);
            $('[name=fecha_almacen]').val(response[0].fecha_almacen);
            $('[name=fecha_traslado]').val(response[0].fecha_traslado);
            $('[name=id_cliente]').val(response[0].id_cliente);
            $('[name=cliente_razon_social]').val(response[0].cliente_razon_social);
            $('[name=tra_serie]').val(response[0].tra_serie);
            $('[name=tra_numero]').val(response[0].tra_numero);
            $('[name=punto_partida]').val(response[0].punto_partida);
            $('[name=punto_llegada]').val(response[0].punto_llegada);
            $('[name=transportista]').val(response[0].transportista).trigger('change.select2');
            $('[name=placa]').val(response[0].placa);
            $('[name=cod_estado]').val(response[0].estado);
            $('[name=usuario]').val(response[0].usuario);
            $('#nombre_usuario label').text(response[0].nombre_trabajador);
            $('#fecha_registro label').text('');
            $('#fecha_registro label').append(formatDateHour(response[0].fecha_registro));
            $('#estado label').text('');
            $('#estado label').append(response[0].estado_doc);
            
            // var tipo = $('[name=tipo]').val();
            // console.log(tipo+'tipo');
            // if (tipo == 1){//Guia de Compra
                if (response[0].id_almacen !== null){
                    listar_guias_compra(response[0].id_almacen);
                }
            // }
            listar_detalle(response[0].id_guia_ven);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function cambiar_tipo(){
    var tp = $('[name=tipo]').val();
    if (tp !== null){
        listar_guias_compra(tp);
    }
}
function save_guia_venta(data, action){
    if (action == 'register'){
        baseUrl = 'guardar_guia_venta';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_guia_venta';
    }
    console.log(data);
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response['id_guia_ven'] > 0){
                alert('Guía de Remisión registrada con éxito');
                changeStateButton('guardar');
                $('#form-guia_venta').attr('type', 'register');
                changeStateInput('form-guia_venta', true);
                
                $('[name=tipo]').val('1').trigger('change.select2');
                var id_almacen = $('[name=id_almacen]').val();
                listar_guias_compra(id_almacen);
                // $('[name=id_guia_ven]').val(response['id_guia_ven']);
                if (action == 'register'){
                    mostrar_guia_ven(response['id_guia_ven']);
                }
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function anular_guia_venta(ids){
    baseUrl = 'anular_guia_venta/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Guía de Remisión anulada con éxito');
                changeStateButton('anular');
                $('#estado label').text('Anulado');
                $('[name=cod_estado]').val('2');
                // clearForm('form-guia_venta');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function generar_salida(){
    var id_guia = $('[name=id_guia_ven]').val();
    var id_usuario = auth_user.id_usuario;
    
    if (id_guia !== ''){
        var estado = $('[name=cod_estado]').val();
        if (estado == '1'){
            var nro_reg = $('#listaDetalle tbody tr').length;
            if (nro_reg > 0){
                var salida = confirm("¿Esta seguro que desea generar el salida a Almacén?\nEste procedimiento moverá los stocks en Almacén y ya no podrá modificar la Guía");
                if (salida){
                    $.ajax({
                        type: 'GET',
                        headers: {'X-CSRF-TOKEN': token},
                        url: 'generar_salida_guia/'+id_guia+'/'+id_usuario,
                        dataType: 'JSON',
                        success: function(id_salida){
                            console.log('id_salida'+id_salida);
                            if (id_salida > 0){
                                alert('Salida Almacén generada con éxito');
                                changeStateButton('guardar');
                                mostrar_guia_ven(id_guia);
                                var id = encode5t(id_salida);
                                window.open('imprimir_salida/'+id);
                            } else {
                                alert(id_salida);
                            }
                        }
                    }).fail( function( jqXHR, textStatus, errorThrown ){
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    });
                }
            } else {
                alert('No se puede procesar una Guía sin Items');
            }
        } else {
            alert('La guia ya fue Procesada!');
        }
    } else {
        alert("Debe seleccionar una Guía de Remision!");
    }
}
function abrir_salida(){
    var id_guia = $('[name=id_guia_ven]').val();
    if (id_guia != ''){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: 'id_salida/'+id_guia,
            dataType: 'JSON',
            success: function(id_salida){
                if (id_salida > 0){
                    var id = encode5t(id_salida);
                    window.open('imprimir_salida/'+id);
                } else {
                    alert('Esta guía no tiene salida');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    } else {
        alert('Debe seleccionar una Guía!');
    }
}
function ceros_numero(numero){
    if (numero == 'numero'){
        var num = $('[name=numero]').val();
        $('[name=numero]').val(leftZero(6,num));
    } 
    else if(numero == 'tra_numero'){
        var num = $('[name=tra_numero]').val();
        $('[name=tra_numero]').val(leftZero(6,num));
    }
}
function direccion(){
    var almacen = $('[name=id_almacen]').val();
    console.log('almacen'+almacen);
    if (almacen !== null){
        // var token = $('#token').val();
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: 'direccion_almacen/'+almacen,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                $('[name=punto_partida]').val(response);
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function actualiza_titulo(){
    var tp_doc = $('select[name="id_tp_doc_almacen"] option:selected').text();
    $('#titulo').text(tp_doc);
}