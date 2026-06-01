<?php

	require_once '../global.php';
	require_once COMUM.'/library/php/funcoes.inc.php';
	require_once COMUM.'/classController/ControllerDispensarMedicamento.php';

	if(isset($_POST['produtos'])){
		
		$id_login = $_SESSION['id_login'];
		$usu_codigo = $_POST['usu_codigo'];
		
		list($medico,$interno) = explode("|",$_POST['medico']);
		$setor = getSetorByLogon();		
		$unidade = getUnidadeByLogon();		

		$transaction = new CommandExecute();
		$controller = new DispensarMedicamento($usu_codigo, $setor, $unidade, $id_login, $medico, $interno);
		
		foreach($_POST['produtos'] as $pro){
			$controller->addItensMovimento($pro);			
		}
		
		echo (int) $transaction->execute($controller);
		exit;
	
	} else 
		die("Produtos não informados!");