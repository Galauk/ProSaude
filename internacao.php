<link href="css/estiloForm.css" rel="stylesheet" type="text/css" />
<link href="css/estiloCommon.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/javascript" src="relatorio/funcoes.js"></script>
<script src=relatorio/script.js></script>
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/base/jquery.ui.all.css">
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css">
<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="/WebSocialComum/library/js/jquery.buscar.js"></script>
<script type="text/javascript">
	$(function(){

	$("#medico").change(function(){
		buscaEsp();		
	}).focus();

	if($("#age_codigo").val() != ""){
		buscaEsp();
	}

	$("#buscar").buscar({
		callback: function(event, ui){
			var usu_codigo = $("#usu_codigo").val();

			if(ui.item){
				$("#iframe").show("normal").find("iframe").attr("src","historico.php?usu_codigo="+usu_codigo);
				$("#final a").focus();
			}
		}
	});
</script>
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
echo $common->menuTab(array('Internacoes'));
echo $common->bodyTab('1');
	if($acao == ""){
		echo $form->openForm("$PHP_SELF","POST");
		echo $common->commonButton("Adicionar Internacao",null,"add.png","onClick=\"location.href='internacao.php?acao=form_add'\"");
			echo $table->openTable("lista");
			echo $form->hiddenForm("acao","form_add");
				echo $table->criaLinha(array("Paciente","Data Nasc.","Dt. Ultima Evolucao",""),null,array("","","",4),"S");
				
				
				
				$sqlSec = "select age.age_codigo,usu_nome,to_char(usu_datanasc,'dd/mm/yyyy') as nasc from internacao_observacao as io
								join agendamento as age on age.age_codigo = io.age_codigo
								join usuario as usu on usu.usu_codigo = age.usu_codigo 
								where io_status = 'I' and io_data_alta is null
								group by  usu_nome,usu_datanasc,age.age_codigo order by usu_nome";
				$qrySec = pg_query($sqlSec);
				while($linha = pg_fetch_array($qrySec)){
					$dt = pg_fetch_array(pg_query("select to_char(io_data_cadastro,'dd/mm/yyyy hh24:mi') as data from internacao_observacao where age_codigo = '$linha[age_codigo]' order by io_data_cadastro desc"));
				$alta = $common->commonButton("Alta",null,"salvar.png","onClick=\"location.href='prontuarioEletronico/alta.php?age_codigo=$linha[age_codigo]'\"");
				$evoluir = $common->commonButton("Evoluir",null,"historico.png","onClick=\"location.href='prontuarioEletronico/interna_med.php?tipo=F&acao=novo&age_codigo=$linha[age_codigo]'\"");
				$medicamentos = $common->commonButton("Dispensar Medicamentos",null,"vacina.png","onClick=\"location.href='prontuarioEletronico/dispensacao.php?tipo=F&acao=novo&age_codigo=$linha[age_codigo]'\"");
				$impressao = $common->commonButton("Impressao",null,"print.png","onClick=\"window.open('prontuarioEletronico/impressa_internacao.php?age_codigo=$linha[age_codigo]')\"");
					echo $table->criaLinha(array("$linha[usu_nome]","$linha[nasc]","$dt[data]","$evoluir","$medicamentos","$impressao","$alta"),array("500","","","10","210","10",""));
				}
			echo $table->closeTable();
		echo $form->closeForm();	
	}
	if($acao == "form_add"){
	echo $form->openForm('salvaSecretaria.php','POST','secretarias');
		echo $form->inputText('nome_secretaria',$nome_secretaria,'Paciente',60,60,'');
		echo $form->inputText('endereco_secretaria',$endereco_secretaria,'Medico Solicitante',30,30,'');
		echo $form->inputText('endereco_secretaria',$endereco_secretaria,'Data/Hora Internacao',30,30,'');
		echo $form->inputText('endereco_secretaria',$endereco_secretaria,'Prestador',30,30,'');
		echo $form->inputText('endereco_secretaria',$endereco_secretaria,'Medico Solicitante',30,30,'');
		echo $form->inputText('endereco_secretaria',$endereco_secretaria,'Caracter de Internacao',30,30,'');
		echo $form->inputText('endereco_secretaria',$endereco_secretaria,'Data da Solicitacao',30,30,'');
		echo "<br><br><div style='margin-left:203px'><a href='secretaria.php'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.jpg'></a>&nbsp;<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg'></div>";   
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
	if($acao == "deletar")
	{
	$pegaSec = "select * from secretaria where codigo_secretaria = $codigo_secretaria";
	$querySec = pg_query($pegaSec);
	$umSec = pg_fetch_array($querySec);
	echo $common->modalConfirm("Deseja deletar a Secretaria $umSec[nome_secretaria]","secretaria.php?acao=del&codigo_secretaria=$codigo_secretaria","secretaria.php");
	}	
	
	if($acao == "del")
{
	$sqlDel = "delete from secretaria where codigo_secretaria = $codigo_secretaria";
	echo $sqlDel;
	exit;
	$qryDel = pg_query($sqlDel);
	echo $common->modalMsg("OK","Secretaria Excluida com Sucesso!","secretaria.php");
}
echo $common->closeTab();


?>

