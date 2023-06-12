function generar(){
    var empre = $('#id_empresa').val();
    var plani = $('#id_tipo_planilla').val();
    var mes = $('#mes').val();
    var perio = $('#periodo').val();
    var firma = $('#firma').val();

    if (empre > 0 && plani > 0 && mes > 0 && perio > 0){
        if (plani == 1){
            var periodo = $('#periodo option:selected').text();
            window.open('generar_planilla_pdf/'+empre+'/'+plani+'/'+mes+'/'+periodo+'/'+firma);
        }else{
            alert('Solo Régimen Común puede generar Boleta de Pagos');
        }
    }else{
        alert('Debe seleccionar todos los campos');
    }
}

function generarSPCC(type) {
    var empre = $('#id_empresa').val();
    var plani = $('#id_tipo_planilla').val();
    var mes = $('#mes').val();
    var perio = $('#periodo').val();

    if (empre > 0 && plani > 0 && mes > 0 && perio > 0){
        if (plani == 1){
            var periodo = $('#periodo option:selected').text();
            if (type == 1) {
                window.open('generar_planilla_spcc_pdf_mes/'+empre+'/'+plani+'/'+mes+'/'+periodo);
            }else{
                window.open('generar_planilla_spcc_pdf/'+empre+'/'+plani+'/'+mes+'/'+periodo);
            }
        }else{
            alert('Solo Régimen Común puede generar Boleta de Pagos');
        }
    }else{
        alert('Debe seleccionar todos los campos');
    }
}

function enviarBoleta(){
    var empre = $('#id_empresa').val();
    var plani = $('#id_tipo_planilla').val();
    var mes = $('#mes').val();
    var perio = $('#periodo').val();
    var row = '';

    if (empre > 0 && plani > 0 && mes > 0 && perio > 0){
        if (plani == 1){
            var periodo = $('#periodo option:selected').text();
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: 'filter_trabajadores',
                // url: 'generar_pdf_trabajdor/' + empre + '/' + plani + '/' + mes + '/' + periodo,
                data: 'empresa=' + empre + '&planilla=' + plani + '&mes=' + mes + '&periodo=' + periodo + '&movim=' + 2,
                dataType: 'JSON',
                success: function(response) {
                    if (response.response == 'ok') {
                        var datax = response.data;
                        datax.forEach(function (element, index) {
                            row += `<div class="col-md-4">
                                <div class="form-group" style="font-size: 10.5px">
                                    <input type="hidden" name="datos[]" value="`+ element.apellido_paterno +` `+ element.apellido_paterno +` `+ element.nombres +`">
                                    <input type="hidden" name="correo[]" value="` + element.correo + `">
                                    <input type="hidden" name="dni[]" value="` + element.nro_documento + `">
                                    
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="id_trabajador[]" value="`+ element.id_trabajador +`" checked>
                                            `+ element.apellido_paterno +` `+ element.apellido_paterno +` `+ element.nombres +` <br> (` + element.correo + `)
                                        </label>
                                    </div>
                                </div>
                            </div>`;
                        });
                    }
                    $("#result").html(row);
                    $("[name=data_empresa]").val(empre);
                    $("[name=data_tipo_planilla]").val(plani);
                    $("[name=data_mes]").val(mes);
                    $("[name=data_periodo]").val(periodo);
                    $('#modal-correos').modal('show');
                }
            }).fail( function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        }else{
            alert('Solo Régimen Común puede generar Boleta de Pagos');
        }
    }else{
        alert('Debe seleccionar los 4 primeros campos');
    }
}

function reportePlanillaGrupal(){
    var grupo = $('#nameGrupo').val();
    var plani = $('#id_tipo_planilla').val();
    var mes = $('#mes').val();
    var perio = $('#periodo').val();
    
    if (grupo != null){
        if (plani > 0){
            if (mes > 0){
                if (perio > 0){
                    var periodo = $('#periodo option:selected').text();
                    window.open('reporte_planilla_grupal_xls/'+plani+'/'+mes+'/'+periodo+'/'+grupo);
                }else{
                    alert('Debe seleccionar el periodo');
                    $('#periodo').focus();
                }
            }else{
                alert('Debe seleccionar el mes');
                $('#mes').focus();
            }
        }else{
            alert('Debe seleccionar el tipo de planilla');
            $('#id_tipo_planilla').focus();
        }
    }else{
        alert('Debe seleccionar la gerencia');
        $('#nameGrupo').focus();
    }
}

function reportePlanilla(){
    var empre = $('#id_empresa').val();
    var plani = $('#id_tipo_planilla').val();
    var mes = $('#mes').val();
    var perio = $('#periodo').val();

    if (empre > 0 && plani > 0 && mes > 0 && perio > 0){
        var periodo = $('#periodo option:selected').text();
        window.open('reporte_planilla_xls/'+empre+'/'+plani+'/'+mes+'/'+periodo+'/1/0');
    }else{
        alert('Debe seleccionar todos los campos');
    }
}

function reportePlanillaNueva(){
    var empre = $('#id_empresa').val();
    var plani = $('#id_tipo_planilla').val();
    var mes = $('#mes').val();
    var perio = $('#periodo').val();

    if (empre > 0 && plani > 0 && mes > 0 && perio > 0){
        var periodo = $('#periodo option:selected').text();
        window.open('reporte_planilla_benef_xls/'+empre+'/'+plani+'/'+mes+'/'+periodo+'/1/0');
    }else{
        alert('Debe seleccionar todos los campos');
    }
}

function reportePlanillaSPCC(type){
    var empre = $('#id_empresa').val();
    var plani = $('#id_tipo_planilla').val();
    var mes = $('#mes').val();
    var perio = $('#periodo').val();

    if (empre > 0 && plani > 0 && mes > 0 && perio > 0){
        var periodo = $('#periodo option:selected').text();
        if (type == 1) {
            window.open('reporte_planilla_spcc_xls_mes/'+empre+'/'+plani+'/'+mes+'/'+periodo);
        }else{
            window.open('reporte_planilla_spcc_xls/'+empre+'/'+plani+'/'+mes+'/'+periodo);
        }
    }else{
        alert('Debe seleccionar todos los campos');
    }
}

function generarBoletaUnica(){
    $('#modal-plani-ind').modal({show: true, backdrop: 'static'});
    $('#modal-plani-ind').on('shown.bs.modal', function(){
        $('[name=name_empleado]').focus();
    });
}

function processBoleta(){
    var empre = $('#id_empresa').val();
    var plani = $('#id_tipo_planilla').val();
    var mes = $('#mes').val();
    var perio = $('#periodo').val();
    var empleado = $('[name=id_trabajador]').val();
    var firma = $('#firma').val();

    if (empre > 0 && plani > 0 && mes > 0 && perio > 0 && empleado > 0){
        var periodo = $('#periodo option:selected').text();
        window.open('reporte_planilla_trabajador_pdf/'+empre+'/'+plani+'/'+mes+'/'+periodo+'/'+empleado+'/'+firma);
    }else{
        alert('Debe seleccionar todos los campos');
    }
}

function reporteGastos(){
    var plani = $('#id_tipo_planilla').val();
    var mes = $('#mes').val();
    var perio = $('#periodo').val();

    if (plani > 0){
        if (mes > 0){
            if (perio > 0){
                var periodo = $('#periodo option:selected').text();
                window.open('reporte_gastos/'+plani+'/'+mes+'/'+periodo);
            }else{
                alert('Debe seleccionar el periodo');
                $('#periodo').focus();
            }
        }else{
            alert('Debe seleccionar el mes');
            $('#mes').focus();
        }
    }else{
        alert('Debe seleccionar el tipo de planilla');
        $('#id_tipo_planilla').focus();
    }
}

function reporteResumen(){
    var plani = $('#id_tipo_planilla').val();
    var mes = $('#mes').val();
    var perio = $('#periodo').val();

    if (plani > 0){
        if (mes > 0){
            if (perio > 0){
                var periodo = $('#periodo option:selected').text();
                window.open('resumen_planilla/'+plani+'/'+mes+'/'+periodo);
            }else{
                alert('Debe seleccionar el periodo');
                $('#periodo').focus();
            }
        }else{
            alert('Debe seleccionar el mes');
            $('#mes').focus();
        }
    }else{
        alert('Debe seleccionar el tipo de planilla');
        $('#id_tipo_planilla').focus();
    }
}