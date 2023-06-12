
$(function(){
    var vardataTables = funcDatatables();

    listarTablaProveedores();
 
    // tabs
    $('ul.nav-tabs li a').click(function(){
        $('ul.nav-tabs li').removeClass('active');
        $(this).parent().addClass('active');
        $('.content-tabs section').hide();
        $('.content-tabs section form').removeAttr('type');
        $('.content-tabs section form').removeAttr('form');

        var activeTab = $(this).attr('type');
        var activeForm = "form-"+activeTab.substring(1);
        
        $("#"+activeForm).attr('type', 'register');
        $("#"+activeForm).attr('form', 'formulario');
        changeStateInput(activeForm, true);
        changeStateButton('inicio');
        $(activeTab).show();
        resizeSide();
 
    });

});

function listarTablaProveedores(){
    var vardataTables = funcDatatables();

    $('#ListaProveedores').dataTable({
        'dom': 'frtip',
        'language' : vardataTables[0],
        'processing': true,
        'bDestroy': true,
        'ajax': '/logistica/listar_proveedores',
         
         
        'columnDefs': [{ 'aTargets': [0], 'sClass': 'invisible'}],
    });
    $('#ListaProveedores').DataTable().on("draw", function(){
        resizeSide();
    });

    $('#ListaProveedores tbody').on('click', 'tr', function(){
        if ($(this).hasClass('eventClick')){
            $(this).removeClass('eventClick');
        }else{
            $('#ListaProveedores').dataTable().$('tr.eventClick').removeClass('eventClick');
            $(this).addClass('eventClick');
        }
    });
}

    function limpiarTabla(idElement){
        var table = document.getElementById(idElement);
        for(var i = table.rows.length - 1; i > 0; i--)
        {
            table.deleteRow(i);
        }
        return null;
    }
    

    function nuevo_proveedor(){
        $('#collapseProveedores').addClass('in');
    }
    function editar_proveedor(){
        $('#collapseProveedores').addClass('in');
        var scrollingElement = (document.scrollingElement || document.body);
        scrollingElement.scrollTop = 120;
    }
    function save_proveedor(data, action, frm_active){

        console.log(data);
        console.log(action);
        console.log(frm_active);
        
    }
    function close_collapse(){
        $('#collapseProveedores').removeClass('in');
    }
    
    function verificar_documento(){
        let nro_documento=document.getElementsByName('nro_ruc')[0].value;
        
    }



    $('#form-contribuyente').on('submit', function(){
        console.log('form-contribuyente.....');
    });
    $('#form-cuentas_bancarias').on('submit', function(){
        console.log('form-cuentas_bancarias.....');
    });
