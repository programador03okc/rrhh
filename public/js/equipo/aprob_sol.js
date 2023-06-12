$(function(){
    var activo = $('ul.nav-tabs li.active a')[0];
    activarTabs(activo);
    $("#tab-sol_aprob section:first form").attr('form', 'formulario');
    /* Efecto para los tabs */
    $('ul.nav-tabs li a').click(function(){
        activarTabs(this);
    });
});
function activarTabs(activo){
    $('ul.nav-tabs li').removeClass('active');
    $(activo).parent().addClass('active');
    $('.content-tabs section').attr('hidden', true);
    $('.content-tabs section form').removeAttr('type');
    $('.content-tabs section form').removeAttr('form');

    var activeTab = $(activo).attr('type');
    var activeForm = "form-"+activeTab.substring(1);
    console.log('activeTab'+activeTab);

    $("#"+activeForm).attr('type', 'register');
    $("#"+activeForm).attr('form', 'formulario');
    // changeStateInput(activeForm, true);
    console.log('activeForm'+activeForm);
    
    //inicio botones (estados)
    $(activeTab).attr('hidden', false);

    clearDataTable();
    if (activeTab == '#aprobaciones'){
        listar_aprob_sol();
    } else if (activeTab == '#todas'){
        listar_sol_todas();
    }
}
function listar_aprob_sol(){
    var rol = auth_user.id_rol;
    var grupo = auth_user.id_grupo;
    var vardataTables = funcDatatables();
    var tabla = $('#listaSolAprobaciones').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        // 'ajax': 'listar_aprob_sol',
        ajax:{
            url:"listar_aprob_sol/"+rol+'/'+grupo,
            dataSrc:""
        },
        'columns': [
            {'data': 'id_solicitud'},
            // {'defaultContent':'<input type="checkbox"/>'},
            {'data': 'codigo'},
            {'data': 'fecha_solicitud'},
            {'data': 'nombre_trabajador'},
            {'data': 'area_descripcion'},
            {'data': 'des_categoria'},
            {'data': 'fecha_inicio'},
            {'data': 'fecha_fin'},
            {'defaultContent': 
            '<button type="button" class="denegar btn btn-danger boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Denegar" >'+
                '<i class="fas fa-times-circle"></i></button>'+
            '<button type="button" class="aprobar btn btn-success boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Aprobar" >'+
                '<i class="fas fa-check-circle"></i></button>'+
            '<button type="button" class="observar btn btn-info boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Observar" >'+
                '<i class="fas fa-info-circle"></i></button>'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    botones('#listaSolAprobaciones tbody',tabla);
}
function botones(tbody, tabla){
    console.log("aprobar");
    $(tbody).on("click","button.aprobar", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
        var rspta = confirm('Esta seguro que desea aprobar la solicitud '+data.codigo);
        if (rspta){
            var obs = prompt("Si desea ingrese alguna observación", "");
            var id_sol = data.id_solicitud;
            guardar_aprobacion(data,1,obs);
            var aprobado = 2;
            actualiza_estado(id_sol,aprobado);
        }
    });
    $(tbody).on("click","button.denegar", function(){
        var data = tabla.row($(this).parents("tr")).data();
        var rspta = confirm('Esta seguro que desea denegar la solicitud '+data.codigo);
        
        if (rspta){
            var obs = prompt("Ingrese su motivo", "");
            var id_sol = data.id_solicitud;
            if (obs !== ''){
                guardar_aprobacion(data,2,obs);
                var denegado = 4;
                actualiza_estado(id_sol,denegado);
            } else {
                alert('Es necesario que ingrese un motivo!');
            }
        }
    });
    $(tbody).on("click","button.observar", function(){
        var data = tabla.row($(this).parents("tr")).data();
        var rspta = confirm('Esta seguro que desea observar la solicitud '+data.codigo);
        
        if (rspta){
            var obs = prompt("Ingrese su observación", "");
            var id_sol = data.id_solicitud;
            if (obs !== null){
                guardar_aprobacion(data,3,obs);
                var observado = 3;
                actualiza_estado(id_sol,observado);
            } else {
                alert('Es necesario que ingrese una observación!');
            }
        }
    });
    $(tbody).on("click","button.flujos", function(){
        var data = tabla.row($(this).parents("tr")).data();
        console.log(data);
        open_flujos(data.id_doc_aprob, data.id_solicitud);
    });
}
function listar_sol_todas(){
    var grupo = auth_user.id_grupo;
    var vardataTables = funcDatatables();
    var tabla = $('#listaSolTodas').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        ajax:{
            url:"mostrar_solicitudes/"+grupo,
            dataSrc:""
        },
        'columns': [
            {'data': 'id_solicitud'},
            // {'defaultContent':'<input type="checkbox"/>'},
            {'data': 'codigo'},
            {'data': 'fecha_solicitud'},
            {'data': 'nombre_trabajador'},
            {'data': 'area_descripcion'},
            {'data': 'des_categoria'},
            {'data': 'fecha_inicio'},
            {'data': 'fecha_fin'},
            {'data': 'estado_doc'},
            {'defaultContent': 
            '<button type="button" class="flujos btn btn-warning boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Flujos" >'+
                '<i class="fas fa-search-plus"></i></button>'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    botones('#listaSolTodas tbody',tabla);
}
function guardar_aprobacion(solicitud,vobo,obs){
    var id_flujo = solicitud.id_flujo;
    var id_doc_aprob = solicitud.id_doc_aprob;

    var data = 'id_flujo='+id_flujo+
            '&id_doc_aprob='+id_doc_aprob+
            '&id_vobo='+vobo+
            '&id_usuario='+auth_user.id_usuario+
            '&id_area='+auth_user.id_area+
            '&detalle_observacion='+obs+
            '&id_rol='+auth_user.id_rol;
    console.log(data);

    var token = $('#token').val();
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: 'guardar_aprobacion',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Aprobación registrada con éxito');
                // $('#modal-equipo_create').modal('hide');
                $('#listaSolAprobaciones').DataTable().ajax.reload();
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function actualiza_estado(id_solicitud,estado){
    var token = $('#token').val();
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: 'solicitud_cambia_estado/'+id_solicitud+'/'+estado,
        // data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function open_flujos(id_doc_aprob,id_solicitud){
    $('#modal-aprob_flujos').modal({
        show: true
    });
    listar_flujos(id_doc_aprob,id_solicitud);
}
function listar_flujos(id_doc_aprob,id_solicitud){
    $('#listaSolFlujos tbody').html('');
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'solicitud_flujos/'+id_doc_aprob+'/'+id_solicitud,
        dataType: 'JSON',
        success: function(response){
            $('#listaSolFlujos tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}