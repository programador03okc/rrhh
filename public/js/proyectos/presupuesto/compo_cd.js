function listar_cd(id_pres){
    $.ajax({
        type: 'GET',
        // headers: {'X-CSRF-TOKEN': token},
        url: 'listar_cd/'+id_pres,
        dataType: 'JSON',
        success: function(response){
            $('#listaCD tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function agregar_componente_cd(){
    var id_pres = $('[name=id_presupuesto]').val();
    if (id_pres !== ''){
        var titulo = prompt("Ingrese un nombre al título", "Ingrese un título...");
        if (titulo != null) {
            var i = 1;
            var filas = document.querySelectorAll('#listaCD tbody tr');
            filas.forEach(function(e){
                var colum = e.querySelectorAll('td');
                var padre = colum[8].innerText;
                if (padre == ''){
                    i++;
                }
            });
            var data = 'id_pres='+id_pres+'&codigo='+leftZero(i,2)+'&descripcion='+titulo+'&cod_compo=';
            guardar_componente_cd(data, id_pres);
        }
    } else {
        alert('Debe seleccionar un ingresar un Presupuesto');
    }
}
function agregar_compo_cd(cod_compo){
    var titulo = prompt("Ingrese un nombre al título", "Ingrese un título..");
    if (titulo != null) {
        var i = 1;
        var filas = document.querySelectorAll('#listaCD tbody tr');
        filas.forEach(function(e){
            var colum = e.querySelectorAll('td');
            var padre = colum[8].innerText;
            var unid = colum[2].innerText;
            if (padre == cod_compo && unid == ''){
                i++;
            }
        });
        var id_pres = $('[name=id_presupuesto]').val();
        var codigo = cod_compo+'.'+leftZero(i,2);
        var data =  'id_pres='+id_pres+'&codigo='+codigo+
                    '&descripcion='+titulo+'&cod_compo='+cod_compo;
        guardar_componente_cd(data, id_pres);
    } else {
        alert("No ha ingresado ningun valor.");
    }
}
function guardar_componente_cd(data, id_pres){
    var token = $('#token').val();
    var rspta = confirm("¿Esta seguro que desea guardar el titulo?");
    if (rspta){
        $.ajax({
            type: 'POST',
            headers: {'X-CSRF-TOKEN': token},
            url: 'guardar_componente_cd',
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log(response);
                if (response > 0){
                    alert('Titulo registrado con éxito');
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

function editar_compo_cd(id_cd_compo){
    $("#com-"+id_cd_compo+" td").find("input[name=descripcion]").attr('disabled',false);
    $("#com-"+id_cd_compo+" td").find("i.blue").removeClass('visible');
    $("#com-"+id_cd_compo+" td").find("i.blue").addClass('oculto');
    $("#com-"+id_cd_compo+" td").find("i.green").removeClass('oculto');
    $("#com-"+id_cd_compo+" td").find("i.green").addClass('visible');
}

function update_compo_cd(id_cd_compo){
    var des = $("#com-"+id_cd_compo+" td").find("input[name=descripcion]").val();
    var data =  'id_cd_compo='+id_cd_compo+
                '&descripcion='+des;
    var token = $('#token').val();
    var id_pres = $('[name=id_presupuesto]').val();
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: 'update_componente_cd',
        data: data,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Título actualizado con éxito');
                $("#com-"+id_cd_compo+" td").find("input[name=descripcion]").attr('disabled',true);
                $("#com-"+id_cd_compo+" td").find("i.blue").removeClass('oculto');
                $("#com-"+id_cd_compo+" td").find("i.blue").addClass('visible');
                $("#com-"+id_cd_compo+" td").find("i.green").removeClass('visible');
                $("#com-"+id_cd_compo+" td").find("i.green").addClass('oculto');
                listar_cd(id_pres);
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_compo_cd(id_cd_compo,codigo){
    var anula = confirm("¿Esta seguro que desea anular éste titulo?");
    if (anula){
        var cod_padre = '';
        var hijos_com = [];
        var hijos_par = [];
        var i = 0;

        var filas = document.querySelectorAll('#listaCD tbody tr');
        filas.forEach(function(e){
            var ids = (e.id).split('-');
            var colum = e.querySelectorAll('td');
            cod_padre = colum[8].innerText;
            
            if (cod_padre === codigo){
                if (ids[0] === "com"){
                    hijos_com[i] = ids[1];
                } 
                else if (ids[0] === "par"){
                    hijos_par[i] = ids[1];
                }
                i++;
            }
        });
        var rspta = true;
        if (hijos_com.length > 0 || hijos_par.length > 0){
            rspta = confirm("Este titulo tiene dependientes. \n¿Está seguro que desea anularlo con sus dependientes?");
        }
        if (rspta) {
            var token = $('#token').val();
            var id_pres = $('[name=id_presupuesto]').val();
            var data =  'id_cd_compo='+id_cd_compo+
                        '&cod_compo='+cod_padre+
                        '&id_pres='+id_pres+
                        '&hijos_com='+hijos_com+
                        '&hijos_par='+hijos_par;
            console.log(data);
    
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': token},
                url: 'anular_compo_cd',
                data: data,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0){
                        alert('Titulo anulado con éxito');
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
}
