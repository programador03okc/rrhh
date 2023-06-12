function nuevo_guia_compra(){
    $('#form-general')[0].reset();
    $('[name=usuario]').val(auth_user.id_usuario);
    $('#listaDetalle tbody').html('');
    $('#oc tbody').html('');
    limpiarCampos();
}
function limpiarCampos(){
    $('[name=id_tp_prorrateo]').val(0);
    $('[name=pro_serie]').val('');
    $('[name=pro_numero]').val('');
    $('[name=doc_fecha_emision]').val(fecha_actual());
    $('[name=tipo_cambio]').val(0);
    $('[name=id_moneda]').val(0);
    $('[name=sub_total]').val(0);
    $('[name=importe]').val(0);
    $('[name=razon_social]').val('');
    $('[name=doc_id_proveedor]').val('');
    $('[name=id_contrib]').val('');
}
$(function(){
    $('#listaProrrateos tbody').html('');
    $("#form-datos-prorrateo").on("submit", function(){
        var data = $(this).serialize();
        console.log(data);
        var id_guia = $('[name=id_guia]').val();
        console.log('submit prorrateo'+id_guia);
        if (id_guia !== ''){
            var p = $('[name=prorrateo]').val();
            console.log('prorrateo: '+p);
            $.ajax({
                type: 'POST',
                url: 'guardar_prorrateo',
                data: data,
                dataType: 'JSON',
                success: function(response){
                    if (response > 0){
                        alert('Prorrateo guardado con éxito');
                        limpiarCampos();
                        console.log('id_guia:'+id_guia);
                        listar_docs_prorrateo(id_guia);
                    }
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }
        return false;
    });

    $("#tab-guia_compra section:first form").attr('form', 'formulario');
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

        var id = $('[name=id_guia]').val();
        
        clearDataTable();
        actualizar_tab(activeForm, id);
        $(activeTab).attr('hidden', false);//inicio botones (estados)
        resizeSide();
    });
    resizeSide();
});
function actualizar_tab(activeForm, id){
    if (id !== ''){
        console.log('id_guia'+id);
        if (activeForm == "form-general"){
            mostrar_guia_com(id);
            listar_detalle(id);
            guia_ocs(id);
        } 
        else if (activeForm == "form-prorrateo"){
            $('[name=id_guia]').val(id);
            listar_docs_prorrateo(id);
            $('[name=prorrateo]').val(1);
            limpiarCampos();
        }
    }
}
function mostrar_guia_com(id){
    console.log('id'+id);
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'mostrar_guia_compra/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response[0]);
            $('[name=id_guia]').val(response[0].id_guia);
            $('[name=id_tp_doc_almacen]').val(response[0].id_tp_doc_almacen).trigger('change.select2');
            $('[name=serie]').val(response[0].serie);
            $('#serie').text(response[0].serie);
            $('[name=numero]').val(response[0].numero);
            $('#numero').text(response[0].numero);
            $('[name=id_proveedor]').val(response[0].id_proveedor).trigger('change.select2');
            $('[name=id_almacen]').val(response[0].id_almacen).trigger('change.select2');
            $('[name=id_motivo]').val(response[0].id_motivo).trigger('change.select2');
            $('[name=id_guia_clas]').val(response[0].id_guia_clas);
            $('[name=id_guia_cond]').val(response[0].id_guia_cond).trigger('change.select2');
            $('[name=fecha_emision]').val(response[0].fecha_emision);
            $('[name=fecha_almacen]').val(response[0].fecha_almacen);
            $('[name=usuario]').val(response[0].usuario).trigger('change.select2');
            $('[name=cod_estado]').val(response[0].estado);
            $('[name=transportista]').val(response[0].transportista).trigger('change.select2');
            $('[name=tra_serie]').val(response[0].serie);
            $('[name=tra_numero]').val(response[0].numero);
            $('[name=fecha_traslado]').val(response[0].fecha_traslado);
            $('[name=punto_partida]').val(response[0].punto_partida);
            $('[name=punto_llegada]').val(response[0].punto_llegada);
            $('[name=placa]').val(response[0].placa);
            $('[id=fecha_registro] label').text('');
            $('[id=fecha_registro] label').append(formatDateHour(response[0].fecha_registro));
            $('[id=estado] label').text('');
            $('[id=estado] label').append(response[0].des_estado);
            
            if (response[0].id_proveedor !== null){
                listar_ordenes(response[0].id_proveedor);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function save_guia_compra(data, action){
    if (action == 'register'){
        baseUrl = 'guardar_guia_compra';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_guia_compra';
    }
    console.log(data);
    var tp_doc = $('[name=id_tp_doc_almacen]').val();
    var serie = $('[name=serie]').val();
    var num = $('[name=numero]').val();
    // var prov = $('[name=id_proveedor]').val();
    var alm = $('[name=id_almacen]').val();
    var mot = $('[name=id_motivo]').val();
    var clas = $('[name=id_guia_clas]').val();
    var ope = $('[name=id_operacion]').val();

    if (tp_doc !== '0'){
        if (serie !== ''){
            if (num !== ''){
                // if (prov !== '0'){
                    if (alm !== '0'){
                        if (mot !== '0'){
                            if (clas !== '0'){
                                if (ope !== '0'){
                                    $.ajax({
                                        type: 'POST',
                                        headers: {'X-CSRF-TOKEN': token},
                                        url: baseUrl,
                                        data: data,
                                        dataType: 'JSON',
                                        success: function(response){
                                            console.log(response);
                                            if (response['id_guia'] > 0){
                                                alert('Guía de Remisión registrada con éxito');
                                                changeStateButton('guardar');
                                                $('#form-guia_compra').attr('type', 'register');
                                                changeStateInput('form-guia_compra', true);
                                                
                                                // $('[name=tipo]').val('1').trigger('change.select2');
                                                listar_ordenes(response['id_proveedor']);
                                                mostrar_guia_com(response['id_guia']);
                                                $('.boton').removeClass('desactiva');
                                                
                                            }
                                        }
                                    }).fail( function( jqXHR, textStatus, errorThrown ){
                                        console.log(jqXHR);
                                        console.log(textStatus);
                                        console.log(errorThrown);
                                    });
                                } else {
                                    alert('Es necesario que seleccione un tipo de operación');
                                }
                            } else {
                                alert('Es necesario que seleccione un tipo de clasificación');
                            }
                        } else {
                            alert('Es necesario que seleccione un motivo');
                        }
                    } else {
                        alert('Es necesario que seleccione un almacén');
                    }
                // } else {
                //     alert('Es necesario que seleccione un proveedor');
                // }
            } else {
                alert('Es necesario que ingrese un número');
            }
        } else {
            alert('Es necesario que ingrese una serie');
        }
    } else {
        alert('Es necesario que seleccione un tipo de documento');
    }
}

function anular_guia_compra(ids){
    baseUrl = 'anular_guia_compra/'+ids;
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
                // clearForm('form-guia_compra');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function generar_ingreso(){
    var id_guia = $('[name=id_guia]').val();
    var id_usuario = auth_user.id_usuario;
    
    if (id_guia !== ''){
        var estado = $('[name=cod_estado]').val();
        if (estado == '1'){
            var nro_reg = $('#listaDetalle tbody tr').length;
            if (nro_reg > 0){
                var rspta = verificaItems();
                console.log(rspta.length);
                if (rspta.length > 0){
                    alert(rspta);
                } else {
                    var ingreso = confirm("¿Esta seguro que desea generar el Ingreso a Almacén?\nEste procedimiento moverá los stocks en Almacén y ya no podrá modificar la Guía");
                    if (ingreso){
                        $.ajax({
                            type: 'GET',
                            headers: {'X-CSRF-TOKEN': token},
                            url: 'generar_ingreso/'+id_guia+'/'+id_usuario,
                            dataType: 'JSON',
                            success: function(id_ingreso){
                                console.log('id_ingreso'+id_ingreso);
                                if (id_ingreso > 0){
                                    alert('Ingreso Almacén generado con éxito');
                                    changeStateButton('guardar');
                                    mostrar_guia_com(id_guia);
                                    var id = encode5t(id_ingreso);
                                    window.open('imprimir_ingreso/'+id);
                                }
                            }
                        }).fail( function( jqXHR, textStatus, errorThrown ){
                            console.log(jqXHR);
                            console.log(textStatus);
                            console.log(errorThrown);
                        });
                    }
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
function abrir_ingreso(){
    var id_guia = $('[name=id_guia]').val();
    console.log(id_guia);
    if (id_guia != ''){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: 'id_ingreso/'+id_guia,
            dataType: 'JSON',
            success: function(id_ingreso){
                if (id_ingreso > 0){
                    console.log(id_ingreso);
                    var id = encode5t(id_ingreso);
                    window.open('imprimir_ingreso/'+id);
                } else {
                    alert('Esta guía no tiene Ingreso');
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
function generar_factura(){
    var id_guia = $('[name=id_guia]').val();
    console.log(id_guia);
    
    if (id_guia !== ''){
        $('#modal-doc_guia').modal({
            show: true
        });
        doc_guia();
    } else {
        alert("Debe seleccionar una Guía de Remision!");
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
    else if(numero == 'pro_numero'){
        var num = $('[name=pro_numero]').val();
        $('[name=pro_numero]').val(leftZero(6,num));
    }
}
function agregar_adicional(){
    var id_guia = $('[name=id_guia]').val();
    
    if (id_guia !== ''){
        $('#modal-doc_create').modal({
            show: true
        });
        open_doc_create();
    } else {
        alert("Debe seleccionar una Guía de Remision!");
    }
}
function direccion(){
    var almacen = $('[name=id_almacen]').val();
    console.log('almacen'+almacen);
    if (almacen !== null){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: 'direccion_almacen/'+almacen,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                $('[name=punto_llegada]').val(response);
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
    var tp = $('[name=id_tp_doc_almacen]').val();
    if (tp == 6){
        $('[name=id_proveedor]').val('0');
        $('[name=id_proveedor]').attr('disabled',true);
    } else {
        $('[name=id_proveedor]').attr('disabled',false);
    }
}
function listar_docs_prorrateo(id_guia){
    $.ajax({
        type: 'GET',
        // headers: {'X-CSRF-TOKEN': token},
        url: 'listar_docs_prorrateo/'+id_guia,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('#listaProrrateos tbody').html(response['html']);
            $('[name=total_comp]').val(response['total']);

            if (response['moneda'] !== null){
                console.log(response['moneda']);
                console.log(response['moneda'].descripcion+' '+response['moneda'].simbolo);
                $('#moneda').text(response['moneda'].descripcion+' '+response['moneda'].simbolo);
            }

            listar_detalle_prorrateo(id_guia, response['total']);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function listar_detalle_prorrateo(guia, total_comp){
    $('#listaDetalleProrrateo tbody').html('');
    // var total_comp = $('[name=total_comp]').val();
    console.log('id_guia'+guia);
    console.log('total_comp'+total_comp);
    console.log();
    var baseUrl = 'listar_guia_detalle_prorrateo/'+guia+'/'+total_comp;
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response['sumas']);
            $('#listaDetalleProrrateo tbody').html(response['html']);
            $('[name=total_suma]').val(response['sumas'][0].suma_total);
            $('[name=total_adicional]').val(response['sumas'][0].suma_adicional);
            $('[name=total_costo]').val(response['sumas'][0].suma_costo);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function editar_adicional(id){
    $("#det-"+id+" td").find("input[name=subtotal]").attr('disabled',false);
    $("#det-"+id+" td").find("input[name=tipocambio]").attr('disabled',false);
    // $("#det-"+id+" td").find("input[name=importe]").attr('disabled',false);
    $("#det-"+id+" td").find("i.blue").removeClass('visible');
    $("#det-"+id+" td").find("i.blue").addClass('oculto');
    $("#det-"+id+" td").find("i.green").removeClass('oculto');
    $("#det-"+id+" td").find("i.green").addClass('visible');
}
function calcula_importe(id){
    var subtotal = $('#det-'+id+' input[name=subtotal]').val();
    var tpcambio = $('#det-'+id+' input[name=tipocambio]').val();
    if (subtotal !== '' && tpcambio !== ''){
        $('#det-'+id+' input[name=importedet]').val(formatDecimal(subtotal * tpcambio));
    } else {
        $('#det-'+id+' input[name=importedet]').val(0);
    }
}
function anular_adicional(id,id_doc){
    var anula = confirm("¿Esta seguro que desea anular éste adicional?");
    if (anula){
        $.ajax({
            type: 'GET',
            headers: {'X-CSRF-TOKEN': token},
            url: 'eliminar_doc_prorrateo/'+id+'/'+id_doc,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Adicional anulado con éxito');
                    // $("#det-"+id).remove();
                    var id = $('[name=id_guia]').val();
                    console.log('id:'+id);
                    listar_docs_prorrateo(id);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function update_adicional(id,id_doc){
    var subtotal = $("#det-"+id+" td").find("input[name=subtotal]").val();
    var tipocambio = $("#det-"+id+" td").find("input[name=tipocambio]").val();
    var importe = $("#det-"+id+" td").find("input[name=importedet]").val();
    var data =  'id_prorrateo='+id+
                '&id_doc='+id_doc+
                '&sub_total='+subtotal+
                '&tipo_cambio='+tipocambio+
                '&importe='+importe;
    console.log(data);

    $.ajax({
        type: 'POST',
        // headers: {'X-CSRF-TOKEN': token},
        url: 'update_doc_prorrateo',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Adicional actualizado con éxito');
                $("#det-"+id+" td").find("input[name=subtotal]").attr('disabled',true);
                $("#det-"+id+" td").find("input[name=tipocambio]").attr('disabled',true);
                // $("#det-"+id+" td").find("input[name=importe]").attr('disabled',false);
                $("#det-"+id+" td").find("i.blue").removeClass('oculto');
                $("#det-"+id+" td").find("i.blue").addClass('visible');
                $("#det-"+id+" td").find("i.green").removeClass('visible');
                $("#det-"+id+" td").find("i.green").addClass('oculto');            
                                
                var id = $('[name=id_guia]').val();
                console.log('despues id_guia:'+id);
                listar_docs_prorrateo(id);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function calculaImporte(){
    var moneda = $('[name=id_moneda]').val();
    var sub_total = $('[name=sub_total]').val();
    if (moneda == 2){
        var tcambio = $('[name=tipo_cambio]').val();
        var imp = formatDecimal(sub_total * tcambio);
        $('[name=importe]').val(imp);
    } else {
        $('[name=importe]').val(sub_total);
    }
}
function getTipoCambio(){
    var fecha = $('[name=doc_fecha_emision]').val();
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'getTipoCambio/'+fecha,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            console.log(response[0]['compra']);
            $('[name=tipo_cambio]').val(response[0]['compra']);                
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function copiar_unitario(){
    $('[name=prorrateo]').prop('checked',true);
    var p = $('[name=prorrateo]').val();
    console.log(p);
    var id_guia = $('[name=id_guia]').val();
    var id = [];
    var uni = [];
    var r = 0;
    
    $('#listaDetalleProrrateo tbody tr').each(function(e){
        var pro = $(this)[0].id.split("-");
        var tds = parseFloat($(this).find("td input[name=unit]").val());
        console.log('unitario:'+tds);
        console.log('id_guia_det:'+pro[1]);
        id[r] = pro[1];
        uni[r] = tds;
        r++;
    });
    var data =  'id_guia='+id_guia+
                '&id_guia_det='+id+
                '&unitario='+uni;
    console.log(data);
    $.ajax({
        type: 'POST',
        url: 'update_guia_detalle',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Item guardado con éxito');
                listar_detalle(id_guia);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function verificaItems(){
    var pos = 0;
    var series = 0;
    var msj = '';
    $('#listaDetalle tbody tr').each(function(e){
        // var pro = $(this)[0].id.split("-");
        var posicion = $(this).find("td select[name=id_posicion]").val();
        if (posicion == "0"){
            pos++;
        }
        var tds = $(this).find("td input[name=series]").val();
        if (tds == 'true'){
            var des = $(this).find("td")[2].innerHTML;
            console.log(des);
            console.log(des.indexOf('Series:'));
            if (des.indexOf('Series:') == -1){
                console.log("la letra Series: no encontrada");
                series++;
            }
        }
    });
    msj = 'No puede realizar ésta acción:'+(pos > 0 ? 
        ('\nFalta asignar una ubicación a '+pos+' productos') : '')+ 
        (series > 0 ? ('\nFalta agregar series a '+series+' productos') : '');
    return msj;
}