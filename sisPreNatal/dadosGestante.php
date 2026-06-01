<html>
<head>
<link rel="stylesheet" type="text/css" href="../css/stylePrincipal.css"> 
<?php 
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/debug.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."json.inc.php";
?>
<meta name="Victor Hugo M.Caldeira- dilee@elotech.com.br" content="" />
<link href="../css/estiloForm.css" rel="stylesheet" type="text/css" />
<link href="../css/estiloCommon.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/javascript" src="../ajax_motor.js"></script>
<script language="JavaScript" type="text/javascript" src="../funcoes_busca.js"></script>
<script language="JavaScript" type="text/javascript" src="../funcaobuscaMunicipio.js"></script>
<script language="JavaScript" type="text/javascript" src="../funcaobuscaUnidade.js"></script>
<script language="JavaScript" type="text/javascript" src="../funcaobuscaEnfermeiro.js"></script>
<script>
function passar_usuario(codigo, nome, mae, data_nasc, cidade, prontuario)
{
	validaBusca("pac_codigo").value = codigo;
	validaBusca("pac_nome").value = nome;
	validaBusca("pac_nascimento").value = data_nasc;
	validaBusca("pac_mae").value = mae;       
	if(document.getElementById("pac_prontuario") != null)
	{
		validaBusca("pac_prontuario").value = prontuario;
	}
	validaBusca('lista_nomes').style.display = 'none';
	validaBusca('pac_nome').focus();
}

function buscar_nome(valor, acao)
{
	url = "../buscar_nomes.php?palavra="+valor+"&acao="+acao;
	ajax_tudo(url, popular_nome);
	validaBusca('lista_nomes').style.display = '';
	validaBusca('table_nomes').innerHTML = '';
	validaBusca("lista_carregando").style.display = '';
}
function focaCampo()
{
	document.getElementById('pac_nome').focus();	
}

window.onload = function(){
	document.getElementById('pac_nome').focus();
}
</script>
<link href='../css/estiloForm.css' rel='stylesheet' type='text/css' />
</head>
<body>
<?
$form = new classForm();
$common = new commonClass();
echo $common->incJquery();
echo $common->menuTab(array("Sis Pr&eacute;-Natal"));
echo $common->bodyTab("1");
	echo $form->openForm("dadosGestante.php","POST");
	echo "
		<table border='0' cellpadding=0 cellspacing=0 width='100%'>
			<tr>
				<td>
					<b>Prontuario:&nbsp;&nbsp;
					<input type=hidden name='pac_codigo' id='pac_codigo' class=boxl size=10 onchange='buscar_dados_paciente();'>
					<input type=hidden name='acao' id='acao' value='inserir'>
					<input type=text name='pac_prontuario' id='pac_prontuario' class=inputForm size=10>&nbsp;
				</td>
				<td>
					<b>Nascimento:
					<input type=text name=pac_nascimento id=pac_nascimento class=inputForm size=15 >&nbsp;&nbsp;
					<a href='#' onclick=\"buscar_nome(\$F('pac_nascimento'), 'buscar_data');return false;\"><img src=../imgs/localizar.jpg id=localizar align=absmiddle border=0></a>".
					divBuscaPaciente('../')
					."
				</td>
				<td>
					<b>Nome:
					<input type=text size=80 name=pac_nome id=pac_nome value='$pac[usu_nome]' class=inputForm  style=\"text-transform:uppercase;\">&nbsp;&nbsp;
					<a href='#' onclick=\"buscar_nome(\$F('pac_nome'), 'buscar_nome');return false;\"><img src=../imgs/localizar.jpg id=localizar align=absmiddle border=0></a>
				</td>
			</tr>
			<tr>
				<td colspan='3'>
					<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp
					M&atilde;e:
					<input type=text name='pac_mae' id='pac_mae' class=inputForm size=80'>
				</td>
			</tr>
			<tr>
				<td style='padding-top:5px;'>
					<input type='image' src='../imgs/continuar_cadastro2.jpg'>
				</td>
			</tr>
	  	</table>";
	echo $form->closeForm();
echo $common->closeTab();
if ($_POST['acao'] == "inserir"){
	$select = "SELECT sequencia";
	$executa = pg_query($select);
	$dado = pg_fetch_array($executa);
	$sispn_codigo = $dado[0];
	
	$insert = "INSERT INTO sis_pre_natal (sispn_codigo, 
												usu_codigo, 
												sispn_data, 
												sispn_data_cadastro, 
												sispn_numero, 
												sispn_qtde_parto_vaginal, 
												sispn_qtde_cesareas, 
												sispn_qtde_abortos, 
												sispn_qtde_filhos_vivos, 
												sispn_qtde_rn_menos_2500, 
												sispn_qtde_rn_mais_4000, 
												sispn_data_ultima_menstruacao, 
												sispn_peso_anterior, 
												sispn_tabagismo, 
												sispn_data_provavel_parto, 
												sispn_estatura, 
												sispn_num_cigarros_dia)
										VALUES ($sispn_codigo, 
												$usu_codigo, 
												$sispn_data, 
												$sispn_data_cadastro, 
												$sispn_numero, 
												$sispn_qtde_parto_vaginal, 
												$sispn_qtde_cesareas, 
												$sispn_qtde_abortos, 
												$sispn_qtde_filhos_vivos, 
												$sispn_qtde_rn_menos_2500, 
												$sispn_qtde_rn_mais_4000, 
												$sispn_data_ultima_menstruacao, 
												$sispn_peso_anterior, 
												$sispn_tabagismo, 
												$sispn_data_provavel_parto, 
												$sispn_estatura, 
												$sispn_num_cigarros_dia)";
	$exec = pg_query($insert);
	//if ($exec){
		echo $common->modalMsg("OK", "Pré-Natal Inserido com sucesso!", "geralSisPreNatal.php?id_login=$id_login&sispn_codigo=$sispn_codigo");
	//}
}
?>
</body>
</html>

    	