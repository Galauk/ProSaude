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
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
?>
<meta name="Dilee C. pacheco - dilee@elotech.com.br" content="" />
</head>

<body>
<?php 
	$form = new classForm();
	$common = new commonClass();
	echo $common->incJquery('../');
	
	echo $common->incJquery('600');

	echo $common->menuTab(array('Dados gerais','Dados do acidente 01','Dados do acidente 02'));

	echo $common->bodyTab('1');
		include 'acidenteTrabalho01.php';
	echo $common->closeTab();

	echo $common->bodyTab('2');
		include 'acidenteTrabalho02.php';
	echo $common->closeTab();
	
	echo $common->bodyTab('3');
		include 'acidenteTrabalho03.php';
	echo $common->closeTab();
	
?>

</BODY>
</HTML>
