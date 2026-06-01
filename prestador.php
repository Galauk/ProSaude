<script type="text/javascript">
function validaCampos(){
	cnes = document.getElementById('cnes');
	med_nome = document.getElementById('med_nome');
	if (med_nome.value.length == 0){
		alert('O campo Razão Social é obrigatório.');
		med_nome.focus();
		return false;
	}
	if (cnes.value.length == 0){
		alert('O campo CNES é obrigatório.');
		cnes.focus();
		return false;
	}
	document.form.submit();
}
</script>
<?
require_once "global.php";

$form = new classForm();
$common = new commonClass();
$table = new tableClass();

echo $common->incJquery();
echo $common->menuTab(array('Cadastro de Prestadores de Servicos'));
echo $common->bodyTab('1');
	if($acao == ""){
		echo $common->commonButton("Adicionar",$PHP_SELF."?acao=form_add","adicionar.png");
			echo $table->openTable("lista");
				echo $table->criaLinha(array("C&oacute;digo","Prestador","Tipo","&nbsp;"),null,array("","","","2"),"S");
				$sqlSec = "SELECT * FROM medico where prestador_servico != 'M'";
				$qrySec = pg_query($sqlSec);
				while($linha = pg_fetch_array($qrySec)){
				if(trim($linha[prestador_servico])=="H") { $npr = "<font color=blue><b>Hospital</b></font>"; }
				if(trim($linha[prestador_servico])=="L") { $npr = "<font color=green><b>Laboratorio</b></font>"; }
					echo $table->criaLinha(array("$linha[med_codigo]","$linha[med_nome]","$npr",
					$common->commonButton("Editar",$PHP_SELF."?acao=form_edit&med_codigo=$linha[med_codigo]","editar_on.png"),
					$common->commonButton("Apagar",$PHP_SELF."?acao=deletar&med_codigo=$linha[med_codigo]","apagar.png")));
				}
			echo $table->closeTable();
	}
	if(($acao == "form_add" OR $acao == "form_edit")){
		echo $form->openForm($PHP_SELF,'POST','form');
		if($acao=="form_add") {
		  echo $form->hiddenForm("acao", "salvar");
		} else {
		  echo $form->hiddenForm("acao", "edita");
		  echo $form->hiddenForm("med_codigo", $med_codigo);
		  $rr = pg_fetch_array(pg_query("select *from medico where med_codigo = '$med_codigo'"));
		}
				echo $form->inputText('med_nome_fantasia',$rr[med_nome_fantasia],'Nome Fantasia',60,60,'');
				echo $form->inputText('med_nome',$rr[med_nome],'Razao Social',50,50,'');
				echo $form->inputText('med_cnpj',$rr[med_cnpj],'Cnpj',19,19,'');
				echo $form->inputText('cnes',$rr[cnes],'CNES',10,10,'');
				echo $form->inputText('med_email',$rr[med_email],'E-mail',40,40,'');
				echo $form->inputText('med_endereco',$rr[med_endereco],'Endereco',60,60,'');
				echo $form->inputText('med_end_bairro',$rr[med_end_bairro],'Bairro',20,20,'');
				echo $form->inputText('med_end_cep',$rr[med_end_cep],'CEP',12,9,'');
					 $cid = "select cid_codigo,cid_nome from cidade order by cid_nome";
				echo $form->inputSelect("cid_codigo",$rr[cid_codigo],"Cidade",$cid,null,null,$rr[cid_codigo])."<br/>";
				echo $form->inputText('med_end_telefone',$rr[med_end_telefone],'Telefone',14,14,'');
				echo $form->inputSelect("prestador_servico",array("L"=>"LABORATORIO","H"=>"HOSPITAL"),"Tipo de prestador",null,null,null,$rr[prestador_servico])."<br/>";		
				echo"<br><br><div style='float:left;width:98px;'>&nbsp;</div><div style='float:left;'>";		
				echo $common->commonButton("voltar",$PHP_SELF,"voltar.png");
				echo"</div>";
				echo"<div style='float:left;'>";
				echo $common->commonButton("Salvar","","report.png","onClick=\"return validaCampos();\"");
				echo"</div><br><br>";
				
				echo $form->closeForm();    
	}
	if($acao == "salvar"){
		$sql = "INSERT INTO medico ( 
					med_nome_fantasia,
					med_crm,
					uf_codigo_crm,
					med_nome,
					med_cnpj,
					cnes,
					med_email,
					med_endereco,
					med_end_bairro,
					med_end_cep,
					cid_codigo,
					med_end_telefone,
					prestador_servico
					 ) VALUES ( 
					'$med_nome_fantasia',
					'NAOTEM',
					'1',
					'$med_nome',
					'$med_cnpj',
					'$cnes',
					'$med_email',
					'$med_endereco',
					'$med_end_bairro',
					'$med_end_cep',
					'$cid_codigo',
					'$med_end_telefone',
					'$prestador_servico')";
 			$query = pg_query($sql) or die(pg_last_error());
			echo $common->modalMsg("OK","Prestador Salvo Com Sucesso!",$PHP_SELF);	
	}
	if($acao == "edita"){
		$sql = "UPDATE medico SET
					med_nome_fantasia='$med_nome_fantasia',
					med_crm='NAOTEM',
					uf_codigo_crm='1',
					med_nome='$med_nome',
					med_cnpj='$med_cnpj',
					cnes='$cnes',
					med_email='$med_email',
					med_endereco='$med_endereco',
					med_end_bairro='$med_end_bairro',
					med_end_cep='$med_end_cep',
					cid_codigo='$cid_codigo',
					med_end_telefone='$med_end_telefone',
					prestador_servico='$prestador_servico'
				WHERE med_codigo = $med_codigo";
		$query = pg_query($sql);
		echo $common->modalMsg("OK","Prestador Salva Com Sucesso!",$PHP_SELF);	
	}
	if($acao == "deletar") {
		$getQuery = pg_query("select * from medico where med_codigo = $med_codigo");
		$getName = pg_fetch_array($getQuery);
		echo $common->modalConfirm("Deseja deletar o Prestador $getName[med_nome]","prestador.php?acao=del&med_codigo=$med_codigo","prestador.php");
	}	
	
	if($acao == "del") {
		$sqlDel = "delete from medico where med_codigo = $med_codigo";
		$qryDel = pg_query($sqlDel);
		echo $common->modalMsg("OK","Prestador Excluida com Sucesso!","prestador.php");
	}
echo $common->closeTab();


?>

