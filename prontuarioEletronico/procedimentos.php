<?php

#require_once '../global.php';

/*
session_start();
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php"; */
echo "\n<script type='text/javascript' src='$_SESSION[linkroot]$_SESSION[comum]library/js/jquery-1.5.2.min.js'></script>\n";
?>
<script type="text/javascript">

$(function(){

	$("#procedimento").change(function(){
		$("#cid")
		.attr("disabled","disabled")
		.html("<option value=\"0\">Carregando...</option>");
		
		$("#recebe").load("selectCidPorProcedimento.ajax.php?procedimento=" + $(this).val());
	});

	$(".deletar").click(function(e){
		e.preventDefault();

		var url = $(this).attr("href");

		$("#msg").remove();
		$("#sys").append("<div id=\"msg\" title=\"Confirme\">Deseja realmente excluir este item?</div>");
		
		$('#msg').dialog({
			resizable: false,
			height:140,
			modal: true,
			buttons: {
				'Confirmar': function() {
					$.ajax({
						url: url,
						async:false,
						success: function(){
							window.location.href = window.location.href;
						}
					}); 
				},
				'Cancelar': function() {
					$( this ).dialog( 'close' );
				}
			}
		});

		return false;
	});
	
});
</script>
<div id="sys"><!-- usado pelo dialog(); --></div>
<?php

// buscar especialidade do médico
$query1 = pg_query("SELECT esp_codigo FROM logon WHERE id_login=$id_login;");
$esp_codigo = pg_result($query1, 0);


if(empty($esp_codigo))
	die("<h1>Especialdiade do médico/enfermeiro não informada</h1>");

$sqlProcedimentos = "SELECT p.proc_codigo, p.proc_nome
						   FROM procedimento AS p
						   JOIN rl_procedimento_ocupacao AS rlpo
						   	 ON p.proc_codigo_sus=rlpo.co_procedimento
						   JOIN especialidade AS esp
						     ON rlpo.co_ocupacao=esp.cod_cbo
						    AND esp.esp_codigo=$esp_codigo
						  ORDER BY p.proc_nome";

$common = new commonClass();
$form = new classForm();
$table = new tableClass();
echo $common->incJquery();
echo $common->menuTab(array("Procedimentos"));
echo $common->bodyTab('1');
echo $form->openForm("prontuario.php?acao=salva&pagina=15&id_login=$id_login&age_codigo=$age_codigo&ate_codigo=$ate_codigo&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_data&co_sub_grupo=$co_sub_grupo","POST");
echo $table->openTable(null,'200');
echo $table->criaLinha(array($form->inputSelect('procedimento','','Procedimento',"$sqlProcedimentos","Onchange='PegaCodigoGrupo(this.value);'",null,null,'style=width:250px')));
echo $table->criaLinha(array("<div id=\"recebe\">".$form->inputSelect('cid','','<abbr title="Classificação Internacional de Doenças">CID</abbr>',NULL,null,null,null,'style=width:250px')."</div>"));
//echo $table->criaLinha(array($form->inputSelect('subGProcedimento','','Procedimentos',"$sql2",null,null,null,'style=width:250px')));
echo $table->criaLinha(array("<input type='image' name='submit' id='submit' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/salvar_on.jpg' style='margin-left:333px;' onClick='return validaf();'>"));
echo $table->closeTable();
echo $form->closeForm();

echo $table->openTable('lista');
echo $table->criaLinha(array('Procedimentos Realizados','CID',''),null,null,'S');
$sql = "SELECT pat.pat_codigo,
			   p.proc_nome,
			   c.cd10_descricao AS cid
		  FROM procedimento_atendimento AS pat
		  JOIN procedimento AS p
		    ON p.proc_codigo=pat.proc_codigo
	 LEFT JOIN cid10 AS c
		  	ON c.cd10_codigo=pat.cd10_codigo
		 WHERE pat.ate_codigo=$ate_codigo";

		  /*
			  join tb_sub_grupo tsg 
			    on tsg.co_sub_grupo = pm.co_sub_grupo 
			   and pm.co_grupo = tsg.co_grupo
				 where usu_codigo = $usu_codigo
				   and promed_data = '$age_data'";*/

$qryConsulta = pg_query($sql) or die("<pre>$sql\n".pg_last_error());
while($linha = pg_fetch_array($qryConsulta)){
	echo $table->criaLinha(array($linha['proc_nome'],is_null($linha['cid'])?"<em>Não especificado</em>":$linha['cid'], "<a class=\"deletar\" href=\"deletarProcMed.ajax.php?pat_codigo={$linha['pat_codigo']}\"><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/btndel.png' border='0'></a>"));
}
echo $table->closeTable();

if($acao == 'salva'){
	
	$procedimento = $_POST['procedimento'];
	$cid		  = $_POST['cid'] == 0 ? "NULL" : $_POST['cid'];
	$ate_codigo   = $_GET['ate_codigo'];
	
	$sql = "INSERT INTO procedimento_atendimento (
						ate_codigo,
						proc_codigo,
						cd10_codigo)
				 VALUES ('$ate_codigo',
				 		'$procedimento',
				 		$cid);";
				 		
	$stmt = pg_query($sql) or die("<pre>$sql\n".pg_last_error());
	
	if($stmt){
		echo $common->modalMsg("OK","Procedimento Salvo com Sucesso!","prontuario.php?pagina=15&id_login=$id_login&age_codigo=$age_codigo&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_data");
	}else{
		echo $common->modalMsg("ERRO","Erro ao salvar procedimento!","prontuario.php?pagina=15&id_login=$id_login&age_codigo=$age_codigo&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_data");
	}
}
echo $common->closeTab();
