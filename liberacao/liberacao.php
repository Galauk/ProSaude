<?php
session_start();
?>
<html>
<link href="estilo.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<link href="tabela.css" rel="stylesheet" type="text/css" />
<link href="../ estilo.css" rel="stylesheet" type="text/css" />
<link href="tabelaLiberacao.css" rel="stylesheet" type="text/css" />
<!--<link rel="stylesheet" type="text/css" href="tabela.css" media="all">-->
<script type="text/javascript" src="view.js"></script>
<script type="text/javascript" src="calendar.js"></script>
<script language="JavaScript" src="atalhos.js"></script>
<script language="JavaScript" type="text/javascript"
	src="<?=$_SESSION[linkroot].$_SESSION[comum]?>library/js/funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="../g_ajax.js"></script>
<script language="JavaScript" type="text/javascript"
	src="<?=$_SESSION[linkroot].$_SESSION[comum]?>library/js/ajax_motor.js"></script>
<script language="JavaScript" type="text/javascript"
	src="../exame/procedimento.js"></script>
<?
require_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
require_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
require_once $_SESSION[root].$_SESSION[modulo]."json.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."funcao.calendario.php";
include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
$common = new commonClass();

function divBuscaPacienteLiberacao()
{
	$div = "<div id='lista_nomes' style='text-align:right;display:none;position:absolute;left:2%;border:1px solid black;background:#FFFFFF;max-height:325px;width:770px;'>
				<img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/jan_fechar.jpg\" style=\"cursor:pointer\" onclick=\"$('lista_nomes').style.display = 'none';\"/>
				<div style=\"width:100px;display:none;background:red;font-weight:bold;border:1px solid black;\" id=\"lista_carregando\">
					Carregando...
				</div>
				<div id=teste style='text-align:left;overflow:auto;max-height:300px;width:770px;'>
					<table id='table_nomes' cellspacing='0' cellpading='0' width='80%' style='white-space:nowrap;'></table>
				</div>
		</div>";
	return $div;
}
?>
<script>
//--------------------aqui comeca preencher campos pela busca----------------------
function mascaraData(campoData){
              var data = campoData.value;
              if (data.length == 2){
                  data = data + '/';
                  document.forms[0].data.value = data;
      return true;              
              }
              if (data.length == 5){
                  data = data + '/';
                  document.forms[0].data.value = data;
                  return true;
              }
         }
function carregaTodosCampos(cod_lib)
{
	url = "busca_paciente_liberacao.php?tipo=cod_lib&cod_lib="+cod_lib;
	ajax_tudo(url,pegaDadosCarrega);
}

function pegaDadosCarrega(txt)
{
	if(txt != "vazio" && txt != undefined && txt != "")
	{
		txt = txt.split(";");
		//document.getElementById('cod_lib').value = txt[0];
		document.getElementById('data_lib').value = txt[1];
		document.getElementById('pac_codigo').value = txt[2];
		document.getElementById('pac_nome').value = txt[3];
		document.getElementById('pac_nascimento').value = txt[4];
		document.getElementById('pac_mae').value = txt[5];
		document.getElementById('pac_cidade').value = txt[6];
	} else {
		//document.getElementById('cod_lib').value = "";
		document.getElementById('pac_codigo').value = "";
		document.getElementById('pac_nome').value = "NADA ENCONTRADO";
		document.getElementById('pac_nascimento').value = "";
		document.getElementById('pac_mae').value = "";
		document.getElementById('pac_cidade').value = "";
	}
}

//--------------------e aqui termina-----------------------------------------------
function pacientes(codigo,nome,nascimento,mae,cidade)
{
	document.getElementById('botaoPrograma').style.visibility = 'visible';
	document.getElementById('botao').style.display = '';
	document.getElementById('gridProdutos').style.display = 'none';
	document.getElementById('resposta').style.display = 'none';
	
	document.getElementById('pro_codigo').value = '';
	document.getElementById('pro_nome').value = '';
	document.getElementById('quantidade').value = '';
	document.getElementById('ite_consolidado').value = '';
	document.getElementById('posologia').value = '';
	document.getElementById('detalhes').value = '';
	document.getElementById('observacoes').value = '';
	
	document.getElementById('gridProdutos').innerHTML = '';
	
	document.getElementById('pac_nome').value=nome;
	document.getElementById('pac_codigo').value=codigo;
	document.getElementById('pac_nascimento').value=nascimento;
	document.getElementById('pac_mae').value=mae;
	document.getElementById('pac_cidade').value=cidade;
	
	document.getElementById('pac_nascimento').focus();
	
	//verificar_medicamento('pacientes');
	//setTimeout( 'verificar_medicamento();', 500 );
}
function buscar_dados_paciente(valor)
{
	url = "buscar_generico.php?tipo=dados_paciente&usu_prontuario="+valor;
	ajax_tudo(url, preencher_campo);
}
function preencher_campo(txt)
{
	if(txt != "vazio" && txt != undefined && txt != "")
	{
		txt = txt.split(";");
		document.getElementById('pac_codigo').value = txt[0];
		document.getElementById('dt_dia_lib').value = txt[1];
		document.getElementById('pac_nascimento').value = txt[2];
		document.getElementById('pac_mae').value = txt[3];
		document.getElementById('pac_cidade').value = txt[4];
		//alert( 'prrenchar_campo' );
		verificar_medicamento( 'prrenchar_campo' );
	} else {
		document.getElementById('pac_codigo').value = "";
		document.getElementById('pac_nome').value = "NADA ENCONTRADO";
		document.getElementById('pac_nascimento').value = "";
		document.getElementById('pac_mae').value = "";
		document.getElementById('pac_cidade').value = "";
	}
	
}

function buscar_prontuario()
{
  pac_codigo = document.getElementById('pac_codigo').value;
  url = "buscar_generico.php?tipo=prontuario_paciente&usu_codigo="+pac_codigo;
  ajax_tudo(url, preencher_campo_prontuario);
}

function preencher_campo_prontuario(txt)
{
	if(txt != "vazio" && txt != undefined && txt != "")
	{
		document.getElementById('pac_prontuario').value = txt;
		//alert('preencher_campo_prontuario');
	}
}

shortcut.add("Right",function() 
{
     add_dest();
});

shortcut.add("Left",function() 
{
     rem_dest();
});

shortcut.add("F2",function() 
{
 buscar_nome($F('pac_nome'), 'buscar_nome');return false;link_f7();
});

shortcut.add("F4",function() 
{
 buscar_nome($F('pac_nascimento'), 'buscar_data');
});

shortcut.add("F8",function() 
{
 var pac_codigo = document.form_msg.pac_codigo.value;
});

shortcut.add("F9",function() 
{
  window.open("../paciente_ficha.php?acao=form_add&type=c&id_login=$id_login&controle=1",null,"height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
});
<!--Fim do Script que BuSCA Paciente-->

function loadDados(event){
	var cod_lib = document.getElementById('cod_lib').value;
	/*var data_lib = document.getElementById('data_lib').value;
	var prontuario = document.getElementById('prontuario').value;
	var pac_nomes = document.getElementById('pac_nomes').value;
	var data_nasc = document.getElementById('data_nasc').value;
	var mae_nome = document.getElementById('mae_nome').value;
	var cidade = document.getElementById('cidade').value;*/
	if(cod_lib != ''){
		url = "loadPac.php?cod_lib="+cod_lib;
		alert(url);
	}
	/*if(data_lib != ''){
		url = "loadPac.php?data_lib"+data_lib;
	}
	if(pac_nome != ''){
		url = "loadPac.php?pac_nome"+pac_nome;	
	}*/	
	ajax_tudo(url, AparecerDiv); 
}
<!-- FIM DO SCRIPT QUE BUSCA OS DADOS DA LIBERACAO -->

function AparecerDiv(txt){   
	document.getElementById('manipulada').innerHTML = txt;
	document.getElementById('manipulada').style.display = "block";
}  
function changeColor(elemento)
{
	if (elemento.className == 'par'){
		elemento.className = 'parSobre';
	}else if(elemento.className == 'parSobre'){
		elemento.className = 'par';
	}else if(elemento.className == 'impar'){
		elemento.className = 'imparSobre';
	}else if(elemento.className == 'imparSobre'){
		elemento.className = 'impar';
	}
}
function selecionaData()
{
	cod_lib = document.getElementById('cod_lib').value;
	url = "selecionaData.php?cod_liberacao="+cod_lib;
	window.open(url, null,"height=200,width=350,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes")
//	document.getElementById('manipulada2').style.display='';	
}
</script>
<body class="fundolib">
<table width="90%">
	<tr>
		<td>Codigo Liberac&atilde;o&nbsp;</td>
		<td width="50">
		<form action="exa_listadt_liberacaoagendamento.php" method="POST">
			<input type="text" name="liberacao" value="" class="box" id="cod_lib" onchange='carregaTodosCampos(this.value)'>
		</td>
		<td width="50">Data Liberac&atilde;o&nbsp;</td>
		<td><input type='text' class='box' name='data_lib' id='data_lib'
			OnKeyUp="mascaraData(this);"> <? 
			//campodata("dt_dia_lib", "dt_mes_lib", "dt_ano_lib");
			?></td>
	</tr>
	<?
	echo "<tr>
			<td>
				Prontuario&nbsp;
			</td>
			<td>
				<input type=text name='pac_codigo' id='pac_codigo' class=boxNumero readonly value='$pac[usu_codigo]'>
			</td>
			  <td>
				Paciente&nbsp;
			  </td>
			  <td>	";
	echo "<input type=text name=pac_nome id=pac_nome value='$pac[usu_nome]' class=boxTexto onkeyup=\"buscar_nome(this.value);\" style=\"text-transform:uppercase;\" onkeypress=\"if(event.keyCode == 13)buscar_nome(\$F('pac_nome'), 'buscar_nome')\">";
	echo "<a href='#' onclick=\"buscar_nome(\$F('pac_nome'), 'buscar_nome');return false;link_f7()\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg id=localizar align=absmiddle border=0></a>";
	echo divBuscaPacienteLiberacao();
	echo "</td>";
	echo "</tr>
			<tr>
				<td>Nascimento&nbsp;</td>";
	echo "<td>
						<input type=text name=pac_nascimento value='$pac[usu_datanasc]' id=pac_nascimento class=boxNumero onkeypress=\"if(event.keyCode == 13)buscar_nome(\$F('pac_nascimento'), 'buscar_data');onKeypress=\"return Ajusta_Data(this, event);\" maxlength=\"10\">
";
	echo "</td>
					<td>M&atilde;e</td>
				 <td><input type=text name=pac_mae id=pac_mae class='boxTexto'>
			  </td>
		</tr>
		<tr>
			 <td>Cidade &nbsp;</td>
				<td><input type=text name=pac_cidade id=pac_cidade value='$pac[usu_end_cidade]' class=boxTexto readonly></td>
							  
	
	<td>
			<span onClick=\"loadDados(this);\" style='cursor: pointer;'><u><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/listar_procedimento_on.jpg'></u></span>
			</td>
	 </tr>";
	?>
</table>
<?php 
	echo $common->divisoria("Lista de Exames");
	echo "<div id='manipulada' style='display:none'>";
	echo "</div>";
	echo "</form>
	";
	?>
</body>
</html>
