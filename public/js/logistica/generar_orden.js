function nueva_orden(){
    $('[name=razon_social]').val('');
    $('#listaDetalleOrden tbody').html('');
    $('[name=monto_subtotal]').val('0');
    $('[name=igv_porcentaje]').val('0');
    $('[name=monto_igv]').val('0');
    $('[name=monto_total]').val('0');
}
function detalle_cotizacion(id_cotizacion){
    $.ajax({
        type: 'GET',
        url: '/detalle_cotizacion/'+id_cotizacion,
        dataType: 'JSON',
        success: function(response){
            $('#listaDetalleOrden tbody').html(response['html']);
            $('[name=monto_subtotal]').val(formatDecimal(response['sub_total']));
            $('[name=igv_porcentaje]').val(formatDecimal(response['igv']));
            actualiza_totales();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function save_orden_compra(data, action){
        // console.log(data);

    var id_tipo_doc = 2;// por defecto tipo doc = orden de compra
    var nombre_doc = '';

    if (action == 'register'){
        baseUrl = '/guardar_orden_compra';
        let id_prod =$('[name=id_producto]').val();
        let id_serv =$('[name=id_servicio]').val();
        let id_equi =$('[name=id_equipo]').val();

        if(id_prod > 0 ){ // evaluar tipo de documento sera la orden  ( compra , servicio)
            id_tipo_doc = 2 // ORDEN DE COMPRA
            nombre_doc = 'Orden de Compra';
            
        }else if(id_serv > 0){
            id_tipo_doc = 3 // ORDEN DE SERVICIO
            nombre_doc = 'Orden de Servicio';
            
        }else if(id_equi > 0){
            id_tipo_doc = 2 //ORDEN DE COMPRA 
            nombre_doc = 'Orden de Compra';
        }
 
    } else if (action == 'edition'){
        baseUrl = '/update_orden_compra';
    }
    var id_val = [];
    var id_item = [];
    var i = 0;
    $('#listaDetalleOrden tbody tr').each(function(e){
        id_val[i] = $(this).find("td input[name=id_valorizacion_cotizacion]").val();
        id_item[i] = $(this).find("td input[name=id_item]").val();
        i++;
    });
    // console.log('id_val'+id_val+' id_item'+id_item);
    $.ajax({
        type: 'POST',
        url: baseUrl,
        data: data+'&id_val='+id_val+'&id_item='+id_item+'&id_tp_documento='+id_tipo_doc,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            if (response > 0){
                alert('Orden de '+nombre_doc+' registrada con éxito');
                changeStateButton('guardar');
                // mostrar_orden(response);
                $('[name=id_orden_compra]').val(response);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function mostrar_cuentas_bco(){
    var id_contri = $('[name=id_contrib]').val();
    // console.log('id_contri'+id_contri);
    $.ajax({
        type: 'GET',
        url: '/mostrar_cuentas_bco/'+id_contri,
        dataType: 'JSON',
        success: function(response){
            // console.log('response mostrar_cuentas_bco');
            // console.log(response);
            // console.log(response.length);
            var option = '';
            var detra = '';
            for (var i=0;i<response.length;i++){
                if (response[i].id_tipo_cuenta !== 2){
                    option+='<option value="'+response[i].id_cuenta_contribuyente+'">'+response[i].nro_cuenta+' - '+response[i].banco+'</option>';
                } else {
                    detra+='<option value="'+response[i].id_cuenta_contribuyente+'">'+response[i].nro_cuenta+' - '+response[i].banco+'</option>';
                }
            }
            $('[name=id_cta_principal]').html('<option value="0" disabled selected>Elija una opción</option>'+option);
            $('[name=id_cta_alternativa]').html('<option value="0" disabled selected>Elija una opción</option>'+option);
            $('[name=id_cta_detraccion]').html('<option value="0" disabled selected>Elija una opción</option>'+detra);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function imprimir_orden(){
    var id_orden = $('[name=id_orden_compra]').val();
    // console.log(id_orden);
    if (id_orden != ''){
        var id = encode5t(id_orden);
        window.open('/generar_orden_pdf/'+id);
    } else {
        alert('Debe seleccionar una Orden de Compra!');
    }
}
function mostrar_orden(id_orden){
    $.ajax({
        type: 'GET',
        url: '/mostrar_orden/'+id_orden,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            // console.log('contri'+re  sponse['orden']['id_contribuyente']);
            $('[name=id_orden_compra]').val(response['orden']['id_orden_compra']);
            $('[name=id_proveedor]').val(response['orden']['id_proveedor']);
            $('[name=id_contrib]').val(response['orden']['id_contribuyente']);
            $('#codigo').text(response['orden']['codigo']);
            $('[name=razon_social]').val(response['orden']['razon_social']);
            $('[name=id_condicion]').val(response['orden']['id_condicion']);
            $('[name=plazo_dias]').val(response['orden']['plazo_dias']);
            $('[name=id_tp_documento]').val(response['orden']['id_tp_documento']).trigger('change.select2');
            $('[name=id_grupo_cotizacion]').val(response['orden']['id_grupo_cotizacion']);
            $('[name=id_cotizacion]').val(response['orden']['id_cotizacion']);
            $('[name=monto_subtotal]').val(response['orden']['monto_subtotal']);
            $('[name=igv_porcentaje]').val(response['orden']['igv_porcentaje']);
            $('[name=monto_igv]').val(response['orden']['monto_igv']);
            $('[name=monto_total]').val(response['orden']['monto_total']);
            $('[name=id_cta_principal]').html('<option value="0" disabled selected>Elija una opción</option>'+response['html']);
            $('[name=id_cta_alternativa]').html('<option value="0" disabled selected>Elija una opción</option>'+response['html']);
            $('[name=id_cta_detraccion]').html('<option value="0" disabled selected>Elija una opción</option>'+response['detra']);
            $('[name=id_cta_principal]').val(response['orden']['id_cta_principal']);
            $('[name=id_cta_alternativa]').val(response['orden']['id_cta_alternativa']);
            $('[name=id_cta_detraccion]').val(response['orden']['id_cta_detraccion']);
            $('#estado label').text(response['orden']['estado_doc']);
            $('[name=cod_estado]').val(response['orden']['estado']);
            $('[name=responsable]').val(response['orden']['personal_responsable']).trigger('change.select2');
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function listar_detalle_orden(id_orden){
    $.ajax({
        type: 'GET',
        url: '/listar_detalle_orden/'+id_orden,
        dataType: 'JSON',
        success: function(response){
            $('#listaDetalleOrden tbody').html(response);
            // $('[name=monto_subtotal]').val(formatDecimal(response['sub_total']));
            // actualiza_totales();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function anular_orden_compra(ids){
    baseUrl = '/anular_orden_compra/'+ids;
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Orden de Compra anulada con éxito');
                changeStateButton('anular');
                $('#estado label').text('Anulado');
                $('[name=cod_estado]').val('2');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function actualiza_totales(){
    var sub_total = parseFloat($('[name=monto_subtotal]').val());
    // var dscto = $('[name=total_descuento]').val();

    // var total = parseFloat(sub_total) - parseFloat(dscto);
    // $('[name=total]').val(formatDecimal(total));
    var pigv = parseFloat($('[name=igv_porcentaje]').val());
    // console.log('pigv'+pigv+' total'+total);

    var igv = sub_total * parseFloat(pigv) / 100;
    $('[name=monto_igv]').val(formatDecimal(igv));
    var total_a_pagar = sub_total + igv;
    // console.log('total_a_pagar'+total_a_pagar);
    $('[name=monto_total]').val(formatDecimal(total_a_pagar));
}
