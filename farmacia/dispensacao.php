<?php

	include_once "../global.php";
	setError(1);
	include_once COMUM.'library/php/funcoes.db.php'; // getConfig
	
?><html>
<head>
	<title>Dispensação de Medicamentos</title>
<?php
	$common = new commonClass();
	$form = new classForm();
	$table = new tableClass();
	echo "<link type=\"text/css\" href=\"".LINKSAUDE."/estiloPE.css\" rel=\"stylesheet\"/>";
	echo $common->incJquery();

	$sqlMedicoI = "SELECT u.usr_codigo AS cod,
	  					  UPPER(u.usr_nome) AS nome
					FROM usuarios AS u
				   WHERE 
					u.usr_tipo_medico='M' OR
				    u.usr_tipo_medico='D'
				   ORDER BY  UPPER(u.usr_nome);";
	
	$sqlMedicoE = "SELECT med_codigo AS cod,
	                      UPPER(med_nome) AS nome
	                 FROM medico 
	                WHERE prestador_servico = 'M'
	                  AND med_nome <> ''
	                ORDER BY UPPER(med_nome)";
	$query1 = pg_query($sqlMedicoI);
	$query2 = pg_query($sqlMedicoE);
	
	$selectMedico  = "<select id=\"medico\" class=\"inputForm\" style=\"width:300px;\">";
	$selectMedico .= "<option value=\"0\">-- SELECIONE --</option>";
	$selectMedico .= "<optgroup label=\"Internos\">";
	while($r = pg_fetch_assoc($query1)){
		$selectMedico .= "<option value=\"".$r['cod']."|1"."\">".$r['nome']."</option>";	
	}	
	$selectMedico .= "</optgroup>";
	$selectMedico .= "<optgroup label=\"Externos\">";
	while($r = pg_fetch_assoc($query2)){
		$selectMedico .= "<option value=\"".$r['cod']."|0"."\">".$r['nome']."</option>";	
	}	
	$selectMedico .= "</optgroup>";
	$selectMedico .= "</select>";
	
	// verifica se deve mostrar somente produtos com saldo na busca
	//die(var_dump(getConfig("FARMACIA_DISPENSACAO_LISTARSOMENTECOMSALDO")));
	$tipoDeBusca = (getConfig("FARMACIA_DISPENSACAO_LISTARSOMENTECOMSALDO")?"medicamentos_com_saldo":"medicamentos");

	
?><link rel="stylesheet" href="/WebSocialSaude/lib/themes/base/jquery.ui.all.css">
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css">
<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="/WebSocialComum/library/js/jquery.shortcuts.min.js"></script>
<script type="text/javascript" src="/WebSocialComum/library/js/jquery.buscar.js"></script>
<script type="text/javascript">
function editarPaciente(id){	
	usu_codigo = document.getElementById('usu_codigo').value;	
	id_login = document.getElementById('id_login').value;	
	var url ="../paciente.php?acao=form&usu_codigo="+usu_codigo+"&id_login="+id_login+"&porta=S";
	window.open(url,null,'height=750,width=750,status=yes,toolbar=no,menubar=no,scrollbars=yes,location=no');
}
$(function(){

	$.Shortcuts.add({
	    type: 'down',
	    enableInInput: true,
	    mask: 'Ctrl+F11',
	    handler: function() {
			//evt.preventDefault();
			validarDados();
			//return false;
	    },
	    list: 'abreJanelaQuant'
	});

	$.Shortcuts.add({
	    type: 'down',
	    enableInInput: true,
	    mask: 'Ctrl+F12',
	    handler: function() {
			//evt.preventDefault();
			dispensar();
			//return false;
	    },    
	    list: 'dispensar'
	});

	$(".ocultar").parents("div.linha").hide();
	$("#ver-mais").hover(
		function(){
			$(".ocultar").parents("div.linha").show("normal");
		},
		function(){
			$(".ocultar").parents("div.linha").hide("normal");
		}
	);

	$("#medico").change(function(){ 
		if($("#usu_codigo").val()) // jï¿½ ha paciente selecionado
			$("#medicamentos input").focus();
		else
			$("#buscar").select();			
	}).focus();

	$("#buscar").buscar({
		callback: function(event,ui){
			$("#parte2").show("normal");
			$("#medicamentos input").focus();
			$.ajax({
				url:"ultimosDispensados.php",//arquivo que vai realizar o ajax
				data:{//passa os parametros para o arquivo
					usu_codigo:$("#usu_codigo").val()
				},
				success:function(retorno){// se der tudo certo ele tras o retorno do ajax
					if(retorno != "-"){
						historico(retorno);
					}
				}
			})

			// atalhos de tecla
			$.Shortcuts.start('abreJanelaQuant');
					
		}
	});

	$("#medicamentos input").buscar({
		tipo:'<?=$tipoDeBusca;?>',
		callback: function(e,ui){
			mudaFotoProduto(ui.item.id,ui.item.data['pro_nome']);
			verificaSeTemVencido(ui.item.id);
		
			var jaExiste = false;
			$("#selecionados select option").each(function(){
				if($(this).val() == $("#pro_codigo").val()){
					jaExiste = true;
				}
			});
			
			if(!jaExiste)
				$("#selecionados select").append( "<option value=\""+ $("#pro_codigo").val() +"\">"+ $("#pro_nome").val() +"</option>" );
			
			$("#medicamentos input").val("").focus();
		},
		template : function(ul, item) {
			return $("<li></li>").data("item.autocomplete", item).append(
					"<a>" + item.label + "</a>").appendTo(ul);
		}
	});

	function moverParaEsquerda(){
	    $("#selecionados select option:selected").remove();
	}
	
	$("#selecionados select").keydown(function(e){
		
        if(e.keyCode == 37){
			e.preventDefault();
            moverParaEsquerda();
        }
	}).dblclick(function(){
	    moverParaEsquerda();                
	}).keyup(function(){
		var id = $(this).val();
		mudaFotoProduto(id,"");
	}).click(function(){
		var id = $(this).val();
		mudaFotoProduto(id,"");
	});
	

});


function verificaSeTemVencido(id){
	$.ajax({
		url: 'verificaSeTaVencido.php',
		type: "POST",
		data:{
			pro_codigo: id
		},
		success: function(txt){
			if(txt != ""){
				$("body").append("<div id=\"vencidos-dialog\" title=\"Produtos relacionados com validade Vencida\"></div>");
				$("#vencidos-dialog").html(txt);
			    $("#vencidos-dialog").dialog({
			            modal: true,
			            width: 500,
			            height: 140,
			            buttons:{
			                    Ok: function(){                        
			                        $("#vencidos-dialog").dialog("destroy").remove();
			                    }
			            }
			    });
			}
		}
	});
}

function mudaFotoProduto(id,nome){
	// mostrar a foto do produto
	$("#foto-produto").html("<img src=\"/WebSocialComum/imgs/medicamentos/"+id+".jpg\" title=\""+nome+"\"/>");
}

var proSelecionados = [];
function abreJanelaQuant(){

	if(proSelecionados.length == 0){
		alert("Selecione algum medicamento");
		$("#medicamentos select").focus();
		return false;
	}
	
	//abre o modal
	$("#msg")
	.attr("title","Informe as quantidades")
	.html("<div style=\"text-align:center\"><img src=\"<?=LINKCOMUM;?>/imgs/load.gif\" alt=\"Carregando...\" title=\"Carregando...\"/></div>")
	.load("quantidade.php",{"pro_codigo[]": proSelecionados},function(){
		$(this).dialog({
			modal: true,
			width: 700,
			height: 300,
			buttons:{  
				"Continuar":function(){
					// pegar as quantidades digitadas
					var pro_qtd = [];
					$(".pro-qtd").each(function(){
						pro_qtd.push( $(this).attr("name") +"|"+ $(this).val()  );
					});
					var pro_fracionado = [];
					$(".fracionado").each(function(){
						pro_fracionado.push( $(this).attr("name") +"|"+ $(this).val()  );
					});

					var pro_duracao = [];
					$(".duracao").each(function(){
						pro_duracao.push( $(this).attr("name") +"|"+ $(this).val()  );
					});
					

					$("#msg")
					.html("<div style=\"text-align:center\"><img src=\"<?=LINKCOMUM;?>/imgs/load.gif\" alt=\"Carregando...\" title=\"Carregando...\"/></div>")
					.load("selecionaMelhorLote.php",{"pro_qtd[]": pro_qtd,"pro_fracionado[]":pro_fracionado,"pro_duracao[]":pro_duracao},function(){
						$(this).dialog({
							buttons:{
								"Dispensar": function(){
									dispensar();	
								}
							}
						});
						$(".ui-dialog-buttonset button").focus(); // Fechar
					});
					$.Shortcuts.start('dispensar');
				}
			}
				
		});
	});	

}

function dispensar(){
	var out = [];
	$("input.dispensar").each(function(){
		out.push($(this).val());
	});

	$.ajax({
		url: 'dispensar.php',
		type: "POST",
		data:{
			produtos: out,
			usu_codigo: $("#usu_codigo").val(),
			medico: $("#medico").val()
		},
		success: function(){
			$("#msg")
			.html("<h2 style=\"text-align:center\">Medicamentos dispensados com sucesso!</h2><div style=\"text-align:center\"><img src=\"<?=LINKCOMUM;?>/imgs/load.gif\" alt=\"Carregando...\" title=\"Carregando...\"/></div>")
			.dialog({
				buttons:{
					"Fechar": function(){
						window.location = window.location;
					}
				}
			});
			$(".ui-dialog-buttonset button").focus(); // Fechar
			if(out != ""){
				if($("#config").val() == "t"){
					var url = "viaFarmacia.php?pro="+out+"&usu_codigo="+$("#usu_codigo").val();
					window.open(url,null,"height=800,width=1200,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
				}
			}
			setTimeout('window.location = window.location;',2000);
		}
	});
}

function validarDados(){
	if($("#medico").val() == 0){
		alert("Selecione o mï¿½dico.");
		$("#medico").focus();
		return false;
	}
	if(!$("#usu_codigo").val()){
		alert("Selecione um paciente.");
		$("#buscar").select();
		return false;
	}

    var t = [];
	$("#selecionados select option").each(function(){
		t.push( $(this).val() );

	});
	proSelecionados = t;

	abreJanelaQuant();
	
	return true;
}

function historico(dias){

	if(!$("#usu_codigo").val()){
		alert("Selecione um paciente.");
		$("#buscar").select();
		return false;
	}
	
	$("#msg")
	.html("<div id=\"msg-frame\" style=\"text-align:center;max-height:300px;overflow:auto\"><img src=\"<?=LINKCOMUM;?>/imgs/load.gif\" alt=\"Carregando...\" title=\"Carregando...\"/></div>")
	.dialog({
		width: 630,
		height: 430,
		buttons:{
			"Fechar": function(){
				$(this).dialog('destroy');
			}
		}
	})
	
	var get = "";
	if(typeof(dias) != "undefined")
		get = "?dias="+dias;

	$("#msg-frame").load('../historico/dispensacao.php'+get,{usu_codigo:$("#usu_codigo").val()});

	
}


</script>
</head>
	<body>
	<input type="hidden" id="pro_codigo" />
	<input type="hidden" id="pro_nome" />
	<div id="ver-mais" class="click" style="position:absolute;top:122px; left:535px;z-index:1000;cursor:s-resize">
		<img src="<?=LINKCOMUM?>/imgsBotoes/adicionar.png" alt="[+]" title="" />
	</div>
	<?=$common->menuTab(array("Dispensa Medicamentos")); ?>
	<?=$common->bodyTab('1'); ?>
	<form method="POST" action="">
		<?
		$sqlConfig = "SELECT * FROM config WHERE conf_chave = 'VIA_FARMACIA'";
		$queryConfig = pg_query($sqlConfig);
		$regConfig = pg_fetch_array($queryConfig);
		?>
		<input type="hidden" name="config" id=config value="<?=$regConfig[conf_valor_bool];?>">
		<input type="hidden" name="usu_codigo" id="usu_codigo" />
		<?=$form->inputLabel("Profissional");?><?=$selectMedico;?>
		
		
		<div id="bloco">
			<div id="pac-dados">
				<?=$form->inputText('buscar', $valor,'Buscar','50');?>
				<?=$form->inputText('usu_prontuario', $valor,'Prontuario','50',NULL,null,NULL,"S",NULL,NULL,NULL,"inputForm ocultar");?>
				<?//$form->inputText('usu_nome', $valor,'Nome do Paciente','60',NULL,null,NULL,"S");?>
				<?=$form->inputText("usu_nome", $valor,"Nome do Paciente", 50, NULL, "onChange=\"return buscaDispensados(this.value)\"");?>
				<?=$form->inputText('usu_mae', $valor,'Nome da Mae','50',NULL,NULL,NULL,"S",NULL,NULL,NULL,"inputForm ocultar");?>
				<?=$form->inputText('usu_pai', $valor,'Nome do Pai','50',NULL,NULL,NULL,"S",NULL,NULL,NULL,"inputForm ocultar");?>
				<?=$form->inputText('usu_datanasc', $valor,'Data de Nascimento','10',NULL,NULL,NULL,"S",NULL,NULL,NULL,"inputForm ocultar");?>
				<?=$form->hiddenForm('usu_codigo', $valor,'usu_codigo');?>
				<?=$form->hiddenForm('id_login', $_SESSION['id_login'],'id_login');?>
				
			</div>
		</div>
		<div class="clear"></div>
	
	</form>
	<div class="clear"></div>
	<div id="parte2" style="margin-top:10px;display:none">
		<div id="medicamentos" style="float:left;">
			<div class="linha">
				<label for="buscar">
				    <div class="cL0"></div>
				    <div class="cL1"><img src="<?=LINKSAUDE;?>/imgs/cap01.png"></div>
				    <div class="cL2" style="text-align:left"> Medicamentos:</div>
				    <div class="cL3"><img src="<?=LINKSAUDE;?>/imgs/cap02.png"></div>
				    <div class="cL4"></div>
				</label>
			 </div>
			<input style="border:1px solid #B0CCE5;background-color:#E8F4FE;width:400px" />
			<div id="foto-produto" style="width:400px;text-align:center"></div>
		</div>
		<div id="selecionados" style="float:left;margin-left:10px;">
			<div class="linha">
				<label for="buscar">
				    <div class="cL0"></div>
				    <div class="cL1"><img src="<?=LINKSAUDE;?>/imgs/cap01.png"></div>
				    <div class="cL2" style="text-align:left"> Selecionados:</div>
				    <div class="cL3"><img src="<?=LINKSAUDE;?>/imgs/cap02.png"></div>
				    <div class="cL4"></div>
				</label>
			 </div>
			<select size="10" class="mutipleForm" style="width:400px"></select>
		</div>
		<div class="clear"></div>
		
		<span id="salvar"><a href="#" onclick="validarDados();return false;"><?=$common->commonButton("Salvar","javascript:void(0);","adicionar.png",NULL);?></a></span>
		<span id="historico"><a href="#" onclick="historico();return false;"><?=$common->commonButton("Histórico","javascript:void(0);","historico.png",NULL);?></a></span>
	</div>
	<?=$common->closeTab();?>
	<div id="msg" title="Dispensa&ccedil;&atilde;o de Medicamentos"></div>
	</body>
</html>