// '.tbl-content' consumed little space for vertical scrollbar,
// scrollbar width depend on browser/os/platfrom. 
//Here calculate the scollbar width .
export const scrollTable=()=>{

    var scrollWidth  = (parseInt(document.getElementsByClassName('tbl-content')[0].offsetWidth)) - (parseInt(document.getElementById('table-fix').offsetWidth));
    document.getElementById('table-header').style.paddingRight=scrollWidth+"px";
     
     
}