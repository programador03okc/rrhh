function agrega_series(id_guia_det, descripcion, cant){
    $('#modal-guia_com_barras').modal({
        show: true
    });
    // clearDataTable();
    listarSeries(id_guia_det);
    $('[name=id_guia_det]').val(id_guia_det);
    $('[name=cant_items]').val(cant);
    $('#descripcion').text(descripcion);
    $('[name=serie_prod]').val('');
    $('#listaBarras tbody').val('');
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
                tr+='<tr id="reg-'+response[i].serie+'"><td hidden>'+response[i].id_prod_serie+'</td><td><input type="text" class="oculto" name="series" value="'+response[i].serie+'"/>'+response[i].serie+'</td><td><i class="btn btn-danger fas fa-trash fa-lg" onClick="eliminar_serie('+"'"+response[i].serie+"'"+');"></i></td></tr>';
            }
            $('#listaBarras tbody').html(tr);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ){
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}
function agregar_serie(){
    var serie = $('[name=serie_prod]').val();
    var items = $('[name=cant_items]').val();
    var cant = $('#listaBarras tbody tr').length;
    var td = '<tr id="reg-'+serie+'"><td hidden>0</td><td><input type="text" class="oculto" name="series" value="'+serie+'"/>'+serie+'</td><td><i class="btn btn-danger fas fa-trash fa-lg" onClick="eliminar_serie('+"'"+serie+"'"+');"></i></td></tr>';
    if ((cant+1)<= items){
        $('#listaBarras tbody').append(td);
        $('[name=serie_prod]').val('');
    } else {
        alert('Ha superado la cantidad del producto!\nYa no puede agregar mas series.');
    }
}
function eliminar_serie(serie){
    var elimina = confirm("¿Esta seguro que desea eliminar la serie "+serie);
    if (elimina){
        var id = $("#reg-"+serie)[0].firstChild.innerHTML;
        if (id !== '0'){
            var a = $('[name=anulados]').val();
            if (a == ''){
                a +=id;
            } else {
                a +=','+id;
            }
            $('[name=anulados]').val(a);
        }
        $("#reg-"+serie).remove();
    }
}
function guardar_series(){
    var guarda = confirm("¿Esta seguro que desea guardar las serie(s)?");
    if (guarda){
        var series = new Array();
        $('input[name*="series"]').each(function(){ 
            var id = $(this).parents("tr").find("td").eq(0).text();
            if (id == 0){
                series.push($(this).val());
            }
        });
        
        var id_guia_det = $('[name=id_guia_det]').val();
        var anulados = $('[name=anulados]').val();
        var token = $('#token').val();
    
        data = 'id_guia_det='+id_guia_det+'&series='+series+'&anulados='+anulados;
        console.log(data);
        
        $.ajax({
            type: 'POST',
            headers: {'X-CSRF-TOKEN': token},
            url: 'guardar_series',
            data: data,
            dataType: 'JSON',
            success: function(response){
                console.log('response'+response);
                if (response > 0){
                    alert('Series registradas con éxito');
                }
            }
        }).fail( function( jqXHR, textStatus, errorThrown ){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
        $('#modal-guia_com_barras').modal('hide');
    }
}
$(function(){
    document.getElementById('importar').addEventListener("change", function(e) {
        var files = e.target.files,file;
        if (!files || files.length == 0) return;
        file = files[0];
        var fileReader = new FileReader();
        fileReader.onload = function (e) {
            var filename = file.name;
            // pre-process data
            var binary = "";
            var bytes = new Uint8Array(e.target.result);
            var length = bytes.byteLength;
            for (var i = 0; i < length; i++) {
                binary += String.fromCharCode(bytes[i]);
            }
            // call 'xlsx' to read the file
            var oFile = XLSX.read(binary, {type: 'binary', cellDates:true, cellStyles:true});
            var result = {};
            oFile.SheetNames.forEach(function(sheetName) {
                var roa = XLS.utils.sheet_to_row_object_array(oFile.Sheets[sheetName]);
                if(roa.length > 0){
                result[sheetName] = roa;
                }
            });
            var td = '';
            var i = 0;
            var items = $('[name=cant_items]').val();
            var cant = $('#listaBarras tbody tr').length; 
            var msj = false;
            console.log('items'+items+' cant'+cant);
            
            for(i=0;i<result.Hoja1.length;i++){
                console.log(result.Hoja1[i].serie);
                td = '<tr id="reg-'+result.Hoja1[i].serie+'"><td hidden>0</td><td><input type="text" class="oculto" name="series" value="'+result.Hoja1[i].serie+'"/>'+result.Hoja1[i].serie+'</td><td><i class="btn btn-danger fas fa-trash fa-lg " onClick="eliminar_serie('+result.Hoja1[i].serie+');"></i></td></tr>';
                cant++;
                if (cant <= items){
                    $('#listaBarras tbody').append(td);
                } else {
                    msj = true;
                }
            }
            if (msj){
                alert('No se cargaron todas las series porque superan a la cantidad del producto.');
            }
        };
        fileReader.readAsArrayBuffer(file);
    });
});