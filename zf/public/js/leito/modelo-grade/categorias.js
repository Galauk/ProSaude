$(function(){	
	
	$("#categorias ul li").click(function(){
		var lgc = $(this).data("codigo");
		var categoria = $(this).find("a").html();
		$("#modelos").slideDown();
		$("#modelos-medicamentos").slideUp();

		$("#grid").jqGrid().setGridParam({
                    url: baseUrl+'/leito/modelo-grade/jqgrid/categoria/'+lgc,
                    editurl: baseUrl+'/leito/modelo-grade/salvar-categoria/id/'+lgc
		}).trigger("reloadGrid");
		$(".ui-jqgrid-title:first").html("Modelos para "+categoria);
	});
	
	$("#grid").jqGrid({ 
		url: 'about:blank',
		datatype: "json", 
		colNames:['Cod.','Descrição','Intervalo','Repetições'], 
		colModel:[ {
			name:'lgm_codigo',
			index:'lgm_codigo', 
			width:65
		}, 
		{
			name:'lgm_descricao',
			index:'lgm_descricao', 
			width:250,
			editable: true
		}, 
		{
			name:'lgm_intervalo',
			index:'lgm_intervalo', 
			width:60,
			align: 'center',
			editable: true
		}, 
		{
			name:'lgm_repeticoes',
			index:'lgm_repeticoes', 
			width:50,
			align: 'center',
			editable: true
		}],
		rowNum:10, 
		rowList:[10,20,30], 
		pager: '#footer', 
		sortname: 'lgm_descricao', 
		sortorder: "asc",
		caption: "Modelos",
		onSelectRow: carregaGrid2
	}).navGrid(
		"#footer",{
			edit:true,
			add:true,
			del:false,
			search:false
		},{
			closeAfterAdd: true,
			closeAfterEdit: true
		},{
			closeAfterAdd: true,
			closeAfterEdit: true
		});
		
		
	
	$("#grid2").jqGrid({ 
		url: 'about:blank',
		datatype: "json", 
		colNames:['Cod.','','Medicamento','Quant.'], 
		colModel:[ {
			name:'ligm_codigo',
			index:'ligm_codigo', 
			width:65
		}, 
		{
			name:'pro_codigo',
			index:'pro_codigo',
			hidden: true
		}, 
		{
			name:'pro_nome',
			index:'pro_nome', 
			width:300
		}, 
		{
			name:'ligm_quantidade',
			index:'ligm_quantidade', 
			width:60,
			align: 'center'
		}],
		rowNum:10, 
		rowList:[10,20,30], 
		pager: '#footer2', 
		sortname: 'pro_nome', 
		sortorder: "asc",
		caption: "Medicamentos"
	}).navGrid(
		"#footer2",{
			edit:false,
			add:false,
			del:false,
			search:false
		},{
			closeAfterAdd: true,
			closeAfterEdit: true
		},{
			closeAfterAdd: true,
			closeAfterEdit: true
		}).navButtonAdd('#footer2',{
			caption:"", 
			title:"Apagar",
			buttonicon:"ui-icon-trash", 
			onClickButton: function(){ 
				var ligm_codigo = $('#grid2').jqGrid('getGridParam','selrow');
				if(ligm_codigo) {
					confirme('Confirme','Deseja realmente retirar este produto do modelo?', 320, 120, function(){
						apagarProduto(ligm_codigo);
					});
				}
				else {
					mensagem('Erro','Selecione um registro', 200, 120);
				}
				return false;				
			}, 
			position:"first"
		}).navButtonAdd('#footer2',{
			caption:"", 
			title:"Editar",
			buttonicon:"ui-icon-pencil", 
			onClickButton: function(){ 
				var ligm_codigo = $('#grid2').jqGrid('getGridParam','selrow');
				if(ligm_codigo) {
					var data = $("#grid2").jqGrid('getRowData', ligm_codigo);
					
					janelaEditarProduto(data.pro_codigo, data.ligm_quantidade, ligm_codigo, data.pro_nome);
				}
				else {
					mensagem('Erro','Selecione um registro', 200, 120);
				}
				return false;				
			}, 
			position:"first"
		}).navButtonAdd('#footer2',{
			caption:"", 
			title:"Adicionar",
			buttonicon:"ui-icon-plus", 
			onClickButton: function(){ 
				janelaEditarProduto();
			}, 
			position:"first"
		});
});

function janelaEditarProduto(pro_codigo, qtd, ligm_codigo, pro_nome){
	
	if(typeof(pro_codigo) == "undefined"){
		qtd = '1';
		pro_codigo = '';
		ligm_codigo = '';
		pro_nome = '';
	}
	
	var form = '<form><input id="ligm_codigo" value="'+ligm_codigo+'" type="hidden" /><div id="form-erro" class="h ui-state-error"/><label style="width: 75px">Produto:</label><input id="pro_nome" value="'+pro_nome+'" style="margin-left:3px;width: 240px" class=\"ui-state-default\" /><input id="pro_codigo" value="'+pro_codigo+'" type="hidden" /><br /><label style="width: 75px">Quantidade:</label><input id="ligm_quantidade" value="'+qtd+'" style="margin-left:3px;width: 75px" class=\"ui-state-default\" /></form>'
	
	$("body").append("<div id=\"produto-modelo-dialog\" title=\"Adicionar Novo Produto\" />");
	$("#produto-modelo-dialog")
	.html(form)
	.dialog({
		modal: true,
		width: 350,
		height: 180,
		close: function(){
			$(this).remove();
		},
		buttons: {
			Salvar: salvarForm,
			Cancelar: function(){
				$(this).dialog('close');
			}
		}
	});
	
	$("#pro_nome").buscar({
		url: baseUrl+"/produto/medicamento",
		template : function(ul, item) {
			return jQuery("<li></li>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		}
	});
}

function salvarForm(){
	var lgm_codigo = $("#lgm_codigo").val();
	var ligm_codigo = $("#ligm_codigo").val();
	var pro_codigo = $("#pro_codigo").val();
	var ligm_quantidade = $("#ligm_quantidade").val();
	
	if(ligm_quantidade <= 0)
		return false;
	
	$.ajax({
		url: baseUrl+'/leito/modelo-grade/salvar-modelo/',
		type:'post',
		data:{
			lgm_codigo: lgm_codigo,
			ligm_codigo: ligm_codigo,
			pro_codigo: pro_codigo,
			ligm_quantidade: ligm_quantidade
		},
		success: function(json){
			if(json.erro)
				$("#form-erro").html(json.erro).show().effect("highlight", {}, 1000)
			else{
				$("#grid2").jqGrid().trigger("reloadGrid");
				$('#produto-modelo-dialog').dialog('close');
			}
		}
	});
}

function apagarProduto(ligm_codigo){
	$.ajax({
		url: baseUrl+'/leito/modelo-grade/salvar-modelo',
		type:'POST',
		data:{
			acao: 'excluir',
			ligm_codigo: ligm_codigo
		},
		success: function(){
			$("#grid2").jqGrid().trigger("reloadGrid");
		}
	});
}

function carregaGrid2(id){	
	$("#modelos-medicamentos").slideDown();
	
	if(!$("#lgm_codigo").size())
		$("body").append('<input id="lgm_codigo" value="'+id+'" type="hidden" />');
	else
		$("#lgm_codigo").val(id);
	
	$("#grid2").jqGrid().setGridParam({
		url: baseUrl+'/leito/modelo-grade/jqgrid/modelo/'+id
	}).trigger("reloadGrid");
}