$(function(){
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');

     $('#listaAportaciones').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_aportacion',
        'columns': [
            {'data': 'id_aportacion'},
            {'data': 'concepto'},
            {'data': 'valor'},
            {
                'render': function (data, type, row) {
                    return moment(row['fecha_inicio']).format("DD-MM-YYYY") + ' / ' + moment(row['fecha_fin']).format("DD-MM-YYYY");
                }
            }
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    
    $('.group-table .mytable tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('.dataTable').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        clearForm(form);
        mostrar_aportacion(id);
        changeStateButton('historial');
    });
    resizeSide();
});

function mostrar_aportacion(id){
    baseUrl = 'cargar_aportacion/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            $('[name=id_aportacion]').val(response[0].id_aportacion);
            $('[name=concepto]').val(response[0].concepto);
            $('[name=id_variable_aportacion]').val(response[0].id_variable_aportacion);
            $('[name=valor]').val(response[0].valor);
            $('[name=fecha_inicio]').val(response[0].fecha_inicio);
            $('[name=fecha_fin]').val(response[0].fecha_fin);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_aportacion(data, action){
    var msj;
    if (action == 'register'){
        baseUrl = 'guardar_aportacion';
        msj = 'Aportación registrada con exito';
    }else if(action == 'edition'){
        baseUrl = 'editar_aportacion';
        msj = 'Aportación editada con exito';
    }
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert(msj);
                $('#listaAportaciones').DataTable().ajax.reload();
                changeStateButton('guardar');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_aportacion(ids){
    baseUrl = 'anular_aportacion/'+ids;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (response > 0){
                alert('Aportación anulada con exito');
                $('#listaAportaciones').DataTable().ajax.reload();
                changeStateButton('anular');
                clearForm('form-tipo_aporte');
            }
        }
    }).fail( function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}