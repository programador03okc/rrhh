$(function(){
    $('#listaGuiasCompra tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        } else {
            $('#listaGuiasCompra').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
        var id = $(this)[0].firstChild.innerHTML;
        var idPr = $(this)[0].childNodes[5].innerHTML;
        $('.modal-footer #mid_guia_com').text(id);
        $('.modal-footer #mid_guia_prov').text(idPr);
    });
});

function listarGuiasCompra(){
    var vardataTables = funcDatatables();
    console.log('des_Estado');
    $('#listaGuiasCompra').dataTable({
        'dom': vardataTables[1],
        'buttons': vardataTables[2],
        'language' : vardataTables[0],
        'ajax': 'mostrar_guias_compra',
        'columns': [
            {'data': 'id_guia'},
            {'data': 'razon_social'},
            {'render':
                function (data, type, row){
                    return (row['serie']+'-'+row['numero']);
                }
            },
            {'render':
                function (data, type, row){
                    return (formatDate(row['fecha_emision']));
                }
            },
            {'data': 'des_estado'},
            {'data': 'id_proveedor'},
        ],
        'columnDefs': [{ 'aTargets': [0,5], 'sClass': 'invisible'}],
    });
}

function guia_compraModal(){
    $('#modal-guia_compra').modal({
        show: true
    });
    clearDataTable();
    listarGuiasCompra();
}

function selectGuiaCompra(){
    var myId = $('.modal-footer #mid_guia_com').text();
    var idPr = $('.modal-footer #mid_guia_prov').text();
    var page = $('.page-main').attr('type');

    if (page == "guia_compra"){
        var activeTab = $("#tab-guia_compra #myTab li.active a").attr('type');
        var activeForm = "form-"+activeTab.substring(1);
        actualizar_tab(activeForm, myId);
    }    
    $('#modal-guia_compra').modal('hide');
}