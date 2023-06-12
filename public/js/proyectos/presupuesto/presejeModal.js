function listarPresEje(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaPresEje').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'retrieve': true,
        'ajax': 'listar_presint/'+3,//3 Presupuesto de Ejecucion
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
    $('#listaPresEje tbody').on("click","tr", function(){
        var id = $(this)[0].firstChild.innerHTML;
        console.log(id);
        mostrar_preseje(id);
    });
}
function presejeModal(){
    $('#modal-preseje').modal({
        show: true
    });
    listarPresEje();
}
function mostrar_preseje(id){
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'mostrar_presint/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            console.log(response['preseje']);
            $('[name=id_presupuesto]').val(response['preseje'].id_presupuesto);
            $('[name=nombre_opcion]').val(response['preseje'].descripcion);
            $('[name=simbolo]').val(response['preseje'].simbolo);
            $('[name=importe]').val(response['preseje'].total_presupuestado);
            $('[name=moneda]').val(response['preseje'].moneda);
            $('[name=fecha_emision]').val(response['preseje'].fecha_emision);
            $('[name=total_costo_directo]').val(response['preseje'].total_costo_directo);
            $('[name=porcentaje_ci]').val(response['preseje'].porcentaje_ci);
            $('[name=total_ci]').val(response['preseje'].total_ci);
            $('[name=porcentage_gg]').val(response['preseje'].porcentage_gg);
            $('[name=total_gg]').val(response['preseje'].total_gg);
            $('[name=sub_total]').val(response['preseje'].sub_total);
            $('[name=porcentaje_utilidad]').val(response['preseje'].porcentaje_utilidad);
            $('[name=total_utilidad]').val(response['preseje'].total_utilidad);
            $('[name=porcentaje_igv]').val(response['preseje'].porcentaje_igv);
            $('[name=total_igv]').val(response['preseje'].total_igv);
            $('[name=total_presupuestado]').val(response['preseje'].total_presupuestado);
            $('[name=total_costo_directo_pc]').val(response['importes_pc'].total_costo_directo);
            $('[name=porcentaje_ci_pc]').val(response['importes_pc'].porcentaje_ci);
            $('[name=total_ci_pc]').val(response['importes_pc'].total_ci);
            $('[name=porcentage_gg_pc]').val(response['importes_pc'].porcentage_gg);
            $('[name=total_gg_pc]').val(response['importes_pc'].total_gg);
            $('[name=sub_total_pc]').val(response['importes_pc'].sub_total);
            $('[name=porcentaje_utilidad_pc]').val(response['importes_pc'].porcentaje_utilidad);
            $('[name=total_utilidad_pc]').val(response['importes_pc'].total_utilidad);
            $('[name=porcentaje_igv_pc]').val(response['importes_pc'].porcentaje_igv);
            $('[name=total_igv_pc]').val(response['importes_pc'].total_igv);
            $('[name=total_presupuestado_pc]').val(response['importes_pc'].total_presupuestado);
            $('#codigo').text(response['preseje'].codigo);
    
            var activeTab = $("#tab-preseje #myTab li.active a").attr('type');
            var activeForm = "form-"+activeTab.substring(1);
            actualizar_tab(activeForm, response['preseje'].id_presupuesto);
    
            console.log(response['preseje'].id_presupuesto);
            $('#modal-preseje').modal('hide');
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}