<script type="text/javascript">
function validaCampos(){

	document.form.submit();
}
</script>
<?
require_once "global.php";

$form = new classForm();
$common = new commonClass();
$table = new tableClass();

echo $common->incJquery();
echo $common->menuTab(array('Cadastro de Medicos Externos'));
echo $common->bodyTab('1');
	if($acao == ""){
		echo $common->commonButton("Adicionar",$PHP_SELF."?acao=form_add","adicionar.png");
			echo $table->openTable("lista");
				echo $table->criaLinha(array("C&oacute;digo","Nome","Númedo do conselho","&nbsp;"),null,array("","","","2"),"S");
				$sqlSec = "SELECT * FROM medico where prestador_servico = 'M'";
				$qrySec = pg_query($sqlSec);
				while($linha = pg_fetch_array($qrySec)){
					echo $table->criaLinha(array("$linha[med_codigo]","$linha[med_nome]","$linha[med_crm]",
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
				echo $form->inputText('med_nome',$rr[med_nome],'Nome',50,50,'');
				echo $form->inputText('med_cnpj',$rr[med_crm],'Número do conselho',19,19,'');
				?>
					<div>
					<select style="width: 158px;margin: 1px 0px;" name="con_codigo" id="con_codigo" class="ui-state-default" onchange="mostraNumeroConselho()" style="width: 200px;">
			            <option value="">Tipo do Conselho</option>
                        <option value="19" <?=$rr[con_codigo] == 19? 'selected' : '' ?> > CREF</option>
                        <option value="23" <?=$rr[con_codigo] == 23? 'selected' : '' ?> > CRR</option>
                        <option value="17" <?=$rr[con_codigo] == 17? 'selected' : '' ?> > CRFA</option>
                        <option value="66" <?=$rr[con_codigo] == 66? 'selected' : '' ?> > COREN</option>
                        <option value="62" <?=$rr[con_codigo] == 62? 'selected' : '' ?> > CRESS</option>
                        <option value="69" <?=$rr[con_codigo] == 69? 'selected' : '' ?> > CRF</option>
                        <option value="70" <?=$rr[con_codigo] == 70? 'selected' : '' ?> > CREFITO</option>
                        <option value="71" <?=$rr[con_codigo] == 71? 'selected' : '' ?> > CRM</option>
                        <option value="74" <?=$rr[con_codigo] == 74? 'selected' : '' ?> > CRN</option>
                        <option value="75" <?=$rr[con_codigo] == 75? 'selected' : '' ?> > CRO</option>
                        <option value="77" <?=$rr[con_codigo] == 77? 'selected' : '' ?> > CRP</option>
                        <option value="83" <?=$rr[con_codigo] == 83? 'selected' : '' ?> > RMS</option>
                        <option value="15" <?=$rr[con_codigo] == 15? 'selected' : '' ?> > CRBM</option>
                        <option value="18" <?=$rr[con_codigo] == 18? 'selected' : '' ?> > CRBIO</option>
                        <option value="26" <?=$rr[con_codigo] == 26? 'selected' : '' ?> > CRTR</option>
                        <option value="61" <?=$rr[con_codigo] == 61? 'selected' : '' ?> > CRA</option>
                        <option value="64" <?=$rr[con_codigo] == 64? 'selected' : '' ?> > CRC</option>
                        <option value="67" <?=$rr[con_codigo] == 67? 'selected' : '' ?> > CREA</option>
                        <option value="72" <?=$rr[con_codigo] == 72? 'selected' : '' ?> > CRMV</option>
                        <option value="78" <?=$rr[con_codigo] == 78? 'selected' : '' ?> > CRQ</option>
                        <option value="81" <?=$rr[con_codigo] == 81? 'selected' : '' ?> > MIN</option>
                    </select>
                </div>
				<?
				echo"<br><br><div style='float:left;width:98px;'>&nbsp;</div><div style='float:left;'>";		
				echo $common->commonButton("voltar",$PHP_SELF,"voltar.png");
				echo"</div>";
				echo"<div style='float:left;'>";
				echo $common->commonButton("Salvar","","report.png","onClick=\"return validaCampos();\"");
				echo"</div><br><br>";
				
				echo $form->closeForm();    
	}
	if($acao == "salvar"){
		$recebeConCodigo = $con_codigo;
		if (empty($recebeConCodigo)) {
			$recebeConCodigo = 71;
		}
		$sql = "
				INSERT INTO medico ( med_nome, med_crm, uf_codigo_crm, prestador_servico, con_codigo ) 
					VALUES ( '$med_nome','$med_cnpj','1','M', $recebeConCodigo)";
 			$query = pg_query($sql) or die(pg_last_error());
			echo $common->modalMsg("OK","Prestador Salvo Com Sucesso!",'medico_externo.php');	
	}
	if($acao == "edita"){
		$sql = "UPDATE medico SET
					med_nome='$med_nome',
					med_crm='$med_cnpj',
					prestador_servico='M',
					con_codigo= $con_codigo
				WHERE med_codigo = $med_codigo";
		$query = pg_query($sql);
		echo $common->modalMsg("OK","Prestador Salva Com Sucesso!",'medico_externo.php');	
	}
	if($acao == "deletar") {
		$getQuery = pg_query("select * from medico where med_codigo = $med_codigo");
		$getName = pg_fetch_array($getQuery);
		echo $common->modalConfirm("Deseja deletar o Prestador $getName[med_nome]","medico_externo.php?acao=del&med_codigo=$med_codigo","medico_externo.php");
	}	
	
	if($acao == "del") {
		$sqlDel = "delete from medico where med_codigo = $med_codigo";
		$qryDel = pg_query($sqlDel);
		echo $common->modalMsg("OK","Prestador Excluida com Sucesso!","medico_externo.php");
	}
echo $common->closeTab();


?>

