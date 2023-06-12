function agrega_partida_cd(cod_compo){
    console.log('agrega_partida');
    $('#modal-partidaCDCreate').modal({
        show: true
    });

    var i = 1;
    var filas = document.querySelectorAll('#listaCD tbody tr');
    filas.forEach(function(e){
        var colum = e.querySelectorAll('td');
        var padre = colum[8].innerText;
        var unid = colum[2].innerText;

        if (padre == cod_compo && unid !== ''){
            i++;
        }
    });
    var id_cd = $('[name=id_presupuesto]').val();
    console.log(id_cd);
    $('[name=cod_compo]').val(cod_compo);
    $('[name=id_cd]').val(id_cd);
    $('[name=codigo]').val(cod_compo+'.'+leftZero(i,2));
    $('[name=id_partida]').val('');
    $('[name=id_cu]').val('');
    $('[name=cod_acu]').val('');
    $('[name=des_acu]').val('');
    $('[name=unid_medida]').val('');
    $('[name=cantidad]').val('');
    $('[name=precio_unitario]').val('');
    $('[name=precio_total]').val('');
    $('[name=id_sistema]').val('');
}

function calculaPrecioTotal(){
    var cant = $('[name=cantidad]').val();
    var unit = $('[name=precio_unitario]').val();
    let precio_tot = 0;

    if (cant !== null && unit !== null){
        precio_tot = (cant * unit).toFixed(2);
    }
    $('[name=precio_total]').val(precio_tot);
}

function guardar_partida_cd(){
    var id_pres = $('[name=id_presupuesto]').val();
    var id = $('[name=id_partida]').val();
    var id_cd = $('[name=id_cd]').val();
    var id_cu = $('[name=id_cu]').val();
    var cod = $('[name=codigo]').val();
    var des = $('[name=des_acu]').val();
    var unid = $('[name=unid_medida]').val();
    var cant = $('[name=cantidad]').val();
    var unit = $('[name=precio_unitario]').val();
    var tot = $('[name=precio_total]').val();
    var sis = $('[name=id_sistema]').val();
    var comp = $('[name=cod_compo]').val();

    var data = 'id_partida='+id+
            '&id_cd='+id_cd+
            '&id_cu='+id_cu+
            '&codigo='+cod+
            '&descripcion='+des+
            '&unid_medida='+unid+
            '&cantidad='+cant+
            '&unitario='+unit+
            '&total='+tot+
            '&sis='+sis+
            '&comp='+comp;
    
    var token = $('#token').val();
    console.log(data);

    var baseUrl;
    if (id !== ''){
        baseUrl = 'update_partida_cd';
    } else {
        baseUrl = 'guardar_partida_cd';
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
                listar_cd(id_pres);
                $('#modal-partidaCDCreate').modal('hide');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function editar_partida_cd(id_partida){
    $('#modal-partidaCDCreate').modal({
        show: true
    });

    var id_cd = $('[name=id_presupuesto]').val();
    $('[name=id_cd]').val(id_cd);

    var filas = document.querySelectorAll('#par-'+id_partida);
    filas.forEach(function(e){
        var colum = e.querySelectorAll('td');
        cu      = (colum[0].id).split('-');
        cod_cu  = (colum[1].id).split('-');
        unid    = (colum[2].id).split('-');
        sis     = (colum[6].id).split('-');

        $('[name=id_partida]').val(id_partida);
        $('[name=id_cu]').val(cu[1]);
        $('[name=codigo]').val(colum[0].innerText);
        $('[name=cod_acu]').val(cod_cu[1]);
        $('[name=des_acu]').val(colum[1].innerText);
        $('[name=unid_medida]').val(unid[1]);
        $('[name=cantidad]').val(colum[3].innerText);
        $('[name=precio_unitario]').val(colum[4].innerText);
        $('[name=precio_total]').val(colum[5].innerText);
        $('[name=id_sistema]').val(sis[1]);
        $('[name=cod_compo]').val(colum[8].innerText);
    
    });

    $("#par-"+id_partida+" td").find("input[name=descripcion]");
}

function anular_partida_cd(id_partida){
    var anula = confirm("¿Esta seguro que desea anular ésta partida?");
    var cod = '';
    var id_pres = $('[name=id_presupuesto]').val();

    var filas = document.querySelectorAll('#par-'+id_partida);
    filas.forEach(function(e){
        var colum = e.querySelectorAll('td');
        cod = colum[8].innerText;
    });

    var token = $('#token').val();
    if (anula){
        $.ajax({
            type: 'POST',
            headers: {'X-CSRF-TOKEN': token},
            url: 'anular_partida_cd',
            data: 'id_partida='+id_partida+'&cod_compo='+cod+'&id_pres='+id_pres,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Partida anulada con éxito');
                    listar_cd(id_pres);
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
    }
}