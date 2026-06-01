<?php 
	session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE> New Document </TITLE>
<link rel="stylesheet" type="text/css" href="../css/stylePrincipal.css"> 
 <link href="<?= $_SESSION[linkroot].$_SESSION[modulo];?>css/estiloForm.css" rel="stylesheet" type="text/css" />
 <link href="<?= $_SESSION[linkroot].$_SESSION[modulo];?>css/estiloCommon.css" rel="stylesheet" type="text/css" />
</HEAD>
<?php
	include_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
 ?>
 <meta name="Dilee C. pacheco - dilee@elotech.com.br" content="" />
 </head>

 <body>
 <?php 
	$form = new classForm();
	$common = new commonClass();
	echo $common->incJquery('../');
	
	echo $common->incJquery('600');

	echo $common->menuTab(array('Vigilância Sanitária'));

	echo $common->bodyTab('1');
	echo "<font size=2>";
		echo "<li type=square style='color: #A0C6E6'><a href=manengerAcidenteTrabalho.php>Acidente de Trabalho</a></li>";
		echo "<li type=square style='color: #A0C6E6'><a href=#>Alvará</a></li>";
		echo "<li type=square style='color: #A0C6E6'><a href=#>Atividade PPI</a></li>";
		echo "<li type=square style='color: #A0C6E6'><a href=#>Auto Termo</a></li>";
		echo "<li type=square style='color: #A0C6E6'><a href=#>Controle de vistoria de Agentes</a></li>";
		echo "<li type=square style='color: #A0C6E6'><a href=#>Controle de Vistorias</a></li>";
		echo "<li type=square style='color: #A0C6E6'><a href=#>Vigilância Educativa</a></li>";
		echo "<li type=square style='color: #A0C6E6'><a href=#>Viliância Inspeção</a></li>";
		echo "<li type=square style='color: #A0C6E6'><a href=#>Pedidos de documentos</a></li>";
		echo "<li type=square style='color: #A0C6E6'><a href=#>Prazo de suspenção</a></li>";
		echo "<li type=square style='color: #A0C6E6'><a href=#>Reclamaçãoes</a></li>";
		echo "<li type=square style='color: #A0C6E6'><a href=#>Roteiro</a></li>";
		echo "<li type=square style='color: #A0C6E6'><a href=#>Saúde do Trabalhador Investigação</a></li>";
		echo "<li type=square style='color: #A0C6E6'><a href=#>Saúde do Trabalhador Notificação</a></li>";
		echo "<li type=square style='color: #A0C6E6'><a href=#>Surto de DVA</a></li>";
		echo "<li type=square style='color: #A0C6E6'><a href=#>Termo de AA</a></li>";	
		echo "</font>";	
		
		
		
		
	
	echo $common->closeTab();
	
?>

</BODY>
</HTML>
