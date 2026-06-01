function imprimir() {
    var ate_codigo = $("#ate_codigo").val();
  //  alert(ate_codigo);
    var _arraySelecinados = new Array();
console.log(ate_codigo);
console.log('as');
    if ($("input:checkbox[name=imprimir]:checked").length > 0) {
        $(".produto").each( function() {
            if($(this).attr("checked") == "checked") {
                _arraySelecinados.push($(this).val());
            }
        });
    } else {
        _arraySelecinados = null;
    }
    console.log(_arraySelecinados);
    popup(baseUrl+'/prontuario/encaminhamento/imprimir/selecionados/'+_arraySelecinados+'/ate_codigo/'+ate_codigo,'Encaminhamento',1400,600);
}
function imprimirExterno() {
    var ate_codigo = $("#ate_codigo").val();
    //  alert(ate_codigo);
    var _arraySelecinados = new Array();
    console.log(ate_codigo);
    console.log('as');
    if ($("input:checkbox[name=imprimir]:checked").length > 0) {
        $(".produto").each( function() {
            if($(this).attr("checked") == "checked") {
                _arraySelecinados.push($(this).val());
            }
        });
    } else {
        _arraySelecinados = null;
    }
    console.log(_arraySelecinados);
    popup(baseUrl+'/prontuario/encaminhamento/encaminhamento-externo-imprimir/selecionados/'+_arraySelecinados+'/ate_codigo/'+ate_codigo,'Encaminhamento',1400,600);
}