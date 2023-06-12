
$(function(){
    var vardataTables = funcDatatables();

    listarTablaReq();
 

});

function listarTablaReq(){
    var vardataTables = funcDatatables();

    $('#ListaReq').dataTable({
        'dom': 'frtip',
        'language' : vardataTables[0],
        'processing': true,
        'bDestroy': true,
        'ajax': '/logistica/listar_requerimientos',
        'order' : []
    });
    $('#ListaReq').DataTable().on("draw", function(){
        resizeSide();
    });

    $('#ListaReq tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        }else{
            $('#ListaReq').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
    });
}

function check(cbx){
    var idcb = cbx.id;
    var id_req = $(cbx).attr('data-primary');
    var id_det = $(cbx).attr('data-secundary');
    if($(cbx).is(":checked")){
        $("#"+idcb).attr('checked', 'checked');
        openModalObserva(id_req, id_det, idcb);
    }else{
        $("#"+idcb).removeAttr('checked');
    }
}

function openModalObserva(req, detalle, check){
    var ask = confirm('¿Desea agregar una observación al Item?');
    if (ask == true){
        $('#modal-obs-motivo [name=id_requerimiento]').val(req);
        $('#modal-obs-motivo [name=id_detalle_requerimiento]').val(detalle);
        // $('#modal-obs-motivo [name=value_check]').val(check);
        $("#"+check).attr('disabled', true);
        $('#modal-obs-motivo').modal({show: true, backdrop: 'static', keyboard: false});
    }else{
        $("#"+check).removeAttr('checked');
        return false;
    }
}

function editarListaReq(id){
    localStorage.setItem("idGral", id);
    location.assign('gestionar');
}

function crearCoti(req){
    localStorage.setItem("idReqCot", req);
    location.assign('../cotizacion/gestionar');
}

function atender_requerimiento(id, doc, flujo, type){
    $('#form-aprobacion').attr('type', type);
    if (type == 'aprobar'){
        $('[name=id_documento]').val(id);
        $('[name=doc_aprobacion]').val(doc);
        $('[name=flujo]').val(flujo);
        openModalAprob();
    }else if(type == 'observar'){
        openModalObs(id, doc, flujo);
    }else if(type == 'denegar'){
        $('[name=id_documento]').val(id);
        $('[name=doc_aprobacion]').val(doc);
        $('[name=flujo]').val(flujo);
        openModalAprob();
    }else if(type == 'aprobar_sustento'){
        var ask = confirm('¿Desea Aceptar la Sustentación?');
        if (ask == true){
            // console.log(id, doc, flujo, type);  
            let data = {id,doc,flujo,type};         
            $.ajax({
                type: 'POST',
                // headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '/logistica/aceptar_sustento',
                data: data,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                
                    if (response == 1) {
                        alert('Se agregó una actualizo el estado del Requerimiento a Pendiente por Aprobación');
                        listarTablaReq();

                    }
                }
            });
        }else{
            return false;
        }
    }
}

function viewFlujo(req, doc){
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: '/logistica/ver_flujos/' + req + '/' + doc,
        dataType: 'JSON',
        beforeSend: function(){
            $(document.body).append('<span class="loading"><div></div></span>');
        },
        success: function(response){
            console.log(response.siguiente);
            
            $('.loading').remove();
            if (response.cont > 0){
                $('#flujo-detalle').removeClass('oculto');
                $('#flujo-proximo').removeClass('oculto');
            }else{
                $('#flujo-detalle').addClass('oculto');
                $('#flujo-proximo').addClass('oculto');
            }

            $('#req-detalle').html(response.requerimiento);
            $('#flujo-detalle').html(response.flujo);
            $('#flujo-proximo').html(response.siguiente);
            $('#modal-flujo-aprob').modal({show: true, backdrop: 'static'});
        }
    });
    return false;
}

function imprimirReq(id){
    window.open('/logistica/imprimir-requerimiento-pdf/'+id+'/'+0, 'Requerimiento', 'width=864, height=650');
}

function verArchivosAdjuntosRequerimiento(id){
    $('#modal-adjuntar-archivos-requerimiento').modal({
        show: true,
        backdrop: 'static'
    });

    $('#section_upload_files').addClass('invisible');

    adjuntos_requerimiento=[];
    baseUrl = '/logistica/mostrar-adjuntos/'+id;
    $.ajax({
        type: 'GET',
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            // console.log(response);
            if(response.length >0){
                for (x=0; x<response.length; x++){
                    adjuntos_requerimiento.push({ 
                            'id_archivo':response[x].id_archivo,
                            'id_detalle_requerimiento':response[x].id_detalle_requerimiento,
                            'archivo':response[x].archivo,
                            'fecha_registro':response[x].fecha_registro,
                            'estado':response[x].estado,
                            'file':[]
                            });
                    }
            llenar_tabla_archivos_adjuntos(adjuntos_requerimiento);
            
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

    function limpiarTabla(idElement){
        console.log("limpiando tabla....");
        var table = document.getElementById(idElement);
        for(var i = table.rows.length - 1; i > 0; i--)
        {
            table.deleteRow(i);
        }
        return null;
    }
    
