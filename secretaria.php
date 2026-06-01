<link href="css/estiloForm.css" rel="stylesheet" type="text/css" />
<link href="css/estiloCommon.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/javascript" src="relatorio/funcoes.js"></script>
<script src=relatorio/script.js></script>
<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

$form = new classForm();
$common = new commonClass();
$table = new tableClass();

echo $common->incJquery();
echo $common->menuTab(array('Cadastro Secretaria de Sa&uacute;de'));
echo $common->bodyTab('1');
	if($acao == ""){
		echo $form->openForm("$PHP_SELF","POST");
		echo "<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.png'>";
			echo $table->openTable("lista");
			echo $form->hiddenForm("acao","form_add");
				echo $table->criaLinha(array("C&oacute;digo","Nome",""),null,array("","",2),"S");
				$sqlSec = "SELECT * FROM secretaria";
				$qrySec = pg_query($sqlSec);
				while($linha = pg_fetch_array($qrySec)){
					echo $table->criaLinha(array("$linha[codigo_secretaria]","$linha[nome_secretaria]","<a href=\"$PHP_SELF?acao=deletar&codigo_secretaria=$linha[codigo_secretaria]\"><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg' style='float:right'></a><a href=\"$PHP_SELF?acao=form_edit&codigo_secretaria=$linha[codigo_secretaria]\"><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' style='float:right'></a>"));
				}
			echo $table->closeTable();
		echo $form->closeForm();	
	}
	if($acao == "form_add"){
	echo $form->openForm('salvaSecretaria.php','POST','secretarias');
		echo $form->inputText('nome_secretaria',$nome_secretaria,'Nome Secretaria',60,60,'');
		echo $form->inputText('endereco_secretaria',$endereco_secretaria,'Endere&ccedil;o',30,30,'');
		echo $form->inputText('numero_end_secretaria',$numero_end_secretaria,'Numero',10,5,'');
		echo $form->inputText('cnes_secretaria',$cnes_secretaria,'CNES',20,20,'');
		echo $form->inputText('cnpj_secretaria',$cnpj_secretaria,'CNPJ',20,20,'');
		echo "<br><br><a href='secretaria.php'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.jpg'></a>&nbsp;<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/enviar_dados_on.jpg'>";   
	echo $form->closeForm();    
	}
	if($acao == "salvar"){
		 $stmt = "INSERT INTO secretaria ( 
					nome_secretaria, 
					cnes_secretaria, 
					cnpj_secretaria, 
					endereco_secretaria, 
					numero_end_secretaria
					 ) VALUES ( 
					UPPER('$nome_secretaria'), 
					UPPER('$cnes_secretaria'), 
					UPPER('$cnpj_secretaria'), 
					UPPER('$endereco_secretaria'), 
					UPPER('$numero_end_secretaria') )";
 			$qry = pg_query($stmt);
			echo $common->modalMsg("OK","Secretaria Salva Com Sucesso!","secretaria.php");	
	}
	if($acao == "form_edit"){
		echo $form->openForm("$PHP_SELF",'POST','secretarias');
		$sqlPegaDados = "select * from secretaria where codigo_secretaria = $codigo_secretaria";
					echo $codigo_secretaria."aa";
		$qryPegaDados = pg_query($sqlPegaDados);
		$umReg = pg_fetch_array($qryPegaDados);
			echo $form->hiddenForm("acao","edita");

			echo $form->hiddenForm("codigo_secretaria","$codigo_secretaria");
			echo $form->inputText('nome_secretaria',$umReg[nome_secretaria],'Nome Secretaria',60,60,'');
			echo $form->inputText('endereco_secretaria',$umReg[endereco_secretaria],'Endere&ccedil;o',30,30,'');
			echo $form->inputText('numero_end_secretaria',$umReg[numero_end_secretaria],'Numero',10,5,'');
			echo $form->inputText('cnes_secretaria',$umReg[cnes_secretaria],'CNES',20,20,'');
			echo $form->inputText('cnpj_secretaria',$umReg[cnpj_secretaria],'CNPJ',20,20,'');
			echo "<br><br><input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.jpg'>&nbsp;<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/enviar_dados_on.jpg'>";   
		echo $form->closeForm();		
	}
	if($acao == "edita"){
		$stmt = "UPDATE secretaria SET
					nome_secretaria = UPPER('$nome_secretaria'), 
					cnes_secretaria = UPPER('$cnes_secretaria'), 
					cnpj_secretaria = UPPER('$cnpj_secretaria'), 
					endereco_secretaria = UPPER('$endereco_secretaria'), 
					numero_end_secretaria = UPPER('$numero_end_secretaria')
				WHERE codigo_secretaria = $codigo_secretaria";
		$exec = pg_query($stmt);
	}
	if($acao == "deletar")	{
		$pegaSec = "select * from secretaria where codigo_secretaria = $codigo_secretaria";
		$querySec = pg_query($pegaSec);
		$umSec = pg_fetch_array($querySec);
		echo $common->modalConfirm("Deseja deletar a Secretaria $umSec[nome_secretaria]","secretaria.php?acao=del&codigo_secretaria=$codigo_secretaria","secretaria.php");
	}	
	
	if($acao == "del"){
		$sqlDel = "delete from secretaria where codigo_secretaria = $codigo_secretaria";
		echo $sqlDel;
		exit;
		$qryDel = pg_query($sqlDel);
		echo $common->modalMsg("OK","Secretaria Excluida com Sucesso!","secretaria.php");
	}
echo $common->closeTab();


?>

