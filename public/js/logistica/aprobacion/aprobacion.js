$(function(){
    $('#form-aprobacion').on('submit', function(){
        var data = $(this).serialize();
        var type = $(this).attr('type');
        var ask = confirm('¿Desea guardar este registro?');

        if (type == 'aprobar'){
            url = '/logistica/aprobar_documento';
            msj = 'Aprobación grabada con éxito';
        }else if (type == 'denegar'){
            url = '/logistica/denegar_documento';
            msj = 'Se anuló el documento con éxito';
        }

        if (ask == true){
            // console.log(data);
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: url,
                data: data,
                beforeSend: function(){
                    $(document.body).append('<span class="loading"><div></div></span>');
                },
                success: function(response){
                    $('.loading').remove();
                    $('#ListaReq').DataTable().ajax.reload();
                    if (response == 'ok') {
                        alert(msj);
                        $('#modal-aprobacion-docs').modal('hide');
                    }
                }
            });
            return false;
        }else{
            return false;
        }
    });

    $('#form-obs-detalle').on('submit', function(){
        var data = $(this).serialize();
        var ask = confirm('¿Desea guardar esta observación?');
        if (ask == true){
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '/logistica/observar_detalles',
                data: data,
                beforeSend: function(){
                    $(document.body).append('<span class="loading"><div></div></span>');
                },
                success: function(response){
                    $('.loading').remove();
                    if (response == 'ok') {
                        alert('Se agregó una observación al Item');
                        $('#modal-obs-motivo').modal('hide');
                    }
                }
            });
            return false;
        }else{
            return false;
        }
    });

    $('#form-obs-requerimiento').on('submit', function(){
        var data = $(this).serialize();
        var ask = confirm('¿Desea guardar esta observación?');
        if (ask == true){
            $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '/logistica/observar_contenido',
                data: data,
                beforeSend: function(){
                    $(document.body).append('<span class="loading"><div></div></span>');
                },
                success: function(response){
                    $('.loading').remove();
                    if (response == 'ok') {
                        alert('Se agregó una observación al Requerimiento');
                        $('#modal-obs-req').modal('hide');
                        $('#ListaReq').DataTable().ajax.reload();
                    }
                }
            });
            return false;
        }else{
            return false;
        }
    });
});

function openModalAprob(id, type){
    $('#modal-aprobacion-docs').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
}

function openModalObs(req, doc, flujo){
    $('#modal-obs-req [name=doc_req]').val(doc);
    $('#modal-obs-req [name=flujo_req]').val(flujo);
    $.ajax({
        type: 'GET',
        url: '/logistica/observar_req/' + req + '/' + doc,
        dataType: 'JSON',
        beforeSend: function(){
            $(document.body).append('<span class="loading"><div></div></span>');
        },
        success: function(response){
            $('.loading').remove();
            $('#obs-req-detalle').html(response.view);
            $('#modal-obs-req [name=id_requerimiento]').val(response.id_req);
            $('#modal-obs-req').modal({show: true, backdrop: 'static'});
        }
    });
    return false;
}