<?php
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
	$sus = $_SESSION['susUpdate'];
	
	function format($n){
		return number_format($n,0,",",".");
	}
	
	if( $sus['cod'] === 1){
		$mensagem = "O arquivo enviado e exatamente igual ao ultimo arquivo enviado.<br />Nenhuma alteracao foi feita.";
		
	} elseif($sus['cod'] == 2){
		
		$mensagem = sprintf("Atualizacaoo concluida!<br />%d(+%d) registro atualizados, %d registros novos e %d falha(s).", 
							format($sus['countQuery']["u"]),
							format($sus['countQuery']["u2"]),
							format($sus['countQuery']["i"]),
							format($sus['countQuery']["f"]));
							
		$inicio = $sus['dhInicio'];
		$fim    = $sus['dhFim'];
		$total  = $fim-$inicio;
		
		$mensagem .= sprintf("<br /><br />A atualizacaoo demorou %d segundos.",$total);

		// salvar log_sus_update
		$md5 = $sus['md5'];
		$dhInicio = date("Y-m-d H:i:s", $sus['dhInicio']);
		$dhFim    = date("Y-m-d H:i:s", $sus['dhFim']);
		$sqlLog = "INSERT INTO log_sus_update (lsu_dh_inicio, lsu_dh_fim, lsu_md5, usr_codigo) VALUES ('$dhInicio','$dhFim', '$md5', $id_login)";
		$logQuery = pg_query($sqlLog) or die($sqlLog."<br />".pg_last_error());
		
	}
	
	unset($_SESSION['susUpdate']);
	
	echo $mensagem;
	