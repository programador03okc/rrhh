function agrega_partida_ci(cod_compo){
    console.log('agrega_partida');
    $('#modal-partidaCICreate').modal({
        show: true
    });

    var i = 1;
    var filas = document.querySelectorAll('#listaCI tbody tr');
    filas.forEach(function(e){
        var colum = e.querySelectorAll('td');
        var padre = colum[11].innerText;
        var unid = colum[2].innerText;

        if (padre == cod_compo && unid !== ''){
            i++;
        }
    });
    var id_ci = $('[name=id_presupuesto]').val();
    console.log(id_ci);
    $('[name=cod_compo_ci]').val(cod_compo);
    $('[name=id_ci]').val(id_ci);
    $('[name=codigo_ci]').val(cod_compo+'.'+leftZero(i,2));
    $('[name=id_ci_detalle]').val('');
    $('[name=id_cu_ci]').val('');
    $('[name=cod_acu_ci]').val('');
    $('[name=des_acu_ci]').val('');
    $('[name=unid_medida_ci]').val('');
    $('[name=cantidad_ci]').val('');
    $('[name=precio_unitario_ci]').val('');
    $('[name=participacion]').val('');
    $('[name=tiempo]').val('');
    $('[name=veces]').val('');
    $('[name=precio_total_ci]').val('');
}

function calculaPrecioTotalCI(){
    var cant = $('[name=cantidad_ci]').val();
    var unit = $('[name=precio_unitario_ci]').val();
    var par = $('[name=participacion]').val();
    var tiem = $('[name=tiempo]').val();
    var vec = $('[name=veces]').val();
    var precio_tot = 0;

    console.log('cant'+cant+' unit'+unit);
    if (cant !== null && unit !== null){
        precio_tot = (cant * unit).toFixed(2);
    }
    if (par > 0) { precio_tot *= par; }
    if (tiem > 0) { precio_tot *= tiem; }
    if (vec > 0) { precio_tot *= vec; }

    console.log('par'+par+' tiem'+tiem+' vec'+vec);
    console.log('precio_tot'+precio_tot);

    $('[name=precio_total_ci]').val(precio_tot);
}

function guardar_partida_ci(){
    var id_pres = $('[name=id_presupuesto]').val();
    var id = $('[name=id_ci_detalle]').val();
    var id_ci = $('[name=id_ci]').val();
    var id_cu = $('[name=id_cu_ci]').val();
    var cod = $('[name=codigo_ci]').val();
    var des = $('[name=des_acu_ci]').val();
    var unid = $('[name=unid_medida_ci]').val();
    var cant = $('[name=cantidad_ci]').val();
    var unit = $('[name=precio_unitario_ci]').val();
    var par = $('[name=participacion]').val();
    var tiem = $('[name=tiempo]').val();
    var vec = $('[name=veces]').val();
    var tot = $('[name=precio_total_ci]').val();
    var comp = $('[name=cod_compo_ci]').val();

    var data = 'id_ci_detalle='+id+
            '&id_ci='+id_ci+
            '&id_cu='+id_cu+
            '&codigo='+cod+
            '&descripcion='+des+
            '&unid_medida='+unid+
            '&cantidad='+cant+
            '&unitario='+unit+
            '&total='+tot+
            '&participacion='+par+
            '&tiempo='+tiem+
            '&veces='+vec+
            '&comp='+comp;
    
    var token = $('#token').val();
    console.log(data);

    var baseUrl;
    if (id !== ''){
        baseUrl = 'update_partida_ci';
    } else {
        baseUrl = 'guardar_partida_ci';
    }
    console.log(baseUrl);
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Partida registrada con éxito');
                listar_ci(id_pres);
                $('#modal-partidaCICreate').modal('hide');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function editar_partida_ci(id_ci_detalle){
    $('#modal-partidaCICreate').modal({
        show: true
    });

    var id_ci = $('[name=id_presupuesto]').val();
    $('[name=id_ci]').val(id_ci);

    var filas = document.querySelectorAll('#par-'+id_ci_detalle);
    filas.forEach(function(e){
        var colum = e.querySelectorAll('td');
        cu      = (colum[0].id).split('-');
        cod_cu  = (colum[1].id).split('-');
        unid    = (colum[2].id).split('-');

        $('[name=id_ci_detalle]').val(id_ci_detalle);
        $('[name=id_cu_ci]').val(cu[1]);
        $('[name=codigo_ci]').val(colum[0].innerText);
        $('[name=cod_acu_ci]').val(cod_cu[1]);
        $('[name=des_acu_ci]').val(colum[1].innerText);
        $('[name=unid_medida_ci]').val(unid[1]);
        $('[name=cantidad_ci]').val(colum[3].innerText);
        $('[name=precio_unitario_ci]').val(colum[4].innerText);
        $('[name=participacion]').val(colum[5].innerText);
        $('[name=tiempo]').val(colum[6].innerText);
        $('[name=veces]').val(colum[7].innerText);
        $('[name=precio_total_ci]').val(colum[8].innerText);
        $('[name=cod_compo_ci]').val(colum[11].innerText);
    
    });

    $("#par-"+id_ci_detalle+" td").find("input[name=descripcion]");
}

function anular_partida_ci(id_ci_detalle){
    var anula = confirm("¿Esta seguro que desea anular ésta partida?");
    var cod = '';
    var id_pres = $('[name=id_presupuesto]').val();

    var filas = document.querySelectorAll('#par-'+id_ci_detalle);
    filas.forEach(function(e){
        var colum = e.querySelectorAll('td');
        cod = colum[11].innerText;
    });

    var token = $('#token').val();
    if (anula){
        $.ajax({
            type: 'POST',
            headers: {'X-CSRF-TOKEN': token},
            url: 'anular_partida_ci',
            data: 'id_ci_detalle='+id_ci_detalle+'&cod_compo='+cod+'&id_pres='+id_pres,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Partida anulada con éxito');
                    listar_ci(id_pres);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}