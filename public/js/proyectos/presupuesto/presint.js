$(function(){
    $('[name=fecha_emision]').val(fecha_actual());
    $('[name=id_empresa]').val(auth_user.id_empresa);
    $('[name=elaborado_por]').val(auth_user.id_usuario);
    $('#listaCD tbody').html('');
    $('#listaCI tbody').html('');
    $('#listaGG tbody').html('');

    $("#tab-presint section:first form").attr('form', 'formulario');
    
    /* Efecto para los tabs */
    $('ul.nav-tabs li a').click(function(){
        $('ul.nav-tabs li').removeClass('active');
        $(this).parent().addClass('active');
        $('.content-tabs section').attr('hidden', true);
        $('.content-tabs section form').removeAttr('type');
        $('.content-tabs section form').removeAttr('form');

        var activeTab = $(this).attr('type');
        var activeForm = "form-"+activeTab.substring(1);

        $("#"+activeForm).attr('type', 'register');
        $("#"+activeForm).attr('form', 'formulario');
        changeStateInput(activeForm, true);

        var id = $('[name=id_presupuesto]').val();
        console.log('activeForm'+activeForm+' id'+id);
        clearDataTable();
        actualizar_tab(activeForm, id);
        $(activeTab).attr('hidden', false);//inicio botones (estados)
    });

});

function actualizar_tab(activeForm, id){
    if (id !== ''){
        if (activeForm == "form-cd"){
            $('#listaCD tbody').html('');
            listar_cd(id);
        } 
        else if (activeForm == "form-ci"){
            $('#listaCI tbody').html('');
            listar_ci(id);
        }
        else if (activeForm == "form-gg"){
            $('#listaGG tbody').html('');
            listar_gg(id);
        }
    }
}

function save_propuesta(data, action){
    console.log(action);
    console.log(data);
    if (action == 'register'){
        baseUrl = 'guardar_presint';
    } else if (action == 'edition'){
        baseUrl = 'update_presint';
    }
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            alert('Presupuesto registrado con éxito');
            changeStateButton('guardar');
            if (action == 'edition'){
                if (response['importe'] > 0){
                    $('[name=id_presupuesto]').val(response['id_pres']);
                }
            } else if (action == 'register'){
                if (response > 0){
                    $('[name=id_presupuesto]').val(response);
                }
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function sim_moneda(){
    console.log('sim_moneda');
    var mnd = $('select[name="moneda"] option:selected').text();
    var sim = mnd.split(' - ');
    console.log(mnd)
    $('[name=simbolo]').val(sim[1]);
}
function generar_propuesta(){
    var id_pres = $('[name=id_presupuesto]').val();
    if (id_pres !== ''){
        var rspta = confirm("¿Esta seguro que desea generar una Propuesta Cliente?\nEste procedimiento copiará todas las partidas");
        if (rspta){
            $.ajax({
                type: 'GET',
                headers: {'X-CSRF-TOKEN': token},
                url: 'generar_propuesta/'+id_pres,
                dataType: 'JSON',
                success: function(id_propuesta){
                    console.log('id_propuesta'+id_propuesta);
                    if (id_propuesta > 0){
                        alert('Propuesta Cliente generada con éxito');
                        changeStateButton('guardar');
                        localStorage.setItem("id_pres_cli",id_propuesta);
                        location.assign("propuesta");
                    }
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }
    } else {
        alert("Debe seleccionar un Presupuesto!");
    }
}