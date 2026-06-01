<?php 
	session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Cp1252">
<title>Exporta&ccedil;&atilde;o BPA</title>
<script type="text/javascript" src="<?=$_SESSION[linkroot].$_SESSION[comum]?>library/js/ajax_motor.js"></script>
</head>
	<body>
    <?php
    	function montaArray($mes){
			$array = array();
			$todosMeses = array('01'=>"Janeiro", '02'=>"Fevereiro", '03'=>"Mar&ccedil;o", '04'=>"Abril", '05'=>"Maio", '06'=>"Junho", '07'=>"Julho", '08'=>"Agosto", '09'=>"Setembro", '10'=>"Outubro", '11'=>"Novembro", '12'=>"Dezembro");
			for($i = 0; $i <= 3; $i++){
				if (intval($mes) == 0){
					$mes = 12;
				}
				$array[$mes] = $todosMeses[$mes];
				$mes = str_pad(intval($mes)-1, 2, "0", STR_PAD_LEFT);
			}
			return $array;		
		}
    	session_start();
		include_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
		include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
		include_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
		
		$form = new classForm();
		$common = new commonClass();
		echo $common->incJquery();
		$table = new tableClass();
		
		echo $common->menuTab(array("BPA Consolidado", "BPA Individualizado"));
		
		echo $common->bodyTab('1');
			$tipoBPA = "C";
			include "formularioExportaBPA.php";
		echo $common->closeTab();
		
		echo $common->bodyTab('2');
			$tipoBPA = "I";
			include "formularioExportaBPA.php";
		echo $common->closeTab();
	?>
	</body>
</html>