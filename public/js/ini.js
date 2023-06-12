let page;
// $(document).ajaxStart(function () {
// 	Pace.restart();
// });
$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
});
$(document).ready(function(){
	seleccionarMenu(window.location);
	$(":file").filestyle();
	$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
		checkboxClass: 'icheckbox_flat-green',
		radioClass: 'iradio_flat-green'
	});
	$('.js-example-basic-single').select2();
	
    page = $('.page-main').attr('type');
	var form = $('.page-main form[type=register]').attr('id');
	
	if (page == 'asistencia'){
		$('.sidebar-mini').addClass('sidebar-collapse');
	}else if (page == 'datos_rrhh'){
		$('.sidebar-mini').addClass('sidebar-collapse');
	}else if (page == 'planilla'){
		$('.sidebar-mini').addClass('sidebar-collapse');
	}
	// Para los tabs
	$('.page-main section form').removeAttr('type');
	$("#tab-"+page+" section:first").attr('hidden', false);
	$("#tab-"+page+" section:first form").attr('type', 'register');
    
	$('.mytable').css('width', '100%');

    changeStateInput(form, true);
	changeStateButton('inicio');
	
    $('.btn-okc').on('click', function(){
		var forms = $('.page-main form[form=formulario]').attr('id');
		var frm_active = $('.page-main form[type=register]').attr('id');
		if (frm_active == undefined){
			var frm_active = $('.page-main form[type=edition]').attr('id');
		}
		var element = $(this).attr('id');
        
        switch (element){
            case 'btnNuevo':
				changeStateInput(forms, false);
				changeStateButton('nuevo');
				if (page !== 'ubicacion'){
					clearForm(forms);
				}
				if (page == 'guia_compra'){
					nuevo_guia_compra();
				}else if (page == 'guia_venta'){
					nuevo_guia_venta();
				}else if (page == 'doc_compra'){
					nuevo_doc_compra();
				}else if (page == 'doc_venta'){
					nuevo_doc_venta();
				}else if (page == 'tp_combustible'){
					nuevo_tp_combustible();
				}else if (page == 'equi_sol'){
					nuevo_equi_sol();
				}else if (page == 'requerimiento'){
					nuevo_req();
				}else if (page == 'orden'){
					nueva_orden();
				}
            break;
            case 'btnGuardar':
				var data = $("#"+forms).serialize();
				var action = $("#"+forms).attr('type');
				changeStateButton('guardar'); //// enviar a cada formulario SAVE
				eventRegister(page, data, action, frm_active);
				$('#'+forms).attr('type', 'register'); //// enviar a cada formulario SAVE
				changeStateInput(frm_active, true); //// enviar a cada formulario SAVE
            break;
            case 'btnEditar':
                changeStateInput(frm_active, false);
				changeStateButton('editar');
                $('#'+forms).attr('type', 'edition');
				if (page == 'requerimiento'){
					editRequerimiento();
				}else if (page == 'cuadro_comparativo'){
					editValorizaciones();
				}
            break;
			case 'btnAnular':
				var ids = $("#"+forms+' input[primary="ids"]').val();
                var ask = confirm('¿Esta seguro que desea anular?');
            	if (ask){
					if (ids == undefined){
						ids = $("#"+frm_active+' input[primary="ids"]').val();
					}
					anularRegister(page, ids, frm_active);
					changeStateInput(frm_active, true);
            	}
            break;
            case 'btnHistorial':
				changeStateButton('historial');
				openModal(page, frm_active);
            break;
            case 'btnCancelar':
                $('#'+forms).attr('type', 'register');
				changeStateInput(forms, true);
				changeStateButton('cancelar');
				clearForm(forms);
				if (page == 'requerimiento'){
					cancelarRequerimiento();
				}
            break;
        }
    });
});

function resizeSide(){
	var wrapper = document.getElementById("wrapper-okc");
	var altura;
	if (page == 'guia_compra'){
		altura = wrapper.offsetHeight + 400;
	} else {
		altura = wrapper.offsetHeight + 100;
	}
	// console.log(altura);
	$('.sidebar').css('min-height', altura + 'px');
}

function changeStateInput(element, state){
	var evalu = $("#"+element).attr('type');
    if(evalu == 'register'){
		$("#"+element+" .activation").attr('disabled', state);
    }
}

function changeStateButton(type){
	switch(type){
		case 'nuevo':
			$('#btnNuevo').attr('disabled', true);
		    $('#btnGuardar').attr('disabled', false);
		    $('#btnEditar').attr('disabled', true);
		    $('#btnAnular').attr('disabled', true);
		    $('#btnHistorial').attr('disabled', true);
		    $('#btnCancelar').attr('disabled', false);
		break;
		case 'guardar':
			$('#btnNuevo').attr('disabled', false);
		    $('#btnGuardar').attr('disabled', true);
		    $('#btnEditar').attr('disabled', false);
		    $('#btnAnular').attr('disabled', false);
		    $('#btnHistorial').attr('disabled', false);
		    $('#btnCancelar').attr('disabled', true);
		break;
		case 'editar':
			$('#btnNuevo').attr('disabled', true);
		    $('#btnGuardar').attr('disabled', false);
		    $('#btnEditar').attr('disabled', true);
		    $('#btnAnular').attr('disabled', true);
		    $('#btnHistorial').attr('disabled', true);
		    $('#btnCancelar').attr('disabled', false);
		break;
		case 'anular':
			$('#btnNuevo').attr('disabled', false);
		    $('#btnGuardar').attr('disabled', true);
		    $('#btnEditar').attr('disabled', true);
		    $('#btnAnular').attr('disabled', true);
		    $('#btnHistorial').attr('disabled', false);
		    $('#btnCancelar').attr('disabled', true);
		break;
		case 'historial':
			$('#btnNuevo').attr('disabled', false);
		    $('#btnGuardar').attr('disabled', true);
		    $('#btnEditar').attr('disabled', false);
		    $('#btnAnular').attr('disabled', false);
		    $('#btnHistorial').attr('disabled', false);
		    $('#btnCancelar').attr('disabled', true);
		break;
		case 'cancelar':
			$('#btnNuevo').attr('disabled', false);
		    $('#btnGuardar').attr('disabled', true);
		    $('#btnEditar').attr('disabled', true);
		    $('#btnAnular').attr('disabled', true);
		    $('#btnHistorial').attr('disabled', false);
		    $('#btnCancelar').attr('disabled', true);
		break;
		case 'inicio':
			$('#btnNuevo').attr('disabled', false);
		    $('#btnGuardar').attr('disabled', true);
		    $('#btnEditar').attr('disabled', true);
		    $('#btnAnular').attr('disabled', true);
		    $('#btnHistorial').attr('disabled', false);
		    $('#btnCancelar').attr('disabled', true);
		break;
		default:
			$('#btnNuevo').attr('disabled', true);
		    $('#btnGuardar').attr('disabled', true);
		    $('#btnEditar').attr('disabled', true);
		    $('#btnAnular').attr('disabled', true);
		    $('#btnHistorial').attr('disabled', true);
		    $('#btnCancelar').attr('disabled', true);
		break;
	}
}

function seleccionarMenu(url){
    $('ul.sidebar-menu a').filter(function () {
        return this.href == url;
    }).parent().addClass('active');

    $('ul.treeview-menu a').filter(function () {
        return this.href == url;
    }).parentsUntil(".sidebar-menu > .treeview-menu").addClass('active');
}