$(function(){
	$(".salvar").click(function(){
		ate_codigo = $("#ate_codigo").val();
		io_codigo = $("#io_codigo").val();	
		window.opener.document.location= baseUrl + "/leito/atendimento/index/cod/"+io_codigo+"/ate_codigo/"+ate_codigo;
		
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
		
	var print = $("#imprimir").val();
	if(typeof print != "undefined" && print != 0 && print != ""){
		popup(baseUrl+'/prontuario/exame/imprimir/selecionados/'+print,'exame',630,540);
	}
	
	// validações    
	// first: form com o procedimento
	$("form:first").validate({
		rules: {
			proc_nome: {
				required: true
			}
		},
		messages: {
			proc_nome: {
				required: "Infome um procedimento"
			}
		}
	});
        
       
	
});