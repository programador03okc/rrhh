$(function(){
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');

    $('#listaAlmacen').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_almacenes',
        'columns': [
            {'data': 'id_almacen'},
            {'data': 'sede_descripcion'},
            {'data': 'descripcion'},
            {'data': 'tp_almacen'},
            {'render':
                function (data, type, row){
                    return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });

    $('.group-table .mytable tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaAlmacen').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        clearForm(form);
        mostrar_almacen(id);
        changeStateButton('historial');
    });
});

function mostrar_almacen(id){
    baseUrl = 'cargar_almacen/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_almacen]').val(response[0].id_almacen);
            $('[name=id_sede]').val(response[0].id_sede);
            $('[name=id_tipo_almacen]').val(response[0].id_tipo_almacen);
            $('[name=descripcion]').val(response[0].descripcion);
            $('[name=ubicacion]').val(response[0].ubicacion);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_almacen(data, action){
    var msj;
    if (action == 'register'){
        baseUrl = 'guardar_almacen';
        msj = 'Almacén registrado con exito';
    }else if(action == 'edition'){
        baseUrl = 'editar_almacen';
        msj = 'Almacén editado con exito';
    }
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert(msj);
                $('#listaAlmacen').DataTable().ajax.reload();
                changeStateButton('guardar');
                $('#form-almacenes').attr('type', 'register');
				changeStateInput('form-almacenes', true); 
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_almacen(ids){
    baseUrl = 'anular_almacen/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Almacén anulado con exito');
                $('#listaAlmacen').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-almacenes');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}