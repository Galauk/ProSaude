<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE> New Document </TITLE>
<link rel="stylesheet" type="text/css" href="../css/stylePrincipal.css"> 
 <link href="../css/estiloForm.css" rel="stylesheet" type="text/css" />
 <link href="../css/estiloCommon.css" rel="stylesheet" type="text/css" />
</HEAD>
<?php 
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";	
?>
 <meta name="Dilee C. pacheco - dilee@elotech.com.br" content="" />
 </head>

 <body>
 <?php 
	$form = new classForm();
	$common = new commonClass();
	echo $common->incJquery('../');
	
	echo $common->incJquery('600');

	echo $common->menuTab(array('Dados Obst&eacute;tricos','Gesta&ccedil;&otilde;es','Hist&oacute;rico Pr&eacute;-Natal','Exames',"Puerperal","Caract. BEB&Ecirc;"));

	echo $common->bodyTab('1');
		include 'dadosObs.php';
	echo $common->closeTab();

	echo $common->bodyTab('2');
		include 'dadosGestacoes.php';
	echo $common->closeTab();
	
	echo $common->bodyTab('3');
		include 'historicoPrenatal.php';
	echo $common->closeTab();
	
	echo $common->bodyTab('4');
		include 'historicoExames.php';
	echo $common->closeTab();
	
	echo $common->bodyTab('5');
		include 'puerperal.php';
	echo $common->closeTab();
	
	echo $common->bodyTab('6');
		include 'caracteristicasBebe.php';
	echo $common->closeTab();
?>

</BODY>
</HTML>
