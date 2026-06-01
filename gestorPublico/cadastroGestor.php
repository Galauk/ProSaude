<link href="../css/estiloForm.css" rel="stylesheet" type="text/css" />
<link href="../css/estiloCommon.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
<script src=../relatorio/script.js></script>
<?
session_start();
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";    
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";

$form = new classForm();
$common = new commonClass();
$table = new tableClass();

echo $common->incJquery();
echo $common->menuTab(array('Cadastro Gestor Publico'));
echo $common->bodyTab('1');
if($acao == ""){
	echo $form->openForm("$PHP_SELF","POST");
		echo "<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.png'>";
			echo $form->hiddenForm("acao","form_add");
			echo $table->openTable("lista");
			echo $form->hiddenForm("acao","form_add");
				echo $table->criaLinha(array("C&oacute;digo","Nome","Secretaria",""),null,null,"S");
				$sqlGest = "select usu.usu_codigo,usu.usu_nome,sec.nome_secretaria from usuario as usu
									 join secretaria as sec
									   on sec.codigo_secretaria = usu.codigo_secretaria
									where usu_gestor = 'S'";
				$queryGest = pg_query($sqlGest);
				while($linha = pg_fetch_array($queryGest)){
					echo $table->criaLinha(array("$linha[usu_codigo]","$linha[usu_nome]","$linha[nome_secretaria]","<a href='$PHP_SELF?acao=delete&usu_codigo=$linha[usu_codigo]''><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg' style='border:0px;float:right'></a>&nbsp;<a href='$PHP_SELF?acao=form_edit&usu_codigo=$linha[usu_codigo]'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' style='border:0px;float:right;'></a>"));
				}
			echo $table->closeTable();
		echo $form->closeForm();
}
if($acao == "form_add"){
	echo $form->openForm("$PHP_SELF",'POST');
		echo $form->hiddenForm("acao","salvar");
		echo $form->inputText('nome_gestor',$nome_gestor,'Nome',30,30,'');
		echo $form->inputText('endereco_gestor',$endereco_gestor,'Endere&ccedil;o',30,30,'');
		echo $form->inputText('numero_end_gestor',$numero_end_gestor,'Numero',10,5,'');
		echo $form->inputText('cpf_gestor',$cpf_gestor,'CPF',20,20,'');
		echo $form->inputText('rg_gestor',$rg_gestor,'RG',20,20,'');
		echo $form->inputText('tel_gestor',$tel_gestor,'Telefone:',20,20,'');
		$sqlSec = "select * from secretaria  WHERE tipo_secretaria = 'SAU'";
		echo $form->inputSelect("secretaria",null,"Secretaria",$sqlSec);
		echo $form->inputRadio('sexo','M','Sexo','','Masculino');
		echo $form->inputRadio('sexo','F','','','Feminino');
		echo "<br/><br/><input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/enviar_dados_on.jpg'>";   
	echo $form->closeForm();
}
if($acao =="salvar"){
	 $stmt = "INSERT INTO usuario (usu_nome, 
										 usu_end_rua, 
										 usu_end_nr, 
										 usu_rg, 
										 usu_cpf,
										 usu_fone,
										 usu_gestor,
										 usu_sexo,
										 codigo_secretaria,
										 usr_cad
							   ) VALUES (UPPER('$nome_gestor'), 
										 UPPER('$endereco_gestor'), 
										 UPPER('$numero_end_gestor'), 
										 UPPER('$rg_gestor'),
										 UPPER('$cpf_gestor'), 
										 UPPER('$tel_gestor'),
										 UPPER('S'),
										 UPPER('$sexo'),
										 $secretaria,
										 $id_login)";
 	$qry = pg_query($stmt);	
	echo $common->modalMsg("OK","Gestor Salvo com Sucesso!","cadastroGestor.php");
}
if($acao == "form_edit"){
	$pegaValores = "SELECT usu.usu_codigo,
						   usu.usu_nome,
						   usu.usu_end_rua,
						   usu.usu_end_nr,
						   usu.usu_cpf,
						   usu.usu_rg,
						   usu.usu_fone,
						   usu.usu_sexo,
						   sec.nome_secretaria, 
						   sec.codigo_secretaria
					  FROM usuario as usu
					  JOIN secretaria as sec
					    ON sec.codigo_secretaria = usu.codigo_secretaria
					 WHERE usu.usu_codigo = $usu_codigo";
	$queryValores = pg_query($pegaValores);
	$umValor = pg_fetch_array($queryValores);
	
	echo $form->openForm("$PHP_SELF",'POST');
		echo $form->hiddenForm("acao","editar");
		echo $form->hiddenForm("usu_codigo","$usu_codigo");
		echo $form->inputText('nome_gestor',$umValor['usu_nome'],'Nome',60,60,'');
		echo $form->inputText('endereco_gestor',$umValor['usu_end_rua'],'Endere&ccedil;o',60,60,'');
		echo $form->inputText('numero_end_gestor',$umValor['usu_end_nr'],'Numero',10,5,"onKeyPress=\"return apenasNumero(this)\" onKeyUp=\"return apenasNumero(this)\"");
		echo $form->inputText('cpf_gestor',$umValor['usu_cpf'],'CPF',20,20,"onKeyPress=\"return apenasNumero(this)\" onKeyUp=\"return apenasNumero(this)\"");
		echo $form->inputText('rg_gestor',$umValor['usu_rg'],'RG',20,20,"onKeyPress=\"return apenasNumero(this)\" onKeyUp=\"return apenasNumero(this)\"");
		echo $form->inputText('tel_gestor',$umValor['usu_fone'],'Telefone:',20,13,"onKeyPress=\"soNumeroTelefone(this)\" onKeyUp=\"soNumeroTelefone(this)\"");
		$sqlSec ="select codigo_secretaria,nome_secretaria from secretaria WHERE tipo_secretaria = 'SAU'";
		echo $form->inputSelect("secretaria",null,"Secretaria",$sqlSec,null,null,$umValor['codigo_secretaria']); 
		echo $form->inputRadio('sexo','M',"Sexo"," $umValor[usu_sexo] " . ($umValor['usu_sexo'] == "M") ? "checked" : '' ."",'Masculino');
		echo $form->inputRadio('sexo','F','','','Feminino');
		echo "<br/><br/><input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/enviar_dados_on.jpg'>";   
	echo $form->closeForm();
}
if($acao == "editar"){
	$stmt = "UPDATE usuario 
				SET usu_nome = UPPER('$nome_gestor'), 
					usu_end_rua = UPPER('$endereco_gestor'), 
					usu_end_nr = UPPER('$numero_end_gestor'), 
					usu_rg = UPPER('$rg_gestor'), 
					usu_cpf = UPPER('$cpf_gestor'),
					usu_fone = UPPER('$tel_gestor'),
					usu_sexo = UPPER('$sexo'),
					usr_alt = $id_login,
					codigo_secretaria = $secretaria
			  WHERE usu_codigo = $usu_codigo";
	$queryUp = pg_query($stmt);
	echo $common->modalMsg("OK","Paciente Editado com Sucesso!","cadastroGestor.php");
}
if($acao == "delete"){
	$pegaPac = "SELECT * FROM usuario WHERE usu_codigo = $usu_codigo";
	$queryPac = pg_query($pegaPac);
	$umPac = pg_fetch_array($queryPac);
	echo $common->modalConfirm("Deseja deletar o paciente $umPac[usu_nome]","cadastroGestor.php?acao=del&usu_codigo=$usu_codigo","cadastroGestor.php");
}
if($acao == "del")
{
	$sqlDel = "DELETE FROM usuario WHERE usu_codigo = $usu_codigo";
	$qryDel = pg_query($sqlDel);
	echo $common->modalMsg("OK","Paciente Excluido com Sucesso!","cadastroGestor.php");
}
echo $common->closeTab();
?>