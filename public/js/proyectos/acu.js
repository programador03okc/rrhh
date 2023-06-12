$(function(){
    var vardataTables = funcDatatables();
    var tabla = $('#listaAcu').DataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'listar_acus',
        'columns': [
            {'data': 'id_cu'},
            {'data': 'codigo'},
            {'data': 'descripcion'},
            {'data': 'abreviatura'},
            {'data': 'rendimiento'},
            {'data': 'total'},
            {'render':
                function (data, type, row){
                    return ((row['estado'] == 1) ? 'Activo' : 'Inactivo');
                }
            },
            {'defaultContent': 
            '<button type="button" class="editar btn btn-primary boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Editar" >'+
                '<i class="fas fa-edit"></i></button>'+
            '<button type="button" class="anular btn btn-danger boton" data-toggle="tooltip" '+
                'data-placement="bottom" title="Anular" >'+
                '<i class="fas fa-trash"></i></button>'}
        ],
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    botones('#listaAcu tbody',tabla)
    // var tipos;
    // $.ajax({
    //     type: 'GET',
    //     headers: {'X-CSRF-TOKEN': token},
    //     url: 'listar_tipo_insumos',
    //     dataType: 'JSON',
    //     success: function(response){
    //         // console.log(response['data']);
    //         tipos = response['data'];
    //         //Asignamos el valor al objeto localStorage
    //         localStorage.setItem('tipos',JSON.stringify(tipos));
    //     }
    // });

});
function botones(tbody, tabla){
    console.log("editar");
    $(tbody).on("click","button.editar", function(){
        var data = tabla.row($(this).parents("tr")).data();
        open_acu_create(data);
    });
    $(tbody).on("click","button.anular", function(){
        var data = tabla.row($(this).parents("tr")).data();
        anular_acu(data.id_cu);
    });
}
function open_acu_create(data){
    $('#modal-acu_create').modal({
        show: true
    });
    if (data !== undefined){
        console.log(data);
        $('[name=id_cu]').val(data.id_cu);
        $('[name=codigo]').val(data.codigo);
        $('[name=descripcion]').val(data.descripcion);
        $('[name=rendimiento]').val(data.rendimiento);
        $('[name=unid_medida]').val(data.unid_medida);
        $('[name=total_acu]').val(data.total);
        //metodo para traer el detalle
        listar_acu_detalle(data.id_cu);
    } else {
        $('[name=id_cu]').val('');
        $('[name=codigo]').val('');
        $('[name=descripcion]').val('');
        $('[name=rendimiento]').val('');
        $('[name=unid_medida]').val('');
        $('#AcuInsumos tbody').html('');
    }
}
function listar_acu_detalle(id){
    $.ajax({
        type: 'GET',
        headers: {'X-CSRF-TOKEN': token},
        url: 'listar_acu_detalle/'+id,
        dataType: 'JSON',
        success: function(response){
            $('#AcuInsumos tbody').html(response);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
// function mostrar_acu(id){
//     baseUrl = 'mostrar_acu/'+id;
//     $.ajax({
//         type: 'GET',
//         headers: {'X-CSRF-TOKEN': token},
//         url: baseUrl,
//         dataType: 'JSON',
//         success: function(response){
//             // console.log(response);
//             $('[name=id_cu]').val(response['data']['acu'][0].id_cu);
//             $('[name=codigo]').val(response['data']['acu'][0].codigo);
//             $('[name=descripcion]').val(response['data']['acu'][0].descripcion);
//             $('[name=abreviatura]').text(response['data']['acu'][0].abreviatura);
//             $('[name=unid_medida]').val(response['data']['acu'][0].unid_medida);
//             $('[name=total]').val(response['data']['acu'][0].total);
//             $('[name=rendimiento]').val(response['data']['acu'][0].rendimiento);
//             $('[name=observacion]').val(response['data']['acu'][0].observacion);
//             $('[name=estado]').val(response['data']['acu'][0].estado);
//             $('[id=fecha_registro] label').text('');
//             $('[id=fecha_registro] label').append(formatDateHour(response['data']['acu'][0].fecha_registro));
            
//             var insumos = response['data']['acu_detalle'];
//             calculaTotales(insumos);
//             var htmls = '';
//             for (x=0; x<insumos.length; x++){
//                 htmls += '<tr><td>'+insumos[x].id_insumo+'</td><td>'+insumos[x].codigo+'</td><td>'+insumos[x].descripcion+'</td><td>'+insumos[x].cod_tp_insumo+
//                 '</td><td>'+insumos[x].abreviatura+'</td><td>'+insumos[x].cuadrilla+'</td><td>'+insumos[x].cantidad+'</td><td>'+insumos[x].precio_unit+
//                 '</td><td>'+insumos[x].precio_total+'</td><td><button class="btn btn-primary boton"><i class="fas fa-edit"></i></button><button class="btn btn-danger boton"><i class="fas fa-trash-alt"></i></button></td></tr>';
//             }
//             $('#AcuInsumos tbody').html(htmls);
            
//             var presupuestos = response['data']['presupuestos'];
//             console.log(presupuestos);
//             var htmls = '';
//             for (x=0; x<presupuestos.length; x++){
//                 htmls += '<tr><td>'+presupuestos[x].id_presupuesto+'</td><td>'+presupuestos[x].codigo+'</td><td>'+presupuestos[x].descripcion+
//                 '</td><td>'+presupuestos[x].razon_social+'</td><td>'+((presupuestos[x].estado == 1) ? 'Activo' : 'Inactivo' )+'</td></tr>';
//             }
//             $('#AcuPresupuestos tbody').html(htmls);
            
//             var lecciones = response['data']['obs'];
//             console.log(lecciones);
//             var htmls = '';
//             for (x=0; x<lecciones.length; x++){
//                 htmls += '<tr><td>'+lecciones[x].id_obs+'</td><td>'+lecciones[x].codigo+'</td><td>'+lecciones[x].descripcion+
//                 '</td><td>'+lecciones[x].nombre_usuario+'</td><td>'+formatDate(lecciones[x].fecha_registro)+'</td></tr>';
//             }
//             $('#AcuLecciones tbody').html(htmls);
//         }
//     }).fail( function( jqXHR, textStatus, errorThrown ){
//         console.log(jqXHR);
//         console.log(textStatus);
//         console.log(errorThrown);
//     });
// }

function guardar_acu(){
    var id = $('[name=id_cu]').val();
    var des = $('[name=descripcion]').val();
    var ren = $('[name=rendimiento]').val();
    var und = $('[name=unid_medida]').val();
    var tot = $('[name=total_acu]').val();
    var obs = $('[name=observacion]').val();
    var elim = $('[name=anulados]').val();

    var id_det = [];
    var id_insumo = [];
    var cuadrilla = [];
    var cantidad = [];
    var unitario = [];
    var total = [];
    var i = 0;

    var filas = document.querySelectorAll('#AcuInsumos tbody tr');
    filas.forEach(function(e){
        var colum = e.querySelectorAll('td');
        id_det[i] = e.id;
        id_insumo[i] = colum[0].innerText;
        cuadrilla[i] = colum[5].innerText;
        cantidad[i] = colum[6].innerText;
        unitario[i] = colum[7].innerText;
        total[i] = colum[8].innerText;
        ++i;
    });

    var datax = 'id_cu='+id+
            '&descripcion='+des+
            '&rendimiento='+ren+
            '&unid_medida='+und+
            '&total_acu='+tot+
            '&observacion='+obs+
            '&id_det='+id_det+
            '&id_insumo='+id_insumo+
            '&cuadrilla='+cuadrilla+
            '&cantidad='+cantidad+
            '&unitario='+unitario+
            '&total='+total+
            '&det_eliminados='+elim;

    var token = $('#token').val();
    console.log(datax);

    var baseUrl;
    if (id !== ''){
        baseUrl = 'actualizar_acu';
    } else {
        baseUrl = 'guardar_acu';
    }
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': token},
        url: baseUrl,
        data: datax,
        dataType: 'JSON',
        success: function(response){
            console.log(response);
            if (response > 0){
                alert('Costo Unitario registrado con éxito');
                $('#listaAcu').DataTable().ajax.reload();
                $('#modal-acu_create').modal('hide');
            }
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function anular_acu(ids){
    if (ids !== ''){
        var rspta = confirm("¿Está seguro que desea anular éste A.C.U?")
        if (rspta){
            baseUrl = 'anular_acu/'+ids;
            $.ajax({
                type: 'GET',
                headers: {'X-CSRF-TOKEN': token},
                url: baseUrl,
                dataType: 'JSON',
                success: function(response){
                    console.log(response);
                    if (response > 0){
                        alert('Costo Unitario anulado con éxito');
                        $('#listaAcu').DataTable().ajax.reload();
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

// function calculaTotales(insumos){
//     let suma = 0;
//     let total = 0;
//     var tipos = JSON.parse(localStorage.getItem('tipos'));
//     let mo = 0;
//     var html = '';

//     if (tipos.length > 0 && insumos.length > 0){
//         for (let tipo of tipos){
//             for (let item of insumos){
//                 if (item.tp_insumo === tipo.id_tp_insumo){
//                     suma = suma + parseFloat(item.precio_total);
//                 }
//             }
//             if (tipo.id_tp_insumo == 1){
//                 mo = suma;
//             }
//             tipo.suma = (suma).toFixed(4);
//             total += suma;
//             suma = 0;
//             console.log(tipo.descripcion+'='+tipo.suma);
//             if (tipo.suma > 0){
//                 html += '<tr><td>'+tipo.descripcion+'</td><td>S/</td><td>'+tipo.suma+'</td></tr>';
//             }
//         }
//         console.log(total);
//         $('[name=total]').val(total);
//         $('#acu-totales tbody tr').remove();
//         $('#acu-totales tbody').append(html);
//         if (mo > 0){
//             localStorage.setItem('mo',mo);
//         }
//     }
// }

function calculaCantidad(){
    let tipo = $('[name=tp_insumo]').val();
    let rend = $('[name=rendimiento]').val();
    console.log(tipo);
    if (rend !== null && rend !== ""){
        if (tipo !== null && tipo !== ""){
            let cuad = $('[name=cuadrilla]').val();
            let jornal = 8; //revisar que sea un dato ingresable
        
            if (tipo == 'MA'){//MATERIALES
                cant = cuad;
            } else {
                cant = ((cuad * jornal)/rend).toFixed(2);
            }
            console.log('cantidad ' + cant);
            $('[name=cantidad]').val(cant);
            calculaPrecioTotal();
        } else {
            alert("Es necesario que seleccione un Insumo");
        }    
    } else {
        alert("Es necesario que ingrese un Rendimiento");
        $('[name=cuadrilla]').val('');
    }
}

function calculaPrecioTotal(){
    var cant = $('[name=cantidad]').val();
    var unit = $('[name=precio_unitario]').val();
    var id_insumo = $('[name=id_insumo]').val();

    let precio_tot = 0;
    if (cant !== null && unit !== null){
        precio_tot = (cant * unit).toFixed(4);//convierte a 2 decimales
    }
    if (id_insumo == 326){// SI ES HERRAMIENTAS MANUALES
        precio_tot = (precio_tot/100).toFixed(4);
    }
    $('[name=precio_total]').val(precio_tot);

}
function actualizaTotal(){
    var total = 0;
    var filas = document.querySelectorAll('#AcuInsumos tbody tr');
    filas.forEach(function(e){
        var colum = e.querySelectorAll('td');
        total += parseFloat(colum[8].innerText);
        console.log(total);
    });
    // $('#AcuInsumos tbody tr').each(function(e) {
    //     console.log($(this)[0]);
    //     var col = e.querySelectorAll('td');
    //     console.log(col[8].value);
    //     // console.log($(this)[0].closest('td').siblings()[0].firstChild.value);
    //     total += parseFloat($(this)[0].childNodes[8].innerHTML);
    //     console.log(total);
    // });
    $('[name=total_acu]').val(total);
}
function agregar(){

    var id = $('[name=id_insumo]').val();
    var cod = $('[name=cod_insumo]').val();
    var des = $('[name=des_insumo]').val();
    var tipo = $('[name=tp_insumo]').val();
    var unid = $('[name=unidad]').val();
    var cuad = $('[name=cuadrilla]').val();
    var cant = $('[name=cantidad]').val();
    var unit = $('[name=precio_unitario]').val();
    var tot = $('[name=precio_total]').val();

    var fila='<tr id="0"><td>'+id+'</td><td>'+cod+'</td><td>'+des+'</td><td>'+tipo+
    '</td><td>'+unid+'</td><td>'+cuad+'</td><td>'+cant+'</td><td>'+unit+'</td><td>'+tot+
    '</td><td><button class="btn btn-danger boton" onClick="anular('+id+
    ');"><i class="fas fa-trash-alt"></i></button></td></tr>';
    //<button class="btn btn-primary boton" onClick="editar('+id+');"><i class="fas fa-edit"></i></button>
    //<button class="btn btn-success boton oculto" onClick="update('+id+');"><i class="fas fa-save"></i></button>
    $('#AcuInsumos tbody').append(fila);
    actualizaTotal();
    limpiar_nuevo();
}
function editar(id){
    $("#det-"+id+" td").find("input[name=cuad]").attr('disabled',false);
    $("#det-"+id+" td").find("input[name=cant]").attr('disabled',false);
    $("#det-"+id+" td").find(".btn-primary").removeClass('visible');
    $("#det-"+id+" td").find(".btn-primary").addClass('oculto');
    $("#det-"+id+" td").find(".btn-success").removeClass('oculto');
    $("#det-"+id+" td").find(".btn-success").addClass('visible');
}
function update(id){
    $("#det-"+id+" td").find("input[name=cuad]").attr('disabled',true);
    $("#det-"+id+" td").find("input[name=cant]").attr('disabled',true);
    $("#det-"+id+" td").find(".btn-primary").removeClass('oculto');
    $("#det-"+id+" td").find(".btn-primary").addClass('visible');
    $("#det-"+id+" td").find(".btn-success").removeClass('visible');
    $("#det-"+id+" td").find(".btn-success").addClass('oculto');
}
function anular(id){
    var elimina = confirm("¿Esta seguro que desea eliminar éste insumo?");
    if (elimina){
        if (id !== '0'){
            var a = $('[name=anulados]').val();
            if (a == ''){
                a +=id;
            } else {
                a +=','+id;
            }
            $('[name=anulados]').val(a);
        }
        $("#"+id).remove();
        actualizaTotal();
    }
}
function limpiar_nuevo(){
    $('[name=id_insumo]').val("");
    $('[name=cod_insumo]').val("");
    $('[name=des_insumo]').val("");
    $('[name=tp_insumo]').val("");
    $('[name=unidad]').val("");
    $('[name=cuadrilla]').val("");
    $('[name=cantidad]').val("");
    $('[name=precio_unitario]').val("");
    $('[name=precio_total]').val("");
}
function unid_abrev(){
    $unidad = $('select[name="unid_medida"] option:selected').text();
    console.log($unidad);
    $abreviatura = $unidad.split(" - ");
    if ($abreviatura.length > 0){
        console.log($abreviatura[1]);
        $('[name=abreviatura]').text($abreviatura[1]);
    } else {
        $('[name=abreviatura]').text("");
    }
}