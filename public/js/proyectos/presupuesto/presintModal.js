function listarPresint(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaPresint').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'bDestroy': true,
        'retrieve': true,
        'ajax': 'listar_presint/'+1,//1 Presupuesto Interno
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
    $('#listaPresint tbody').on("click","tr", function(){
        var id = $(this)[0].firstChild.innerHTML;
        console.log(id);
        mostrar_presint(id);
    });
}
function presintModal(){
    $('#modal-presint').modal({
        show: true
    });
    listarPresint();
}
function mostrar_presint(id){
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'mostrar_presint/'+id,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            $('[name=id_presupuesto]').val(response.id_presupuesto);
            $('[name=nombre_opcion]').val(response.descripcion);
            $('[name=simbolo]').val(response.simbolo);
            $('[name=importe]').val(response.total_presupuestado);
            $('[name=moneda]').val(response.moneda);
            $('[name=fecha_emision]').val(response.fecha_emision);
            $('[name=total_costo_directo]').val(response.total_costo_directo);
            $('[name=porcentaje_ci]').val(response.porcentaje_ci);
            $('[name=total_ci]').val(response.total_ci);
            $('[name=porcentage_gg]').val(response.porcentage_gg);
            $('[name=total_gg]').val(response.total_gg);
            $('[name=sub_total]').val(response.sub_total);
            $('[name=porcentaje_utilidad]').val(response.porcentaje_utilidad);
            $('[name=total_utilidad]').val(response.total_utilidad);
            $('[name=porcentaje_igv]').val(response.porcentaje_igv);
            $('[name=total_igv]').val(response.total_igv);
            $('[name=total_presupuestado]').val(response.total_presupuestado);
            $('#codigo').text(response.codigo);
    
            var activeTab = $("#tab-presint #myTab li.active a").attr('type');
            var activeForm = "form-"+activeTab.substring(1);
            actualizar_tab(activeForm, response.id_presupuesto);
    
            console.log(response.id_presupuesto);
            $('#modal-presint').modal('hide');
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}