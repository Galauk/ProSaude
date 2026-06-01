<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE> New Document </TITLE>
<link rel="stylesheet" type="text/css" href="../css/stylePrincipal.css"> 
</HEAD>
<?php 
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
?>
 <meta name="Victor Hugo M.Caldeira- victormarques@elotech.com.br" content="" />
 <link href="../css/estiloForm.css" rel="stylesheet" type="text/css" />
 <link href="../css/estiloCommon.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/javascript" src="../ajax_motor.js"></script>
<script language="JavaScript" type="text/javascript" src="../funcoes_busca.js"></script>
<script language="JavaScript" type="text/javascript" src="../funcaobuscaMunicipio.js"></script>
<script language="JavaScript" type="text/javascript" src="../funcaobuscaUnidade.js"></script>
<script language="JavaScript" type="text/javascript" src="../funcaobuscarMedico.js"></script>
<script>
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
</script>
 <?php 
$form = new classForm();
$common = new commonClass();
echo $common->incJquery('../');
echo $common->incJquery('600');
echo $common->menuTab(array('Exames','Dados Diagn&oacute;stico','Acompanhamento'));
$usu_codigo = $_POST['pac_codigo'];

if($acao == "retorno"){
	$usu_codigo = $usu_codigo_retorno;
}	
if($usu_codigo == null){
	echo $common->modalMsg("ERRO","O Paciente N&atilde;o foi Selecionado Por Favor Efetue a Busca!","dadospessoais.php");		
}
	echo $form->openForm("geralTuberculose.php?acao=salvar&usu_codigo=$usu_codigo","POST");
	echo "<input type='hidden' name='usu_codigo' id='usu_codigo' value='$usu_codigo'>";
		echo $common->bodyTab('1');
			include "exames.php";
		echo $common->closeTab();
		echo $common->bodyTab('2');
			include "diagnostico.php";
		echo $common->closeTab();
		echo $common->bodyTab('3');
			include "acompanhamento.php";
		echo $common->closeTab();
	echo $form->closeForm();
	
	if($acao == "salvar"){

		$med_codigo = $_POST['med_codigo'];
		$enf_codigo = $_POST['enf_codigo'];
		$usu_codigo = $_POST['usu_codigo'];
		$tub_ind_tipo_entrada = $_POST['tipoEntrada'];
		$tub_ind_tuberculinico = $_POST['testeTuberculinico'];
		$tub_ind_forma = $_POST['forma'];
		$tub_ind_extrapulmonar = $_POST['extrapulmonar'];
		$tub_ind_agravo = $_POST['agravos'];
		$tub_ind_baciloscopia_escarro = $_POST['bacEscarro'];
		$tub_ind_baciloscopia_outro = $_POST['bacOutroMaterial'];
		$tub_ind_cultura_escarro = $_POST['culturaEscarro'];
		$tub_ind_cultura_outro = $_POST['culturaOutroMaterial'];
		$tub_ind_hiv = $_POST['hiv'];
		$tub_ind_histopatologia = $_POST['histopatologia'];
		$tub_data_cadastro = $_POST['data'];
		$tub_nome_investigador = $_POST['nmInvestigador'];
		$tub_tratamento_supervisionado = $_POST['tratSupervisionado'];
		$tub_drogas = $_POST['usaDrogas'];
		$tub_dro_outros = $_POST['outrasDrogas'];
		$tub_dro_esquema = $_POST['esquema'];
		$tub_dro_retorno = $_POST['retorno'];
		//echo "<pre>".print_r($_POST,true)."</pre>";outrasDrogas
			
		$stmt ="INSERT INTO tuberculose ( 
							usu_codigo, 
							enf_codigo, 
							med_codigo, 
							tub_data_cadastro, 
							tub_nome_investigador, 
							tub_tratamento_supervisionado, 
							tub_drogas
				) VALUES ( 
							'$usu_codigo', 
							'$enf_codigo', 
							'$med_codigo', 
							'$tub_data_cadastro', 
							'$tub_nome_investigador', 
							'$tub_tratamento_supervisionado', 
							'$tub_drogas' )";
		$qry = pg_query($stmt) or die (pg_last_error());

		$consulta = "select * from tuberculose where usu_codigo = 302673 and tub_data_cadastro = '05/05/2011'";
		$qryConsulta = pg_query($consulta);
		$linha = pg_fetch_array($qryConsulta);
		$tub_codigo = $linha['tub_codigo'];
		
		$stmt2 = "   INSERT INTO tuberculose_individualidades ( 
								tub_codigo, 
								tub_ind_tipo_entrada, 
								proc_codigo, 
								tub_ind_situacao, 
								tub_ind_forma, 
								tub_ind_agravo, 
								tub_ind_baciloscopia_escarro, 
								tub_ind_cultura_escarro, 
								tub_ind_baciloscopia_outro, 
								tub_ind_cultura_outro, 
								tub_ind_hiv, 
								tub_ind_tuberculinico, 
								tub_ind_extrapulmonar,
								tub_ind_histopatologia
					) VALUES ( 
								'$tub_codigo', 
								UPPER('$tub_ind_tipo_entrada'), 
								'$proc_codigo', 
								'$tub_ind_situacao', 
								UPPER('$tub_ind_forma'), 
								UPPER('$tub_ind_agravo'), 
								UPPER('$tub_ind_baciloscopia_escarro'), 
								UPPER('$tub_ind_cultura_escarro'), 
								UPPER('$tub_ind_baciloscopia_outro'), 
								UPPER('$tub_ind_cultura_outro'), 
								UPPER('$tub_ind_hiv'), 
								UPPER('$tub_ind_tuberculinico'), 
								UPPER('$tub_ind_extrapulmonar'),
								UPPER('$tub_ind_histopatologia')  )";
		 $qry2 = pg_query($stmt2) or die (pg_last_error());
		 if(isset($_POST["drogas"])) {
             foreach($_POST["drogas"] as $key => $value) {
				 $stmt3 = "INSERT INTO tuberculose_drogas ( 
									   tub_codigo, 
									   tub_dro_tipo, 
									   tub_dro_outros, 
									   tub_dro_esquema, 
									   tub_dro_retorno)
							  VALUES( 
									  '$tub_codigo', 
									  UPPER('$value'), 
									  '$tub_dro_outros', 
									  '$tub_dro_esquema', 
									  '$tub_dro_retorno' )";
				$qry3 = pg_query($stmt3) or die (pg_last_error());
			 }
		 }
	}
?>
</BODY>
</HTML>
