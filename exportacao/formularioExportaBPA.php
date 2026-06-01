<? 
	session_start();
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
?>
<html>
<head>

<script type="text/javascript">
function checkCall() {   
	tipoBPA = document.getElementById('tipoBPA').value;
	mes = document.getElementById('mes').value;
	ano = document.getElementById('ano').value;

	data = new Date();
	mesAtual = data.getMonth()+1;
	anoAtual = data.getFullYear();
	alert(mesAtual);
	if ((mesAtual < mes) && (ano == anoAtual)){
		alert("A data de geração do BPA não pode ser maior que a atual!");
		return false;
	}
	
	cnes = document.getElementById('cnes').value;
	var endereco = '../exportacao/exportaBPA.php?tipoBPA='+tipoBPA+'&mes='+mes+'&ano='+ano+'&cnes='+cnes; 
	ajax_tudo(endereco,criarLink);    
	return false;
}

function criarLink(txt)
{	
	if(txt == 1)
	{
		tipoBPA = ((document.getElementById('tipoBPA').value == "C") ? "CON" : "IND"); 
		mes = document.getElementById('mes').value;
		var ext;
		switch(mes)
		{
			case "1":
				ext = "JAN";
				break;
			case "2":
				ext = "FEV";
				break;
			case "3":
				ext = "MAR";
				break;
			case "4":
				ext = "ABR";
				break;
			case "5":
				ext = "MAI";
				break;
			case "6":
				ext = "JUN";
				break;
			case "7":
				ext = "JUL";
				break;
			case "8":
				ext = "AGO";
				break;
			case "9":
				ext = "SET";
				break;
			case "10":
				ext = "OUT";
				break;
			case "11":
				ext = "NOV";
				break;
			case "12":
				ext = "DEZ";
				break;
			default:
				ext = "ERR";
		}
		var endereco = "../lib/baixarArquivo.php?arquivo=../exportacao/arquivos/PABpa"+tipoBPA+"."+ext;
		window.location = endereco;
	}else{
		alert("Houve um erro na geração do arquivo, tente novamente.");
	}  
}
</script>
</head>
<body>
<?php
	echo $form->hiddenForm("tipoBPA", $tipoBPA, "tipoBPA");
	$mesAtual = date('m');
	$meses = montaArray($mesAtual);
	
	$anos = array();
	$anoIteracao = date('Y');
	$qtdeAnos = 1;
	if($mesAtual <= 3){
		$qtdeAnos = 2;
	}
	for($i = 0; $i < $qtdeAnos; $i++){
		$anos[$anoIteracao] = $anoIteracao;
		$anoIteracao--;
	}
	$selecionaCNES = "SELECT med_cnes, 
							 med_nome 
					    FROM medico 
					   WHERE med_cnes is not null 
					     AND med_cnes <> 0
					   ORDER BY med_nome ASC";
	echo $form->inputSelect("cnes", null, "CNES", $selecionaCNES, null, null, null, "style=width:262px;");
	echo $form->inputSelect("mes", $meses, "M&ecirc;s", null, null, "mes", date('m'), "style=width:262px;");
	echo $form->inputSelect("ano", $anos, "Ano", null, null, "ano", date('Y'), "style=width:262px;");
	echo $form->inputText("tipo", ($tipoBPA == "C"? "Consolidado" : "Individualizado"), "Tipo", 50, null, null, "text", "S");
	echo "<br><br>";
	echo $common->commonButton("Gerar BPA", null, "gerar.png", "onClick=\"return checkCall();\"");
?>
</body>
</html>