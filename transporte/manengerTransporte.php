<?php 
/*error_reporting(E_ALL & ~E_NOTICE ); // & ~E_NOTICE 
ini_set("display_errors",1);
ini_set("ignore_repeated_errors",0);
*/
include("../global.php");

?><link rel="stylesheet" href="/WebSocialSaude/lib/themes/base/jquery.ui.all.css">
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css">
<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.16.custom.min.js"></script>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>

</HEAD>

 </head>

 <body bgcolor="#E8F4F">
 <?php 
   		$form = new classForm();
		$common = new commonClass();
		$table = new tableClass();
		echo $common->incJquery('../');

echo $common->menuTab(array('Viagens','Veiculos','Rotas','Despesas'));

echo $common->bodyTab('1');
echo "a";
 //require_once 'veiculo.php';
echo $common->closeTab();

echo $common->bodyTab('2');
 require_once 'veiculo.php';
echo $common->closeTab();

echo $common->bodyTab('3');
 require_once 'viagem.php';
echo $common->closeTab();

echo $common->bodyTab('4');
 echo "c";
 #require_once 'veiculo.php';
echo $common->closeTab();


?>

</BODY>
</HTML>
