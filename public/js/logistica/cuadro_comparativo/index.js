let buenaPro={};
let itemSelected={};
let buenasPro=[];
$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaCuadroComaparativo tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaCuadroComaparativo').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        $('.modal-footer #id_grupo').text(idTr);
        
    });


    $('#form-valorizacion-item').on('submit', function(){
        var data = $(this).serialize();
        var ask = confirm('¿Desea guardar este registro?');
        if (ask == true){
            var id_val_coti = items_valorizacion[indice_actual].id_valorizacion_cotizacion;
            var id_coti = items_valorizacion[indice_actual].id_cotizacion;
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '/logistica/valorizacion_item',
                data: data+'&id_valorizacion_cotizacion='+id_val_coti+'&id_cotizacion='+id_coti,
                beforeSend: function(){
                    $(document.body).append('<span class="loading"><div></div></span>');
                },
                success: function(response){
                    $('.loading').remove();
                    if (response > 0){
                        listaItemValorizar(response);
                        alert('Se actualizó la valorización con éxito');
                    }
                }
            });
            return false;
        }else{
            return false;
        }
    });
    $('#form-valorizacion-especificacion').on('submit', function () {
        var data = $(this).serialize();
        var ask = confirm('¿Desea guardar este registro?');
        if (ask == true) {
            var id_val_coti = items_valorizacion[indice_actual].id_valorizacion_cotizacion;
            var id_coti = items_valorizacion[indice_actual].id_cotizacion;
            // console.log(data);
            $.ajax({
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '/logistica/valorizacion_especificacion',
                data: data + '&id_valorizacion_cotizacion=' + id_val_coti + '&id_cotizacion=' + id_coti,
                beforeSend: function () {
                    $(document.body).append('<span class="loading"><div></div></span>');
                },
                success: function (response) {
                    $('.loading').remove();
                    if (response > 0) {
                        alert('Se actualizó la especificación con éxito');
                    }
                }
            });
            return false;
        } else {
            return false;
        }
    });
});
function limpiarTabla(idElement,type=0) {
    
    let table = document.getElementById(idElement).getElementsByTagName( 'tbody' )[0];
    switch (type) {
        case 'ALL':
            table = document.getElementById(idElement).getElementsByTagName( 'thead' )[0];
            for (let i = table.rows.length - 1; i >= 0; i--) {
                table.deleteRow(i)
            }
            table = document.getElementById(idElement).getElementsByTagName( 'tbody' )[0];
            for (let i = table.rows.length - 1; i >= 0; i--) {
                table.deleteRow(i)
            }
        break;
            
        default:
                table = document.getElementById(idElement).getElementsByTagName( 'tbody' )[0];
        break;
    }
    // console.log('limpiando tabla....')
    // const table = document.getElementById(idElement).getElementsByTagName( 'tbody' )[0];
    // console.log(table.rows.length);
    
    for (let i = table.rows.length - 1; i >= 0; i--) {
        table.deleteRow(i)
        
    }
    return null
}

function disabledControl(element, value) {
    // console.log("disable control");
    let i
    for (i = 0; i < element.length; i++) {
        if (value === false) {
            element[i].removeAttribute('disabled')
            element[i].classList.remove("disabled");

        } else {
            element[i].setAttribute('disabled', 'true')
            element[i].classList.add("disabled");

        }
    }
    return null
}

function editValorizaciones() {
    const btnEditarValorizacion = document.getElementsByName(
        'btnValorizarCotizacion'
    )
    disabledControl(btnEditarValorizacion, false)
}

// buscar por codigo de cotizacion o codigo de cuadro comparativo
let grupoCotizacion = []
function getGrupoCotizaciones() {
    const codigo = document.getElementsByName('codigo')[0].value
    const tipoCodigo = document.getElementsByName('tipoCodigo')[0].value
    grupoCotizaciones(codigo,tipoCodigo);

}

function grupoCotizaciones(codigo,tipoCodigo){

    // habilitar boton mostrar cuadro comparativo ***********//
    const btnMostrarCuadroComarativo = document.getElementsByName('btnMostrarCuadroComparativo');
    disabledControl(btnMostrarCuadroComarativo, false);
    // **************************************************//

    const baseUrl = '/logistica/cuadro_comparativo/grupo_cotizaciones'
    let url = ''
    switch (tipoCodigo) {
        case '1': // codigo cuadro comparartivo
            url = baseUrl.concat(`/0/${  codigo}/0`)
            break
        case '2': // codigo cotización
            url = baseUrl.concat(`/${  codigo  }/0/0`)
            break
        case '3': // id grupo cotizacion ( id cuadro comparativo)
            url = baseUrl.concat(`/0/0/${  codigo  }`)
            break
        default:
            break
    }
    $.ajax({
        type: 'GET',
        url,
        dataType: 'JSON',
        success(response) {
            grupoCotizacion = response
            llenarTablaGrupoCotizacion(grupoCotizacion)
            // console.log(grupoCotizacion);
            $('[name=id_grupo_cotizacion]').val(grupoCotizacion[0].id_grupo_cotizacion);

        },
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR)
        console.log(textStatus)
        console.log(errorThrown)
    })
}

// mostrar tabla de grupo cotizaciones
function llenarTablaGrupoCotizacion(grupoCotizacion) {
    changeStateButton('historial')
    limpiarTabla('listaGrupoCotizaciones')

    htmls = '<tr></tr>'
    $('#listaGrupoCotizaciones tbody').html(htmls)
    const table = document.getElementById('listaGrupoCotizaciones')

    grupoCotizacion.map((currentValue, index, array) => {
        const row = table.insertRow(index + 1)
        const tdIdCotizacion = row.insertCell(0)
        tdIdCotizacion.innerHTML = currentValue.id_cotizacion
        tdIdCotizacion.setAttribute('class', 'hidden')

        row.insertCell(1).innerHTML = index + 1
        row.insertCell(2).innerHTML = currentValue.codigo_grupo
        row.insertCell(3).innerHTML = currentValue.codigo_cotizacion
        row.insertCell(4).innerHTML = currentValue.requerimientos
            .map(function(k) {
                return k.codigo_requerimiento
            })
            .join(', ')
        const tdProveedor = row.insertCell(5)
        tdProveedor.innerHTML = currentValue.proveedor.razon_social
        tdProveedor.setAttribute(
            'title',
            `${currentValue.proveedor.nombre_doc_identidad 
                }: ${ 
                currentValue.proveedor.nro_documento}`
        )
        const tdBtnAction = row.insertCell(6)
        tdBtnAction.setAttribute('width', 'auto')
        tdBtnAction.innerHTML =
            '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">' +
            '<button class="btn btn-warning btn-sm" name="btnValorizarCotizacion" title="Valorizar Cotización" onClick="valorizarCotizacion(event,'+index+ 
            ');" disabled ><i class="fas fa-file-invoice-dollar"></i></button>' +
            '</div>';
    })
}

// valorizar cotizacion
function valorizarCotizacion(event, index) {
    event.preventDefault()
    $('#modal-valorizarCotizacion').modal({
        show: true,
        backdrop: 'static',
    })
    let item = grupoCotizacion[index];
    let id_cotizacion = item.id_cotizacion;
    listaItemValorizar(id_cotizacion);
    
}

function listaItemValorizar(id_cotizacion){
    const baseUrl = '/logistica/cuadro_comparativos/valorizacion/lista_item/'+id_cotizacion;

    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success(response) {
            // console.log(response);
            llenarTablaItemsValorizacion(response);
        },
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR)
        console.log(textStatus)
        console.log(errorThrown)
    })
}
let items_valorizacion = [];
function llenarTablaItemsValorizacion(items){
    // console.log(items);
    items_valorizacion = items;
    limpiarTabla('listarItemCotizacion')

    htmls = '<tr></tr>';
    $('#listarItemCotizacion tbody').html(htmls)
    const table = document.getElementById('listarItemCotizacion').getElementsByTagName( 'tbody' )[0];

    items.map((currentValue, index, array) => {
        const row = table.insertRow(index + 1)
        const tdIdValorizacionCotizacion = row.insertCell(0)
        tdIdValorizacionCotizacion.innerHTML = currentValue.id_valorizacion_cotizacion
        tdIdValorizacionCotizacion.setAttribute('class', 'hidden')
        row.insertCell(1).innerHTML = index + 1
        row.insertCell(2).innerHTML = currentValue.codigo
        row.insertCell(3).innerHTML = currentValue.descripcion
        row.insertCell(4).innerHTML = currentValue.unidad_medida
        row.insertCell(5).innerHTML = currentValue.cantidad
        row.insertCell(6).innerHTML = formatDecimal(currentValue.precio_referencial)
        row.insertCell(7).innerHTML = currentValue.abrev_unidad_medida_cotizado
        row.insertCell(8).innerHTML = currentValue.cantidad_cotizada?currentValue.cantidad_cotizada:'0'
        row.insertCell(9).innerHTML = currentValue.precio_cotizado?formatDecimal(currentValue.precio_cotizado):"-"
        row.insertCell(10).innerHTML = (currentValue.cantidad_cotizada * currentValue.precio_cotizado)
        row.insertCell(11).innerHTML = currentValue.flete?formatDecimal(currentValue.flete):'-'
        row.insertCell(12).innerHTML = currentValue.porcentaje_descuento?formatDecimal(currentValue.porcentaje_descuento):'-'
        row.insertCell(13).innerHTML = currentValue.monto_descuento?formatDecimal(currentValue.monto_descuento):'-'
        row.insertCell(14).innerHTML = currentValue.subtotal?formatDecimal(currentValue.subtotal):'0.00'
        const tdBtnAction = row.insertCell(15)
        tdBtnAction.setAttribute('width', 'auto')
        tdBtnAction.innerHTML =
            '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">' +
            '<button class="btn btn-primary btn-sm" name="btnValorizarCotizacion" title="Valorizar Cotización" onClick="valorizacionEspecificacion(event,'+index+ 
            ');"><i class="fas fa-edit"></i></button>' +
            '</div>';
    })
}

let indice_actual=0;
function valorizacionEspecificacion(event,index) {
    event.preventDefault()
    // item_val = items_valorizacion[index];
    // console.log(items_valorizacion[index]);
    fillInputsModalValorizacionEspecificacion(items_valorizacion[index]);
    
    indice_actual = index;

    $('#modal-valorizacion-especificacion').modal({
        show: true,
        backdrop: 'static',
    })
}


function fillInputsModalValorizacionEspecificacion(item){
    // console.log(item);
    
    $('#id_valorizacion_cotizacion').val(item.id_valorizacion_cotizacion);
    $('#id_detalle_requerimiento').val(item.id_detalle_requerimiento);
    $('#unidad_medida_valorizacion').val(item.id_unidad_medida_cotizado);
    $('#cantidad_valorizacion').val(item.cantidad_cotizada);
    $('#precio_valorizacion').val(item.precio_cotizado);
    $('#flete_valorizacion').val(item.flete);
    $('#porcentaje_descuento_valorizacion').val(item.porcentaje_descuento);
    $('#monto_descuento_valorizacion').val(item.monto_descuento);
    $('#subtotal_valorizacion').val(item.subtotal);

    $('#igv').val(item.incluye_igv);
    $('#garantia').val(item.garantia);
    $('#plazo_entrega').val(item.plazo_entrega);
    $('#lugar_entrega').val(item.lugar_despacho);
    $('#detalle_adicional').val(item.detalle);

    CalValuesModalValorizacion();
    get_data_archivos_adjuntos(item.id_detalle_requerimiento);
    get_data_archivos_adjuntos_proveedor(item.id_valorizacion_cotizacion);
 
}


function get_data_archivos_adjuntos(id){
 
    adjuntos=[];
    baseUrl = '/logistica/mostrar-archivos-adjuntos/'+id;
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            if(response.length >0){
                for (x=0; x<response.length; x++){
                    id_detalle_requerimiento= response[x].id_detalle_requerimiento;
                        adjuntos.push({ 
                            'id_archivo':response[x].id_archivo,
                            'id_valorizacion_cotizacion':response[x].id_valorizacion_cotizacion,
                            'id_detalle_requerimiento':response[x].id_detalle_requerimiento,
                            'archivo':response[x].archivo,
                            'fecha_registro':response[x].fecha_registro,
                            'estado':response[x].estado,
                            'file':[]
                            });
                    }
                    llenar_tabla_archivos_adjuntos(adjuntos);
            }else{
                var table = document.getElementById("listaArchivos");
                var row = table.insertRow(-1);
                var tdSinData =  row.insertCell(0);
                tdSinData.setAttribute('colspan','5');
                tdSinData.setAttribute('class','text-center');
                tdSinData.innerHTML = 'No se encontro ningun archivo adjunto';

            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
    
}

var adjuntos_proveedor = [];

function get_data_archivos_adjuntos_proveedor(id){
 
    adjuntos_proveedor=[];
    baseUrl = '/logistica/mostrar-archivos-adjuntos-proveedor/'+id;
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            if(response.length >0){
                for (x=0; x<response.length; x++){
                    id_detalle_requerimiento= response[x].id_detalle_requerimiento;
                        adjuntos_proveedor.push({ 
                            'id_archivo':response[x].id_archivo,
                            'id_valorizacion_cotizacion':response[x].id_valorizacion_cotizacion,
                            'id_detalle_requerimiento':response[x].id_detalle_requerimiento,
                            'archivo':response[x].archivo,
                            'fecha_registro':response[x].fecha_registro,
                            'estado':response[x].estado,
                            'file':[]
                            });
                    }
                    llenar_tabla_archivos_adjuntos_proveedor(adjuntos_proveedor);
            }else{
                var table = document.getElementById("listaArchivos");
                var row = table.insertRow(-1);
                var tdSinData =  row.insertCell(0);
                tdSinData.setAttribute('colspan','5');
                tdSinData.setAttribute('class','text-center');
                tdSinData.innerHTML = 'No se encontro ningun archivo adjunto';

            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
    
}

function llenar_tabla_archivos_adjuntos(adjuntos){
    limpiarTabla('listaArchivos');
    htmls ='<tr></tr>';
    $('#listaArchivos tbody').html(htmls);
    var table = document.getElementById("listaArchivos");
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

function llenar_tabla_archivos_adjuntos_proveedor(adj_prov){
    limpiarTabla('listaArchivosProveedor');
    htmls ='<tr></tr>';
    $('#listaArchivosProveedor tbody').html(htmls);
    var table = document.getElementById("listaArchivosProveedor");
    for(var a=0;a < adj_prov.length;a++){

        var row = table.insertRow(a+1);
        var tdIdArchivo =  row.insertCell(0);
            tdIdArchivo.setAttribute('class','hidden');
            tdIdArchivo.innerHTML = adj_prov[a].id_archivo?adj_prov[a].id_archivo:'0';
        var tdIdDetalleReq =  row.insertCell(1);
            tdIdDetalleReq.setAttribute('class','hidden');
            tdIdDetalleReq.innerHTML = adj_prov[a].id_detalle_requerimiento?adj_prov[a].id_detalle_requerimiento:'0';
        row.insertCell(2).innerHTML = a+1;
        row.insertCell(3).innerHTML = adj_prov[a].archivo?adj_prov[a].archivo:'-';
        row.insertCell(4).innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
        '<a'+
        '    class="btn btn-primary btn-sm "'+
        '    name="btnAdjuntarArchivos"'+
        '    href="/files/logistica/cotizacion/'+adj_prov[a].archivo+'"'+
        '    target="_blank"'+
        '    data-original-title="Descargar Archivo"'+
        '>'+
        '    <i class="fas fa-file-download"></i>'+
        '</a>'+
        '</div>';

    }
    return null;
}


let only_adjuntos_proveedor=[];

function agregarAdjuntoProveedor(event){ 
    let archivo_proveedor ={
        id_archivo: 0,
        id_valorizacion_cotizacion: $('#id_valorizacion_cotizacion').val(),
        id_detalle_requerimiento: $('#id_detalle_requerimiento').val(),
        archivo:event.target.files[0].name,
        fecha_registro: new Date().toJSON().slice(0, 10),
        estado: 1
    }
    let only_file = event.target.files[0]
    adjuntos_proveedor.push(archivo_proveedor);
    only_adjuntos_proveedor.push(only_file);
    // console.log("agregar adjunto");
    console.log(adjuntos_proveedor);

    $('#listaArchivosProveedor tbody').html(htmls);
    var table = document.getElementById("listaArchivosProveedor");
    var indicadorTd='';
    for(var a=0;a < adjuntos_proveedor.length;a++){
        var row = table.insertRow(-1);

        if(adjuntos_proveedor[a].id_archivo ==0){
            indicadorTd="green"; // si es nuevo
        }
        var tdIdArchivo =  row.insertCell(0);
        tdIdArchivo.setAttribute('class','hidden');
        tdIdArchivo.innerHTML = adjuntos_proveedor[a].id_archivo?adjuntos_proveedor[a].id_archivo:'0';
        var tdIdDetalleReq =  row.insertCell(1);
        tdIdDetalleReq.setAttribute('class','hidden');
        tdIdDetalleReq.innerHTML = 0;
        var tdNumItem = row.insertCell(2);
        tdNumItem.innerHTML = a+1;
        var tdNameFile = row.insertCell(3);
        tdNameFile.innerHTML = adjuntos_proveedor[a].archivo?adjuntos_proveedor[a].archivo:'-';
        tdNameFile.setAttribute('class',indicadorTd);
        row.insertCell(4).innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
        '<a'+
        '    class="btn btn-primary btn-sm "'+
        '    name="btnAdjuntarArchivos"'+
        '    href="/files/logistica/cotizacion/'+adjuntos_proveedor[a].archivo+'"'+
        '    target="_blank"'+
        '    data-original-title="Descargar Archivo de Proveedor"'+
        '>'+
        '    <i class="fas fa-file-download"></i>'+
        '</a>'+
        '</div>';
    }
}

function guardarAdjuntosProveedor(){
    // console.log(adjuntos_proveedor);
    // console.log(only_adjuntos_proveedor);
    let id_valorizacion_cotizacion = adjuntos_proveedor[0].id_valorizacion_cotizacion;
    let id_detalle_requerimiento = adjuntos_proveedor[0].id_detalle_requerimiento;
    const onlyNewAdjuntos = adjuntos_proveedor.filter(id => id.id_archivo == 0); // solo enviar los registros nuevos
    var myformData = new FormData();        
    // myformData.append('archivo_adjunto', JSON.stringify(adjuntos_proveedor));
    for(let i=0;i<only_adjuntos_proveedor.length;i++){
        myformData.append('only_adjuntos_proveedor[]', only_adjuntos_proveedor[i]);
        
    }
    myformData.append('detalle_adjuntos', JSON.stringify(onlyNewAdjuntos));
    myformData.append('id_detalle_requerimiento', id_detalle_requerimiento);
    myformData.append('id_valorizacion_cotizacion', id_valorizacion_cotizacion);

    baseUrl = '/logistica/guardar-archivos-adjuntos-proveedor';
    $.ajax({
        type: 'POST',
        processData: false,
        contentType: false,
        cache: false,
        data: myformData,
        enctype: 'multipart/form-data',
        // dataType: 'JSON',
        url: baseUrl,
        success: function(response){
            // console.log(response);     
            if (response > 0){
                alert("Archivo(s) Guardado(s)");
                only_adjuntos_proveedor=[];
                get_data_archivos_adjuntos_proveedor(id_valorizacion_cotizacion);
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });  
}


// historial cuadro comparativo

function modalCuadroComparativo(){
    $('#modal-cuadro_comparativo').modal({
        show: true,
        backdrop: 'static'
    });
    listarCuadroComparativos();
}
function listarCuadroComparativos() {
    var vardataTables = funcDatatables();
    $('#listaCuadroComaparativo').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'processing': true,
        "bDestroy": true,
        'ajax': '/logistica/cuadro_comparativos',
        'columns': [
            {'data': 'id_grupo_cotizacion'},
            {'data': 'codigo_grupo'},
            {'data': 'proveedor[, ]'},
            {'data': 'empresa.0'}
            // {'data': 'fecha_inicio'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        'order': [
            [2, 'asc']
        ]
    });
}

function selectCuadroComparativo(){
    var id = $('#id_grupo').text();
    // var page = $('.page-main').attr('type');
    // var form = $('.page-main form[type=register]').attr('id');
        // clearForm(form);
        changeStateButton('historial');
        mostrar_cuadro_comparativo(id);
        // console.log($(":file").filestyle('disabled'));
    $('#modal-cuadro_comparativo').modal('hide');
}

function mostrar_cuadro_comparativo(id){
    url = '/logistica/cuadro_comparativo/'+id;
    // let items={};
    // $(":file").filestyle('disabled', false);
    data_item = [];
    baseUrl = url;
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            data = response;
            // console.log(response);
            if(response !== undefined){
                $('[name=id_grupo_cotizacion]').val(response.id_grupo_cotizacion);
                $('[name=codigo]').val(response.codigo_grupo);

                grupoCotizaciones(response.codigo_grupo,'1');


            }else{
                alert("no se encontro resultados");
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

// onchange input modal valorizacion
function onChangeInputValorizacion() {
    CalValuesModalValorizacion();
};

function CalValuesModalValorizacion(){
    var cantidad = $('#cantidad_valorizacion').val()? $('#cantidad_valorizacion').val():0;
    var precio = $('#precio_valorizacion').val()?$('#precio_valorizacion').val():0;
    var flete = $('#flete_valorizacion').val()?$('#flete_valorizacion').val():0;
    var porcentaje_descuento = $('#porcentaje_descuento_valorizacion').val()?$('#porcentaje_descuento_valorizacion').val():0;
    var monto_descuento = $('#monto_descuento_valorizacion').val()?$('#monto_descuento_valorizacion').val():0;
    
    let subtotal01= 0;
    let nuevo_monto_descuento =0;
    if ( cantidad > 0){
        subtotal01 = parseFloat(cantidad * precio) + parseFloat(flete);
        nuevo_monto_descuento = parseFloat(porcentaje_descuento * subtotal01)/100;
            if(monto_descuento >0){
                nuevo_monto_descuento=monto_descuento;
                $('#monto_descuento_valorizacion').val(nuevo_monto_descuento);
            }
            subtotal01 = parseFloat(cantidad * precio) + parseFloat(flete) - parseFloat(nuevo_monto_descuento);
    }    
    $('#subtotal_valorizacion').val(subtotal01);
}
// function get_detalle_unidad_medida(id_unidad_medida){
//     let resp = 0;
//     let url = '/logistica/detalle_unidad_medida/'+id_unidad_medida;
//     baseUrl = url;
//     $.ajax({
//         type: 'GET',
//         url: baseUrl,
//         dataType: 'JSON',
//         async:false,
//         success: function(response){
//             resp = response;
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
//     return resp; 

// }


  // guardar valorizacion
function updateValorizacion(){
    var unidad_medida = $('#unidad_medida_valorizacion').val();
    var cantidad = $('#cantidad_valorizacion').val()?$('#cantidad_valorizacion').val():'0';
    var precio = $('#precio_valorizacion').val()?$('#precio_valorizacion').val():'0';
    var flete = $('#flete_valorizacion').val()?$('#flete_valorizacion').val():'0';
    var porcentaje_descuento = $('#porcentaje_descuento_valorizacion').val()?$('#porcentaje_descuento_valorizacion').val():'0';
    var monto_descuento = $('#monto_descuento_valorizacion').val()?$('#monto_descuento_valorizacion').val():'0';
    var subtotal = $('#subtotal_valorizacion').val();

    let valorizacion = {
        'unidad_medida':unidad_medida,
        'cantidad':cantidad,
        'precio':precio,
        'flete':flete,
        'porcentaje_descuento':porcentaje_descuento,
        'monto_descuento':monto_descuento,
        'subtotal':subtotal
    }

    updateItem(valorizacion);
    
}
function updateUnidadMedida(id) {
    let resp = 0;
    let url = '/logistica/detalle_unidad_medida/'+id;
    baseUrl = url;
    return $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON'
    })
}

async function updateItem(valorizacion) {
    // console.log(valorizacion);
    // console.log('calling');
    var unidad_medida_abrev = '';
    if(valorizacion.unidad_medida >0){
        let data_unidad_medida = await updateUnidadMedida(valorizacion.unidad_medida);
        unidad_medida_abrev = data_unidad_medida.abreviatura.trim()
    }
    items_valorizacion[indice_actual].id_unidad_medida_cotizado = valorizacion.unidad_medida;
    items_valorizacion[indice_actual].unidad_medida = unidad_medida_abrev;
    items_valorizacion[indice_actual].cantidad_cotizada = valorizacion.cantidad;
    items_valorizacion[indice_actual].precio_cotizado = valorizacion.precio;
    items_valorizacion[indice_actual].flete = valorizacion.flete;
    items_valorizacion[indice_actual].porcentaje_descuento = valorizacion.porcentaje_descuento;
    items_valorizacion[indice_actual].monto_descuento = valorizacion.monto_descuento;
    items_valorizacion[indice_actual].subtotal = valorizacion.subtotal;

    
    // console.log(items_valorizacion[indice_actual]);
    saveChangesValorizacion(items_valorizacion[indice_actual])
    
}

// function saveChangesValorizacion(item){
        
//     baseUrl = '/logistica/cuadro_comparativos/update_valorizacion/1';
//     $.ajax({
//         type: 'PUT',
//         url: baseUrl,
//         data: item,
//         dataType: 'JSON',
//         success: function(response){
//             // console.log(response);
            
//             // if (response > 0){
//             //     alert("Valorización Guardada");
//             // }
//         }
//     }).fail( function(jqXHR, textStatus, errorThrown){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });  
// }

// mostrar cuadro comparativo en pantalla
function vista_extendida(){
    let body=document.getElementsByTagName('body')[0];
    body.classList.add("sidebar-collapse"); 
}

let cuadro_comparativo ={};
function mostrarCuadroComparativo(){
    limpiarTabla('cuadro_comparativo','ALL');
    const btnExportarCuadroComarativo = document.getElementsByName('btnExportarCuadroComparativo');
    disabledControl(btnExportarCuadroComarativo, false);
    vista_extendida();
    if( parseInt($('[name=id_grupo_cotizacion]').val()) > 0){// siempre debe existir un id_grupo cotizacion para generar el cuadro

        var id_grupo = $('[name=id_grupo_cotizacion]').val(); 
        // var tipoCodigo = $('[name=tipoCodigo]').val();
        var baseUrl = '/logistica/cuadro_comparativo/mostrar_comparativo/'+id_grupo;
            $.ajax({
            type: 'GET',
            url: baseUrl,
            dataType: 'JSON',
            async:false,
            success: function(response){
                cuadro_comparativo = response;
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            if(qXHR.status === 500){
                alert("Error "+qXHR.status+" No se puede Generar el Cuadro Comparativo");
            }
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });


        var head = document.getElementById("head-cuadro");
        // head.innerHTML = '<h2 class="text-center"><strong>CC-1906-0005</strong></h2>';
        let titulo = '<div><h2 class="text-center"><strong>'+cuadro_comparativo.head.codigo_grupo+'</strong></h2>';
        let tabla_head =    '<table class="table table-condensed table-bordered">'+
                                ' <tr>'+
                                '     <th>Empresa</th>'+
                                '     <td>'+cuadro_comparativo.head.empresa_razon_social+' ['+cuadro_comparativo.head.empresa_nombre_doc_identidad+': '+cuadro_comparativo.head.empresa_nro_documento+']</td>'+
                                ' </tr>'+
                            '<tr>'+
                                '<th>Fecha Inicio</th>'+
                                '<td>'+cuadro_comparativo.head.fecha_inicio+'</td>'+
                            '</tr>'+
                            '</table></div>';
        head.innerHTML = titulo.concat(tabla_head); 
        
      

        let cantidad_proveedores = cuadro_comparativo.proveedores.length;
        
        // $('#cuadro_comparativo tbody').html(htmls)
        const table_head = document.getElementById('cuadro_comparativo').getElementsByTagName( 'thead' )[0];
        const table_body = document.getElementById('cuadro_comparativo').getElementsByTagName( 'tbody' )[0];
        
        const rowH = table_head.insertRow(0)
        const tdContador = rowH.insertCell(0)
        tdContador.innerHTML = "#";
        tdContador.setAttribute('rowspan', '3')
        const tdCodigo = rowH.insertCell(1)
        tdCodigo.innerHTML = "CODIGO";
        tdCodigo.setAttribute('rowspan', '3')
        const tdDescripcion = rowH.insertCell(2)
        tdDescripcion.innerHTML = "DESCRIPCIÓN";
        tdDescripcion.setAttribute('rowspan', '3')
        const tdUnidad = rowH.insertCell(3)
        tdUnidad.innerHTML = "UNIDAD";
        tdUnidad.setAttribute('rowspan', '3')
        const tdCantidad = rowH.insertCell(4)
        tdCantidad.innerHTML = "CANTIDAD";
        tdCantidad.setAttribute('rowspan', '3')
        const tdPrecioRef = rowH.insertCell(5)
        tdPrecioRef.innerHTML = "PRECIO REF.";
        tdPrecioRef.setAttribute('rowspan', '3')
        const tdProv = rowH.insertCell(6)
        tdProv.innerHTML = "PRORVEEDORES";
        tdProv.setAttribute('colspan', cantidad_proveedores*4)
        tdProv.setAttribute('class', 'text-center')

        const rowH2 = table_head.insertRow(1)
        const rowH3 = table_head.insertRow(2)

        cuadro_comparativo.proveedores.map((proveedor, index, array) => { 
            const tdNameProv = rowH2.insertCell(0)
            tdNameProv.innerHTML = proveedor.razon_social;
            tdNameProv.setAttribute('colspan', '4')
            tdNameProv.setAttribute('class', 'text-center')

            rowH3.insertCell(0).innerHTML = "UNID."
            rowH3.insertCell(1).innerHTML = "CANT."
            rowH3.insertCell(2).innerHTML = "PRECIO."
            rowH3.insertCell(3).innerHTML = "TOTAL"
        });


        cuadro_comparativo.cuadro_comparativo.map((detalle_req, indice, array) => {
            const row = table_body.insertRow(indice)
            const tdidDetalleReq = row.insertCell(0)
                tdidDetalleReq.innerHTML = detalle_req.id_detalle_requerimiento
                tdidDetalleReq.setAttribute('class', 'hidden')
            row.insertCell(1).innerHTML = indice + 1
            row.insertCell(2).innerHTML = detalle_req.codigo
            row.insertCell(3).innerHTML = detalle_req.descripcion
            row.insertCell(4).innerHTML = detalle_req.unidad_medida
            row.insertCell(5).innerHTML = detalle_req.cantidad
            row.insertCell(6).innerHTML = 'S/.'+detalle_req.precio_referencial
            detalle_req.proveedores.map((proveedor, index, array) => {
                // console.log(Object.keys(proveedor.valorizacion).length);
                
                if(Object.keys(proveedor.valorizacion).length > 0){
                    let totalValorizado = parseFloat(proveedor.valorizacion.cantidad_cotizada * proveedor.valorizacion.precio_cotizado);
                    row.insertCell(7).innerHTML = proveedor.valorizacion.unidad_medida_cotizada
                    row.insertCell(8).innerHTML = proveedor.valorizacion.cantidad_cotizada
                    row.insertCell(9).innerHTML = parseFloat(proveedor.valorizacion.precio_cotizado) > 0? '<button class="badge" onclick="darBuenaPro(event,'+indice+','+proveedor.valorizacion.id_valorizacion_cotizacion+','+detalle_req.id_detalle_requerimiento+','+proveedor.valorizacion.id_proveedor+','+proveedor.valorizacion.id_empresa+');"> '+'S/.'+proveedor.valorizacion.precio_cotizado+'</button>' : '-'
                    row.insertCell(10).innerHTML = typeof(totalValorizado) === 'number' &&  totalValorizado > 0 ? 'S/.'+totalValorizado:'-'
                }else{    
                    row.insertCell(7).innerHTML = '-'
                    row.insertCell(8).innerHTML = '-'
                    row.insertCell(9).innerHTML = '-'
                    row.insertCell(10).innerHTML = '-'

                }
            })
        })

        var scrollingElement = (document.scrollingElement || document.body);
        scrollingElement.scrollTop = scrollingElement.scrollHeight;

        llenarBuenaPro(cuadro_comparativo);
    }else{
        alert("no existe id_grupo");
    }
}

function llenarBuenaPro(cuadro_comparativo){
    buenaPro={};
    buenasPro=[];
    if(cuadro_comparativo.buena_pro.length >0){
        cuadro_comparativo.buena_pro.map((element,index)=>{
            buenaPro={
                'item_codigo': element.codigo_item,
                'item_descripcion': element.descripcion_item,
                'id_valorizacion_cotizacion': element.id_valorizacion_cotizacion,
                'id_proveedor': element.id_proveedor,
                'razon_social_proveedor': element.razon_social,
                'documento_proveedor': element.nombre_doc_identidad,
                'nro_documento_proveedor': element.nro_documento,
                'id_empresa': element.id_empresa,
                'razon_social_empresa': element.empresa_razon_social,
                'documento_empresa': element.empresa_nombre_doc_identidad,
                'nro_documento_empresa': element.empresa_nro_documento,
                'precio_valorizacion': element.precio_cotizado,
                'cantidad_valorizacion': element.cantidad_cotizada,
                'unidad_valorizacion': element.unidad_medida_cotizada,
                'justificacion':element.justificacion
            }
            buenasPro.push(buenaPro);
        });
        printListBuenaPro(buenasPro);

    }

}
//Buena pro
var btnPriceActive ='';
function darBuenaPro(event,index,id_valorizacion_cotizacion,id_detalle_requerimiento,id_proveedor,id_empresa){
        // console.log(index,id_valorizacion_cotizacion,id_detalle_requerimiento,id_proveedor,id_empresa);
        // console.log(cuadro_comparativo);

        btnPriceActive = event.target;
        // event.target.style.background = '#cf5c3f';

    itemSelected={
        'id_detalle_requerimiento': id_detalle_requerimiento,
        'codigo_requerimiento': cuadro_comparativo.cuadro_comparativo[index].codigo_requerimiento,
        'item_codigo': cuadro_comparativo.cuadro_comparativo[index].codigo,
        'item_descripcion': cuadro_comparativo.cuadro_comparativo[index].descripcion,
        'item_cantidad': cuadro_comparativo.cuadro_comparativo[index].cantidad,
        'unidad_medida': cuadro_comparativo.cuadro_comparativo[index].unidad_medida,
        'precio_referencial': cuadro_comparativo.cuadro_comparativo[index].precio_referencial,
        'fecha_entrega': cuadro_comparativo.cuadro_comparativo[index].fecha_entrega

    }
    
    buenaPro={
        'item_codigo': cuadro_comparativo.cuadro_comparativo[index].codigo,
        'item_descripcion': cuadro_comparativo.cuadro_comparativo[index].descripcion,
        'id_valorizacion_cotizacion': id_valorizacion_cotizacion,
        'id_proveedor': id_proveedor,
        'razon_social_proveedor': cuadro_comparativo.proveedores.filter(item => item.id_proveedor == id_proveedor)[0].razon_social,
        'documento_proveedor': cuadro_comparativo.proveedores.filter(item => item.id_proveedor == id_proveedor)[0].nombre_doc_identidad,
        'nro_documento_proveedor': cuadro_comparativo.proveedores.filter(item => item.id_proveedor == id_proveedor)[0].nro_documento,
        'id_empresa': id_empresa,
        'razon_social_empresa':cuadro_comparativo.cuadro_comparativo[index].proveedores.filter(item => item.id_proveedor == id_proveedor)[0].valorizacion.empresa.empresa_razon_social,
        'documento_empresa':cuadro_comparativo.cuadro_comparativo[index].proveedores.filter(item => item.id_proveedor == id_proveedor)[0].valorizacion.empresa.empresa_nombre_doc_identidad,
        'nro_documento_empresa':cuadro_comparativo.cuadro_comparativo[index].proveedores.filter(item => item.id_proveedor == id_proveedor)[0].valorizacion.empresa.empresa_nro_documento,
        'cantidad_valorizacion':cuadro_comparativo.cuadro_comparativo[index].proveedores.filter(item => item.id_proveedor == id_proveedor)[0].valorizacion.cantidad_cotizada,
        'unidad_valorizacion':cuadro_comparativo.cuadro_comparativo[index].proveedores.filter(item => item.id_proveedor == id_proveedor)[0].valorizacion.unidad_medida_cotizada,
        'precio_valorizacion':cuadro_comparativo.cuadro_comparativo[index].proveedores.filter(item => item.id_proveedor == id_proveedor)[0].valorizacion.precio_cotizado,
        'plazo_entrega':cuadro_comparativo.cuadro_comparativo[index].proveedores.filter(item => item.id_proveedor == id_proveedor)[0].valorizacion.plazo_entrega,
        'monto_descuento':cuadro_comparativo.cuadro_comparativo[index].proveedores.filter(item => item.id_proveedor == id_proveedor)[0].valorizacion.monto_descuento,
        'lugar_despacho':cuadro_comparativo.cuadro_comparativo[index].proveedores.filter(item => item.id_proveedor == id_proveedor)[0].valorizacion.lugar_despacho,
        'incluye_igv':cuadro_comparativo.cuadro_comparativo[index].proveedores.filter(item => item.id_proveedor == id_proveedor)[0].valorizacion.incluye_igv,
        'justificacion':''
    }

        $('#modal-buena_pro').modal({
            show: true,
            backdrop: 'static'
        });
        // console.log(itemSelected);
        // console.log(buenaPro);
        FillTablaBuenaProSelected(itemSelected,buenaPro);   
        
    }
    
function FillTablaBuenaProSelected(itemSelected,buenaPro){
    limpiarTabla('buena_pro');

    const table_buena_pro_body = document.getElementById('buena_pro').getElementsByTagName( 'tbody' )[0];

    let row = table_buena_pro_body.insertRow(0)
    const tdid = row.insertCell(0)
    tdid.innerHTML = buenaPro.id_valorizacion_cotizacion
    tdid.setAttribute('class', 'hidden')
    const tdempresa = row.insertCell(1)
    tdempresa.innerHTML = 'Empresa'
    tdempresa.setAttribute('class', 'text-left negrita')
    const tdDataEmpresa = row.insertCell(2)
    // tdDataEmpresa.innerHTML = 'Ok Computer ERIL RUC 483893912'
    tdDataEmpresa.innerHTML = buenaPro.razon_social_empresa+' '+buenaPro.documento_empresa + ' '+ buenaPro.nro_documento_empresa 
    tdDataEmpresa.setAttribute('class', 'text-center')
    tdDataEmpresa.setAttribute('colspan', '2')

    row = table_buena_pro_body.insertRow(1)
    const tdItem = row.insertCell(0)
    tdItem.innerHTML = 'Item'
    tdItem.setAttribute('class', 'text-left negrita')
    const tdDataItem = row.insertCell(1)
    // tdDataItem.innerHTML = '[201000050014] LAPICERO FC.035 COLOR ROJO Y NEGRO'
    tdDataItem.innerHTML = '['+ itemSelected.item_codigo +']'+ ' ' +itemSelected.item_descripcion
    tdDataItem.setAttribute('class', 'text-center')
    tdDataItem.setAttribute('colspan', '2')

    row = table_buena_pro_body.insertRow(2)
    const td = row.insertCell(0)
    td.innerHTML = ''
    const tdNumReq = row.insertCell(1)
    // tdNumReq.innerHTML = 'RQA-160101'
    tdNumReq.innerHTML = itemSelected.codigo_requerimiento
    tdNumReq.setAttribute('class', 'text-center text-info active negrita')
    const tdProve = row.insertCell(2)
    // tdProve.innerHTML = 'Maxima SAC'
    tdProve.innerHTML = buenaPro.razon_social_proveedor + ' '+ buenaPro.documento_proveedor + ' ' + buenaPro.nro_documento_proveedor
    tdProve.setAttribute('class', 'text-center text-success success negrita')

    row = table_buena_pro_body.insertRow(3)
    const tdUnidad = row.insertCell(0)
    tdUnidad.innerHTML = 'Unidad'
    tdUnidad.setAttribute('class', 'text-left negrita')
    const tdDataUnidad01 = row.insertCell(1)
    // tdDataUnidad01.innerHTML = 'Und.'
    tdDataUnidad01.innerHTML = itemSelected.unidad_medida
    tdDataUnidad01.setAttribute('class', 'text-center text-info active')
    const tdDataUnidad02 = row.insertCell(2)
    // tdDataUnidad02.innerHTML = 'Und.'
    tdDataUnidad02.innerHTML = buenaPro.unidad_valorizacion
    tdDataUnidad02.setAttribute('class', 'text-center text-success success')

    row = table_buena_pro_body.insertRow(4)
    const tdCantidad = row.insertCell(0)
    tdCantidad.innerHTML = 'Cantidad'
    tdCantidad.setAttribute('class', 'text-left negrita')
    const tdDataCantidad01 = row.insertCell(1)
    // tdDataCantidad01.innerHTML = '22'
    tdDataCantidad01.innerHTML = itemSelected.item_cantidad
    tdDataCantidad01.setAttribute('class', 'text-center text-info active')
    const tdDataCantidad02 = row.insertCell(2)
    // tdDataCantidad02.innerHTML = '22'
    tdDataCantidad02.innerHTML = buenaPro.cantidad_valorizacion
    tdDataCantidad02.setAttribute('class', 'text-center text-success success')
    
    row = table_buena_pro_body.insertRow(5)
    const tdPrecio = row.insertCell(0)
    tdPrecio.innerHTML = 'Precio'
    tdPrecio.setAttribute('class', 'text-left negrita')
    const tdDataPrecio01 = row.insertCell(1)
    // tdDataPrecio01.innerHTML = 'S/.640'
    tdDataPrecio01.innerHTML = itemSelected.precio_referencial
    tdDataPrecio01.setAttribute('class', 'text-center text-info active')
    const tdDataPrecio02 = row.insertCell(2)
    // tdDataPrecio02.innerHTML = 'S/.650'
    tdDataPrecio02.innerHTML = buenaPro.precio_valorizacion
    tdDataPrecio02.setAttribute('class', 'text-center text-success success')

    row = table_buena_pro_body.insertRow(6)
    const tdEntrega = row.insertCell(0)
    tdEntrega.innerHTML = 'Entrega'
    tdEntrega.setAttribute('class', 'text-left negrita')
    const tdDataEntrega01 = row.insertCell(1)
    // tdDataEntrega01.innerHTML = 'S/.640'
    tdDataEntrega01.innerHTML = itemSelected.fecha_entrega
    tdDataEntrega01.setAttribute('class', 'text-center text-info active')
    const tdDataEntrega02 = row.insertCell(2)
    // tdDataEntrega02.innerHTML = 'S/.650'
    tdDataEntrega02.innerHTML = buenaPro.plazo_entrega+' Días'
    tdDataEntrega02.setAttribute('class', 'text-center text-success success')

}

function addBuenaPro(event){
    event.preventDefault();
    buenaPro.justificacion = document.getElementById('justificacionBuenaPro').value;
    buenasPro.push(buenaPro);
    // console.log(buenasPro);
    printListBuenaPro(buenasPro);
    buenaPro={};
    itemSelected={};
    document.getElementById('justificacionBuenaPro').value = '';
    
    btnPriceActive.style.background = '#00a65a';
    
    $('#modal-buena_pro').modal('hide');    
}

function printListBuenaPro(buenaPro){
    // console.log(buenaPro);
    
    let panelBuenaPro = document.getElementById('panel-buena_pro');
    let btnAction = document.getElementById('btn-action-buena_pro');
    let html = '';
    if(buenaPro.length >0){
        html ='';
        buenaPro.forEach(function(buenaPro, index) {
        html += '<div class="panel panel-success">'+
                '<div class="panel-heading" role="tab" id="headingOne">'+
                '    <h4 class="panel-title">'+
                '        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse'+index+'" aria-expanded="true" aria-controls="collapse'+index+'">'+
                '            <strong>Proveedor: </strong>'+buenaPro.razon_social_proveedor+' <strong>Item:</strong>['+buenaPro.item_codigo+'] '+buenaPro.item_descripcion+' <strong>Cantidad:</strong> '+buenaPro.cantidad_valorizacion+' <strong>Precio:</strong> '+buenaPro.precio_valorizacion+''+
                '        </a>'+
                '        <button type="button" class="close" data-dismiss="alert" aria-label="Close" onClick="EliminarBuenaPro('+index+');"><span aria-hidden="true">&times;</span></button>'+
                '    </h4>'+
                '</div>'+
                '<div id="collapse'+index+'" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">'+
                '    <div class="panel-body  panel-default">'+
                '        <table class="mytable table table-condensed table-bordered table-okc-view">'+
                '            <thead>'+
                '                <th>Proveedor</th>'+
                '                <th>Unidad</th>'+
                '                <th>Cantidad</th>'+
                '                <th>Precio</th>'+
                '                <th>Justificacion</th>'+
                '                <th>Empresa</th>'+
                '            </thead>'+
                '            <tbody>'+
                '                <tr>'+
                '                    <td>'+buenaPro.razon_social_proveedor+' '+buenaPro.documento_proveedor+' '+buenaPro.nro_documento_proveedor+'</td>'+
                '                    <td>'+buenaPro.unidad_valorizacion+'</td>'+
                '                    <td>'+buenaPro.cantidad_valorizacion+'</td>'+
                '                    <td>'+buenaPro.precio_valorizacion+'</td>'+
                '                    <td>'+buenaPro.justificacion+'</td>'+
                '                    <td>'+buenaPro.razon_social_empresa+'</td>'+
                '                </tr>'+
                '            </tbody>'+
                '        </table>'+
                '    </div>'+
                '</div>'+
                '</div>';
        });

        let htmlBtnGuardarBuenaPro = '<div class="row">'+
                                        '   <div class="col-md-12 text-center">'+
                                        '       <button type="submit" class="btn btn-success btn-flat" onClick="guardarBuenaPro();" >Guardar Buena Pro</button>'+
                                        '   </div>'+
                                        '</div>';
        btnAction.innerHTML= htmlBtnGuardarBuenaPro;
                            
    }

    panelBuenaPro.innerHTML= html;
}

function EliminarBuenaPro(index){
    
    var ask = confirm('¿Desea eliminar esta buena pro?');
    if (ask == true){
        // console.log(buenasPro); 
        let id_valorizacion = buenasPro[index].id_valorizacion_cotizacion;
        if(id_valorizacion > 0){
            $.ajax({
                type: 'PUT',
                url: '/logistica/cuadro_comparativo/eliminar_buena_pro/'+id_valorizacion,
                dataType: 'JSON',
                success(response) {
                    // console.log(response);
                    
                    if(response >0){
                        buenasPro.splice(index)
                        alert("Se eliminó la buena Pro");
                        // console.log(buenasPro); 
                        printListBuenaPro(buenasPro);
                    }else{
                        alert("no se puedo elimnar")
                    }

                },
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR)
                console.log(textStatus)
                console.log(errorThrown)
            })
        }else{
            alert("No se puedo eliminar");
        }


        return false;
    }else{
        return false;
    }
}

function guardarBuenaPro(){
    var myformData = new FormData();        
    myformData.append('buenasPro', JSON.stringify(buenasPro));

    // console.log(buenasPro);
    if(buenasPro.length > 0){

        $.ajax({
            type: 'POST',
            url: '/logistica/cuadro_comparativo/guardar_buenas_pro',
            processData: false,
            contentType: false,
            cache: false,
            data: myformData,
            enctype: 'multipart/form-data',
            beforeSend: function(){
                $(document.body).append('<span class="loading"><div></div></span>');
            },
            success: function(response){
                console.log(response);
                $('.loading').remove();
                if (response > 0){
                    alert("Se guardo Correctamente la Buenas Pro");
                }else{
                    alert("Error al guardar");
                }
            }
        });
        
    }else{
        alert("no hay Buena Pro para guardar");
    }

}

// generar formato excel de cuadro comparativo
function exportarCuadroComparativo(){

    var id_grupo = $('[name=id_grupo_cotizacion]').val();
    window.open('/logistica/cuadro_comparativo/exportar_excel/'+id_grupo);

}