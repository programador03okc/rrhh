$(function(){
    var idReqCot = localStorage.getItem('idReqCot');
    if (idReqCot != null){       
        mostrar_detalle_requerimiento(idReqCot);
        localStorage.removeItem('idReqCot');
    }
});
function mostrar_detalle_requerimiento(id){   
    $.ajax({
        type: 'GET',
        url: '/detalle_requerimiento/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            
            // var verifica = false;
            // $('#listaItemsRequerimiento tbody tr').each(function(e){
            //     var id_requerimiento = $(this).find("td input[name=id_requerimiento]").val();
            //     if (id_requerimiento == id){
            //         verifica = true;
            //     }
            // });
            // if (!verifica){
                $('#listaItemsRequerimiento tbody').append(response);
            // } else {
            //     alert('El requerimiento seleccionado ya fue agregado!');
            // }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function generar_cotizacion(){
    var items = [];
    var r = 0;
    $("input[type=checkbox]:checked").each(function(){
        var id_detalle = $(this).closest('td').find("input[name=id_detalle]").val();
        items[r] = id_detalle;
        ++r;
    });
    var id_grupo_cotizacion = $('[name=id_grupo_cotizacion]').val();
    
    if (items.length > 0){
        $.ajax({
            type: 'GET',
            url: '/guardar_cotizacion/'+items+'/'+id_grupo_cotizacion,
            dataType: 'JSON',
            success: function(response){
                if (response['id_cotizacion'] > 0){
                    alert('Cotización registrada con éxito');
                    var id_grupo = response['id_grupo'];
                    $('[name=id_grupo_cotizacion]').val(id_grupo);
                    listar_cotizaciones(id_grupo);
                    listar_items_cotizaciones(id_grupo);
                    changeStateButton('guardar');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function listar_cotizaciones(id_grupo){
    $.ajax({
        type: 'GET',
        url: '/cotizaciones_por_grupo/'+id_grupo,
        dataType: 'JSON',
        success: function(response){           
            $('#listaCotizaciones tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function listar_items_cotizaciones(id_grupo){
    $.ajax({
        type: 'GET',
        url: '/items_cotizaciones_por_grupo/'+id_grupo,
        dataType: 'JSON',
        success: function(response){
            $('#listaItemsRequerimiento tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function mostrar_grupo_cotizacion(id_grupo){
    $.ajax({
        type: 'GET',
        url: '/mostrar_grupo_cotizacion/'+id_grupo,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_grupo_cotizacion]').val(response.id_grupo_cotizacion);
            $('[name=codigo_grupo]').val(response.codigo_grupo);
            $('[name=fecha_inicio]').val(response.fecha_inicio);
            $('[name=fecha_fin]').val(response.fecha_fin);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function open_cotizacion(id_cotizacion, codigo_cotizacion){
    $('#modal-cotizacion_proveedor').modal({
        show: true
    });
    mostrar_cotizacion(id_cotizacion);
}
function mostrar_cotizacion(id_cotizacion){
    // console.log('id_cotizacion'+id_cotizacion);
    $.ajax({
        type: 'GET',
        url: '/mostrar_cotizacion/'+id_cotizacion,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            $('[name=id_cotizacion]').val(response['cotizacion'].id_cotizacion);
            $('#codigo_cotizacion').val(response['cotizacion'].codigo_cotizacion);
            $('[name=razon_social]').val(response['cotizacion'].razon_social);
            $('[name=id_proveedor]').val(response['cotizacion'].id_proveedor);
            $('[name=id_empresa]').val(response['cotizacion'].id_empresa);
            
            var option = '';
            for (var i=0;i<response['contacto'].length;i++){
                option+='<option value="'+response['contacto'][i].id_datos_contacto+'">'+response['contacto'][i].nombre+' - '+response['contacto'][i].cargo+' - '+response['contacto'][i].email+'</option>';
            }
            $('[name=id_contacto]').html('<option value="0" disabled selected>Elija una opción</option>'+option);
            $('[name=id_contacto]').val(response['cotizacion'].id_contacto);
            cargar_imagen();
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function change_proveedor(id_prov){
    $.ajax({
        type: 'GET',
        url: '/mostrar_email_proveedor/'+id_prov,
        dataType: 'JSON',
        success: function(response){
            // console.log('response change_proveedor');
            // console.log(response);
            var option = '';
            for (var i=0;i<response.length;i++){
                option+='<option value="'+response[i].id_datos_contacto+'">'+response[i].nombre+' - '+response[i].cargo+' - '+response[i].email+'</option>';
            }
            $('[name=id_contacto]').html('<option value="0" disabled selected>Elija una opción</option>'+option);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

 

function action_cotizacion(option){
    let urlBase ='';
    switch (option) {
        case 'UPDATE':
                urlBase= '/update_cotizacion';
            break;

        case 'DUPLICATE':
                urlBase= '/duplicate_cotizacion';
            break;
    
        default:
            console.log("no hay acción disponible");
            
            break;
    }
    var id_cotizacion = $('[name=id_cotizacion]').val();
    var id_proveedor = $('[name=id_proveedor]').val();
    var id_empresa = $('[name=id_empresa]').val();
    var id_contacto = $('[name=id_contacto]').val();
    var contacto = $('select[name="id_contacto"] option:selected').text();
    var cont_array = contacto.split(" - ");
    // console.log(cont_array[2]);

    var data = 'id_proveedor='+id_proveedor+
            '&id_cotizacion='+id_cotizacion+
            '&id_empresa='+id_empresa+
            '&id_contacto='+id_contacto+
            '&email_proveedor='+cont_array[2];

    // console.log(data);

    if (id_proveedor !== ''){
        if (id_empresa !== null){
            if (id_contacto !== null){
                $.ajax({
                    type: 'POST',
                    url: urlBase,
                    data: data,
                    dataType: 'JSON',
                    success: function(response){
                        console.log(response);
                        if (response > 0){
                            var id_grupo = $('[name=id_grupo_cotizacion]').val();
                            listar_cotizaciones(id_grupo);

                            if(option=='UPDATE'){
                                $('#modal-cotizacion_proveedor').modal('hide');
                            }

                        }
                    }
                }).fail( function( jqXHR, textStatus, errorThrown ){
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });                             
            } else {
                alert("Es necesario que seleccione un email-proveedor");
            }
        } else {
            alert("Es necesario que seleccione una empresa");
        }
    } else {
        alert("Es necesario que seleccione un proveedor");
    }
}

function downloadSolicitudCotizacion(id_cotizacion){
    window.open('/solicitud_cotizacion_excel/'+id_cotizacion);
}

function ModalArchivosAdjuntosCotizacion(id_cotizacion){
    $('#modal-adjuntos_cotizacion').modal({
        show: true
    });
    if(id_cotizacion >0){
        listar_archivos_adjuntos_cotizacion(id_cotizacion);
    }else{
        alert("ERROR - No existe id_cotizacion");
    }
}

function listar_archivos_adjuntos_cotizacion(id_cotizacion){
   let adjuntos_cotizacion=[];

    $.ajax({
        type: 'GET',
        url: '/archivos_adjuntos_cotizacion/'+id_cotizacion,
        dataType: 'JSON',
        success: function(response){
            if (response.length > 0){
                for (x=0; x<response.length; x++){
                    adjuntos_cotizacion.push(
                        { 
                            'id_archivo':response[x].id_archivo,
                            'id_detalle_requerimiento':response[x].id_detalle_requerimiento,
                            'archivo':response[x].archivo,
                            'fecha_registro':response[x].fecha_registro,
                            'estado':response[x].estado,
                            'file':[]
                        } 
                    );
                }

                llenar_tabla_archivos_adjuntos_cotizacion(adjuntos_cotizacion);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function llenar_tabla_archivos_adjuntos_cotizacion(adjuntos){   
    limpiarTabla('listaArchivosCotizacion');
    htmls ='<tr></tr>';
    $('#listaArchivosCotizacion tbody').html(htmls);
    var table = document.getElementById("listaArchivosCotizacion");
    for(var a=0;a < adjuntos.length;a++){

        var row = table.insertRow(a+1);
        var tdIdArchivo =  row.insertCell(0);
            tdIdArchivo.setAttribute('class','hidden');
            tdIdArchivo.innerHTML = adjuntos[a].id_archivo?adjuntos[a].id_archivo:'0';
        var tdIdDetalleReq =  row.insertCell(1);
            tdIdDetalleReq.setAttribute('class','hidden');
            tdIdDetalleReq.innerHTML = adjuntos[a].id_detalle_requerimiento?adjuntos[a].id_detalle_requerimiento:'0';
        row.insertCell(2).innerHTML = a+1;
        row.insertCell(3).innerHTML = adjuntos[a].archivo?adjuntos[a].archivo:'-';
        row.insertCell(4).innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
        '<a'+
        '    class="btn btn-primary btn-sm "'+
        '    name="btnAdjuntarArchivos"'+
        '    href="/files/logistica/detalle_requerimiento/'+adjuntos[a].archivo+'"'+
        '    target="_blank"'+
        '    data-original-title="Descargar Archivo"'+
        '>'+
        '    <i class="fas fa-file-download"></i>'+
        '</a>'+
        '</div>';

    }
    return null;
}

function limpiarTabla(idElement){
    var table = document.getElementById(idElement);
    for(var i = table.rows.length - 1; i > 0; i--)
    {
        table.deleteRow(i);
    }
    return null;
}

function anular_cotizacion(id_cotizacion){
    var rspta = confirm("¿Está seguro que desea anular ésta cotización?");
    if (rspta){
        $.ajax({
            type: 'GET',
            url: '/anular_cotizacion/'+id_cotizacion,
            dataType: 'JSON',
            success: function(response){
                // console.log(response);
                if (response > 0){
                    alert('Cotización anulada con éxito.');
                    var id_grupo = $('[name=id_grupo_cotizacion]').val();
                    listar_cotizaciones(id_grupo);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}
function cargar_imagen(){
    var id_empresa = $('[name=id_empresa]').val();
    // console.log('id_empresa'+id_empresa);
    if (id_empresa == 3){
        $('#img').attr('src','/images/logo_proyectec.png');
    } else if (id_empresa == 4){
        $('#img').attr('src','/images/logo_okc.png');
    } else if (id_empresa == 5){
        $('#img').attr('src','/images/logo_smart.png');
    }
}
function ver_saldos(id_producto,tipo){
    // console.log('id_producto'+id_producto+' tipo'+tipo);
    if (tipo == 1){
        $('#modal-saldos_producto').modal({
            show: true
        });
        // $('#des_producto').text(descripcion);
        $('#listaSaldos tbody').html('');
        listar_saldos_productos(id_producto);
    }
}
function listar_saldos_productos(id_producto){
    var vardataTables = funcDatatables();
    var tabla = $('#listaSaldos').DataTable({
        'dom': 'rt',
        // 'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'destroy' : true,
        ajax:{
            url:"/saldo_por_producto/"+id_producto,
            dataSrc:""
        },
        'columns': [
            {'data': 'id_prod_ubi'},
            // {'data': 'codigo'},
            // {'data': 'descripcion'},
            {'data': 'des_almacen'},
            {'data': 'cod_posicion'},
            {'data': 'stock'}
            // {'defaultContent': 
            // '<button type="button" class="saldos btn btn-primary boton" data-toggle="tooltip" '+
            //     'data-placement="bottom" title="Separar" >'+
            //     '<i class="fas fa-search-plus"></i></button>'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    botones('#listaSaldos tbody',tabla);
}
function botones(tbody, tabla){
    // console.log("saldos");
    $(tbody).on("click","button.saldos", function(){
        var data = tabla.row($(this).parents("tr")).data();
        // console.log(data);
        
    });
}