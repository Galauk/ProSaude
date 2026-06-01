<?
	$form = new classForm();
	$common = new commonClass();
	$tabela = new tableClass();
	echo $common->incJquery();
	
	echo $common->menuTab(array('Atendidos'));
	  echo $common->bodyTab('1');
	  
	  echo $common->closeTab();
?>