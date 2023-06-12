let data = [];
let data_item=[];
var adjuntos=[];
var id_detalle_requerimiento=0;
var obs=false;
var gobal_observacion_requerimiento=[];

$(function(){
    var idGral = localStorage.getItem('idGral');

    if (idGral != null){
        mostrar_requerimiento(idGral);
        localStorage.clear();
        changeStateButton('historial');
    }
    resizeSide();

    $('#form-obs-sustento').on('submit', function(){
        var data = $(this).serialize();
        var ask = confirm('¿Desea guardar el sustento?');
        if (ask == true){
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '/logistica/guardar_sustento',
                data: data,
                beforeSend: function(){
                    $(document.body).append('<span class="loading"><div></div></span>');
                },
                success: function(response){
                    $('.loading').remove();
                    if (response == 'ok_req') {
                        alert('Se agregó sustento al Requerimiento');
                        $('#modal-sustento').modal('hide');
                    }else if (response == 'ok_det') {
                        alert('Se agregó sustento al detalle del requerimiento');
                        $('#modal-sustento').modal('hide');
                    }
                }
            });
            return false;
        }else{
            return false;
        }
    });
});

function nuevo_req(){
    data_item=[];
    data=[];
    $('#form-requerimiento')[0].reset();
    $('#body_detalle_requerimiento').html('<tr id="default_tr"><td></td><td colspan="7"> No hay datos registrados</td></tr>');
}



function disabledControl(element,value){   
    // console.log("disable control"); 
    var i;
    for (i = 0; i < element.length; i++) {
        if(value === false){
            element[i].removeAttribute("disabled");
            element[i].classList.remove("disabled");

        }else{
            element[i].setAttribute("disabled","true");
        }
    }
    return null;
}

function handleKeyDown(event){
    const key = event.key;
    if(key == 'Backspace' || key == 'Delete'){
        $('[name=id_item]').val(0);
        $('[name=codigo_item]').val('SIN CODIGO');
        $('[name=id_producto]').val(0);
        $('[name=id_servicio]').val(0);
        $('[name=id_equipo]').val(0);
    }

}

function handleKeyPress(event){    
    $('[name=id_item]').val(0);
    $('[name=codigo_item]').val('SIN CODIGO');
    $('[name=id_producto]').val(0);
    $('[name=id_servicio]').val(0);
    $('[name=id_equipo]').val(0);

}

function modalRequerimiento(){
    $('#modal-requerimiento').modal({
        show: true,
        backdrop: 'static'
    });
    listarRequerimiento();
}

function listarRequerimiento() {
    var vardataTables = funcDatatables();
    $('#listaRequerimiento').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'processing': true,
        "bDestroy": true,
        'ajax': '/logistica/requerimientos',
        'columns': [
            {'data': 'id_requerimiento'},
            {'data': 'codigo'},
            {'data': 'tipo_req_desc'},
            {'data': 'alm_req_concepto'},
            {'data': 'adm_grupo_descripcion'},
            {'data': 'area_desc'},
            {'data': 'usuario'},
            {'data': 'rrhh_rol_concepto'},
            {'data': 'fecha_requerimiento'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
        'order': [
            [2, 'asc']
        ]
    });
}
$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaRequerimiento tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaRequerimiento').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        $('.modal-footer #id_requerimiento').text(idTr);

        
    });
});
function selectRequerimiento(){
    // console.log("selectRequerimiento");
    var id = $('#id_requerimiento').text();
    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');
        clearForm(form);
        changeStateButton('historial');
        mostrar_requerimiento(id);
        // console.log($(":file").filestyle('disabled'));
    $('#modal-requerimiento').modal('hide');
}

function get_requerimiento_por_codigo(){
    var codigo = $('[name=codigo]').val();
    mostrar_requerimiento(codigo);
}


function mostrar_requerimiento(IdorCode){
    // console.log("mostrar_requeriniento");

    if (! /^[a-zA-Z0-9]+$/.test(IdorCode)) { // si tiene texto
        url = '/logistica/requerimiento/0/'+IdorCode;
    }else{
        url = '/logistica/requerimiento/'+IdorCode+'/0';
    }

    let items={};
    $(":file").filestyle('disabled', false);
    data_item = [];
    baseUrl = url;
    $.ajax({
        type: 'GET',
        // headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            data = response;
            // console.log(response);
            if(response['requerimiento'] !== undefined){

                $('[name=id_requerimiento]').val(response['requerimiento'][0].id_requerimiento);
                $('[name=codigo]').val(response['requerimiento'][0].codigo);
                $('[name=concepto]').val(response['requerimiento'][0].concepto);
                $('[name=fecha_requerimiento]').val(response['requerimiento'][0].fecha_requerimiento);
                $('[name=prioridad]').val(response['requerimiento'][0].id_prioridad);
                $('[name=empresa]').val(response['requerimiento'][0].id_empresa);
                $('[name=sede]').val(response['requerimiento'][0].id_sede);
                $('[name=id_area]').val(response['requerimiento'][0].id_area);
                $('[name=id_grupo]').val(response['requerimiento'][0].id_grupo);
                $('[name=nombre_area]').val(response['requerimiento'][0].area_descripcion);
                $('[name=moneda]').val(response['requerimiento'][0].id_moneda);
                // $('[name=tipo]').val(response['requerimiento'][0].id_tipo_requerimiento);
                $('[name=id_proyecto]').val(response['requerimiento'][0].id_proyecto);
                $('[name=codigo_proyecto]').val(response['requerimiento'][0].codigo_presupuesto);
                $('[name=descripcion_proyecto]').val(response['requerimiento'][0].descripcion_proyecto);
                $('[name=cliente]').val(response['requerimiento'][0].descripcion_cliente);
                $('[name=id_presupuesto]').val(response['requerimiento'][0].id_presupuesto);
                $('[name=presupuesto]').val(response['requerimiento'][0].importe_presupuesto);

                if(response['requerimiento'][0].id_grupo ===3){
                        document.getElementById('section-proyectos').setAttribute('class', 'row')
                }
                /* detalle */
                var detalle_requerimiento = response['det_req'];
                // console.log(detalle_requerimiento);                
                for (x=0; x<detalle_requerimiento.length; x++){
                    let adjunto=[];
                        items ={
                        'id_item':detalle_requerimiento[x].id_item,
                        'id_tipo_item':detalle_requerimiento[x].id_tipo_item,
                        'id_producto':detalle_requerimiento[x].id_producto,
                        'id_servicio':detalle_requerimiento[x].id_servicio,
                        'id_equipo':detalle_requerimiento[x].id_equipo,
                        'id_requerimiento':response['requerimiento'][0].id_requerimiento,
                        'id_detalle_requerimiento':detalle_requerimiento[x].id_detalle_requerimiento,
                        'cod_item':detalle_requerimiento[x].codigo_item,
                        'des_item':detalle_requerimiento[x].descripcion?detalle_requerimiento[x].descripcion:detalle_requerimiento[x].descripcion_adicional, 
                        'id_unidad_medida':detalle_requerimiento[x].id_unidad_medida,
                        'unidad':detalle_requerimiento[x].unidad_medida,
                        'cantidad':detalle_requerimiento[x].cantidad,
                        'precio_referencial':detalle_requerimiento[x].precio_referencial,
                        'fecha_entrega':detalle_requerimiento[x].fecha_entrega,
                        'lugar_entrega':detalle_requerimiento[x].lugar_entrega?detalle_requerimiento[x].lugar_entrega:"",
                        'id_partida':detalle_requerimiento[x].id_partida,
                        'cod_partida':detalle_requerimiento[x].codigo_partida,
                        'des_partida':detalle_requerimiento[x].descripcion_partida,
                        'obs':detalle_requerimiento[x].obs,
                        'estado':detalle_requerimiento[x].estado
                    };
                        for(j=0; j<detalle_requerimiento[x].adjunto.length; j++){
                        adjunto.push({ 'archivo_id_archivo':detalle_requerimiento[x].adjunto[j].archivo_id_archivo,
                            'archivo_archivo':detalle_requerimiento[x].adjunto[j].archivo_archivo,
                            'archivo_estado':detalle_requerimiento[x].adjunto[j].archivo_estado,
                            'archivo_id_detalle_requerimiento':detalle_requerimiento[x].adjunto[j].id_detalle_requerimiento
                            });
                        }
                        items['adjunto']=adjunto;
                        data_item.push(items);
                    }
                    // fill_table_detalle_requerimiento(data_item);
                    // console.log(data_item);
                    
                    llenar_tabla_detalle_requerimiento(data_item);
                    // desbloquear el imprimir requerimiento
                    var btnImprimirRequerimientoPdf = document.getElementsByName("btn-imprimir-requerimento-pdf");
                    disabledControl(btnImprimirRequerimientoPdf,false);

                // get observaciones  
                let htmlObservacionReq = '';
                    // console.log(response.observacion_requerimiento);
                    if(response.observacion_requerimiento.length > 0){
                        gobal_observacion_requerimiento = response.observacion_requerimiento;
                        response.observacion_requerimiento.forEach(element => {
                            
                    //         htmlObservacionReq +=   '<div class="col-sm-12">'+
                    //     '<div class="alert alert-warning alert-dismissible" role="alert">'+
                    //         '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
                    //         '<strong>Observación</strong> </em>'+element.nombre_completo+'<em> '+element.descripcion+
                    //     '</div>'+
                    // '</div>'; 
                            htmlObservacionReq +='<div class="col-sm-12">'+
                        '<blockquote style="border-left: 5px solid #f1c907;">'+
                        '<p>'+element.descripcion+'</p>'+
                        '<footer><cite title="Source Title">'+element.nombre_completo+'</cite></footer>'+
                        '</blockquote>'+
                    '</div>'; 
                        });
                    }
                    // if(response.observacion_requerimiento != ''){
                    //     htmlObservacionReq =   '<div class="col-sm-12">'+
                    //     '<div class="alert alert-warning alert-dismissible" role="alert">'+
                    //         '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
                    //         '<strong>Observación Requerimiento</strong> '+response.observacion_requerimiento+
                    //     '</div>'+
                    // '</div>';
                    // }

                let obsReq = document.getElementById('observaciones_requerimiento');
                obsReq.innerHTML = '<div class="col-sm-12"><legend><h2>OBSERVACIONES</h2></legend></div></br>'+htmlObservacionReq;

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

//imprimir requerimiento pdf
function ImprimirRequerimientoPdf(){
    var id = document.getElementsByName("id_requerimiento")[0].value;
    window.open('/logistica/imprimir-requerimiento-pdf/'+id+'/0');
    
    // baseUrl = '/logistica/imprimir-requerimiento-pdf/'+id+'/0';
    // $.ajax({
    //     type: 'GET',
    //     url: baseUrl,
    //     // dataType: 'JSON',
    //     success: function(response){  
            
    //     }
    // }).fail( function( jqXHR, textStatus, errorThrown ){
    //     console.log(jqXHR);
    //     console.log(textStatus);
    //     console.log(errorThrown);
    // });
}

// modal area grupos
function modal_area(){
    var id_emp = $('[name=empresa]').val();
    if(id_emp >0){

        $('#modal-empresa-area').modal({
            show: true,
            backdrop: 'static'
        });
        cargarEstOrg(id_emp);
    }else{
        alert("Debe seleccionar  la empresa");
        $('[name=id_empresa]').focus();
    }
    
}

// function areaSelectModal(sede, grupo, area, text){
//     // alert('sede:'+sede+' grupo:'+grupo+' area:'+area);
//     $('[name=id_grupo]').val(grupo);
//     $('[name=id_area]').val(area);
//     $('[name=nombre_area]').val(text);
//     $('#modal-empresa-area').modal('hide');
    
//     if(grupo === 3){
//             document.getElementById('section-proyectos').setAttribute('class', 'row')
//     }
// }


// function getIdGrupo(){
//     var area = document.getElementById("area");
//     var id_grupo = area.options[area.selectedIndex].parentNode.dataset.dataIdGrupo;
//     // console.log(id_grupo);
//     if(id_grupo !== undefined){
//         document.getElementsByName("id_grupo")[0].value = id_grupo;
//     }else{
//         document.getElementsByName("id_grupo")[0].value = 0;
//     }
// }

function limpiarFormularioDetalleRequerimiento(){
    $('[name=estado]').val('');
    $('[name=id_item]').val('');
    $('[name=id_producto]').val('');
    $('[name=id_servicio]').val('');
    $('[name=id_equipo]').val('');
    $('[name=id_tipo_item]').val('');
    $('[name=id_detalle_requerimiento]').val('');
    $('[name=codigo_item]').val('');
    $('[name=descripcion_item]').val('');
    $('[name=unidad_medida_item]').val('');
    $('[name=cantidad_item]').val('');
    $('[name=precio_ref_item]').val('');
    $('[name=fecha_entrega_item]').val('');
    $('[name=lugar_entrega_item]').val('');
    $('[name=id_partida]').val('');
    $('[name=cod_partida]').val('');
    $('[name=des_partida]').val('');
}

function agregarItem(){
    var table = document.getElementById("ListaDetalleRequerimiento");
    var len = table.querySelectorAll('tr').length;
    for (var i=0; i < len; i++){
    // console.log(table.querySelectorAll('tr')[i].getAttribute('id'));
    
        if ( table.querySelectorAll('tr')[i].getAttribute('id') == "default_tr"){
        // table.querySelectorAll('tr')[i].setAttribute('class', 'yourID')
            table.deleteRow(i);
        }
    }
    let item = get_data_detalle_requerimiento();
    // console.log(item);
    // verficar codigo de item exista para poder ser agregado ////////
    if(item.cod_item ==="" || item.cod_item ===null || item.cod_item ===undefined ){
        alert("No puede ingresar un item en blanco");
        return null;
    }
    /////////////////////////////////////////


        data_item.push(item);
        // console.log(item.id_producto);
        // console.log(item.id_servicio);
        // console.log(item.id_equipo);
        // let descripcion_unidad = '';
        // if(item.id_producto > 0){
        //     descripcion_unidad = item.unidad;
        // }else if(item.id_servicio > 0){
        //     descripcion_unidad = "Servicio";
        // }else if(item.id_equipo >0){
        //     descripcion_unidad = "Equipo";
        // }else{
        //     descripcion_unidad ='--';
        // }

        llenar_tabla_detalle_requerimiento(data_item);
    // print data_item in table
    // var row = table.insertRow(data_item.length);
    // row.insertCell(0).innerHTML = item.cod_item?item.cod_item:'0';
    // row.insertCell(1).innerHTML = item.des_item?item.des_item:'-';
    // row.insertCell(2).innerHTML = descripcion_unidad;
    // row.insertCell(3).innerHTML = item.cantidad?item.cantidad:'0';
    // row.insertCell(4).innerHTML = item.precio_referencial?item.precio_referencial:'0';
    // row.insertCell(5).innerHTML = item.fecha_entrega?item.fecha_entrega:null;
    // row.insertCell(6).innerHTML = item.lugar_entrega?item.lugar_entrega:'-';
    // row.insertCell(7).innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
    // '<button class="btn btn-secondary btn-sm" id="basic-addon3" data-toggle="tooltip" title="Editar" onClick="" disabled=""><i class="fas fa-edit fa-x2"></i>'+
    // '</button></div>';

    limpiarFormularioDetalleRequerimiento();
}

 
function llenar_tabla_detalle_requerimiento(data_item){
    // console.log(data_item);
    
    limpiarTabla('ListaDetalleRequerimiento');
    // limpiando
    htmls ='<tr></tr>';
    $('#ListaDetalleRequerimiento tbody').html(htmls);
    // mejorarndo recorrer data_item para llenar tabla detalle requerimiento...
    var table = document.getElementById("ListaDetalleRequerimiento");
    // console.log("data ",data_item);
    // console.log("length ",data_item.length);
    
    let widthGroupBtnAction='auto';
    let classHiden='';
    let classDisabled='';
    let classBtnAdjuntos ='';
    for(var a=0;a < data_item.length;a++){
        if(data_item[a].estado >0){
            
        if(data_item[a].obs === true){
                classHiden='';
                classDisabled='disabled';
                widthGroupBtnAction='120';
            }else if(data_item[a].obs === false || data_item[a].obs === null || data_item[a].obs === ''){
                classHiden='hidden'; 
                classDisabled='';
            }

            
            if(!data_item[a].id_detalle_requerimiento >0){
                classBtnAdjuntos='disabled';
            }

            var row = table.insertRow(a+1);
            let descripcion_unidad = '';
    
            if(data_item[a].id_producto > 0){
                descripcion_unidad = data_item[a].unidad;
            }else if(data_item[a].id_servicio > 0){
                descripcion_unidad = "Servicio";
            }else if(data_item[a].id_equipo >0){
                descripcion_unidad = "Equipo";
            }else{
                descripcion_unidad = data_item[a].unidad;
            }
            row.insertCell(0).innerHTML = data_item[a].id_item?data_item[a].id_item:'0';
            row.insertCell(1).innerHTML = data_item[a].cod_item?data_item[a].cod_item:'0';
            row.insertCell(2).innerHTML = data_item[a].des_item?data_item[a].des_item:'-';
            row.insertCell(3).innerHTML = descripcion_unidad;
            row.insertCell(4).innerHTML = data_item[a].cantidad?data_item[a].cantidad:'0';
            row.insertCell(5).innerHTML = data_item[a].precio_referencial?data_item[a].precio_referencial:'0';
            row.insertCell(6).innerHTML = data_item[a].fecha_entrega?data_item[a].fecha_entrega:null;
            row.insertCell(7).innerHTML = data_item[a].lugar_entrega?data_item[a].lugar_entrega:'-';

            var tdBtnAction = row.insertCell(8);
            // tdBtnAction.className = classHiden;
            tdBtnAction.setAttribute('width',widthGroupBtnAction);
            tdBtnAction.innerHTML = '<div class="btn-group btn-group-sm" role="group" aria-label="Second group">'+
            '<button class="btn btn-secondary btn-sm '+classHiden+' '+classDisabled+'" name="btnEditarItem" data-toggle="tooltip" title="Editar" onClick="detalleRequerimientoModal(event, '+a+');" ><i class="fas fa-edit"></i></button>'+
            '<button class="btn btn-danger btn-sm '+classHiden+' '+classDisabled+'" name="btnEliminarItem" data-toggle="tooltip" title="Eliminar" onclick="eliminarItemDetalleRequerimiento(event, '+a+');" ><i class="fas fa-trash-alt"></i></button>'+
            '<button class="btn btn-primary btn-sm '+ classBtnAdjuntos+'" name="btnAdjuntarArchivos" data-toggle="tooltip"title="Adjuntos" onClick="archivosAdjuntosModal(event, '+a+');"><i class="fas fa-paperclip"></i></button>'+
            '</div>';

        }
    }

}


function get_data_requerimiento(){
    let requerimiento = {
        'id_requerimiento':$('[name=id_requerimiento]').val(),
        'codigo':$('[name=codigo]').val(),
        'concepto':$('[name=concepto]').val(),
        'fecha_requerimiento':$('[name=fecha_requerimiento]').val(),
        'id_prioridad':$('[name=prioridad]').val(),
        'id_empresa':$('[name=empresa]').val(),
        'id_grupo':$('[name=id_grupo]').val(),
        'id_area':$('[name=id_area]').val(),
        'nombre_area':$('[name=nombre_area]').val(),
        'id_moneda':$('[name=moneda]').val(),
        // 'id_tipo_requerimiento':$('[name=tipo]').val(),
        'id_proyecto':$('[name=id_proyecto]').val(),
        'descripcion_proyecto':$('[name=descripcion_proyecto]').val(),
        'codigo_proyecto':$('[name=codigo_proyecto]').val(),
        'descripcion_cliente':$('[name=cliente]').val(),
        'descripcion_presupuesto':$('[name=presupuesto]').val(),
        'id_presupuesto':$('[name=id_presupuesto]').val()
    };

return requerimiento;
}

function get_data_detalle_requerimiento(){
    // if($('[name=unidad_medida_item]').val() ==false){
    //     alert("nulo");
    // }else{
        
    //     alert("otra cosaa");
    // }
    var id_item = $('[name=id_item]').val();
    var id_tipo_item = $('[name=id_tipo_item]').val();
    var id_producto = $('[name=id_producto]').val();
    var id_servicio = $('[name=id_servicio]').val();
    var id_equipo = $('[name=id_equipo]').val();
    var id_detalle_requerimiento = $('[name=id_detalle_requerimiento]').val();
    var cod_item = $('[name=codigo_item]').val();
    var des_item = $('[name=descripcion_item]').val();
    // var id_unidad_medida = $('[name=unidad_medida_item]').val() !=="" ?$('[name=unidad_medida_item]').val():0;
    var id_unidad_medida = $('[name=unidad_medida_item]').val();
    // var und = document.getElementsByName("unidad_medida_item")[0];
    // var und_text = und.options[und.selectedIndex].text;   
    var und_text = $('[name=unidad_medida_item]').find('option:selected').text();
    var cantidad = $('[name=cantidad_item]').val();
    var precio_referencial = $('[name=precio_ref_item]').val();
    var fecha_entrega = $('[name=fecha_entrega_item]').val();
    var lugar_entrega = $('[name=lugar_entrega_item]').val();
    var id_partida = $('[name=id_partida]').val();
    var cod_partida = $('[name=cod_partida]').val();
    var des_partida = $('[name=des_partida]').val();
    if($('[name=estado]').val() === ""){
        var estado = 1;
    }else{
        var estado = $('[name=estado]').val();
        
    }

    let item = {
        'id_item':parseInt(id_item),
        'id_tipo_item':parseInt(id_tipo_item),
        'id_producto':parseInt(id_producto),
        'id_servicio':parseInt(id_servicio),
        'id_equipo':parseInt(id_equipo),
        'id_detalle_requerimiento':parseInt(id_detalle_requerimiento),
        'cod_item':cod_item,
        'des_item':des_item,
        'id_unidad_medida':parseInt(id_unidad_medida),
        'unidad':und_text,
        'cantidad':parseFloat(cantidad),
        'precio_referencial':parseFloat(precio_referencial),
        'fecha_entrega':fecha_entrega,
        'lugar_entrega':lugar_entrega,
        'id_partida':parseInt(id_partida),
        'cod_partida':cod_partida,
        'des_partida':des_partida,
        'estado':parseInt(estado)
        };
        return item;
}

function aceptarCambiosItem(){
    var id_det = $('[name=id_detalle_requerimiento]').val();
    var id_req = $('[name=id_requerimiento]').val();
    let item = get_data_detalle_requerimiento();
    if(indice >= 0){
        update_data_item(indice, item);
        $('#modal-detalle-requerimiento').modal('hide');
        // alert(id_req+'-'+id_det);
        if(id_req > 0){

            openSustento(id_req, id_det, 'det');
        }
    }else{
        alert("El indice no es numérico");
    }
}

function update_data_item(indice,item){
    data_item[indice]=item;
    llenar_tabla_detalle_requerimiento(data_item);
}

function eliminarItemDetalleRequerimiento(event,index){
    event.preventDefault();

    if(index  !== undefined){ // editando item
        let item = data_item[index]; 
        item.estado=0;
       alert("Se cambio el estado del Item, guarde el Requerimiento para salvar los cambios");
       llenar_tabla_detalle_requerimiento(data_item);

    }

}
// modal detalle 
var indice='';
function detalleRequerimientoModal(event,index){
    $('#form-detalle-requerimiento')[0].reset();
    event.preventDefault();
    var btnAceptarCambio = document.getElementsByName("btn-aceptar-cambio");
    var btnAgregarCambio = document.getElementsByName("btn-agregar-item");
    if(index  !== undefined){ // editando item
        let item = data_item[index]; 
        // console.log(item.id_detalle_requerimiento);
        // console.log(gobal_observacion_requerimiento);
        if(gobal_observacion_requerimiento.length >0 && item.id_detalle_requerimiento >0){
            gobal_observacion_requerimiento.map((element,index)=>{
                element.obs_item.map((obs_det,i)=>{
                    if(obs_det.id_detalle_requerimiento == item.id_detalle_requerimiento){
                        // console.log(obs_det.descripcion);
                        $('#obs_det').text('Observacion: '+obs_det.descripcion); 
                    }
                })
            })
        }
      
        indice = index;       
        fill_input_detalle_requerimiento(item);
        controlUnidadMedida();
        disabledControl(btnAgregarCambio,true);
        disabledControl(btnAceptarCambio,false);
    }else{
        disabledControl(btnAgregarCambio,false);
        disabledControl(btnAceptarCambio,true);
    }
    
    $('#modal-detalle-requerimiento').modal({
        show: true,
        backdrop: 'static'
    });
}

function fill_input_detalle_requerimiento(item){
    $('[name=id_tipo_item]').val(item.id_tipo_item);
    $('[name=id_item]').val(item.id_item);
    $('[name=id_producto]').val(item.id_producto);
    $('[name=id_servicio]').val(item.id_servicio);
    $('[name=id_equipo]').val(item.id_equipo);
    $('[name=id_detalle_requerimiento]').val(item.id_detalle_requerimiento);
    $('[name=codigo_item]').val(item.cod_item);
    $('[name=descripcion_item]').val(item.des_item);
    $('[name=unidad_medida_item]').val(item.id_unidad_medida);
    $('[name=cantidad_item]').val(item.cantidad);
    $('[name=precio_ref_item]').val(item.precio_referencial);
    $('[name=fecha_entrega_item]').val(item.fecha_entrega);
    $('[name=lugar_entrega_item]').val(item.lugar_entrega);
    $('[name=id_partida]').val(item.id_partida);
    $('[name=cod_partida]').val(item.cod_partida);
    $('[name=des_partida]').val(item.des_partida);
    $('[name=estado]').val(item.estado);
}

//modal adjunta archivos
function archivosAdjuntosModal(event,index){
    event.preventDefault();
    $('#modal-adjuntar-archivos-requerimiento').modal({
        show: true,
        backdrop: 'static'
    });

    if(data_item.length >0){
        id_detalle_requerimiento = data_item[index].id_detalle_requerimiento;
        obs = data_item[index].obs;
        $('[name=id_requerimiento]').val(data_item[index].id_requerimiento);
            // console.log('id_detalle_requerimiento',id_detalle_requerimiento);
            // console.log(data_item[index]);
        if(data_item[index].id_detalle_requerimiento >0){ // es un requerimiento traido de la base de datos
            get_data_archivos_adjuntos(data_item[index].id_detalle_requerimiento);
            
        }else{ //no existe id_detalle_requerimiento => es un nuevo requerimiento
            alert("es nuevo requerimiento.... debe guardar el requerimiento primero");
            
            
        }
    }
    
}


function get_data_archivos_adjuntos(index){
    adjuntos=[];
    limpiarTabla('listaArchivos');
    baseUrl = '/logistica/mostrar-archivos-adjuntos/'+index;
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

let only_adjuntos=[];
function agregarAdjunto(event){ //agregando nuevo archivo adjunto
    let archivo ={
        id_archivo: 0,
        id_detalle_requerimiento: id_detalle_requerimiento,
        archivo:event.target.files[0].name,
        fecha_registro: new Date().toJSON().slice(0, 10),
        estado: 1
        // file:event.target.files[0]
    }
    let only_file = event.target.files[0]
    adjuntos.push(archivo);
    only_adjuntos.push(only_file);
    // console.log("agregar adjunto");
    // console.log(adjuntos);

    $('#listaArchivos tbody').html(htmls);
    var table = document.getElementById("listaArchivos");
    var indicadorTd='';
    for(var a=0;a < adjuntos.length;a++){
        var row = table.insertRow(-1);

        if(adjuntos[a].id_archivo ==0){
            indicadorTd="green"; // si es nuevo
        }
        var tdIdArchivo =  row.insertCell(0);
        tdIdArchivo.setAttribute('class','hidden');
        tdIdArchivo.innerHTML = adjuntos[a].id_archivo?adjuntos[a].id_archivo:'0';
        var tdIdDetalleReq =  row.insertCell(1);
        tdIdDetalleReq.setAttribute('class','hidden');
        tdIdDetalleReq.innerHTML = 0;
        var tdNumItem = row.insertCell(2);
        tdNumItem.innerHTML = a+1;
        var tdNameFile = row.insertCell(3);
        tdNameFile.innerHTML = adjuntos[a].archivo?adjuntos[a].archivo:'-';
        tdNameFile.setAttribute('class',indicadorTd);
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
}

function guardarAdjuntos(){
    
    // console.log(obs);
    let id_req = $('[name=id_requerimiento]').val();
    if(id_req < 0){
        alert("error 790: GuardarAdjunto");
    }
    
    console.log(adjuntos);
    console.log(only_adjuntos);
    let id_detalle_requerimiento = adjuntos[0].id_detalle_requerimiento;

    const onlyNewAdjuntos = adjuntos.filter(id => id.id_archivo == 0); // solo enviar los registros nuevos
 

    if(obs == true){ // esta observado el adjunto
        
        var myformData = new FormData();        
        // myformData.append('archivo_adjunto', JSON.stringify(adjuntos));
        for(let i=0;i<only_adjuntos.length;i++){
            myformData.append('only_adjuntos[]', only_adjuntos[i]);
            
        }
        myformData.append('detalle_adjuntos', JSON.stringify(onlyNewAdjuntos));
        myformData.append('id_detalle_requerimiento', id_detalle_requerimiento);
    
        baseUrl = '/logistica/guardar-archivos-adjuntos';
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
                    only_adjuntos=[];
                    get_data_archivos_adjuntos(id_detalle_requerimiento);

                    openSustento(id_req, id_detalle_requerimiento, 'det');

                }
            }
        }).fail( function(jqXHR, textStatus, errorThrown){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });  
    }else{
        var myformData = new FormData();        
        // myformData.append('archivo_adjunto', JSON.stringify(adjuntos));
        for(let i=0;i<only_adjuntos.length;i++){
            myformData.append('only_adjuntos[]', only_adjuntos[i]);
            
        }
        myformData.append('detalle_adjuntos', JSON.stringify(onlyNewAdjuntos));
        myformData.append('id_detalle_requerimiento', id_detalle_requerimiento);
    
        baseUrl = '/logistica/guardar-archivos-adjuntos';
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
                    only_adjuntos=[];
                    get_data_archivos_adjuntos(id_detalle_requerimiento);
                }
            }
        }).fail( function(jqXHR, textStatus, errorThrown){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });  
    }

   
}

function limpiarTabla(idElement){
    console.log("limpiando tabla....");
    var table = document.getElementById(idElement);
    for(var i = table.rows.length - 1; i > 0; i--)
    {
        table.deleteRow(i);
    }
    return null;
}

// modal catalogo items
function catalogoItemsModal(){   
    $('#modal-catalogo-items').modal({
        show: true,
        backdrop: 'static'
    });
    listarItems();
}

$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaItems tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaItems').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idItem = $(this)[0].children[0].innerHTML;
        var idProd = $(this)[0].children[1].innerHTML;
        var idServ = $(this)[0].children[2].innerHTML;
        var idEqui = $(this)[0].children[3].innerHTML;
        $('.modal-footer #id_item').text(idItem);
        $('.modal-footer #id_producto').text(idProd);
        $('.modal-footer #id_servicio').text(idServ);
        $('.modal-footer #id_equipo').text(idEqui);
    });
});

function listarItems() {
    var vardataTables = funcDatatables();
    $('#listaItems').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'processing': true,
        "bDestroy": true,
        'ajax': '/logistica/mostrar_items',
        'columns': [
            {'data': 'id_item'},
            {'data': 'id_producto'},
            {'data': 'id_servicio'},
            {'data': 'id_equipo'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'unidad_medida_descripcion'},
            {'data': 'stock'}
        ],
        'columnDefs': [
            { 'aTargets': [0], 'sClass': 'invisible'},
            { 'aTargets': [1], 'sClass': 'invisible'},
            { 'aTargets': [2], 'sClass': 'invisible'},
            { 'aTargets': [3], 'sClass': 'invisible'}
                    ],
        'order': [
            [2, 'asc']
        ]
    });
}
function controlUnidadMedida(){
    var id_tipo_item = document.getElementsByName("id_tipo_item")[0].value;    
    var id_servicio = document.getElementsByName("id_servicio")[0].value;    
    var selectUnidadMedida = document.getElementsByName("unidad_medida_item");    
    // console.log(id_tipo_item);
    // console.log(id_servicio);
    if(id_tipo_item == 1){
        disabledControl(selectUnidadMedida,false);
    }
    if(id_tipo_item  == 2){
        disabledControl(selectUnidadMedida,true);

    }
    if(id_tipo_item == 3){
        disabledControl(selectUnidadMedida,true);
    }
}

function selectItem(){
    var id_item = $('.modal-footer #id_item').text();
    var id_producto = $('.modal-footer #id_producto').text();
    var id_servicio = $('.modal-footer #id_servicio').text();
    var id_equipo = $('.modal-footer #id_equipo').text();
    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');
    mostrar_item(id_item);
    var selectUnidadMedida = document.getElementsByName("unidad_medida_item");    
    // console.log(id_item);
    // console.log(id_producto);
    // console.log(id_servicio);
    // console.log(id_equipo);
    if(id_producto > 0){
        disabledControl(selectUnidadMedida,false);
        document.getElementsByName("id_tipo_item")[0].value = 1;
    }
    if(id_servicio > 0){
        disabledControl(selectUnidadMedida,true);
        document.getElementsByName("id_tipo_item")[0].value = 2;

    }
    if(id_equipo > 0){
        disabledControl(selectUnidadMedida,true);
        document.getElementsByName("id_tipo_item")[0].value = 3;
    }
    $('#modal-catalogo-items').modal('hide');
}


function mostrar_item(id){
    $(":file").filestyle('disabled', false);
    baseUrl = '/logistica/mostrar_item/'+id;
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);  
            $('[name=id_item]').val(response[0].id_item);
            $('[name=id_producto]').val(response[0].id_producto);
            $('[name=id_servicio]').val(response[0].id_servicio);
            $('[name=id_equipo]').val(response[0].id_equipo);
            $('[name=codigo_item]').val(response[0].codigo);
            $('[name=descripcion_item]').val(response[0].descripcion);
            $('[name=unidad_medida_item]').val(response[0].id_unidad_medida);

        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}


// modal partidas
function partidasModal(){  
    var grupo = $('[name=id_grupo]').val();
    if (grupo !== ''){
        $('#modal-partidas').modal({
            show: true,
            backdrop: 'static'
        });
        listarPartidas(grupo);
    } else {
        alert('Es necesario que seleccione un Área!');
    }
}
function listarPartidas(id_grupo){
    $.ajax({
        type: 'GET',
        url: '/listar_partidas/'+id_grupo,
        dataType: 'JSON',
        success: function(response){
            $('#listaPartidas').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function apertura(id_presup){
    if ($("#pres-"+id_presup+" ").attr('class') == 'oculto'){
        $("#pres-"+id_presup+" ").removeClass('oculto');
        $("#pres-"+id_presup+" ").addClass('visible');
    } else {
        $("#pres-"+id_presup+" ").removeClass('visible');
        $("#pres-"+id_presup+" ").addClass('oculto');
    }
}
function selectPartida(id_partida){
    var codigo = $("#par-"+id_partida+" ").find("td[name=codigo]")[0].innerHTML;
    var descripcion = $("#par-"+id_partida+" ").find("td[name=descripcion]")[0].innerHTML;
    
    $('#modal-partidas').modal('hide');
    $('[name=id_partida]').val(id_partida);
    $('[name=cod_partida]').val(codigo);
    $('[name=des_partida]').val(descripcion);
}


// modal proyectos

function modal_proyectos(){

    $('#modal-proyectos').modal({
        show: true,
        backdrop: 'static'
    });
    listarProyectos();
}

function listarProyectos(){
    var vardataTables = funcDatatables();
    $('#listaProyectos').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'processing': true,
        "bDestroy": true,
        'ajax': '/logistica/proyectos_contratos',
        'columns': [
            {'data': 'id_proyecto'},
            {'data': 'nro_contrato'},
            {'data': 'fecha_contrato'},
            {'data': 'descripcion'},
            {'data': 'razon_social'},
            {'data': 'simbolo'},
            {'data': 'importe'}
        ],
        'order': [
            [2, 'asc']
        ]
    });
}
$(function(){
    /* Seleccionar valor del DataTable */
    $('#listaProyectos tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaProyectos').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var idTr = $(this)[0].firstChild.innerHTML;
        $('.modal-footer #id_proyecto').text(idTr);
    });
});




function selectProyecto(){
    var myId = $('.modal-footer #id_proyecto').text();
    // var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');
        mostrar_proyecto(myId); 
    $('#modal-proyectos').modal('hide');
}


function mostrar_proyecto(id){
    baseUrl = '/logistica/proyecto/'+id;
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){            
            $('[name=id_proyecto]').val(response['proyecto'][0].id_proyecto);
            $('[name=codigo_proyecto]').val(response['proyecto'][0].codigo);
            $('[name=descripcion_proyecto]').val(response['proyecto'][0].descripcion);
            $('[name=cliente]').val(response['proyecto'][0].razon_social);
            $('[name=presupuesto]').val(response['proyecto'][0].importe);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_requerimiento(action){
    let actual_id_area = auth_user.id_area;
    let actual_id_usuario = auth_user.id_usuario;
    let actual_id_rol = auth_user.id_rol;
    // let actual_id_grupo = auth_user.id_grupo;
    let requerimiento = get_data_requerimiento();
    let detalle_requerimiento = data_item;

    requerimiento.id_usuario = actual_id_usuario; //update -> usuario actual
    requerimiento.id_area = actual_id_area; // update -> id area actual
    requerimiento.id_rol = actual_id_rol; // update -> id rol actual
    // requerimiento.id_grupo = actual_id_grupo; // update -> id area actual
    let data = {requerimiento,detalle:detalle_requerimiento};
    // console.log(data);
    
    
    if (action == 'register'){
        // funcion guardar nuevo

        data.requerimiento.id_estado_doc =1  // estado elaborado 
        data.requerimiento.estado = 1  // estado 
        
        baseUrl = '/logistica/guardar_requerimiento';
        $.ajax({
            type: 'POST',
            url: baseUrl,
            data: data,
            dataType: 'JSON',
            success: function(response){
                // console.log(response);
                if (response > 0){
                    
                    let lastIdRequerimiento =  response;
                    mostrar_requerimiento(lastIdRequerimiento);
                    changeStateButton('guardar');
                    $('#form-requerimiento').attr('type', 'register');
                    changeStateInput('form-requerimiento', true);
                    alert("Requerimiento Guardado");
                }
            }
        }).fail( function(jqXHR, textStatus, errorThrown){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });  
        
    }else if(action == 'edition'){
        // funcion editar
        baseUrl = '/logistica/actualizar_requerimiento/'+data.requerimiento.id_requerimiento;
        $.ajax({
            type: 'PUT',
            url: baseUrl,
            data: data,
            dataType: 'JSON',
            success: function(response){
                // console.log(response);
                if (response > 0){
                    alert("Requerimiento Actualizado");
                    /* edgar */
                    openSustento(data.requerimiento.id_requerimiento, 0, 'req');
                }
            }
        }).fail( function(jqXHR, textStatus, errorThrown){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });   
    }
}

function openSustento(req, det, type){
    var id_req;
    var id_det;
    if (type == 'req'){
        id_req = req;
        id_det = 0;
    }else if (type == 'det'){
        id_req = req;
        id_det = det;
    }
    $('[name=id_requerimiento_sustento]').val(id_req);
    $('[name=id_detalle_requerimiento_sustento]').val(id_det);
    $('#modal-sustento').modal({show: true, backdrop: 'static'});
}

function editRequerimiento(){
    // console.log("editando..")
    var btnEditarItem = document.getElementsByName("btnEditarItem");
    disabledControl(btnEditarItem,false);
    var btnAdjuntarArchivos = document.getElementsByName("btnAdjuntarArchivos");
    disabledControl(btnAdjuntarArchivos,false);
    var btnEliminarItem = document.getElementsByName("btnEliminarItem");
        disabledControl(btnEliminarItem,false);
    return null;
}


function cancelarRequerimiento(){
    // console.log("cancelar");
    $('#body_detalle_requerimiento').html('<tr id="default_tr"><td></td><td colspan="7"> No hay datos registrados</td></tr>');
    $('[name=codigo]').val('');
    var btnEditarItem = document.getElementsByName("btnEditarItem");
        disabledControl(btnEditarItem,true);
    var btnAdjuntarArchivos = document.getElementsByName("btnAdjuntarArchivos");
        disabledControl(btnAdjuntarArchivos,false);
    var btnEliminarItem = document.getElementsByName("btnEliminarItem");
        disabledControl(btnEliminarItem,true);
}