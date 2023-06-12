$(function(){
    var vardataTables = funcDatatables();
    var form = $('.page-main form[type=register]').attr('id');

    // $('#listaInsumo').dataTable({
    //     'dom': vardataTables[1],
    //     'buttons': vardataTables[2],
    //     'language' : vardataTables[0],
    //     'ajax': 'listar_insumos',
    //     'columns': [
    //         {'data': 'id_insumo'},
    //         {'data': 'codigo'},
    //         {'data': 'descripcion'},
    //         {'data': 'cod_tp_insumo'},
    //         {'data': 'abreviatura'},
    //         // {'render':
    //         //     function (data, type, row){
    //         //         return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
    //         //     }
    //         // }
    //         // {'render':
    //         //     function (data, type, row){
    //         //         return (formatDate(row['fecha_registro']));
    //         //     }
    //         // }
    //     ]
    // });

    // $('.group-table .mytable tbody').on('click', 'tr', function(){
    //     if ($(this).hasClass('eventClick')){
    //         $(this).removeClass('eventClick');
    //     } else {
    //         $('#listaInsumo').dataTable().$('tr.eventClick').removeClass('eventClick');
    //         $(this).addClass('eventClick');
    //     }
    //     var id = $(this)[0].firstChild.innerHTML;
    //     clearForm(form);
    //     mostrar_insumo(id);
    //     changeStateButton('historial');
    // });
    
});

function mostrar_insumo(id){
    var page = $('.page-main').attr('type');
    baseUrl = 'mostrar_insumo/'+id;
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        dataType: 'JSON',
        success: function(response){
            if (page == "insumo"){
                $('[name=id_insumo]').val(response[0].id_insumo);
                $('[name=codigo]').val(response[0].codigo);
                $('[name=descripcion]').val(response[0].descripcion);
                $('[name=tp_insumo]').val(response[0].tp_insumo);
                $('[name=peso_unitario]').val(response[0].peso_unitario);
                $('[name=precio]').val(response[0].precio);
                $('[name=iu]').val(response[0].iu).trigger('change.select2');
                $('[name=unid_medida]').val(response[0].unid_medida);
                $('[name=flete]').val(response[0].flete);
                $('[name=estado]').val(response[0].estado);
                $('[id=fecha_registro] label').text('');
                $('[id=fecha_registro] label').append(formatDateHour(response[0].fecha_registro));    
            }
            else if (page == "acu"){
                $('[name=id_insumo]').val(response[0].id_insumo);
                $('[name=des_insumo]').val(response[0].descripcion);
                $('[name=tp_insumo]').val(response[0].tp_insumo);
                $('[name=unid_medida]').text(response[0].unid_medida);
                $('[name=precio_unitario]').val((response[0].id_insumo == 326) ? this.state.objeto.mo : response[0].precio);
        
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function save_insumo(data, action){
    if (action == 'register'){
        baseUrl = 'guardar_insumo';
    } else if (action == 'edition'){
        baseUrl = 'actualizar_insumo';
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
                alert('Insumo registrado con exito');
                $('#listaInsumo').DataTable().ajax.reload();
                changeStateButton('guardar');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_insumo(ids){
    baseUrl = 'anular_insumo/'+ids;
    // $.ajax({
    //     type: 'GET',
    //     headers: {'X-CSRF-TOKEN': token},
    //     url: 'revisarinsumo/'+ids,
    //     dataType: 'JSON',
    //     success: function(response){
    //         console.log(response);
    //         if (response >= 1){
    //             alert('No es posible anular. \nEl insumo seleccionado está relacionado con '
    //             +response+' categoría(s).');
    //         }
    //         else {
                $.ajax({
                    type: 'GET',
                    headers: {'X-CSRF-TOKEN': token},
                    url: baseUrl,
                    dataType: 'JSON',
                    success: function(response){
                        console.log(response);
                        if (response > 0){
                            alert('Insumo anulado con exito');
                            $('#listaInsumo').DataTable().ajax.reload();
                            changeStateButton('anular');
                            clearForm('form-insumo');
                        }
                    }
                }).fail( function( jqXHR, textStatus, errorThrown ){
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
    //         }
    //     }
    // }).fail( function( jqXHR, textStatus, errorThrown ){
    //     console.log(jqXHR);
    //     console.log(textStatus);
    //     console.log(errorThrown);
    // });
    
}