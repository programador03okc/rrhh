$(function(){
    listar_mttos();
    var form = $('.page-main form[type=register]').attr('id');
    $('#listaMttos tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaMttos').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        $('.modal-footer #id_mtto').text(id);
        console.log(id);
        // clearForm(form);
        // mostrar_mtto(id);
        // changeStateButton('historial');
    });
});
function mttoModal(){
    $('#modal-mtto').modal({
        show: true
    });
    clearDataTable();
    listar_mttos();
}
function selectMtto(){
    var myId = $('.modal-footer #id_mtto').text();
    var page = $('.page-main').attr('type');
    var form = $('.page-main form[type=register]').attr('id');

    if (page == "mtto"){
        clearForm(form);
        mostrar_mtto(myId);
        changeStateButton('historial');
        console.log($(":file").filestyle('disabled'));
    }
    $('#modal-mtto').modal('hide');
}
function listar_mttos(){
    var vardataTables = funcDatatables();
    $('#listaMttos').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_mttos',
        'columns': [
            {'data': 'id_mtto'},
            {'data': 'codigo'},
            {'data': 'fecha_mtto'},
            {'data': 'des_equipo'},
        ]
    });
}
function mostrar_mtto(id){
    baseUrl = 'mostrar_mtto/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var htmls = '<option value="0" disabled>Elija una opción</option>';
            Object.keys(response['sedes']).forEach(function (key){
                htmls += '<option value="'+response['sedes'][key]['id_sede']+'">'+response['sedes'][key]['descripcion']+'</option>';
            });
            $('[name=id_sede]').html(htmls);

            var htmls = '<option value="0" disabled>Elija una opción</option>';
            Object.keys(response['grupos']).forEach(function (key){
                htmls += '<option value="'+response['grupos'][key]['id_grupo']+'">'+response['grupos'][key]['descripcion']+'</option>';
            });
            $('[name=id_grupo]').html(htmls);

            var htmls = '<option value="0" disabled>Elija una opción</option>';
            Object.keys(response['areas']).forEach(function (key){
                htmls += '<option value="'+response['areas'][key]['id_area']+'">'+response['areas'][key]['descripcion']+'</option>';
            });
            $('[name=id_area]').html(htmls);

            $('[name=id_mtto]').val(response['mtto'].id_mtto);
            $('[name=fecha_mtto]').val(response['mtto'].fecha_mtto);
            $('[name=id_proveedor]').val(response['mtto'].id_proveedor);
            $('[name=id_equipo]').val(response['mtto'].id_equipo);
            $('[name=codigo]').val(response['mtto'].codigo);
            $('[name=kilometraje]').val(response['mtto'].kilometraje);
            $('[name=costo_total]').val(response['mtto'].costo_total);
            $('[name=observaciones]').val(response['mtto'].observaciones);
            $('[name=id_area]').val(response['mtto'].id_area);
            $('[name=id_grupo]').val(response['mtto'].id_grupo);
            $('[name=id_sede]').val(response['mtto'].id_sede);
            $('[name=id_empresa]').val(response['mtto'].id_empresa);
            
            listar_mtto_pendientes(response['mtto'].id_equipo);
            listar_mtto_detalle(response['mtto'].id_mtto);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
