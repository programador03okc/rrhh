function listarPropuesta(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaPropuesta').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'retrieve': true,
        'ajax': 'listar_presint/'+2,//2 Propuesta Cliente
        'columns': [
            {'data': 'id_presupuesto'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_emision']));
                }
            },
            {'data': 'simbolo'},
            {'data': 'total_presupuestado'},
            {'data': 'moneda'}
        ]
    });
    $('#listaPropuesta tbody').on("click","tr", function(){
        var id = $(this)[0].firstChild.innerHTML;
        console.log(id);
        mostrar_propuesta(id);
    });
}
function propuestaModal(){
    $('#modal-propuesta').modal({
        show: true
    });
    listarPropuesta();
}
function mostrar_propuesta(id){
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'mostrar_presint/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            console.log(response['propuesta']);
            $('[name=id_presupuesto]').val(response['propuesta'].id_presupuesto);
            $('[name=nombre_opcion]').val(response['propuesta'].descripcion);
            $('[name=simbolo]').val(response['propuesta'].simbolo);
            $('[name=importe]').val(response['propuesta'].total_presupuestado);
            $('[name=moneda]').val(response['propuesta'].moneda);
            $('[name=fecha_emision]').val(response['propuesta'].fecha_emision);
            $('[name=total_costo_directo]').val(response['propuesta'].total_costo_directo);
            $('[name=porcentaje_ci]').val(response['propuesta'].porcentaje_ci);
            $('[name=total_ci]').val(response['propuesta'].total_ci);
            $('[name=porcentage_gg]').val(response['propuesta'].porcentage_gg);
            $('[name=total_gg]').val(response['propuesta'].total_gg);
            $('[name=sub_total]').val(response['propuesta'].sub_total);
            $('[name=porcentaje_utilidad]').val(response['propuesta'].porcentaje_utilidad);
            $('[name=total_utilidad]').val(response['propuesta'].total_utilidad);
            $('[name=porcentaje_igv]').val(response['propuesta'].porcentaje_igv);
            $('[name=total_igv]').val(response['propuesta'].total_igv);
            $('[name=total_presupuestado]').val(response['propuesta'].total_presupuestado);
            $('[name=total_costo_directo_pi]').val(response['importes_pi'].total_costo_directo);
            $('[name=porcentaje_ci_pi]').val(response['importes_pi'].porcentaje_ci);
            $('[name=total_ci_pi]').val(response['importes_pi'].total_ci);
            $('[name=porcentage_gg_pi]').val(response['importes_pi'].porcentage_gg);
            $('[name=total_gg_pi]').val(response['importes_pi'].total_gg);
            $('[name=sub_total_pi]').val(response['importes_pi'].sub_total);
            $('[name=porcentaje_utilidad_pi]').val(response['importes_pi'].porcentaje_utilidad);
            $('[name=total_utilidad_pi]').val(response['importes_pi'].total_utilidad);
            $('[name=porcentaje_igv_pi]').val(response['importes_pi'].porcentaje_igv);
            $('[name=total_igv_pi]').val(response['importes_pi'].total_igv);
            $('[name=total_presupuestado_pi]').val(response['importes_pi'].total_presupuestado);
            $('#codigo').text(response['propuesta'].codigo);
    
            var activeTab = $("#tab-propuesta #myTab li.active a").attr('type');
            var activeForm = "form-"+activeTab.substring(1);
            actualizar_tab(activeForm, response['propuesta'].id_presupuesto);
    
            console.log(response['propuesta'].id_presupuesto);
            $('#modal-propuesta').modal('hide');
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}