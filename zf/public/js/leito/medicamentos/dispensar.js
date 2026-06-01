$(function(){
    
        $("#mensagemErroAdministracao").hide();
    
	$(".medicamentos").click(function(){
		ate_codigo = $("#ate_codigo").val();
		io_codigo = $("#io_codigo").val();
                prontuario = $("#prontuario").val();		
		url = baseUrl + "/leito/atendimento/index/cod/"+io_codigo+"/ate_codigo/"+ate_codigo+"/#tabs3-2";		
		document.grade.submit();

                if(prontuario == "S"){
                    window.opener.document.location= baseUrl + "/prontuario/internacao-observacao/index/id/"+io_codigo+"/ate_codigo/"+ate_codigo;
                    window.close();
                }else{
                    window.opener.document.location= baseUrl + "/leito/atendimento/index/cod/"+io_codigo+"/ate_codigo/"+ate_codigo;
                    window.close();
                    
                }
		

	});
	
	$("#busca input").autocomplete( {
		source : baseUrl+"/produto/medicamento/limite/15",
		select : function(event, ui) {
			if (ui.item && ui.item.id) {
				adicionar(ui.item.label,ui.item.id);
				$(this).val("").focus();
			} else {
				$(this).val("");
			}
			return false;
		}
	})
	
	$("button").click(function(){
		$("#busca input").autocomplete("search");
	});
	
	// CATEGORIAS
	$("#categorias ul li").bind('click',function(){
		var lgc = $(this).data("codigo");		
		
		$("#categorias").slideUp();
		$("#modelos")
		.slideDown()
		.html( imgCarregando() )
		.load( baseUrl + "/leito/modelo-grade/modelos/categoria/"+lgc, function(){
			bindModelos();
		});
	});
});

function bindModelos(){
	$("a#voltar-categoria").html(function(){
		return "<div><img src=\""+baseUrl+"/public/images/icons/voltar.png\" /></div>"+$(this).html();
	}).bind("click",function(){
		$("#modelos, #categorias").slideToggle();
	});
	
	$("#modelos ul li").bind('click', function(){
		var lgm = $(this).data("codigo");
		adicionarDoModelo(lgm);
	})
}

function adicionarDoModelo(lgm){
	$("#tabs").tabs( "select" , 0 );
	carregandoAba(1);
	
	$.ajax({
		url: baseUrl + "/leito/modelo-grade/modelo/lgm/"+lgm,
		dataType: 'json',
		data:{
			modelo: lgm
		},
		success: function(json){
			$("#lgra_intervalo").val(json.intervalo);
			$("#lgra_repeticoes").val(json.repeticoes);
			
			for(var i in json.produtos){
				adicionar(json.produtos[i].pro_nome, json.produtos[i].pro_codigo);
				$("#li_"+json.produtos[i].pro_codigo+" input").val(json.produtos[i].ligm_quantidade);
			}
			carregandoAba(0);
		}
	});
}

function adicionar(pro_nome, pro_codigo){
    
    //valida se foi selecionado uma administracao
    if($("#administracao_controle").val() != ''){
        if($("#li_"+pro_codigo).size()){
                destacar(pro_codigo);
                return false;
        }

        var li = "<li id=\"li_"+pro_codigo+"\">";
        li += "<input id=\"pro_codigo[]\"  name=\"pro_codigo["+pro_codigo+"]\" value=\"1\" style=\"width:20px;\" />";
        li += "<input type=\"hidden\" name=\"adm["+pro_codigo+"]\" value='"+$("#administracao_controle").val()+"'>";
        li += "<img onclick=\"remover("+pro_codigo+");\" src=\""+baseUrl+"/public/images/icons/excluir.png\" alt=\"Remover\" title=\"Remover\" />";
        li += pro_nome;
        li += ' - ';
        li += $("#administracao_nome").val();
        li += "</li>";
        li += "<li id=\"li_"+$("#administracao_nome").val()+"\">"
        $("#lista").prepend(li);
        bindQuantidade();
        destacar(pro_codigo);
        $(".neutra").prop('selected', true);
        return true;
    }else{
        $("#mensagemErroAdministracao").show();
    }
}

function bindQuantidade(){
	$("li input").unbind()
	.each(function(){
		$(this).bind("click",function(){
			var pro_codigo = /[0-9]+/.exec($(this).attr("name"));
			pro_codigo
			setTimeout('$("#li_'+pro_codigo+' input").select();',500);
		})
	});
}

function remover(pro_codigo){
	$("#li_"+pro_codigo).slideUp(function(){
		$(this).remove();
	});
}

function destacar(pro_codigo){
	$("#li_"+pro_codigo).effect("highlight", {}, 1000);
}

function validarAdministracao(adm_codigo, adm_nome){
    
    if(adm_codigo == 0){
        $("#administracao_controle").val('');
        $("#administracao_nome").val('');
    }else{
        $("#administracao_controle").val(adm_codigo);
        $("#administracao_nome").val(adm_nome);
        $("#mensagemErroAdministracao").hide();
    }
}