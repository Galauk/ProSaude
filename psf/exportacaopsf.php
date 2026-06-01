<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Cp1252">
<title>Exporta&ccedil;&atilde;o PSF</title>
</head>
	<body>
    <?php
    	session_start();
		include_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
		include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
		include_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
		
		$form = new classForm();
		$common = new commonClass();
		echo $common->incJquery();
		$table = new tableClass();
		
		echo $common->menuTab(array("SSA2"));
		
		echo $common->bodyTab('1');
			$tipoBPA = "C";
			include "formularioExportaSSA2.php";
		echo $common->closeTab();
		
		echo $common->bodyTab('2');
			$tipoBPA = "I";
			include "formularioExportaBPA.php";
		echo $common->closeTab();
	?>
	</body>
</html>