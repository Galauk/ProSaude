<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<script src="funcoes.js"></script>
<script src="g_ajax.js"></script>
<script>
	function buscarInv(arg, texto){
		if(arg == 1){
			div = "buscarInvConteudo";
			data = document.getElementById('data').value;
			gru_codigo = document.getElementById('gru_codigo').value;
			set_codigo = document.getElementById('set_codigo').value;
			url = "op_buscarCadInventario.php?acao=buscar&texto="+texto+"&data="+data+"&gru_codigo="+gru_codigo+"&set_codigo="+set_codigo;
		} else {
			div = "buscarInv";
			url = "op_buscarCadInventario.php";
		}
		exec_ajax(url,div);

	}
	function passar(inv_codigo)
	{
		//location.href="relDiferencaEstoqueInventario.php?inv_codigo="+inv_codigo;
		window.open("relDiferencaEstoqueInventario.php?inv_codigo="+inv_codigo,"","");
	}
	function relAcuracia(inv_codigo)
	{
		window.open("relAcuraciaInventario.php?inv_codigo="+inv_codigo,"","");
	}
	function atualizar(inv_codigo)
	{
		confirmacao = confirm("Aten��o, a gera��o da movimenta��o implica no fechamento do invent�rio e consequentemente a indisponibilidade de altera��o de seus respectivos dados.\nTem certeza de que pretende efetuar a movimenta��o?");
		if(confirmacao == true)
		{
			location.href="op_atualizarInventario.php?inv_codigo="+inv_codigo;
		}
	}
	function cadastro(inv_codigo)
	{
        id_login = document.getElementById('cod_login').value;
		location.href="op_buscarInventario.php?inv_codigo="+inv_codigo+"&id_login="+id_login;
	}
	function validaCamposObrigatorios(){
		data = document.getElementById('data');
		grupo = document.getElementById('gru_codigo');
		setor = document.getElementById('set_codigo');

		if (data.value == ''){
			data.focus();
			alert('O campo data n�o pode ser vazio!');
			return false;
		}
		if (grupo.value == 0){
			grupo.focus();
			alert('Selecione o grupo de produtos!');
			return false;
		}
		if (setor.value == 0){
			setor.focus();
			alert('Selecione o setor a ser realizado o invent�rio!');
			return false;
		}
		document.op_cadInventario.submit();
		return true;
	}
</script>
</head>
<body>

<?php
$data = date("d/m/Y");

session_start();
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once "authlib.inc.php";

$id_login = $_GET['id_login'];
           
$stmt = "SELECT uni_codigo 
		   FROM logon 
		  WHERE id_login = $id_login";
		  
$stmt = db_query($stmt);
$dados = pg_fetch_array($stmt);

$form = new classForm();
$common = new commonClass();
echo $common->incJquery();
switch($acao)
{
	case "inserir":
		
		echo $common->menuTab(array("Cadastro de Invent&aacute;rio"));
		echo $common->bodyTab('1');
			$url = 'op_cadInventario.php';
			echo $form->openForm($url, 'POST', 'op_cadInventario');
		    	echo $form->hiddenForm('id_login', $id_login);
		    	echo $form->hiddenForm('dt_inicial', '01-01-1901');
		    	echo $form->inputText('data', $data, 'Data', '12', '10', 'onKeypress=\'return Ajusta_Data(this, event);\'');
		    	$selectGrupo = "SELECT gru_codigo,
		    						   gru_nome 
		    					  FROM grupo 
		    					 ORDER BY gru_nome";
		    	echo $form->inputSelect('gru_codigo', null, "Grupo de Produto", $selectGrupo, null, null, null, "style=\"width:255px;\"", "SELECIONE", "style=\"width:255px;\"", "N", "S");
		    	$selectSetor = "SELECT set_codigo, 
		    						   set_nome 
		    					  FROM setor 
		    					 WHERE set_estoque = 'S' "
							.($dados[0]=="" ? "" : " AND uni_codigo = ".$dados[0]).
							" ORDER BY set_nome";
		    	echo $form->inputSelect('set_codigo', null, "Setor", $selectSetor, null, null, null, "style=\"width:255px;\"", "SELECIONE", "style=\"width:255px;\"", "N", "S");
		    	echo $form->inputText('responsavel', null, "Respons&aacute;vel", 100);
		    	echo $form->inputText('equipe', null, "Equipe", 100);
				echo "<div style='clear:both;'>";
					echo "<div style='float:left;width:195px;text-align:right;padding-right:5px;'>";
						echo $form->submitButton(null,$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg", "onclick='return validaCamposObrigatorios();'");
					echo "</div>";
					echo "<div style='float:left;width:200px;padding-left:5px;'>
							<a href=inventario.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.jpg border=0> </a>
						  </div>";
				echo "</div>";
			echo $form->closeForm();
		echo $common->closeTab();
		
	break;

	case "buscar":
		echo $form->hiddenForm("id_login", $id_login, $id);
		echo "<input type=hidden id=\"cod_login\" name=id_login value=$id_login>";
		echo $common->menuTab(array('Digita&ccedil;&atilde;o da Contagem'));
		echo $common->bodyTab('1');
		echo "<div id=\"buscarInv\">";
		echo $form->inputText('data', $data, 'Data', 12, 10, 'onKeypress=\'return Ajusta_Data(this, event);\'');
    	$selectGrupo = "SELECT gru_codigo,
    						   gru_nome 
    					  FROM grupo 
    					 ORDER BY gru_nome";
    	echo $form->inputSelect('gru_codigo', null, "Grupo de Produto", $selectGrupo, null, null, null, "style=\"width:255px;\"", "SELECIONE", "style=\"width:255px;\"", "N", "S");
    	$selectSetor = "SELECT set_codigo, 
    						   set_nome
						  FROM Setor
						 WHERE set_estoque = 'S' "
						.($dados[0]=="" ? "" : " AND uni_codigo = ".$dados[0]).
						"ORDER BY set_nome";
    	echo $form->inputSelect('set_codigo', null, "Setor", $selectSetor, null, null, null, "style=\"width:255px;\"", "SELECIONE", "style=\"width:255px;\"", "N", "S");
    	echo "<div style='clear:both;'>";
			echo "<div style='float:left;width:195px;text-align:right;padding-right:5px;'>";
				if(chmodbtn($id_login, 'procurar_if', 'inventario.php'))
				{
					echo $form->submitButton("procurar", $_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg", "onClick=\"buscarInv(1,'buscar')\"");
				} else {
					echo "<img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_off.jpg\" alt=\"Procurar\">";
				}
			echo "</div>";
			echo "<div style='float:left;width:200px;padding-left:5px;'>
					<a href=inventario.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.jpg border=0> </a>
				  </div>";
		echo "</div>";
		echo "<div id=\"buscarInvConteudo\" style=\"overflow:auto;clear:both;padding-top:20px;\">";
		echo "</div>";
		echo $form->closeForm();
		echo $common->closeTab();
		echo "</div>";
	break;

	case "relatorio":
		echo $form->hiddenForm("id_login", $id_login, $id);
		echo "<input type=hidden id=\"cod_login\" name=id_login value=$id_login>";
		echo $common->menuTab(array('Relat&oacute;rio'));
		echo $common->bodyTab('1');
		echo "<div id=\"buscarInv\">";
		echo $form->inputText('data', $data, 'Data', 12, 10, 'onKeypress=\'return Ajusta_Data(this, event);\'');
    	$selectGrupo = "SELECT gru_codigo,
    						   gru_nome 
    					  FROM grupo 
    					 ORDER BY gru_nome";
    	echo $form->inputSelect('gru_codigo', null, "Grupo de Produto", $selectGrupo, null, null, null, "style=\"width:255px;\"", "SELECIONE", "style=\"width:255px;\"", "N", "S");
    	$selectSetor = "SELECT set_codigo, 
    						   set_nome
						  FROM Setor
						 WHERE set_estoque = 'S' "
						.($dados[0]=="" ? "" : " AND uni_codigo = ".$dados[0]).
						"ORDER BY set_nome";
    	echo $form->inputSelect('set_codigo', null, "Setor", $selectSetor, null, null, null, "style=\"width:255px;\"", "SELECIONE", "style=\"width:255px;\"", "N", "S");
    	echo "<div style='clear:both;'>";
			echo "<div style='float:left;width:195px;text-align:right;padding-right:5px;'>";
				if(chmodbtn($id_login, 'procurar_if', 'inventario.php'))
				{
					echo $form->submitButton("procurar", $_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg", "onClick=\"buscarInv(1,'relatorio')\"");
				} else {
					echo "<img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_off.jpg\" alt=\"Procurar\">";
				}
			echo "</div>";
			echo "<div style='float:left;width:200px;padding-left:5px;'>
					<a href=inventario.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.jpg border=0> </a>
				  </div>";
		echo "</div>";
		echo "<div id=\"buscarInvConteudo\" style=\"overflow:auto;clear:both;padding-top:20px;\">";
		echo "</div>";
		echo $form->closeForm();
		echo $common->closeTab();
		echo "</div>";
	break;

	case "atualizar":
		echo "<fieldset>";
		echo "<legend>Buscar</legend> \n";
		echo "<div id=\"buscarInv\">";
		echo "<table>";
		echo "<tr>";
			echo "<td>";
				echo "Data";
			echo "</td>";
			echo "<td>";
				echo "<input id=\"data\" type=\"text\" name=\"data\" value=\"$data\" class=box onKeypress='return Ajusta_Data(this, event);' maxlength=\"10\">";
			echo "</td>";
			echo "<td>";
				echo "Grupo";
			echo "</td>";
			echo "<td>";
				echo "<select id=\"gru_codigo\" name='gru_codigo' class=box>\n";
					$UniQuery=pg_query("SELECT gru_codigo, 
											   gru_nome 
										  FROM grupo 
										 ORDER BY gru_nome");
					while($SetArray=pg_fetch_array($UniQuery)) {
						echo ($gru_codigo==$SetArray[gru_codigo])?"<option value='$SetArray[gru_codigo]' selected> $SetArray[gru_nome]</option>":"<option value='$SetArray[gru_codigo]' > $SetArray[gru_nome]</option>\n";
					}
				echo "</select>\n";
			echo "</td>\n";
			echo "<td>";
				echo "Setor";
			echo "</td>";
			echo "<td>";
				echo "<select id=\"set_codigo\" name='set_codigo' class=box>\n";
				$UniQuery=pg_query("SELECT set_codigo, 
										   set_nome 
									  FROM Setor 
									 WHERE set_estoque = 'S'
									 ORDER BY set_nome");
				while($SetArray=pg_fetch_array($UniQuery)) {
					echo ($set_codigo==$SetArray[set_codigo])?"<option value='$SetArray[set_codigo]' selected> $SetArray[set_nome]</option>":"<option value='$SetArray[set_codigo]' > $SetArray[set_nome]</option>\n";
				}
				echo "</select>\n";
			echo "</td>\n";
			echo "<td>"; //onclick=\"buscarCadInventario();\"
				echo "<input type=\"button\" value=\"Buscar\" onClick=\"buscarInv(1,'atualizar')\">";
			echo "</td>";
		echo "<tr>";
	echo "</table>";
	echo "</div>";
	echo "</fieldset>";
	//echo "</form>";
	break;
		case "acuracia":
		echo $form->hiddenForm("id_login", $id_login, $id);
		echo "<input type=hidden id=\"cod_login\" name=id_login value=$id_login>";
		echo $common->menuTab(array('Relat&oacute;rio de Acur&aacute;cia'));
		echo $common->bodyTab('1');
		echo "<div id=\"buscarInv\">";
		echo $form->inputText('data', $data, 'Data', 12, 10, 'onKeypress=\'return Ajusta_Data(this, event);\'');
    	$selectGrupo = "SELECT gru_codigo,
    						   gru_nome 
    					  FROM grupo 
    					 ORDER BY gru_nome";
    	echo $form->inputSelect('gru_codigo', null, "Grupo de Produto", $selectGrupo, null, null, null, "style=\"width:255px;\"", "SELECIONE", "style=\"width:255px;\"", "N", "S");
    	$selectSetor = "SELECT set_codigo, 
    						   set_nome
						  FROM Setor
						 WHERE set_estoque = 'S' "
						.($dados[0]=="" ? "" : " AND uni_codigo = ".$dados[0]).
						"ORDER BY set_nome";
    	echo $form->inputSelect('set_codigo', null, "Setor", $selectSetor, null, null, null, "style=\"width:255px;\"", "SELECIONE", "style=\"width:255px;\"", "N", "S");
    	echo "<div style='clear:both;'>";
			echo "<div style='float:left;width:195px;text-align:right;padding-right:5px;'>";
				if(chmodbtn($id_login, 'procurar_if', 'inventario.php'))
				{
					echo $form->submitButton("procurar", $_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg", "onClick=\"buscarInv(1,'acuracia')\"");
				} else {
					echo "<img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_off.jpg\" alt=\"Procurar\">";
				}
			echo "</div>";
			echo "<div style='float:left;width:200px;padding-left:5px;'>
					<a href=inventario.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.jpg border=0> </a>
				  </div>";
		echo "</div>";
		echo "<div id=\"buscarInvConteudo\" style=\"overflow:auto;clear:both;padding-top:20px;\">";
		echo "</div>";
		echo $form->closeForm();
		echo $common->closeTab();
		echo "</div>";
	break;
}
?>


</body>
</html>
