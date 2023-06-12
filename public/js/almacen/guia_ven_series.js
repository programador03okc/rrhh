function agrega_series(id_guia_det, descripcion, cant, id_guia_ven_det){
    $('#modal-guia_ven_series').modal({
        show: true
    });
    // clearDataTable();
    listarSeries(id_guia_det);
    $('[name=id_guia_ven_det]').val(id_guia_ven_det);
    $('[name=cant_items]').val(cant);
    $('#descripcion').text(descripcion);
}
function listarSeries(id_guia_det){
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'listar_series/'+id_guia_det,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            var tr = '';
            for (var i=0;i<response.length;i++){
                tr+='<tr><td hidden><input name="id_prod_serie" value="'+response[i].id_prod_serie+'"/></td><td><input type="checkbox" '+(response[i].id_guia_ven_det !== null ? 'checked' : '')+'/></td><td><input type="text" class="oculto" name="series" value="'+response[i].serie+'"/>'+response[i].serie+'</td></tr>';
            }
            $('#listaSeries tbody').html(tr);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function guardar_series(){
    var series = [];
    var r = 0;

    $("input[type=checkbox]:checked").each(function(){
        var serie = $(this).closest('td').siblings().find("input[name=id_prod_serie]").val();
        if (serie !== undefined){
            series[r] = serie;
            ++r;
        }
    });
    
    if (r == 0){
        alert('Debe seleccionar por lo menos un item');
    } else {
        console.log(series);
        var token = $('#token').val();
        var id_guia_ven_det = $("[name=id_guia_ven_det]").val();
        var data =  'id_guia_ven_det='+id_guia_ven_det+
                    '&series='+series;
        console.log(data);
        $.ajax({
            type: 'POST',
            headers: {'X-CSRF-TOKEN': token},
            url: 'update_series',
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Series registradas con éxito');
                    $('#modal-guia_ven_series').modal('hide');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });    
    }
}
