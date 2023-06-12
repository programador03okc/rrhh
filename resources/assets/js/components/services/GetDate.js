
export const fechaHora=()=>{
    var date = new Date()
    var hour = date.getHours();
    var min = date.getMinutes();
    var seg = date.getSeconds();
    var mes = date.getMonth()+1;
    var dia = date.getDate();
    if(mes < 10){mes = '0' + mes;}
    if(dia < 10){dia = '0' + dia;}
    if(hour < 10){hour = '0' + hour;}
    if(min < 10){min = '0' + min;}
    if(seg < 10){seg = '0' + seg;}

    var viewDate = date.getFullYear() + '-' + mes + '-' + dia + ' ' + hour + ':' + min + ':' + seg;
    return viewDate;
}

export const redondeo=(valor,decimales)=>{
    var aux = 1;
    for(let i=0; i<decimales; i++){
        aux *= 10;
    }
    return (valor != null ? (Math.floor((valor * aux) + 0.5)/aux).toFixed(decimales) : '')
}

export const redondeo_dia=(valor,decimales)=>{
    var aux = 1;
    for(let i=0; i<decimales; i++){
        aux *= 10;
    }
    return (valor != null ? (Math.floor((valor * aux) + 0.99)/aux) : '')
}

export const restaFecha=(d,fecha)=>{
    var Fecha = new Date();
    var sFecha = fecha || (Fecha.getFullYear() + "/" + (Fecha.getMonth() +1) + "/" + Fecha.getDate());
    var sep = sFecha.indexOf('/') != -1 ? '/' : '-';
    var aFecha = sFecha.split(sep);
    var fecha = aFecha[0]+'/'+aFecha[1]+'/'+aFecha[2];
    
    fecha= new Date(fecha);
    fecha.setDate(fecha.getDate() - parseInt(d));

    var anno=fecha.getFullYear();
    var mes= fecha.getMonth()+1;
    var dia= fecha.getDate();
    
    mes = (mes < 10) ? ("0" + mes) : mes;
    dia = (dia < 10) ? ("0" + dia) : dia;
    var fechaFinal = anno + sep + mes + sep + dia;

    console.log(fechaFinal);
    return fechaFinal;
}

export const fechaActual=()=>{
    var hoy = new Date();
    var dd = hoy.getDate();
    var mm = hoy.getMonth()+1;
    var yyyy = hoy.getFullYear();

    if (dd<10) {
        dd='0'+dd
    } 
    if (mm<10) {
        mm='0'+mm
    } 
    hoy = yyyy+'-'+mm+'-'+dd;
    return hoy;
}

export const formatFechaDMA=(fechax)=>{
    // console.log(fechax);
    var fecha = new Date(fechax);
    var dd = fecha.getDate();
    var mm = fecha.getMonth()+1;
    var yyyy = fecha.getFullYear();

    if (dd<10) {
        dd='0'+dd
    } 
    if (mm<10) {
        mm='0'+mm
    } 
    var nuevo = dd+'-'+mm+'-'+yyyy;
    return nuevo;
}
// export default function fechaHora(){
//     var date = new Date()
//     var hour = date.getHours();
//     var min = date.getMinutes();
//     var seg = date.getSeconds();
//     var mes = date.getMonth();
//     var dia = date.getDate();
//     if(mes < 10){mes = '0' + mes;}
//     if(dia < 10){dia = '0' + dia;}
//     if(hour < 10){hour = '0' + hour;}
//     if(min < 10){min = '0' + min;}
//     if(seg < 10){seg = '0' + seg;}

//     var viewDate = date.getFullYear() + '/' + mes + '/' + dia + ' ' + hour + ':' + min + ':' + seg;
//     return viewDate;
// }

