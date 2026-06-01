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
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";	
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
function validaUsuario(){
	var nome = document.getElementById('pac_nome').value;	
	if(nome == ''){	
		window.location = "alertaNome.php";
	}
}
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
</head>
<body>
<?php 
$form = new classForm();
$common = new commonClass();
echo $common->incJquery('../');
echo $common->incJquery('600');
echo $common->menuTab(array('Informa&ccedil;&otilde;es Cl&iacute;nicas I','Informa&ccedil;&otilde;es Cl&iacute;nicas II','Dados de Tabelas','Historico Paciente'),"onClick='validaUsuario()'");
$usu_codigo = $_POST['pac_codigo'];
if($acao == "retorno"){
	$usu_codigo = $usu_codigo_retorno;
}
if($acao == "deletado"){
	$usu_codigo = $usu_codigo_deletado;	
}
if($usu_codigo == null){
	echo $common->modalMsg("ERRO","O Paciente N&atilde;o foi Selecionado Por Favor Efetue a Busca!","entrada.php");	
}
	echo $form->openForm("geralHiperdia.php","POST");
		echo "<input type='hidden' name='usu_codigo' id='usu_codigo' value='$usu_codigo'>";
		echo "<input type='hidden' name='acao' id='acao' value='salvar'>";
		echo $common->bodyTab('1');
			include 'informacaoClinica.php';
		echo $common->closeTab();
		echo $common->bodyTab('2');
			include 'informacaoClinica2.php';
		echo $common->closeTab();
		echo $common->bodyTab('3');
			include 'dadosTabela.php';
		echo $common->closeTab();
		echo $common->bodyTab('4');
			include 'historicoPaciente.php';
		echo $common->closeTab();
		echo $form->closeForm();
		if($acao == "salvar")
		{
			$usu_codigo = $_POST['usu_codigo'];
			$med_codigo = $_POST['med_codigo'];
			$hiper_pa_sistolica = $_POST['hiper_pa_sistolica'];
			$hiper_pa_distolica = $_POST['hiper_pa_distolica'];
			$hiper_altura = $_POST['hiper_altura'];
			$hiper_peso = $_POST['hiper_peso'];
			$hiper_glicemia_capilar = $_POST['hiper_glicemia_capilar'];
			$hiper_glicemia_realizada = $_POST['hiper_glicemia_realizada'];
			$hiper_enfermeiro = $_POST['enf_codigo'];
			$hiper_data = $_POST['data'];
			$hipermed_insulina_dia = $_POST['insulina'];
			$hipermed_medicamentoso = $_POST['hipermed_medicamentoso'];
			$hipermed_outros = $_POST['relacaoOutros'];
			$hipermed_lesoes = $_POST['lesoes'];
			$hiper_glicemia_realizada = $_POST['hiper_glicemia_realizada'];
		 	$stmt = "  INSERT INTO hiperdia ( 
								usu_codigo, 
								med_codigo, 
								hiper_pa_sistolica, 
								hiper_pa_distolica, 
								hiper_cintura, 
								hiper_altura, 
								hiper_peso, 
								hiper_glicemia_capilar, 
								hiper_glicemia_realizada, 
								hiper_enfermeiro,
								hiper_data,
								hiper_status
					) VALUES ( 
								'$usu_codigo', 
								'$med_codigo', 
								'$hiper_pa_sistolica', 
								'$hiper_pa_distolica', 
								'$hiper_cintura', 
								'$hiper_altura', 
								'$hiper_peso', 
								'$hiper_glicemia_capilar', 
								'$hiper_glicemia_realizada', 
								'$hiper_enfermeiro',
								'$hiper_data',
								'A')";
			$qry = pg_query($stmt) or die(pg_last_error());
			$sqlHiperdia = "select hiper_codigo from hiperdia where usu_codigo = $usu_codigo and hiper_data = '$hiper_data'";
			$qryHiperdia = pg_query($sqlHiperdia);
			$umaLinha = pg_fetch_array($qryHiperdia);
		    $hiper_codigo = $umaLinha['hiper_codigo'];
		if(isset($_POST["medicamento"])) {
             foreach($_POST["medicamento"] as $key => $value) {
				 if($value == ''){
					 echo "";
				 }else{
				 $valor = split('/',$value);
				 $stmt2 = "  INSERT INTO hiperdia_medicamentos (  
										hiper_codigo, 
										hipermed_medicamentoso, 
										pro_codigo, 
										hipermed_insulina_dia, 
										hipermed_outros,
										hipermed_dosagem,
										hipermed_lesoes
							) VALUES ( 
										'$hiper_codigo', 
										'$hipermed_medicamentoso', 
										'$valor[1]', 
										'$hipermed_insulina_dia', 
										UPPER('$hipermed_outros'),
										'$valor[0]',
										'$hipermed_lesoes')";
				$exec = pg_query($stmt2) or die (pg_last_error());
				 }
             }
         }  
			if(isset($_POST["examesCheck"])) {
             foreach($_POST["examesCheck"] as $key => $value) {
				  $stmt3 = " INSERT INTO hiperdia_exames ( 
										hiper_codigo, 
										proc_codigo,
										hiperexa_glicemia_realizada
						     ) VALUES ( 
										'$hiper_codigo', 
										'$value',
										'$hiper_glicemia_realizada' )";
				   $query = pg_query($stmt3) or die (pg_last_error());
             }
         }
	}
?>

</BODY>
</HTML>
