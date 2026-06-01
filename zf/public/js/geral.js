$(function(){
	megaBind();

	// Configuraçoes de plugins
	$.datepicker.regional['pt-BR'] = {
		closeText: 'Fechar',
		prevText: '&#x3c;Anterior',
		nextText: 'Pr&oacute;ximo&#x3e;',
		currentText: 'Hoje',
		monthNames: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho', 'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
		monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun', 'Jul','Ago','Set','Out','Nov','Dez'],
		dayNames: ['Domingo','Segunda-feira','Ter&ccedil;a-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sabado'],
		dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
		dayNamesMin: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
		weekHeader: 'Sm',
		dateFormat: 'dd/mm/yy',
		firstDay: 0,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''
	};

	$.datepicker.setDefaults($.datepicker.regional['pt-BR']);
	setTimeout(function(){
		$.validator.addMethod("dataBR", function(value, element) {
			return this.optional(element) || /^((([0][1-9]|[12][0-9])\/02\/(19|20)([13579][26]|[02468][048]))|(([0][1-9]|[1][0-9]|[2][0-8])\/02\/(19|20)([02468][12356]|[013579][13579]))|((([0][1-9]|[12][0-9]|30)\/(0[469]|11)|([0][1-9]|[12][0-9]|3[01])\/(0[13578]|1[02]))\/(19|20)[0-9][0-9]))$/.test(value);
		}, "Informe uma data válida (dd/mm/aaaa)");

		$.validator.addMethod("dataFutura", function(value, element) {
				var p = value.split("/");
				p = p[2]+p[1]+p[0];

				var hj = new Date();
				var h = [];
				h.push(hj.getDate()<=9?"0"+hj.getDate():hj.getDate());
				h.push(hj.getMonth()<=9?"0"+(hj.getMonth()+1):hj.getMonth()+1);
				h.push(hj.getFullYear());
				h = h[2]+""+h[1]+""+h[0];

			return this.optional(element) || p>h;
		}, "A data informada deve ser mai que a data atual.");

		$.validator.setDefaults({
			errorElement: "span",
			errorClass: "ui-state-error",
			errorPlacement: function(error, element) {
				error.insertAfter( element ).addClass("ui-state-error");

				// se for hidden, adicionar classe de erro no input* anterior
				if( element.attr("type") == "hidden"){
						element.prev().addClass("ui-state-highlight");
				}
			},
			/*submitHandler: function() {
					alert("submitted!");
			},*/
			highlight: function(input) {
					$(input).addClass("ui-state-highlight");
			},
			unhighlight: function(input) {
				$(input).removeClass("ui-state-highlight");
				if( $(input).attr("type") == "hidden"){
						$(input).prev().removeClass("ui-state-highlight");
				}
			}
		});
	}, 150);

	// jqGrid
	try{
		jQuery.extend(jQuery.jgrid.edit, {
			errorTextFormat: function (data) {
				var json = $.parseJSON(data.responseText);
				return json.error;
			}
		});
	} catch(e){	}

	// novo método
	$.fn.hasAttr = function(name) {
	   return this.attr(name) !== undefined;
	};
});

function megaBind(_pai){

	$("#tabs, .abas", _pai).tabs();

	// foco automático
	$(".focus", _pai).focus();

	// Confirma excluir
	$("a.excluir", _pai).click(function(e){
		var url = $(this).attr("href");

		e.preventDefault();
		e.stopPropagation();
		$("#sys").append("<div id=\"excluir-dialog\" title=\"Confirmação\">Deseja realmente excluir este item?</div>");
		$("#excluir-dialog").dialog({
			modal: true,
			width: 300,
			height: 140,
			buttons:{
				Sim: function(){
					window.location.href = url;
				},
				"Não": function(){
					$("#excluir-dialog").dialog("destroy").remove();
				}
			}
		})
	});

	// confirmção personalizada
	$("a.confirm", _pai).click(function(e){
		var url = this.href;
		var msg = $(this).attr("rel");
		e.preventDefault();
		e.stopPropagation();
		confirme("Confirmação",msg,300,140,function(){
			window.location.href = url;
		});
	});

	// icones nos botões (<a>)
	/**
	 * As imagens devem estar no diretório /zf/public/images/icons
	 */
	$.each(['salvar','salvar-icon','login','limpar','imprimir','historico','novo','voltar','finalizar','detalhes','atualizar','barcode','buscar','add','alta','paciente','procura','excluir3','tempo_pausa','tempo_periodo','transferir'], function(index, value) {
		$("a."+value, _pai).html(function(){
			return "<div><img src=\""+baseUrl+"/public/images/icons/"+value+".png\" /></div>"+$(this).html();
		});
	});

	// a.submit não tem imagem (usado em 'imprimir selecionados', 'buscar')
	$("a.salvar, a.submit", _pai).bind("click",function(e){
		e.preventDefault();

		// adicionar opção de bloqueio da função
		// alguma função que queira bloquear o submit
		// deve declarar uma variável global _canSave = false
		if( $(this).hasClass("ui-state-disabled") || (typeof(_canSave) != "undefined" && !_canSave) )
			return false;

		$(this).parents("form").trigger("submit");
	});

	// CTRL+S somente no submit do primeiro form
	// submits feitos com <a class="submit"> devem ter seu próprio atalho
	$("a.salvar:not([data-atalho]):first", _pai).attr("data-atalho","CTRL+S");

	$("a.limpar", _pai).bind("click",function(e){
		e.preventDefault();
		$(this).parents("form").trigger("reset");
		$(".mask").val(" ");
	});
	$("a.limpar:first", _pai).attr("data-atalho","CTRL+L");

	// a.popup abre em popup!
	/**
	 * Converte <a> em popup
	 * @exemple <a href="pagina.php" class="popup" rel="500x300">Abrir popup</a>
	 */
	$("a.popup", _pai).click(function(e){
		e.preventDefault();
		e.stopPropagation();

		if($(this).hasClass("ui-state-disabled"))
			return false;

		var rel = $(this).attr("rel").split("x");
		var url = $(this).attr("href");
		popup(url, url, rel[0], rel[1]);

		return false;
	});

	// a.modal abre um dialog, com ajax
	$("a.modal", _pai).click(function(e){
		var fazerMegaBind = ($(this).data("megabind"));

		var title = $(this).attr("title");
		if(!title)
			title = document.title;

		var rel = $(this).attr("rel").split("x");
		var pos = $(this).attr("pos");
		if($(".posic")){
			var x = jQuery(this).position().left + jQuery(this).outerWidth();
			var y = jQuery(this).position().top - jQuery(document).scrollTop();
		}else{
			var x = "middle";
			var y = 0;
		}

		var url = $(this).attr("href");
		e.preventDefault();
		e.stopPropagation();
		$("#sys").append("<div id=\"modal-dialog\" title=\""+title+"\"></div>");
                $("#modal-dialog").css("display","none");
		$("#modal-dialog").load(url)
		.dialog({
			modal: true,
			width: rel[0],
			height: rel[1],
			position: [x,y],
			buttons:{
				Fechar: function(){
					// $("#modal-dialog").dialog({ autoOpen: false });
					//jQuery('#modal-dialog').dialog('close');
					jQuery(this).dialog('close');
					//$("#modal-dialog").dialog("destroy").remove();
						//  $("#modal-dialog").dialog("close");
				}
			},
			success: function(){
				if(fazerMegaBind) {
				megaBind($("#modal-dialog"));
				}
			}
		});
	});

	// data
	/**
	 * @exemple <input name="usu_datanasc" class="data" />
	 */
	var date = new Date();
	var currentMonth = date.getMonth();
	var currentDate = date.getDate();
	var currentYear = date.getFullYear();
	$("input.data-futura").datepicker({
			maxDate: new Date(currentYear, currentMonth, currentDate)
	});

	$("tr:odd").addClass("odd");
	$("#conf_visita").val("1");
	$("#conf_desfecho").val("1");

	$("input.data", _pai).datepicker();
	$("input.data-mes-ano", _pai).datepicker( {
		changeMonth: true,
		changeYear: true,
		showButtonPanel: true,
		dateFormat: 'mm/yy',
		onClose: function(dateText, inst) {
			var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
			var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
			$(this).datepicker('setDate', new Date(year, month, 1));
		}
	});

	if($(".auto-dialog", _pai).size()){
		var tmp_width = $(".auto-dialog").css("width").replace("px","");
		var tmp_height = $(".auto-dialog").css("height").replace("px","");
		$(".auto-dialog").dialog({
			modal: true,
			width: tmp_width,
			height: tmp_height,
			buttons:{
				Ok: function(){
					$(".auto-dialog").dialog("destroy");
				}
			}
		});
	}

	// efeito zebra em tabelas
	/**
	 *
	 */
	$("table.zebra").find("tr:odd").addClass("zebra1").end().find("tr:even").addClass("zebra2");

	// efeitos visuais
	$("a.ui-button, form label", _pai).addClass("ui-corner-bl ui-corner-tr");
	$("form input,form textearea", _pai).addClass("ui-state-default");
	$("form input[readonly]", _pai).addClass("ui-priority-secondary");
	$("form .textarea-readonly", _pai).addClass("ui-widget-content");

	// Mascaras
	$(".mask", _pai).each(function(){
		$(this).mask( $(this).attr("rel") );
	});

	$(".float", _pai).each(function(){
		var rel = $(this).attr("rel");
		if(rel){
			limit = parseInt(rel.split(",").shift());
			centsLimit = parseInt(rel.split(",").pop());
		}
		$(this).priceFormat({
			prefix: '',
			centsSeparator: '.',
			thousandsSeparator: '',
			limit: limit+centsLimit,
			centsLimit: centsLimit
		});
	});

	$("#busca-comum", _pai).bind("submit",function(e){
		var busca = $("input[name=busca]");
		if(busca.val() == ""){
			e.preventDefault();
			busca.focus();
		}
	});

	// ler atalhos de teclas
	$("[data-atalho]", _pai).each(function(){
		var obj = $(this);
		var atalho = obj.data("atalho");

		obj.append(" <span class=\"hotkey\">("+atalho+")</span>");

		$.Shortcuts.add({
			type: 'down',
			mask: atalho,
			enableInInput: true,
			handler: function(e) {
				e.preventDefault();

				if(obj.hasClass("ui-state-disabled"))
					return false;

				// se o comando for obstrusivo (alert, confirme, prompt...) o firefox tbm executa o atalho
				setTimeout('$("[data-atalho=\''+atalho+'\']").get(0).click()',1);
				//if(obj.get(0).tagName == "A" && typeof(obj.attr("href")) != "undefined")
				//	window.location.href=obj.attr("href");

				return false; // impede o firefox de executar esse atalho
			}
		}).start();
	});

	setTimeout(function(){
		$('textarea.tinymce', _pai).tinymce({
			// Location of TinyMCE script

			script_url : '/WebSocialSaude/zf/public/js/tiny_mce.js',

			// General options
			//theme : "../css/tinymce/advanced",
			theme : "advanced",
			skin : "o2k7",
			//plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

			// Theme options
			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,fontselect,fontsizeselect",
			theme_advanced_buttons2 : "",
			theme_advanced_buttons3 : "",
			theme_advanced_buttons4 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true
		});
	}, 150);
}

function popupLogin(){
	mensagem("Login","Você precisa fazer login para continuar usando o sistema", 300, 150, function(){
		popup(baseUrl.replace("zf","")+"auth.php?popup=1", "login", 735, 412);
	});
}

function popup(url, nomeJanela, w, h){
	var l = (screen.width/2) - (w/2);
	var t = (screen.height/2) - (h/2);

	window.open(url, nomeJanela, 'toolbar=yes,location=yes,directories=no,status=yes,menubar=yes,scrollbars=yes,resizable=yes,width='+w+',height='+h+',top='+t+',left='+l);
}

function carregandoAba(tipo){
	if(tipo==1){
		$("#tabs ul li:first").append("<img src=\""+baseUrl+"/public/images/loading.gif\" "+($("#tabs ul li:first img").size()?"style=\"display:none\"":"")+" />");
	} else {
		$("#tabs ul li:first img:last").remove();
	}
}

function datatimeToBr(dh){
	var partes = dh.split(" ");
	partes[0] = dataToBr(partes[0]);
	return partes.join(" ");
}

/**
 * Entra: 2012-10-30 -> sai: 30/10/2012
 */
function dataToBr(yyyymmdd){
	if(typeof(yyyymmdd) == "undefined"){
		return "";
	}

	try{
		var p = yyyymmdd.split("-");
		return p[2]+"/"+p[1]+"/"+p[0];
	} catch (e){
		return "";
	}

	return "";

}

/**
 * Entra 30/10/2012 sai 2012-10-30
 */
function brToSql(yyyymmdd){
	if(typeof(yyyymmdd) == "undefined"){
		return "";
	}

	try{
		var p = yyyymmdd.split("/");
		return p[2]+"-"+p[1]+"-"+p[0];
	} catch (e){
		return "";
	}

	return "";

}

/**
 * Entra 08:00 sai 08-00
 */
function converteHoraHiffen(hora){
	if(typeof(hora) == "undefined"){
		return "";
	}

	try{
		var p = hora.split(":");
		return p[0]+"-"+p[1]+"-"+p[2];
	} catch (e){
		return "";
	}
	return "";

}

/**
 * Compara duas datas
 * @param data1 data, no formato dd/mm/aaaa
 * @param data2 (opcional) data, no formato dd/mm/aaaa. Valor padrao: hoje
 *
 * @return int 1: a data 1 é maior; 2: a data 2 é maior; 0: as datas são iguais
 */
function compararDatas(data1,data2){
	var d1 = data1.split("/");
	d1 = new Date(d1[2], d1[1]-1, d1[0]);

	var d2;
	if(typeof(data2) == "undefined"){
		d2 = new Date();
	} else {
		d2 = data2.split("/");
		d2 = new Date(d2[2], d2[1]-1, d2[0]);
	}

	if(d1 > d2) {
		return 1;
	} else if(d1 < d2){
		return 2;
	} else {
		return 0;
	}
}

/**
 * Compara duas datas e horas
 * @param dh1 data, no formato Y-m-d H:i:s (sql)
 * @param dh2 (opcional) data, no formato Y-m-d H:i:s (sql). Valor padrao: agora
 *
 * @return int 1: a data 1 é maior; 2: a data 2 é maior; 0: as datas são iguais
 */
function compararDataHora(dh1, dh2){
	var d1 = new Date(sqlToEn(dh1));
	var d2;
	if(typeof(dh2) != "undefined"){
		d2 = new Date(sqlToEn(dh2));
	} else {
		d2 = new Date();
	}

	if(d1 > d2) return 1;
	else if(d1 < d2) return 2;
	else return 0;
}

/**
 * 2012-03-21 08:00:00 => 03/21/2012 08:00:00
 */
function sqlToEn(dataSql){
	var dh = dataSql.split(" ");
	var d = dh[0].split("-");
	return d[1]+"/"+d[2]+"/"+d[0]+" "+dh[1];
}

/**
 * retorno em segundos
 */
function diferencaEntreHoras(dh1, dh2){
	var a = new Date(sqlToEn(dh1));
	var b;
	if(typeof(dh2) != "undefined"){
		b = new Date(sqlToEn(dh2));
	} else {
		b = new Date();
	}

	var n = a-b;
	if(n<0)n*=-1;

	return n/1000;
}

function segundosToHora(n){
	var S = 1;
	var M = S*60;
	var H = M*60;
	var D = H*24;

	var d = Math.floor(n/D);
	var h = Math.floor(n/H)%24;
	var m = Math.floor(n/M)%60;
	var s = Math.floor(n/S)%60;

	if(h <= 9) h = "0"+h;
	if(m <= 9) m = "0"+m;
	if(s <= 9) s = "0"+s;

	var saida = "";
	if(d > 0)
		saida = d+" dia"+(d>1?"s ":" ");

	saida += h+":"+m+":"+s;
	return saida;
}

function mensagem(titulo,mensagem,x,y, callback,detalhe){
        var conteudo_detalhe = "";
        if(detalhe){
            conteudo_detalhe = "<a href='#' onclick='mostraEsconde()'>Mostrar Detalhes >>>"+
                               "<div id=\"detalhe\" style=\"display:none;\"><input type=\"hidden\" id=\"escondida\" value='1'>"+detalhe+"</div>";
        }
	var modal = "<div id=\"mensagem-dialog\" title=\""+titulo+"\">"+mensagem+"<br/>"+
                                conteudo_detalhe+
                                "</div>";
        $("body").append(modal);
	$("#mensagem-dialog").dialog({
		modal: true,
		width: x,
		height: y,
		close: function(){
			$(this).remove();
		},
		buttons: {
			"Ok": function(){
				if(typeof(callback) != "undefined" && callback != null)
					callback();
				$(this).dialog('close');
			}
		}
	});
}

function mostraEsconde(){
    if($("#escondida").val() == 1){
        $("#escondida").val("0");
        $("#detalhe").show();
    }else{
        $("#escondida").val("1");
        $("#detalhe").hide();
    }
}

function mensagemSemOk(id, titulo, mensagem, x, y){
	$("body").append("<div id=\""+id+"\" title=\""+titulo+"\"><div class=\"c\">"+mensagem+"</div>"+imgCarregando()+"</div>");
	$("#"+id).dialog({
		modal: true,
		resizable: false,
		width: x,
		height: y,
		beforeClose: function(){return false}
	});
}

function fecharMensagemSemOk(id){
	$("#"+id).remove();
}

function confirme(titulo,mensagem,x,y,callback){
	$("body").append("<div id=\"confirme-dialog\" title=\""+titulo+"\">"+mensagem+"</div>")
	$("#confirme-dialog").dialog({
		modal: true,
		width: x,
		height: y,
		close: function(){
			$(this).remove();
		},
		buttons: {
			Sim: function(){
				callback();
				$(this).dialog('close');
			},
			"Não": function(){
				$(this).dialog('close');
			}
		}
	});

}

function imgCarregando(){
	return "<div class=\"c\"><img class=\"loading\" src=\""+baseUrl+"/public/images/load.gif\" alt=\"Carregando...\" title=\"Carregando...\" /></div>";
}

function imc(peso, altura) {
	if(peso && altura)
		return number_format(peso/(altura^2),1);

	return "--";
}

/* Cópia da função do php */
function number_format (number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');}
    return s.join(dec);
}

function abreviaNome(nomecompleto,maxtamanho){
    if(nomecompleto.length > maxtamanho){
        var explode = nomecompleto.split(" ");
        var nomecartao = explode[0]+" ";
        var reducao;
        for(i=1;i<(explode.length)-1;i++){
            var nomedomeio = explode[i];

            if ((nomedomeio == "DE") || (nomedomeio == "DA") || (nomedomeio == "E") || (nomedomeio == "DOS") || (nomedomeio == "DAS") || (nomedomeio == "DI")){
                nomecartao += nomedomeio+" ";
            } else {
                reducao = nomedomeio.substr(0, 1);
                nomecartao += reducao+". ";
            }
        }
        nomecartao += explode[i];
    }else{
        nomecartao = nomecompleto;
    }
    //alert(nomecartao);
    return nomecartao;
}


function TestaCPF(strCPF) { var Soma; var Resto; Soma = 0; if (strCPF == "00000000000" || strCPF == "11111111111" || strCPF == "22222222222" || strCPF == "33333333333" || strCPF == "44444444444" || strCPF == "55555555555" || strCPF == "66666666666" || strCPF == "77777777777" || strCPF == "88888888888" || strCPF == "99999999999") return false; for (i=1; i<=9; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (11 - i); Resto = (Soma * 10) % 11; if ((Resto == 10) || (Resto == 11)) Resto = 0; if (Resto != parseInt(strCPF.substring(9, 10)) ) return false; Soma = 0; for (i = 1; i <= 10; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (12 - i); Resto = (Soma * 10) % 11; if ((Resto == 10) || (Resto == 11)) Resto = 0; if (Resto != parseInt(strCPF.substring(10, 11) ) ) return false; return true; };

function SomenteNumero(e){
    var tecla=(window.event)?event.keyCode:e.which;
    if((tecla>47 && tecla<58)) return true;
    else{
    	if (tecla==8 || tecla==0) return true;
	else  return false;
    }
}

function replaceSpecialChars(str) {

	var specialChars = [
	{val:"a",let:"áàãâä"},
	{val:"e",let:"éèêë"},
	{val:"i",let:"íìîï"},
	{val:"o",let:"óòõôö"},
	{val:"u",let:"úùûü"},
	{val:"c",let:"ç"},
	{val:"A",let:"ÁÀÃÂÄ"},
	{val:"E",let:"ÉÈÊË"},
	{val:"I",let:"ÍÌÎÏ"},
	{val:"O",let:"ÓÒÕÔÖ"},
	{val:"U",let:"ÚÙÛÜ"},
	{val:"C",let:"Ç"},
	{val:"",let:"?!()"},
	{val:"",let:"/"},
        {val:"",let:"'"},
	{val:"",let:":"},
        {val:"",let:"-"}
        ];

	var $spaceSymbol = '';
	var regex;
	var returnString = str;
	for (var i = 0; i < specialChars.length; i++) {
		regex = new RegExp("["+specialChars[i].let+"]", "g");
		returnString = returnString.replace(regex, specialChars[i].val);
		regex = null;
	}
	return returnString.replace(/\s/g,$spaceSymbol);
};

function formToJSON(f) {
    var fd = $(f).serializeArray();
    var d = {};
    $(fd).each(function() {
        if (d[this.name] !== undefined){
            if (!Array.isArray(d[this.name])) {
                d[this.name] = [d[this.name]];
            }
            d[this.name].push(this.value);
        }else{
            d[this.name] = this.value;
        }
    });
    return d;
}

function buscaProcedimentosSus(){
    $("#proc_nome").buscar({
        url: baseUrl + '/procedimento/buscar/',
        minLength: 3,
        template: function(ul, item) {
            return $("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function() {
            return true;
        }
    });
}

function validaRaca(raca){
	var retorno = null;
    if(raca == '' || raca == null)  {
        retorno = "false";
    } else {
        retorno = "true";
    }
    return retorno;
}

function validaNacionalidade(nacionalidade){
	var retorno = null;
    if(nacionalidade=='' || nacionalidade == null)  {
        retorno = "false";
    } else {
        retorno = "true";
    }
    return retorno;
}

function validaDomicilio(usuDom){
	var retorno = null;
    if(usuDom=='' || usuDom == null)  {
        retorno = "false";
    } else {
        retorno = "true";
    }
    return retorno;
}

function VerificaData(e) {

        var cData = $(e).val();
	var data = cData;
	var tam = data.length;
	if (tam != 10) {
		return false;
	}
        var bisexto = 0;
	var dia = data.substr(0,2)
	var mes = data.substr (3,2)
	var ano = data.substr (6,4)

	if(mes > 12){
		return false;
	}

	switch (mes) {
		case '01':
			if (dia <= 31)
			return (true);
		break;

		case '02':
			if ((ano % 4 == 0) || (ano % 100 == 0) || (ano % 400 == 0)) {
				bisexto = 1;
			}
			if ((bisexto == 1) && (dia <= 29)) {
				return true;
			}
			if ((bisexto != 1) && (dia <= 28)) {
				return true;
			}
		break

		case '03':
			if (dia <= 31){
				return (true);
			}
		break;
		
		case '04':
			if (dia <= 30){
				return (true);
			}
		break;

		case '05':
			if (dia <= 31){
				return (true);
			}
		break;

		case '06':
			if (dia <= 30){
				return (true);
			}
		break;

		case '07':
			if (dia <= 31){
				return (true);
			}
		break;
		
		case '08':
			if (dia <= 31){
				return (true);
			}
		break;

		case '09':
			if (dia <= 30){
				return (true);
			}
		break;
		
		case '10':
			if (dia <= 31){
				return (true);
			}
		break;

		case '11':
			if (dia <= 30){
				return (true);
			}
		break;

		case '12':
			if (dia <= 31){
				return (true);
			}
		break;
	}

	{
		return false;
	}
	return true;
}

function buscarRua(){
    $("#rua_codigo").val("");
    //$("#rua_cep").val("");
    $("#rua_nome").buscar({
        url: baseUrl+'/rua/buscar',
        template : function(ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + ""
                    + "<br/><strong>Bairro:</strong>"+ item.data.bai_nome
                    + "</a>&nbsp;").appendTo(ul);
        },
        callback: function(event,ui){
            $("#rua_codigo").val(ui.item.id);
            $("#rua_cep").val(ui.item.data.rua_cep);
            $("#rua_bairro").val(ui.item.data.bai_nome);
            $("#bai_codigo").val(ui.item.data.bai_codigo);
            $("#rua_bairro").attr("disabled");
            $("#localidade").val( ui.item.data['cid_nome'] + " - Distrito: "+ui.item.data['dis_nome']);
            $("#editar_rua").show();
            getEnderecos();
        }
    });
}

function getEnderecos(){
    //alert($("#dom_numero_b").val());
     $.ajax({
        url: baseUrl+"/default/paciente/buscar-numeros-de-domicilio-por-endereco/",
        type: "POST",
        data: {
            rua_codigo: $("#rua_codigo").val(),
            rua_cep: $("#rua_cep").val(),
            rua_bairro: $("#rua_bairro").val(),
            dom_numero: $("#dom_numero").val(),
            cid_codigo: $("#cid_codigo").val(),
            co_tipo_logradouro: $("#co_tipo_logradouro").val(),
            rua_nome:$("#rua_nome").val()
        },
        success: function(json){
              var tr = "<tr class=\"notfirst\">"+
                            "<th class=\"gridtable\">Logradouro </th>"+
                            "<th class=\"gridtable\">Nº </th>"+
                            "<th class=\"gridtable\">CEP </th>"+
                            "<th class=\"gridtable\">Bairro </th>"+
                            "<th class=\"gridtable\">Responsável </th>"+
                            "<th class=\"gridtable\">Opções </th>"+
                        "</tr>";
              for(var i in json){
                  if(json[i].usu_nome == "null" || json[i].usu_nome == "" || json[i].usu_nome == null){
                      json[i].usu_nome = "Não Informado";
                  }
                  if(json[i].dom_numero == "null" || json[i].dom_numero == "" || json[i].dom_numero == null || json[i].dom_numero == 0){
                      json[i].dom_numero = "S/N";
                  }

                  tr += "<tr class=\"regis notfirst hover_class\" >"+
                            "<td class=\"gridtable\">"+json[i].rua_nome+"</td>"+
                            "<td class=\"gridtable\">"+json[i].dom_numero+"</td>"+
                            "<td class=\"gridtable\">"+json[i].rua_cep+"</td>"+
                            "<td class=\"gridtable\">"+json[i].rua_bairro+"</td>"+
                            "<td class=\"gridtable\">"+json[i].usu_nome+"</td>"+
                            "<td class=\"gridtable\" width=\"50\"><img src=\""+baseUrl+"/public/images/selecionar_on.jpg\" onclick=\"selecionaEndereco('"+json[i].dom_numero+"','"+json[i].co_tipo_logradouro+"','"+json[i].ds_tipo_logradouro+"','"+json[i].rua_nome+"','"+json[i].rua_codigo+"','"+json[i].rua_cep+"','"+json[i].rua_bairro+"','"+json[i].dom_codigo+"','"+json[i].usu_codigo+"','"+json[i].usu_nome+"','"+json[i].cid_nome.replace("'", "")+"','"+json[i].cid_codigo+"','"+json[i].bai_codigo+"')\"></td>"+
                        "</tr>";
              }
              $("#results").html(tr);
        }
     });
}

function addRua(){
    window.open(baseUrl + "/rua/novo/popup/1","_blank", "scrollbars=1,height=800,width=900",'width=850,height=700');
}

function editarRua(){
    var rua_codigo = $("#rua_codigo").val();
    if(rua_codigo){
        window.open(baseUrl + "/rua/editar/popup/1/id/"+rua_codigo,"_blank", "scrollbars=1,height=800,width=900",'width=850,height=700');
    }else{
        mensagem("Atenção","Rua não selecionada para edição",300,150);
    }
}

function buscaParticipante() {
	//console.log("teste");
    var idNome = $("#id_nome").val();
    var idCodigo = $("#id_codigo").val();
    var idData = $("#id_data").val();
    var idButton = $("#id_button").val();
    var tipo = $("#id_tipo").val();
	var ativCol = $("#ativCol").val();
	//var tipoDeBusca = $("#tipo_de_busca").val();
    $("#"+idNome).buscar({
        delay: 10,
        minLength: 3,
		//url: baseUrl+'/paciente/buscar/tipo_de_busca/'+tipoDeBusca,
		url: baseUrl+'/paciente/buscar',
        callback: function(event, ui){
            var cns = ui.item.data.usu_cartao_sus;
            var usuCodigo = ui.item.id;
            var nome = ui.item.label;
            var nomeMae = ui.item.data.usu_mae;
            var dtNasc = ui.item.data.usu_datanasc;
            var usuNasc = ui.item.data.cd_nacionalidade;
            var usuRaca = ui.item.data.rac_codigo;
            var usuDom = ui.item.data.dom_codigo;
            if ((cns!="" && cns!=null && cns!="undefined") && (validaNacionalidade(usuNasc)=="true") && (validaRaca(usuRaca)=="true") && (validaCnsDigitado(cns)=="true") && (validaEspacoNome(nome)=="true") && (validaEspacoNomeMae(nomeMae)=="true")){
                    if (idNome!="" && idNome!="null" && idNome!="undefined") {
                        $("#"+idNome).val(nome);    
                    }
                    if (idCodigo!="" && idCodigo!="null" && idCodigo!="undefined") {
                        $("#"+idCodigo).val(usuCodigo);
                    }
                    if (idData!="" && idData!="null" && idData!="undefined") {
                        $("#"+idData).val(dtNasc);
                    }
                    if (idButton!="" && idButton!="null" && idButton!="undefined") {
                        $("#"+idButton).show();
                    }
                    // A - Agendamento
                    if (tipo=='A') {
                        carregarHistoricoDoPaciente();
                    }
            } else {
                atualizaCnsParticipante(usuCodigo,idNome,idData,ativCol);
            }
        }
    });
}
function buscaParticipanteTipo() { 
	//console.log("teste");
    var idNome = $("#id_nome").val();
    var idCodigo = $("#id_codigo").val();
    var idData = $("#id_data").val();
    var idButton = $("#id_button").val();
    var tipo = $("#id_tipo").val();
	var ativCol = $("#ativCol").val();
	var tipoDeBusca = $("#tipo_de_busca").val();
    $("#"+idNome).buscar({
        delay: 10,
        minLength: 3,
		url: baseUrl+'/paciente/buscar/tipo_de_busca/'+tipoDeBusca,
		//url: baseUrl+'/paciente/buscar',
        callback: function(event, ui){
            var cns = ui.item.data.usu_cartao_sus;
            var usuCodigo = ui.item.id;
            var nome = ui.item.label;
            var nomeMae = ui.item.data.usu_mae;
            var dtNasc = ui.item.data.usu_datanasc;
            var usuNasc = ui.item.data.cd_nacionalidade;
            var usuRaca = ui.item.data.rac_codigo;
            var usuDom = ui.item.data.dom_codigo;
            if ((cns!="" && cns!=null && cns!="undefined") && (validaNacionalidade(usuNasc)=="true") && (validaRaca(usuRaca)=="true") && (validaCnsDigitado(cns)=="true") && (validaEspacoNome(nome)=="true") && (validaEspacoNomeMae(nomeMae)=="true")){
                    if (idNome!="" && idNome!="null" && idNome!="undefined") {
                        $("#"+idNome).val(nome);    
                    }
                    if (idCodigo!="" && idCodigo!="null" && idCodigo!="undefined") {
                        $("#"+idCodigo).val(usuCodigo);
                    }
                    if (idData!="" && idData!="null" && idData!="undefined") {
                        $("#"+idData).val(dtNasc);
                    }
                    if (idButton!="" && idButton!="null" && idButton!="undefined") {
                        $("#"+idButton).show();
                    }
                    // A - Agendamento
                    if (tipo=='A') {
                        carregarHistoricoDoPaciente();
                    }
            } else {
                atualizaCnsParticipante(usuCodigo,idNome,idData,ativCol);
            }
        }
    });
}

function validaCnsDigitado(vlr_cns){
    var usuSemCns = $("#usu_sem_cns:checked").val();
    if (usuSemCns!="1") {
        if ( (vlr_cns.substring(0,1) != "7")  && (vlr_cns.substring(0,1) != "8") && (vlr_cns.substring(0,1) != "9") ){
            return validaCNS(vlr_cns);
        }else{
            return ValidaCNS_PROV(vlr_cns);
        }
    } else {
        return "true";
    }
}

function validaCNS(vlrCNS) {
    // Formulário que contem o campo CNS
    var soma = new Number;
    var resto = new Number;
    var dv = new Number;
    var pis = new String;
    var resultado = new String;
    var tamCNS = vlrCNS.length;
    var resposta = null;
    if ((tamCNS) != 15) {
        resposta = "false";
    }
    pis = vlrCNS.substring(0,11);
    soma = (((Number(pis.substring(0,1))) * 15) +
            ((Number(pis.substring(1,2))) * 14) +
                ((Number(pis.substring(2,3))) * 13) +
                ((Number(pis.substring(3,4))) * 12) +
        ((Number(pis.substring(4,5))) * 11) +
        ((Number(pis.substring(5,6))) * 10) +
        ((Number(pis.substring(6,7))) * 9) +
        ((Number(pis.substring(7,8))) * 8) +
        ((Number(pis.substring(8,9))) * 7) +
        ((Number(pis.substring(9,10))) * 6) +
        ((Number(pis.substring(10,11))) * 5));
    resto = soma % 11;
    dv = 11 - resto;
    if (dv == 11) {
            dv = 0;
    }
    if (dv == 10) {
            soma = (((Number(pis.substring(0,1))) * 15) +
                ((Number(pis.substring(1,2))) * 14) +
                    ((Number(pis.substring(2,3))) * 13) +
                    ((Number(pis.substring(3,4))) * 12) +
            ((Number(pis.substring(4,5))) * 11) +
            ((Number(pis.substring(5,6))) * 10) +
            ((Number(pis.substring(6,7))) * 9) +
            ((Number(pis.substring(7,8))) * 8) +
            ((Number(pis.substring(8,9))) * 7) +
            ((Number(pis.substring(9,10))) * 6) +
            ((Number(pis.substring(10,11))) * 5) + 2);
            resto = soma % 11;
    dv = 11 - resto;
    resultado = pis + "001" + String(dv);
    } else {
            resultado = pis + "000" + String(dv);
    }
    if (vlrCNS != resultado) {
        resposta = "false";
    } else {
        resposta = "true";
    }
    return resposta;
}

function ValidaCNS_PROV(Obj)
{
    var pis;
    var resto;
    var dv;
    var soma;
    var resultado;
    var result;
    var resposta;
    result = 0;

	pis = Obj.substring(0,15);

	if (pis == "")
	   {
	      resposta = "false";
	   }

	if ( (Obj.substring(0,1) != "7")  && (Obj.substring(0,1) != "8") && (Obj.substring(0,1) != "9") )
	   {
            resposta = "false";
           }

 	soma = (   (parseInt(pis.substring( 0, 1),10)) * 15)
			+ ((parseInt(pis.substring( 1, 2),10)) * 14)
			+ ((parseInt(pis.substring( 2, 3),10)) * 13)
			+ ((parseInt(pis.substring( 3, 4),10)) * 12)
			+ ((parseInt(pis.substring( 4, 5),10)) * 11)
			+ ((parseInt(pis.substring( 5, 6),10)) * 10)
			+ ((parseInt(pis.substring( 6, 7),10)) * 9)
			+ ((parseInt(pis.substring( 7, 8),10)) * 8)
			+ ((parseInt(pis.substring( 8, 9),10)) * 7)
			+ ((parseInt(pis.substring( 9,10),10)) * 6)
			+ ((parseInt(pis.substring(10,11),10)) * 5)
			+ ((parseInt(pis.substring(11,12),10)) * 4)
			+ ((parseInt(pis.substring(12,13),10)) * 3)
			+ ((parseInt(pis.substring(13,14),10)) * 2)
			+ ((parseInt(pis.substring(14,15),10)) * 1);

	resto = soma % 11;

	if (resto == 0)
	   {
                resposta = "true";
           }
	else
	   {
            resposta = "false";
           }
        return resposta;
}

function validaEspacoNome(nome){
    var retorno = null;
    if(nome.indexOf(" ")==-1)  {
        retorno = "false";
    } else {
        retorno = "true";
    }
    return retorno;
}

function validaEspacoNomeMae(nomeMae){
    //alert("geralaaaaa");
    var retorno = null;
    if(nomeMae.indexOf(" ")==-1)  {
        retorno = "false";
    } else {
        retorno = "true";
    }
    return retorno;
}

function validaCnsDuplicado(cns){
    var resposta = null;
    var validaCNS = $("#usu_sem_cns:checked").val();
    if (validaCNS>"1") {
        $.ajax({
            url: baseUrl+"/default/paciente/valida-cns-duplicado",
            type: "POST",
            async: false,
            data: { cns: cns},
            success:function(txt){
                if (txt > 0) {
                    resposta = "false";
                } else {
                    resposta = "true";
                }
                alert(resposta);
            }
        });
    } else {
        resposta = "true";
    }
    //alert(resposta);
    return resposta;
}

function atualizaCnsParticipante(usuCodigo,idNome,idData,ativCol){
    $("#"+idNome).val("");
    $("#"+idData).val("");
    var link = null;
    // Validação Atividade coletiva para desabilitar não possui CNS
    link = baseUrl+"/default/paciente/esus-form-paciente-cns/usu_codigo/"+usuCodigo;
    
    $("body").append("<div id='atu_cns_cid' title='Atualização dos dados do cidadão' ></div>");
    $("#atu_cns_cid").html("<img src=" + baseUrl + "/public/images/load.gif alt='Carregando' title='Carregando dados ...' />")
    .dialog({
        modal: false,
        width: 730,
        height: 380
    })
    .load(link);
}

function atualizaCnsSalvar(){
    var idNome = $("#id_nome").val();
    var idCodigo = $("#id_codigo").val();
    var idData = $("#id_data").val();
    var idButton = $("#id_button").val();
    var tipo = $("#id_tipo").val();
    var valoresForm = $("#form-esus-cns").serialize();
    var cns = $("#usu_cartao_sus_mc").val();
    var usuCodigo = $("#usu_codigo_mc").val();
    var nome = $("#usu_nome_mc").val();
    var nomeMae = $("#usu_mae_mc").val();
	var dtNasc = $("#usu_datanasc_mc").val();
	var nacionalidade = $("#cd_nacionalidade_mc").val();
	var raca = $("#rac_codigo_mc").val();
    if(validaCnsDuplicado(cns)=="true") {
        if (validaCnsDigitado(cns)=="true") {
            if (validaEspacoNome(nome)=="true") {
                if (validaEspacoNomeMae(nomeMae)=="true") {
					if(validaRaca(raca)=="true"){
						if(validaNacionalidade(nacionalidade)=="true"){
							$.ajax({
								url: baseUrl+'/default/paciente/esus-form-paciente-cns-salvar',
								type: "POST",
								data: valoresForm,
								success: function(txt) {
									setTimeout(function(){
										if (idNome!="" && idNome!="null" && idNome!="undefined") {
											$("#"+idNome).val(nome.toUpperCase());
											$("#"+idNome).focus();
										}
										if (idCodigo!="" && idCodigo!="null" && idCodigo!="undefined") { 
											$("#"+idCodigo).val(usuCodigo); 
										}
										if (idData!="" && idData!="null" && idData!="undefined") {
											$("#"+idData).val(dtNasc);
										}
										if (idButton!="" && idButton!="null" && idButton!="undefined") {
											$("#"+idButton).show();
										}
										// A - Agendamento
										if (tipo=='A') {
											carregarHistoricoDoPaciente();
										}
										$("#atu_cns_cid").dialog("destroy").remove();
									},150);
								}
							});
						} else {
							$('html, body').animate({ scrollTop: $("#"+idNome).offset().top }, 'slow');
							$(".msgAtencaoMenor").hide();
							$("#cns_nacionalidade").show();
						}
					} else {
						$('html, body').animate({ scrollTop: $("#"+idNome).offset().top }, 'slow');
						$(".msgAtencaoMenor").hide();
						$("#cns_raca").show();
					}
                } else {
                    $('html, body').animate({ scrollTop: $("#"+idNome).offset().top }, 'slow');
                    $(".msgAtencaoMenor").hide();
                    $("#cns_mae").show();
                }
            } else {
                $('html, body').animate({ scrollTop: $("#"+idNome).offset().top }, 'slow');
                $(".msgAtencaoMenor").hide();
                $("#cns_nome").show();
            }
        } else {
            $('html, body').animate({ scrollTop: $("#"+idNome).offset().top }, 'slow');
            $(".msgAtencaoMenor").hide();
            $("#cns_invalido").show();
        }
    } else {
        $('html, body').animate({ scrollTop: $("#"+idNome).offset().top }, 'slow');
        $(".msgAtencaoMenor").hide();
        $("#cns_existe").show();
    }
}

function desabilitaCns(){
    var validaCNS = $("#usu_sem_cns:checked").val();
    if (validaCNS!="1") {
        $("#usu_cartao_sus_mc").attr("readonly",false);
    } else {
        $("#usu_cartao_sus_mc").val("");
        $("#usu_cartao_sus_mc").attr("readonly",true);
    }
}


function mascara(o,f){
    v_obj=o;
    v_fun=f;
    setTimeout("execmascara()",1);
}
function execmascara(){
    v_obj.value=v_fun(v_obj.value);
}
function alphanum( v ){
    v=v.replace(/[^a-zA-Z0-9]/g,"");			//Remove tudo o que não é dígito
    return v;
}


jQuery.fn.extend({

	on: function( types, selector, data, fn, /*INTERNAL*/ one ) {
		var origFn, type;

		// Types can be a map of types/handlers
		if ( typeof types === "object" ) {
			// ( types-Object, selector, data )
			if ( typeof selector !== "string" ) {
				// ( types-Object, data )
				data = data || selector;
				selector = undefined;
			}
			for ( type in types ) {
				this.on( type, selector, data, types[ type ], one );
			}
			return this;
		}

		if ( data == null && fn == null ) {
			// ( types, fn )
			fn = selector;
			data = selector = undefined;
		} else if ( fn == null ) {
			if ( typeof selector === "string" ) {
				// ( types, selector, fn )
				fn = data;
				data = undefined;
			} else {
				// ( types, data, fn )
				fn = data;
				data = selector;
				selector = undefined;
			}
		}
		if ( fn === false ) {
			fn = returnFalse;
		} else if ( !fn ) {
			return this;
		}

		if ( one === 1 ) {
			origFn = fn;
			fn = function( event ) {
				// Can use an empty set, since event contains the info
				jQuery().off( event );
				return origFn.apply( this, arguments );
			};
			// Use same guid so caller can remove using origFn
			fn.guid = origFn.guid || ( origFn.guid = jQuery.guid++ );
		}
		return this.each( function() {
			jQuery.event.add( this, types, fn, data, selector );
		});
	},
	one: function( types, selector, data, fn ) {
		return this.on( types, selector, data, fn, 1 );
	},
	off: function( types, selector, fn ) {
		var handleObj, type;
		if ( types && types.preventDefault && types.handleObj ) {
			// ( event )  dispatched jQuery.Event
			handleObj = types.handleObj;
			jQuery( types.delegateTarget ).off(
				handleObj.namespace ? handleObj.origType + "." + handleObj.namespace : handleObj.origType,
				handleObj.selector,
				handleObj.handler
			);
			return this;
		}
		if ( typeof types === "object" ) {
			// ( types-object [, selector] )
			for ( type in types ) {
				this.off( type, selector, types[ type ] );
			}
			return this;
		}
		if ( selector === false || typeof selector === "function" ) {
			// ( types [, fn] )
			fn = selector;
			selector = undefined;
		}
		if ( fn === false ) {
			fn = returnFalse;
		}
		return this.each(function() {
			jQuery.event.remove( this, types, fn, selector );
		});
	},

	trigger: function( type, data ) {
		return this.each(function() {
			jQuery.event.trigger( type, data, this );
		});
	},
	triggerHandler: function( type, data ) {
		var elem = this[0];
		if ( elem ) {
			return jQuery.event.trigger( type, data, elem, true );
		}
	}
})


function carregarDadosEspeciaisPaciente(codPaciente) {
    if (codPaciente !== "") {
        $.ajax({
            url: baseUrl + "/default/paciente/busca-dados-especiais",
            type: "POST",
            dataType: "JSON",
            async: true,
            data: {codPaciente: codPaciente},
            success: function (data) {

                $("#risco_odonto").prop("value", data.risco_odonto ? data.risco_odonto : "");

                $("#risco_psico").prop("value", data.risco_psico ? data.risco_psico : "");

                $("#gestante").prop("checked", data.usu_esta_gestante);
                $("#risco_gestacao").prop("value", data.risco_gestacao ? data.risco_gestacao : "");
                exibeEstratificacaoRadio('gestante', 'estrat_gestante', 'risco_gestacao');

                $("#neces_especial").prop("checked", data.usu_deficiencia);

                $("#diabetico").prop("checked", data.usu_tem_diabete);
                $("#risco_diabetes").prop("value", data.risco_diabetes ? data.risco_diabetes : "");
                exibeEstratificacaoRadio('diabetico', 'estrat_diabetes', 'risco_diabetes');

                $("#hipertensao").prop("checked", data.usu_tem_hipertensao);
                $("#risco_hipertensao").prop("value", data.risco_hipertensao ? data.risco_hipertensao : "");
                exibeEstratificacaoRadio('hipertensao', 'estrat_hipertensao', 'risco_hipertensao');

                $("#datanascimento").prop("value", data.usu_datanasc);
                $("#risco_idoso").prop("value", data.risco_idoso ? data.risco_idoso : "");
                $("#risco_crianca").prop("value", data.risco_crianca ? data.risco_crianca : "");

                $("#estrat_risco_familiar").prop("value", data.estrat_risco_familiar ? trim(data.estrat_risco_familiar) : "");

                if (document.getElementById("estrat_risco_familiar")) {
                    if ((data.dom_codigo == "" || data.dom_codigo == null)) {
                        document.getElementById("estrat_risco_familiar").disabled = true;
                    } else {
                        document.getElementById("estrat_risco_familiar").disabled = false;
                    }
                }

                //carregaEstratificacaoPaciente(codPaciente);

                //exibeEstratificacaoIdade();


                $("#usu_sexo").prop("value", data.usu_sexo);
                $("#dom_codigo").prop("value", data.dom_codigo);
                exibeInfoPaciente();
                checaSexoGestante();


            }
        });
    } else {
        return false;
    }
}



function checaSexoGestante() {
    var tipoMedicoBloqueio = ["P", "A", "F", "B"];


    if ($('#usu_sexo').val() == 'M' || (tipoMedicoBloqueio.indexOf($('#usr_tipo_medico').val()) != -1 && $('#usr_digitador').val() != 'S')) {
        $('#gestante').attr('disabled', true);
        $('#gestante:checked').attr('disabled', true);
        $('#gestante').attr('value', "f");
        return true;
    } else {
        if ($('#datanascimento').val() !== '' && $('#datanascimento').val() !== 'undefined') {
            if (getIdade() >= 9 && getIdade() <= 60) {
                $('#gestante').removeAttr('disabled');
                return true;
            } else {
                $('#gestante').attr('disabled', true);
                $('#gestante:checked').attr('disabled', true);
                $('#gestante').attr('value', "f");
                if ($('#gestante').is(":checked") && !$('#gestante').is(":disabled")) {
                    mensagem("Atenção", "Idade do usuário incompatível com o status de Gestante! Deve estar entre 9 e 60 anos. ", 300, 150);
                    return false;
                }
                return true;
            }
        } else {
            $('#gestante').attr('disabled', true);
            $('#gestante:checked').attr('disabled', true);
            $('#gestante').attr('value', "f");
        }
        return true;
    }
}

function formataData(date) {
    var dt = new Date(date);
    var dia = dt.getDate();
    if (dia.toString().length == 1)
        dia = "0" + dia;
    var mes = dt.getMonth() + 1;
    if (mes.toString().length == 1)
        mes = "0" + mes;
    var ano = dt.getFullYear();
    return dia + "/" + mes + "/" + ano;
}

function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

function imprimirEncaminhamento(idCentroRegulador) {
	var idCentroRegulador = idCentroRegulador
    $.ajax({
        url: baseUrl + '/agendamento/central-de-regulacao/buscar-nome-encaminhamento',
    	type: 'GET',
    	data: {
			idCentroRegulador: idCentroRegulador
		},
		success : function(retorno){
			var recebeRetorno = retorno
			if (recebeRetorno == "0") {
				alert("Encaminhamento ainda não informado !");
			} else{
				// var recebeRetorno = retorno
			  	var esquerda =500;
				var myWindow = window.open("", "myWindow", "top=100,left="+esquerda+",width=700,height=600");
				myWindow.document.write("<img src = "+'http://'+retorno+"> <script> setTimeout(function(){self.print()}, 500)</script>");
			}
		}
    })
    
}

function imprimirAgendamento(idCentroRegulador) {
	var idCentroRegulador = idCentroRegulador
    $.ajax({
        url: baseUrl + '/agendamento/central-de-regulacao/buscar-nome-encaminhamento-agendamento',
    	type: 'GET',
    	data: {
			idCentroRegulador: idCentroRegulador
		},
		success : function(retorno){
			var recebeRetorno = retorno

			console.log(retorno);
			// return false;
			if (recebeRetorno == "0") {
				alert("Encaminhamento ainda não informado !");
			} else{
				// var recebeRetorno = retorno
			  	var esquerda =500;
				var myWindow = window.open("", "myWindow", "top=100,left="+esquerda+",width=800,height=800");
				myWindow.document.write("<iframe src = "+'http://'+retorno+" style = 'width : 500px; height : 600px'><iframe> <script> setTimeout(function(){self.print()}, 500)</script>");
			}
		}
    })
    
}
