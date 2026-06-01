<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE> New Document </TITLE>
<link rel="stylesheet" type="text/css" href="../css/stylePrincipal.css"> 
</HEAD>
 <?php 
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
 ?>
 <meta name="Dilee C. pacheco - dilee@elotech.com.br" content="" />
 <link href="../css/estiloForm.css" rel="stylesheet" type="text/css" />
 <link href="../css/estiloCommon.css" rel="stylesheet" type="text/css" />
 </head>

 <?php 
   		$form = new classForm();
		$common = new commonClass();
		echo $common->incJquery('../');
	
echo $common->incJquery('600');

echo $common->menuTab(array('Dados Pessoais','Comunicante','Exames','Dados Diagn&oacute;stico','Acompanhamento'));

echo $common->bodyTab('1');
 include 'dadospessoais.php';
echo $common->closeTab();

echo $common->bodyTab('2');
 include 'comunicante.php';
echo $common->closeTab();

echo $common->bodyTab('3');
 include 'exames.php';
echo $common->closeTab();

echo $common->bodyTab('4');
 include 'diagnostico.php';
echo $common->closeTab();

echo $common->bodyTab('5');
 include 'acompanhamento.php';
echo $common->closeTab();

?>

</BODY>
</HTML>
