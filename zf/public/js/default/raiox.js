$(function(){
    $("#oculta").hide();
    
    $(".procedimento").click(function(){
        $("#oculta").show();
        var req_codigo = $(".req_codigo",this).val();
        var iframe = "<iframe name='frameZF' src='/WebSocialSaude/raiox/raiox.php?req_codigo="+req_codigo+"' id='frameZF' frameborder='0' marginheight='0' marginwidth='0' scrolling='auto' width='100%' height='1000'></iframe>";
        $("#oculta").html(iframe);
        
    });
    
    $("#procedimento").buscar({
            url: baseUrl+"/procedimento/buscar/",
            template : function(ul, item) {
                    return $("<li></li>").data("item.autocomplete", item).append(
                            "<a>" + item.label + "</a>").appendTo(ul);
            },
            callback: function(){
                 var proc_codigo_sus = $("#proc_codigo_sus").val();
                 var iniciais = proc_codigo_sus.substr(0,4);

                 if(iniciais == "0204" || iniciais == "0205" || iniciais == "0206" || iniciais == "0207" || iniciais == "0210" || iniciais == "0209"){
                     if($("#config").val() == 1){
                         var checked = "checked=checked";
                     }else{
                         var checked = "";
                     }
                     $("#check").html("<input type='checkbox' value='T' name='req_encaminhamento' id='req_encaminhamento'"+checked+"> <b>Encaminhar para laboratório SUS</b>");

                 }else{
                      $("#check").html("");
                 }

                    return true;
            }
    });
    
    $("#buscar1").buscar({
            url: baseUrl+'/paciente/buscar/',
            callback: function(){
                    //carregarItens();
                    return false;
            }
    });
    
    $("#buscar5").buscar({
		url: baseUrl+'/default/usuarios/buscar/',
		categoria: 'categoria',
		template : function(ul, item) {
			return $("<li/>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		},
		callback: function(){
			return true;
		}
	});
});