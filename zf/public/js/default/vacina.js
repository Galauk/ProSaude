$(function(){
	
	// mostra estoque ao abrir
	if($(".estoque").size()){
        carregarEstoque();
    }
	
	$("#carteirinha").disableSelection();
	
	// Ativa a busca do paciente
	try{
		$("#usu_nome").buscar({
            url: baseUrl+'/paciente/buscar',
            template : function(ul, item) {
				return jQuery("<li></li>").data("item.autocomplete", item).append(
					"<a><strong>" + item.label + "</strong>"
					+ "<br><strong>Data Nasc.:</strong> "
					+ item.data.usu_datanasc + " - " + item.data.idade
					+ "<br> <strong>Mãe:</strong> " + item.data.usu_mae
					+ "</a>&nbsp;").appendTo(ul);
			},
			callback: function(event, ui){
                let dados = ui.item.data
                $("#usu_datanasc").val(dados.usu_datanasc + " - " + dados.idade)
                carregarCarteirinha()
            }
		})
	} catch(e){}
	
	if($("#usu_codigo").size() && $("#usu_codigo").val() != ""){
        carregarCarteirinha();
    }
	
	// clique do botão de recarregar as vacinas aplicadas
	$("#rec-vacinas").click(function(e){
		e.preventDefault()
		
		if(!$(this).hasClass("ui-state-disabled")){
			carregarCarteirinha()
		}
	})
	
	// clique do botão de recarregar o estoque
	$("#rec-estoque").click(function(e){
		e.preventDefault()
		carregarEstoque()
	})
	
	// pega o clique no btn de imprimir atestado
	$("#print-atestado").click(imprimirAtestado)
	
	// pega o clique na vacina
	$(".vac, .dose").click(vacina)
    
    $(".trat").click(tratamento)
    
    $(".rev").click(dose)
	
	// trata o clique da vacina:
	function vacina(e){
		var vac = $(this)
		var partes = vac.attr("id").split("_")
		var dose = partes[1]
		var pro_codigo = partes[2]
		
		// verifica se o CTRL está precionado
		if(e.ctrlKey && $(this).hasClass("vacR")){
			return popupReforcos(pro_codigo)
		}
		popupAcoesVacina(pro_codigo, dose)
		return true
    }
    
	function tratamento(e){
        
		var vac = $(this)
		var partes = vac.attr("id").split("_")
		var dose = partes[1]
        var pro_codigo = partes[2]
        
        let t = e.currentTarget.getAttribute('data-trat')
		
		// verifica se o CTRL está precionado
		if(e.ctrlKey && $(this).hasClass("vacR")){
			return popupReforcos(pro_codigo)
		}
        
        popupAcoesVacinaTratamento(pro_codigo, dose, 'T', t)
        
        return true
    }
    
	function dose(e){
		var vac = $(this)
		var partes = vac.attr("id").split("_")
		var dose = partes[1]
        var pro_codigo = partes[2]
        
        let t = e.currentTarget.getAttribute('data-rev')
		
		// verifica se o CTRL está precionado
		if(e.ctrlKey && $(this).hasClass("vacR")){
			return popupReforcos(pro_codigo)
		}
        
        popupAcoesVacinaDose(pro_codigo, dose, 'D', t)
        
        return true
	}
	
	function popupReforcos(pro_codigo){
		$("body").append("<div id=\"reforco-dialog\" title=\"Reforços\">"+$("#vac_R_"+pro_codigo).html()+"</div>")
		$("#reforco-dialog").dialog({
			modal: true,
			width: 550,
			height: 300,
			close: function() {
				$(this).remove()
			},
			buttons: {
				"Ok": function() {
					$(this).dialog('close')
				}
			}
		})
	}
	
	// tratar o click na celula da vacina
	function popupAcoesVacina(pro_codigo, dose, tipo, qtd = null){
		//window.console && console.log("popAcoesVacina();");
				
		var usu_codigo = getUsu()
        
        if(!usu_codigo){
			return false
        }
		
		var ocupado = $("#vac_"+dose+"_"+pro_codigo).is(".vacA, .vacP, .vacZ")
		var buttons = []
				
		var aplicar = {
			id: "btn_aplicar",
			text: "Aplicar",
			click: function() {
				// tem doses abertas?
				var doses = $("#vac_doses_"+pro_codigo+" input").val()
				if(doses == undefined){
					mensagem("Atenção", "Produto sem estoque!", 250, 120)
				} else if(doses == 0){
					mensagem("Atenção", "Não há frascos abertos", 250, 120)
				} else {
                    // aplica a vacina
                    acao(pro_codigo, dose, "A", getData())
                }
				$(this).dialog('close')
			}
		}
		
		var preencher = {
			id: "btn_preencher",
			text: "Preencher",
			click: function() {
                // preenche vacina
                acao(pro_codigo, dose, "P", getData())
				$(this).dialog('close')
			}
		}
		
		var aprazar = {
			id: "btn_aprazar",
			text: "Aprazar",
			click: function() {
                // aprazar
                
                acao(pro_codigo, dose, "Z", getData())
                
				$(this).dialog('close')
			}
		}
		
		var perguntar = function() {
			// cancelar
			confirme("Confirmação","Deseja realemente cancelar esta vacina?", 400,150, cancelar)
			$(this).dialog('close')				
		}
				
		var perguntarCancelar = {
			id: "btn_cancelar",
			text: "Cancelar",
			click: function() {
				perguntar()
				$(this).dialog('close')
			}
		}
		
		var popup = {
			id: "btn_popup",
			text: "Cancelar",
			click: function() {
				popupReforcos(pro_codigo)
				$(this).dialog('close')
			}
		}
		
		var cancelar = function() {
            
                acao(pro_codigo, dose, "C")
            
			$(this).dialog('close')
		}

		if(ocupado){
			if(dose == 6) { // reforço
				buttons.push(aplicar)
				buttons.push(preencher)
				buttons.push(aprazar)
				if( $("#vac_6_"+pro_codigo).hasClass("vacR")){
					buttons.push(popup)
				} else {
					buttons.push(perguntarCancelar)
				}
			} else if(!$("#vac_"+dose+"_"+pro_codigo).is(".vacA")){ // se for preenchido ou aprazado				
				buttons.push(aplicar)
				buttons.push(perguntarCancelar)
			} else {
				perguntar()
				return false
			}
		} else {
			buttons.push(aplicar)
			buttons.push(preencher)
			buttons.push(aprazar)		
        }
        
        if(tipo === "T"){

            $("#vacina-tratamento-dialog").show()
            $("#vacina-tratamento-dialog form select#tratamento").empty()
            for(let ii = 0; ii < qtd; ii++){
                $("#vacina-tratamento-dialog form select#tratamento").append(`<option value="${ii+1}">T${ii+1}</option>`)
            }

            $("#vacina-tratamento-dialog").dialog({
                modal: true,
                width: 540,
                height: 380,
                close: function() {
                    $(this).close()
                },
                buttons: buttons
            })
        } else {
            $("#vacina-dialog").show()
            $("#vacina-dialog").dialog({
                modal: true,
                width: 700,
                height: 350,
                close: function() {
                    $(this).close()
                },
                buttons: buttons
            })
        }
		/*$("#data").datepicker({
			changeMonth: true,
			changeYear: true,
			onSelect: function(data){
				var dif = compararDatas(data);
				if(dif == 1){
					// desabilitar aplicar e preencher
					$("#btn_aplicar, #btn_preencher").attr("disabled","disabled").addClass("ui-state-disabled");
				} else {
					$("#btn_aplicar, #btn_preencher").removeAttr("disabled").removeClass("ui-state-disabled");
				}
			}
		})*/
    }

	function popupAcoesVacinaTratamento(pro_codigo, dose, tipo, qtd = null){
		//window.console && console.log("popAcoesVacina();");
		
		var usu_codigo = getUsu()
        
        if(!usu_codigo){
			return false
        }
		
		var ocupado = $("#vac_"+dose+"_"+pro_codigo).is(".vacA,.vacP,.vacZ")
		var buttons = []
				
		var aplicar = {
			id: "btn_aplicar",
			text: "Aplicar",
			click: function() {
				// tem doses abertas?
				var doses = $("#vac_doses_"+pro_codigo+" input").val()
				if(doses == undefined){
					mensagem("Atenção", "Produto sem estoque!", 250, 120)
				} else if(doses == 0){
					mensagem("Atenção", "Não há frascos abertos", 250, 120)
				} else {
                    // aplica a vacina
                    
                    // let t = $("#vacina-tratamento-dialog form").serializeArray().reduce(function(a, x) {
                        
                    //     a[x.name] = x.value
                    //     return a
                    // }, {})

                    let t = serializeToJson($("#vacina-tratamento-dialog form").serializeArray())

                    delete t.usu_condicao

                    t.usu_condicao = $("#vacina-tratamento-dialog form #usu_condicao").val()
                    
                    acaoTratamento(pro_codigo, dose, t.tratamento, "A", getData() || t.data, t.estrategia, t.usu_condicao)
                }

				$(this).dialog('close')
			}
		}
		
		var preencher = {
			id: "btn_preencher",
			text: "Preencher",
			click: function() {
                // preenche vacina
                acaoTratamento(pro_codigo, dose, qtd, "P", getData())
				$(this).dialog('close')
			}
		}
		
		var aprazar = {
			id: "btn_aprazar",
			text: "Aprazar",
			click: function() {
                // aprazar
                acaoTratamento(pro_codigo, dose, qtd, "Z", getData())
                
				$(this).dialog('close')
			}
		}
		
		var perguntar = function() {
			// cancelar
			confirme("Confirmação","Deseja realemente cancelar esta vacina?", 400,150, cancelar)
			$(this).dialog('close')				
		}
				
		var perguntarCancelar = {
			id: "btn_cancelar",
			text: "Cancelar",
			click: function() {
				perguntar()
				$(this).dialog('close')
			}
		}
		
		var popup = {
			id: "btn_popup",
			text: "Cancelar",
			click: function() {
				popupReforcos(pro_codigo)
				$(this).dialog('close')
			}
		}
		
		var cancelar = function() {
            if(tipo === "T"){
                acaoTratamento(pro_codigo, dose, qtd, "C")
            } else {
                acao(pro_codigo, dose, "C")
            }
			$(this).dialog('close')
		}

		if(ocupado){
			if(dose == 6) { // reforço
				buttons.push(aplicar)
				buttons.push(preencher)
				buttons.push(aprazar)
				if( $("#vac_6_"+pro_codigo).hasClass("vacR")){
					buttons.push(popup)
				} else {
					buttons.push(perguntarCancelar)
				}
			} else if(!$("#vac_"+dose+"_"+pro_codigo).is(".vacA")){ // se for preenchido ou aprazado				
				buttons.push(aplicar)
				buttons.push(perguntarCancelar)
			} else {
				perguntar()
				return false
			}
		} else {
			buttons.push(aplicar)
			buttons.push(preencher)
			buttons.push(aprazar)		
        }
        
        console.log('chegou aqui: '+tipo)

            $("#vacina-tratamento-dialog").show()
            $("#vacina-tratamento-dialog form select#tratamento").empty()
            for(let ii = 0; ii < qtd; ii++){
                $("#vacina-tratamento-dialog form select#tratamento").append(`<option value="${ii+1}">T${ii+1}</option>`)
            }

            $("#vacina-tratamento-dialog").dialog({
                modal: true,
                width: 540,
                height: 380,
                close: function() {
                    $(this).close()
                },
                buttons: buttons
            })
        
		/*$("#data").datepicker({
			changeMonth: true,
			changeYear: true,
			onSelect: function(data){
				var dif = compararDatas(data);
				if(dif == 1){
					// desabilitar aplicar e preencher
					$("#btn_aplicar, #btn_preencher").attr("disabled","disabled").addClass("ui-state-disabled");
				} else {
					$("#btn_aplicar, #btn_preencher").removeAttr("disabled").removeClass("ui-state-disabled");
				}
			}
		})*/
    }
    
    function popupAcoesVacinaDose(pro_codigo, dose, tipo, qtd = null){
		//window.console && console.log("popAcoesVacina();");
				
		var usu_codigo = getUsu()
        
        if(!usu_codigo){
			return false
        }
		
		var ocupado = $("#vac_"+dose+"_"+pro_codigo).is(".vacA,.vacP,.vacZ")
		var buttons = []
				
		var aplicar = {
			id: "btn_aplicar",
			text: "Aplicar",
			click: function() {
				// tem doses abertas?
				var doses = $("#vac_doses_"+pro_codigo+" input").val()
				if(doses == undefined){
					mensagem("Atenção", "Produto sem estoque!", 250, 120)
				} else if(doses == 0){
					mensagem("Atenção", "Não há frascos abertos", 250, 120)
				} else {
                    // aplica a vacina
                    
                    let t = serializeToJson($("#vacina-rev-dialog form").serializeArray())

                    delete t.usu_condicao

                    t.usu_condicao = $("#vacina-rev-dialog form #usu_condicao").val()
                    
                    acaoDose(pro_codigo, dose, t.tratamento, "A", getData() || t.data, t.estrategia, t.usu_condicao)
                }

				$(this).dialog('close')
			}
		}
		
		var preencher = {
			id: "btn_preencher",
			text: "Preencher",
			click: function() {
                // preenche vacina
                acaoDose(pro_codigo, dose, qtd, "P", getData())
				$(this).dialog('close')
			}
		}
		
		var aprazar = {
			id: "btn_aprazar",
			text: "Aprazar",
			click: function() {
                // aprazar
                acaoDose(pro_codigo, dose, qtd, "Z", getData())
                
				$(this).dialog('close')
			}
		}
		
		var perguntar = function() {
			// cancelar
			confirme("Confirmação", "Deseja realemente cancelar esta vacina?", 400, 150, cancelar)
			$(this).dialog('close')				
		}
				
		var perguntarCancelar = {
			id: "btn_cancelar",
			text: "Cancelar",
			click: function() {
				perguntar()
				$(this).dialog('close')
			}
		}
		
		var popup = {
			id: "btn_popup",
			text: "Cancelar",
			click: function() {
				popupReforcos(pro_codigo)
				$(this).dialog('close')
			}
		}
		
		var cancelar = function() {
            acaoDose(pro_codigo, dose, qtd, "C")
            
			$(this).dialog('close')
		}

		if(ocupado){
			if(dose == 6) { // reforço
				buttons.push(aplicar)
				buttons.push(preencher)
				buttons.push(aprazar)
				if( $("#vac_6_"+pro_codigo).hasClass("vacR")){
					buttons.push(popup)
				} else {
					buttons.push(perguntarCancelar)
				}
			} else if(!$("#vac_"+dose+"_"+pro_codigo).is(".vacA")){ // se for preenchido ou aprazado				
				buttons.push(aplicar)
				buttons.push(perguntarCancelar)
			} else {
				perguntar()
				return false
			}
		} else {
			buttons.push(aplicar)
			buttons.push(preencher)
			buttons.push(aprazar)		
        }
        
        $("#vacina-rev-dialog").show()
        $("#vacina-rev-dialog form select#revacinacao").empty()

        for(let ii = 0; ii < qtd; ii++){
            $("#vacina-rev-dialog form select#revacinacao").append(`<option value="${ii+1}">R${ii+1}</option>`)
        }

        console.log('doglwgoiren')

        $("#vacina-rev-dialog").dialog({
            modal: true,
            width: 540,
            height: 380,
            close: function() {
                $(this).close()
            },
            buttons: buttons
        })
        
		/*$("#data").datepicker({
			changeMonth: true,
			changeYear: true,
			onSelect: function(data){
				var dif = compararDatas(data);
				if(dif == 1){
					// desabilitar aplicar e preencher
					$("#btn_aplicar, #btn_preencher").attr("disabled","disabled").addClass("ui-state-disabled");
				} else {
					$("#btn_aplicar, #btn_preencher").removeAttr("disabled").removeClass("ui-state-disabled");
				}
			}
		})*/
    }
    
	// Busca as vacinas do pacinte 
	function carregarCarteirinha(pro_codigo, dose) {
		var usu_codigo = $("#usu_codigo").val()
		
		carregandoAba(1)
		$.ajax({
			url: baseUrl+'/vacina/dados/id/'+usu_codigo,
			dataType: 'json',
			success: preencheVacinas
		})
		
		liberaBotoes()
		return false
	}

	function liberaBotoes(){		
		$("#rec-vacinas, #print-carteirinha, #print-atestado").removeClass("ui-state-disabled")
		$("#print-carteirinha").attr("href",baseUrl+"/vacina/imprimir-carteirinha/usu/"+getUsu())
	}
	
	function preencheVacinas(json){
		limparVacinas()
		for(var i in json){
			var vac = json[i]
			
			// criar uma div dentro da celula, para guardar o tooltip
			var tooltip = "<div class=\"tooltip\">"
			tooltip += "<strong>Ação:</strong> "+nomeAcao(vac.vac_acao)+"<br />"
                        
			tooltip += "<strong>Data:</strong> "+dataToBr(vac.vac_data)+"<br />"
			if(vac.vac_acao == "A"){
				tooltip += "<strong>Lote:</strong> "+vac.ite_lote+"<br />"
				tooltip += "<strong>Validade:</strong> "+dataToBr(vac.ite_validade)+"<br />"
			}
			tooltip += "<strong>Unidade:</strong> "+vac.uni_desc+"<br />"
			tooltip += "<strong>Usuário:</strong> "+vac.usr_nome+"<br />"
			tooltip += "</div>"
			//alert(tooltip)	;
			var obj = $("#vac_"+vac.vac_dose+"_"+vac.pro_codigo)
			
			// verificar se já há algo na celula (reforço já preenchido)
			if((vac.vac_dose == 8 || vac.vac_dose == 9) && obj.is(".vacA, .vacP, .vacZ")){
				obj.removeClass("vacA vacP vacZ").addClass("vacR") // deixar só a ultima ação
			}
			
			if(vac.vac_dose == 8 || vac.vac_dose == 9){
				// adiciona na coluna oculta
				var tr = "<td class=\"ui-widget ui-widget-content\">"+ ($("#vac_R_"+vac.pro_codigo+" table tbody tr").size()+1)+"º</td>"
				tr += "<td class=\"ui-widget ui-widget-content\">"+dataToBr(vac.vac_data)+"</td>";
				tr += "<td class=\"ui-widget ui-widget-content\">"+vac.uni_desc+"</td>";
				tr += "<td class=\"ui-widget ui-widget-content\">"+nomeAcao(vac.vac_acao)+"</td>";
				tr += "<td class=\"ui-widget ui-widget-content\">"+vac.usr_nome+"</td>";
				tr += "<td class=\"ui-widget ui-widget-content c\" id=\"del_"+vac.vac_usu_codigo+"\"><img src=\""+baseUrl+"/public/images/icons/excluir.png\" title=\"Cancelar esta vacina\" onclick=\"cancelarVacina("+vac.vac_usu_codigo+")\" /></td>";
				//alert(vac.pro_codigo);
				$("#vac_R_"+vac.pro_codigo+" table tbody").append( "<tr class=\"row_"+vac.pro_codigo+"\">"+tr+"</tr>" )
			}
			//alert(vac.pro_codigo +"--"+vac.vac_dose);		
			obj
			.html(dataToBr(vac.vac_data)) // Adiciona a data
			.addClass("vac"+vac.vac_acao) // adiciona a cor
			.append(tooltip)
			
			// mostrar lote no carteirinha pra impressão
			if($("#impressao").val() == "1"){
                if(vac.ite_lote=="--") {
                    if(vac.vac_acao=="P") {
                        obj.append("<br /><small>Preenchida</small>")
                    } else {
                        obj.append("<br /><small>Aprazada</small>")
                    }
                } else {
			    	obj.append("<br /><small>Lote: "+vac.ite_lote+"</small>")
                }
			}
        }
		
		// adiciona o tooltip
                
		$(".vacA, .vacP, .vacZ").each(function(){
			var obj = $(this);
			obj.easyTooltip({
                clickRemove:true,
				content: obj.find(".tooltip").html()
			})
        })
             
		carregandoAba(0);
	}
	
	function nomeAcao(acao){
		switch (acao) {
			case "A":
				return "Aplicada";
            break;
			case "P":
				return "Preenchida";
            break;
			case "Z":
				return "Aprazada";
            break;
			default:
				return "";
            break;
		}
	}
	
	// Busca o estoque: lotes, validades, frascos abertos e fechados
	function carregarEstoque(){
		carregandoAba(1);
		$.ajax({
			url: baseUrl+'/vacina/dados-estoque',
			dataType: 'json',
			success: preencheEstoque
		})
	}
	
	function preencheEstoque(json){
		limparEstoque()
		for(var i in json.aberto){
			var vac = json.aberto[i]
			
			$("#vac_lote_"+vac.pro_codigo).html(vac.ite_lote) // Adiciona o lote do produto aberto
			$("#vac_validade_"+vac.pro_codigo).html(verificarData(dataToBr(vac.ite_validade))) // Adiciona a validade do produto aberto
			$("#vac_doses_"+vac.pro_codigo).html(vac.cont_dose+"<input type=\"hidden\" value=\""+vac.cont_dose+"\" />") // Adiciona as doses (abertas)

			// Adiciona o botão de descartar frasco
			$("#vac_opcao_"+vac.pro_codigo).html("<img class=\"descartar\" data-pro_codigo=\""+vac.pro_codigo+"\" src=\""+baseUrl+"/public/images/descartar_vacina.png\" alt=\"Descartar frasco\" title=\"Descartar frasco\" />")
		}		
		
		for(i in json.fechado){
			vac = json.fechado[i]
			$("#vac_qtde_"+vac.pro_codigo).html(vac.sal_qtde) // Adiciona a quantidade fechadas
			
			// Adiciona o botão de abrir o frasco, se não houver aberto
			var opcao = $("#vac_opcao_"+vac.pro_codigo)
			if(opcao.html() == ""){
				opcao.html("<img class=\"abrir\" data-pro_codigo=\""+vac.pro_codigo+"\" src=\""+baseUrl+"/public/images/abrir_vacina.png\" alt=\"Abrir frasco\" title=\"Abrir frasco\" />")
				
				// adiciona mensagem de frasco fechado
				$("#vac_doses_"+vac.pro_codigo).addClass('fechado').html("<em>frasco<br />fechado</em><input type=\"hidden\" value=\"0\" />") // Adiciona as doses (abertas)
				$("#vac_lote_"+vac.pro_codigo+", #vac_validade_"+vac.pro_codigo).addClass('fechado').html("<em>frasco<br />fechado</em>")
			}			
		}	
		carregandoAba(0)
		mensagemDeSemEstoque()
		bindAbrirDescartar()
	}
	
	// pega o clique nas imagens de abrir e descartar frascos
	function bindAbrirDescartar(){
		$(".abrir").click(function(){
			$(this).hide().parent().append("<img src=\""+baseUrl+"/public/images/loading.gif\" alt=\"Carregando...\" title=\"Carregando...\" />")
			
			var pro_codigo = $(this).data("pro_codigo")
			abrirFrasco(pro_codigo)
		});		
		
		$(".descartar").click(function(){
			var pro_codigo = $(this).data("pro_codigo")
			descartarFrasco(pro_codigo)
		})
	}
	
	// aidiciona mensagem de sem estoque aos campos vazios da tabela
	function mensagemDeSemEstoque(){
		$(".estoque").each(function(){
			if($(this).html() == ""){
				$(this).addClass("sem-estoque").html("<em>sem<br />estoque</em>")
            }
		})
	}
	
	// Apaga as vacinas A/P/Z, para limpar a carteirinha
	function limparVacinas(){
		$("#carteirinha .vacA, #carteirinha .vacP, #carteirinha .vacZ, #carteirinha .vacR") // vacR = dois ou mais reforços
		.removeClass("vacA vacP vacZ vacR") // tira todas classes
		.html("") // limpa o conteudo
		.unbind("hover") // desabilita tooltip
	}
	
	// Apaga as informações de estoque
	function limparEstoque(){
		$(".estoque").html("").removeClass("sem-estoque").removeClass("fechado")
	}

	// verificar se a data é maior que atual
	function verificarData(data){
		var dif = compararDatas(data)
		
		if(dif == 0){ // vence hoje
			return "<span class=\"aviso\">"+data+"</span>"
        }

		if(dif == 2){ // já venceu
			return "<span class=\"vencida\">"+data+"</span>"
        }
        
        return data
	}
	
	// abre o frasco
	function abrirFrasco(pro_codigo){	
		abrirDescartarFrasco(pro_codigo, "abrir")
	}
	
	// descartar o frasco
	function descartarFrasco(pro_codigo){	
		var form = "<input type=\"radio\" value=\"1\" name=\"motivo\" id=\"motivo1\" /> <label for=\"motivo1\">Quebra de frasco</label><br />";
		form += "<input type=\"radio\" value=\"2\" name=\"motivo\" id=\"motivo2\" /> <label for=\"motivo2\">Falta de energia</label><br />";
		form += "<input type=\"radio\" value=\"3\" name=\"motivo\" id=\"motivo3\" /> <label for=\"motivo3\">Falha no equipamento</label><br />";
		form += "<input type=\"radio\" value=\"4\" name=\"motivo\" id=\"motivo4\" /> <label for=\"motivo4\">Validade Vencida</label><br />";
		form += "<input type=\"radio\" value=\"5\" name=\"motivo\" id=\"motivo5\" /> <label for=\"motivo5\">Procedimento Inadequado</label><br />";
		form += "<input type=\"radio\" value=\"6\" name=\"motivo\" id=\"motivo6\" /> <label for=\"motivo6\">Falha no transporte</label><br />";
		form += "<input type=\"radio\" value=\"7\" name=\"motivo\" id=\"motivo7\" checked=\"checked\" /> <label for=\"motivo7\">Outros motivos</label><br />";
		
		$("body").append("<div id=\"motivo-dialog\" title=\"Informe o motivo do descarte\"></div>")
		$("#motivo-dialog")
		.html(form)
		.dialog({
			modal: true,
			width: 300,
			height: 250,
			close: function(){
				$(this).remove();
			},
			buttons: {
				Descartar: function(){		
					$("#vac_opcao_"+pro_codigo+" img").hide()
						.parent()
						.append("<img src=\""+baseUrl+"/public/images/loading.gif\" alt=\"Carregando...\" title=\"Carregando...\" />");
					abrirDescartarFrasco(pro_codigo, "descartar", $("input[name=motivo]:checked").val());
					$(this).dialog('close');
				},
				Cancelar: function(){
					$(this).dialog('close');
				}
			}
		});		
	}
	
	function abrirDescartarFrasco(pro_codigo,acao,motivo){
		var url = "/vacina/abrir-descartar-frasco/acao/"+acao+"/pro/"+pro_codigo;
		
		if(typeof(motivo) != "undefined"){
			url += "/motivo/"+motivo
        }
		
		carregandoAba(1);
		$.ajax({
			url: baseUrl+url,
			dataType: "json",
			success: function(json){
				limparEstoque();
				preencheEstoque(json);
			}
		});
		
	}
	
	function acao(pro_codigo, dose, acao, data){
		var usu = getUsu()
		if(!usu){
			return false
        }

		$("#vac_"+dose+"_"+pro_codigo).html("<img src=\""+baseUrl+"/public/images/loading.gif\" alt=\"Carregando...\" title=\"Carregando...\" />")
		
		if(acao == "A" || acao == "C"){
			$("#vac_doses_"+pro_codigo).html("<img src=\""+baseUrl+"/public/images/loading.gif\" alt=\"Carregando...\" title=\"Carregando...\" />")
        }
		
		carregandoAba(1)
		
		$.ajax({
			url: baseUrl+"/vacina/salvar/",
			type: 'post',
			dataType:'json',
			data: {
				usu: usu,
				pro: pro_codigo,
				acao: acao,
				data: data,
                dose: dose,
                situacao: $("#usu_situacao").val(),
                campanha: $("#campanha").val(),
                estrategia: $("#estrategia").val()
			},
			success: function(json){
                if(json){
                    if(json.success == true){
                        carregarCarteirinha();
                        if(acao == "A" || acao == "C"){
                            carregarEstoque()
                        }
                    } else {
                        mensagem(json.mensagem.titulo,json.mensagem.mensagem,json.mensagem.x,json.mensagem.y)
                    }
                    carregandoAba(0)
                }
			}
		})
    }
    
	function acaoTratamento(pro_codigo, dose, tratamento, acao, data, estrategia, condicao){
		var usu = getUsu()
		if(!usu){
			return false
        }

		$("#vac_"+dose+"_"+pro_codigo).html("<img src=\""+baseUrl+"/public/images/loading.gif\" alt=\"Carregando...\" title=\"Carregando...\" />")
		
		if(acao == "A" || acao == "C"){
			$("#vac_doses_"+pro_codigo).html("<img src=\""+baseUrl+"/public/images/loading.gif\" alt=\"Carregando...\" title=\"Carregando...\" />")
        }
		
        carregandoAba(1)
        
        if(!data){
            data = getData()
        }
		
		$.ajax({
			url: baseUrl+"/vacina/salvar/",
			type: 'post',
			dataType:'json',
			data: {
				usu: usu,
				pro: pro_codigo,
				acao: acao,
				data: data || getData(),
                dose: dose+"_"+tratamento,
                condicao: condicao ? condicao.toString() : "",
                campanha: $("#campanha").val(),
                estrategia: $("#estrategia").val() || estrategia
			},
			success: function(json){
                if(json){
                    if(json.success == true){
                        carregarCarteirinha();
                        if(acao == "A" || acao == "C"){
                            carregarEstoque()
                        }
                    } else {
                        mensagem(json.mensagem.titulo,json.mensagem.mensagem,json.mensagem.x,json.mensagem.y)
                    }
                    carregandoAba(0)
                }
			}
		})
    }
    
	function acaoDose(pro_codigo, dose, tratamento = 1, acao, data, estrategia, condicao){
		var usu = getUsu()
		if(!usu){
			return false
        }

		$("#vac_"+dose+"_"+pro_codigo).html("<img src=\""+baseUrl+"/public/images/loading.gif\" alt=\"Carregando...\" title=\"Carregando...\" />")
		
		if(acao == "A" || acao == "C"){
			$("#vac_doses_"+pro_codigo).html("<img src=\""+baseUrl+"/public/images/loading.gif\" alt=\"Carregando...\" title=\"Carregando...\" />")
        }
		
        carregandoAba(1)
        
        if(!data){
            data = getData()
        }

        // console.log(pro_codigo, dose, tratamento, acao, data, estrategia, condicao)
        // return false
		
		$.ajax({
			url: baseUrl+"/vacina/salvar/",
			type: 'post',
			dataType:'json',
			data: {
				usu: usu,
				pro: pro_codigo,
				acao: acao,
				data: data || getData(),
                dose: dose+"_"+tratamento,
                condicao: condicao ? condicao.toString() : "",
                campanha: $("#campanha").val(),
                estrategia: $("#estrategia").val() || estrategia
			},
			success: function(json){
                if(json){
                    if(json.success == true){
                        carregarCarteirinha();
                        if(acao == "A" || acao == "C"){
                            carregarEstoque()
                        }
                    } else {
                        mensagem(json.mensagem.titulo,json.mensagem.mensagem,json.mensagem.x,json.mensagem.y)
                    }
                    carregandoAba(0)
                }
			}
		})
	}
		
	function getUsu(){
		var usu_codigo = $("#usu_codigo").val();
		if(usu_codigo == ""){
			mensagem("Atenção","Selecione um paciente", 250, 120);
			$("#usu_nome").select();
			return false;
		}
		return usu_codigo;
	}
	
	function imprimirAtestado(){
		$("body").append("<div id=\"atestado-dialog\" title=\"Selecione a data do atestado\"><div id=\"data\" /></div>");
		$("#atestado-dialog").dialog({
			modal: true,
			width: 270,
			height: 310,
			close: function(){
				$(this).remove();
			},
			buttons: {
				Imprimir: function(){
                    var data = $("#data").datepicker( "getDate" );
                    var d = (data.getDate()<=9?"0"+data.getDate():data.getDate());
                    var m = (data.getMonth()<=8?"0"+(data.getMonth()+1):(data.getMonth()+1));
                    var Y = data.getFullYear();
                    var dt_atestado = Y+"-"+m+"-"+d;
					var url = baseUrl+"/vacina/imprimir-atestado/usu/"+getUsu()+"/ate/"+dt_atestado;
					popup(url, "atestado-vacina", 628, 535)
					$(this).dialog('close');					
				},
				Cancelar: function(){
					$(this).dialog('close');
				}
			}
		});
		$("#data").datepicker({
			changeMonth: true,
			changeYear: true
		});
    }
    
    function carteirinhaCampanha(value){
        
        if(value !== ""){
            let obj = JSON.parse(window.sessionStorage.getItem('form_tmp'))
            if(typeof obj !== "undefined"){
                obj.campanha = value
                window.sessionStorage.setItem('form_tmp', JSON.stringify(obj))
                // window.location.href = 
            }    
            $("#carteirinha_campanha").load(baseUrl+"/default/vacina/carteirinha-campanha?camp_codigo="+value, () => {$(".vac").click(vacina); $(".trat").click(tratamento); carregarEstoque()})
        }
    }

    document.getElementById("campanha").addEventListener('change', c => {
        carteirinhaCampanha(c.target.value)
    })
      
    $(document).ready(onReady)

    function calcular(data) {
        const datanasc = data.split('/')
        
        const date1 = new Date(datanasc[2]+"-"+datanasc[1]+"-"+datanasc[0])
        const date2 = new Date()
        const diff = new Date(date2.getTime() - date1.getTime())
        
        const idade = diff.getUTCFullYear() - 1970+" anos, "+diff.getUTCMonth()+" meses, "+diff.getUTCDate()+" dias"
        
        return idade
    }

    function onReady(){
        const cookie = getCookie('form_tmp')

        // console.log(typeof cookie !== "")
        // console.log(cookie, cookie.length)
        // return false

        if(cookie !== "" && cookie.length > 0){
            let data = JSON.parse(cookie)
            let form = document.getElementById('upperForm')
            let elms = form.querySelectorAll('input, textarea, select')
            for (let i = 0; i < elms.length; i++) {
                if (elms[i].id && elms[i].type === 'checkbox') {
                    elms[i].checked = data[elms[i].id]
                } else if (elms[i].name && elms[i].type === 'radio') {
                    let inputRadio = form.querySelector('input[name="' + elms[i].name + '"][value="' + data[elms[i].name] + '"]')
                    if (inputRadio) {
                        inputRadio.checked = true
                    }
                } else if (elms[i].id && data[elms[i].id]) {
                    elms[i].value = data[elms[i].id]
                }
            }

            carregarCarteirinha()
            
            carregarEstoque()
            
            carteirinhaCampanha(data.campanha)
        }
    }

    document.getElementById("upperForm").addEventListener('change', ch => {
        setTimeout(() => {
            onChange(ch.target.parentElement.parentElement)
            // console.log(ch.target.parentElement.parentElement)
        }, 500)
    })
    
    function onChange(e){
        let obj = {}
        
        for(let ii in Object.entries(e)){
            if(typeof Object.entries(e)[ii][1].value !== "undefined"){
                obj[Object.entries(e)[ii][1].name] = Object.entries(e)[ii][1].value
            }
        }

        let now = new Date();
        let minutes = 5;
        now.setTime(now.getTime() + (minutes * 60 * 1000));
        document.cookie = "form_tmp="+JSON.stringify(obj)+"; expires="+now.toUTCString()+"; path="+window.location.href
        window.sessionStorage.setItem('form_tmp', JSON.stringify(obj))
    }

    function addCampanha(){
        mensagemSemOk("carregando-vac", "Aguarde", "", 250, 70)
        let options = ``
        $.ajax({
            url: baseUrl+'/default/vacina/get-vacinas',
            type: 'GET',
            success: res => {
                fecharMensagemSemOk("carregando-vac")
                if(res.length > 0){
                    res.forEach(item => {
                        options += `<option value="${item.pro_codigo}">${item.pro_nome}</option>`
                    })
                    
                    $("#addCampanha-dialog select#vacina").empty().append(options)

                    $("#addCampanha-dialog").show()
                    $("#addCampanha-dialog").dialog({
                        modal: true,
                        width: 410,
                        height: 300,
                        close: function(){
                            $(this).close();
                        },
                        buttons: {
                            Salvar: function(){
                                let form = $(this).find('form').get(0)
                                let obj = {}
                                
                                for(let ii in Object.entries(form)){
                                    if(typeof Object.entries(form)[ii][1].value !== "undefined"){
                                        if(Object.entries(form)[ii][1].name === "vacina"){
                                            obj[Object.entries(form)[ii][1].name] = $("#vacina").val()
                                        } else {
                                            obj[Object.entries(form)[ii][1].name] = Object.entries(form)[ii][1].value
                                        }
                                    }
                                }

                                $.ajax({
                                    url: baseUrl+'/default/vacina/nova-campanha',
                                    type: 'POST',
                                    data: obj,
                                    success: (res) => {
                                        console.log(res)
                                        if(typeof res !== "undefined") {
                                            mensagem("Sucesso", "Nova campanha cadastrada com sucesso!", 275, 90, () => {
                                                window.location.reload()
                                            })
                                            $(this).dialog('close')
                                        }
                                    }
                                })
                            },
                            Cancelar: function(){
                                $(this).dialog('close')
                            }
                        }
                    })
                }
            }
        })
    }

    document.getElementById('add_campanha').addEventListener('click', addCampanha)
})

function getData(){
	/*var data = $("#data").datepicker( "getDate" );
	var d = (data.getDate()<=9?"0"+data.getDate():data.getDate());
	var m = (data.getMonth()<=8?"0"+(data.getMonth()+1):(data.getMonth()+1));
	var Y = data.getFullYear();
	return Y+"-"+m+"-"+d;*/
    
    return $("#data-vac, #data-vac-trat").val();
}

function cancelarVacina(vac_usu_codigo){
	confirme("Confirmação","Deseja realemente cancelar esta vacina?", 400,150,function(){
		carregandoAba(1);

		$("#del_"+vac_usu_codigo+" img").hide()
			.parent()
			.append("<img src=\""+baseUrl+"/public/images/loading.gif\" alt=\"Carregando...\" title=\"Carregando...\" />");
		
		$.ajax({
			url: baseUrl+"/vacina/deletar",
			type: 'POST',
			dataType: 'JSON',
			data: {
				vac: vac_usu_codigo
			},
			success: function(r){
				carregandoAba(0);
				if(!r.success){
					mensagem("Erro",r.mensagem, 200, 150);
                } else {
                    $("#reforco-dialog").dialog('close');
                }
			}
		})
	})
}  

function getCookie(cname) {
    var name = cname + "="
    var decodedCookie = decodeURIComponent(document.cookie)
    var ca = decodedCookie.split(';')
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i]
        while (c.charAt(0) == ' ') {
            c = c.substring(1)
        }

        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length)
        }
    }
    return ""
}

function eraseCookie(name) {   
    document.cookie = name+'=; Max-Age=-99999999;';  
}

function serializeToJson(serializer){
    var _string = '{';
    for(var ix in serializer)
    {
        var row = serializer[ix];
        _string += '"' + row.name + '":"' + row.value + '",';
    }
    var end =_string.length - 1;
    _string = _string.substr(0, end);
    _string += '}';

    return JSON.parse(_string);
}