<script>
function valida(esp){
	med_codigo = document.getElementById('med_codigo').value;
	usu_codigo = document.getElementById('usu_codigo').value;
	uni_codigo = document.getElementById('uni_codigo').value;
	age_codigo = document.getElementById('age_codigo').value;
	
	window.open("../print_encaminhamento.php?esp_codigo="+esp+"&med_codigo="+med_codigo+"&usu_codigo="+usu_codigo+"&uni_codigo="+uni_codigo+"&age_codigo="+age_codigo,null,"height=600,width=550,status=yes,toolbar=no,menubar=no,location=no"); 
	//alert(document.getElementById('ate_encaminhamento_esp').value);
	//alert('oi');
}
</script>
<?php
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
$common = new commonClass();
$form = new classForm();
echo $common->incJquery();

if($acao ==""){
	$sql = "select *from especialidade order by esp_nome";

//$updade = "UPDATE atendimento SET ate_encaminhamento_esp = $ate_encaminhamento_esp ";	
	$tabela = $form->openForm(NULL,"GET","formulario");
	
	$tabela .= $common->openModal("Encaminhamento", 700, "OK", "prontuario.php?pagina=99&id_login=$id_login&ate_codigo=$ate_codigo&age_codigo=$age_codigo&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_data");
	$tabela .= $form->hiddenForm('ate_encaminhamento_esp', $ate_encaminhamento_esp);
	$tabela .= $form->hiddenForm('med_codigo', $med_codigo); 
	$tabela .= $form->hiddenForm('uni_codigo', $uni_codigo); 
	$tabela .= $form->hiddenForm('age_codigo', $age_codigo); 
	$tabela .= $form->hiddenForm('usu_codigo', $usu_codigo); 
	//,"prontuario.php?pagina=12&acao=update&id_login=$id_login&ate_codigo=$ate_codigo&age_codigo=$age_codigo&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_data"
		$tabela .= $form->inputSelect("ate_encaminhamento_esp", NULL,"Encaminhamento",$sql,"onChange= valida(this.value);",NULL,NULL,"style=width:200px;");
	$tabela .= $common->closeModal();
	$tabela .= $form->closeForm();
	echo $tabela;
	
	//$form->closeForm();
}



?>