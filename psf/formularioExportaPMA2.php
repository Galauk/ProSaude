<html>
<head>
<script type="text/javascript" src="../ajax_motor.js"></script>
<script type="text/javascript">
function CheckCall() {   
	
	mes = document.getElementById('mes').value;
	ano = document.getElementById('ano').value;
	cnes = document.getElementById('cnes').value;
	var endereco = '../exportacao/exportaBPA.php?mes='+mes+'&ano='+ano+'&cnes='+cnes; 
	ajax_tudo(endereco,criarLink);    
	return false;
}

function criarLink(txt)
{	
	if(txt == 1)
	{
		
		//tipoBPA = ((document.getElementById('tipoBPA').value == "C") ? "CON" : "IND"); 
		mes = document.getElementById('mes').value;	
		var endereco = "../lib/baixarArquivo.php?arquivo=../exportacao/arquivos/pma2.txt";
		window.location = endereco;
	}else{
		alert("Houve um erro na geração do arquivo, tente novamente.");
	}  
}
</script>
</head>
<body>
<?php
	session_start();
	include_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	
	$form = new classForm();
	$common = new commonClass();
	
	$table = new tableClass();
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	
	echo $common->incJquery();
	echo $common->menuTab(array("SSA2"));
	
	echo $common->bodyTab('1');
//echo $form->openForm("exportaBPA.php", "GET");
	//echo $form->hiddenForm("tipoBPA", $tipoBPA, "tipoBPA");
	$meses = array(01=>"Janeiro", 02=>"Fevereiro", 03=>"Mar&ccedil;o", 04=>"Abril", 05=>"Maio", 06=>"Junho", 07=>"Julho", 08=>"Agosto", 09=>"Setembro", 10=>"Outubro", 11=>"Novembro", 12=>"Dezembro");
	
	$anos = array();
	$anoIteracao = date('Y');
	for($i = 0; $i < 5; $i++){
		$anos[$anoIteracao] = $anoIteracao;
		$anoIteracao--;
	}
	
	$selecionaCNES = "SELECT med_cnes, 
							 med_nome 
					    FROM medico 
					   WHERE med_cnes is not null 
					     AND med_cnes <> 0
					   ORDER BY med_nome ASC";
	echo $form->inputSelect("cnes", null, "Medico/Enfermeira", $selecionaCNES, null, null, null, "style=width:262px;");
	echo $form->inputSelect("mes", $meses, "M&ecirc;s", null, null, "mes", date('m'), "style=width:262px;");
	echo $form->inputSelect("ano", $anos, "Ano", null, null, "ano", date('Y'), "style=width:262px;");
	//echo $form->inputText("ano", date('Y'), "Ano", 50, 4, "onKeyPress='somenteNumeros();'");
	//echo $form->inputText("tipo", ($tipoBPA == "C"? "Consolidado" : "Individualizado"), "Tipo", 50, null, null, "text", "S");
	echo "<br><br>";
	echo "<a href=# onClick=\"CheckCall();\">submit</a>";
	echo $common->closeTab();
	//echo $form->submitButton("Exportar Dados", null, "onClick=checkCall();");
	//echo $form->closeForm();
?>
</body>
</html>