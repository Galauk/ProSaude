<?php
	session_start();
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	
	$agex_codigo = $_GET[codigo];
	$linhaTabela = $_GET[linha];
	$usu_nome = $_GET[nome];
	$acao = $_GET[acao];
	
	switch ($acao){
		case "listar":
			include_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
			include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
			$table = new tableClass();
			$common = new commonClass();
			$sql = "SELECT p.proc_nome,
						   to_char(ael.agexl_data, 'DD/MM/YYYY') as data,
						   coalesce(agexl_hora, '07:00:00') hora
					  FROM agendamento_exame_lista ael
					  JOIN usuario u
					    ON u.usu_codigo = ael.usu_codigo
					  JOIN procedimento p
					    ON p.proc_codigo = ael.proc_codigo
					 WHERE agex_codigo = $agex_codigo";
			$exec = pg_query($sql);
			echo $common->divisoria("Lista de Exames Agendados para o Paciente: <em><font color=#FFFFFF>$usu_nome</font></em>");
			echo $table->openTable("lista");
				$arrayConteudo = array("Procedimento", "Data", "Hora");
				$arrayTamanho = array(300, 50, 50);
				echo $table->criaLinha($arrayConteudo, $arrayTamanho, null, "S");
				while ($linha = pg_fetch_row($exec)){
					echo $table->criaLinha($linha);
				}
			echo $table->closeTable();
			break;
		case "recepcao":
			$select = "SELECT DISTINCT agexl_status,
							  agexl_data
						 FROM agendamento_exame_lista
						WHERE agex_codigo = $agex_codigo";
			$executa = pg_query($select);
			$linha = pg_fetch_row($executa);
			$status = $linha[0];
			$data = $linha[1];
			if ($data == date('Y-m-d')){
				if ($linha[0] == "A"){
					$status = 'R';
				}else{
					$status = 'A';
				}
			}else{
				echo "NADA|recepcionar";
				exit;
			}
			$updt = "UPDATE agendamento_exame_lista
					    SET agexl_status = '$status'
					  WHERE agex_codigo = $agex_codigo";
			$exec = pg_query($updt);
			$num = pg_affected_rows($exec);
			if ($num > 0){
				echo "$status|$linhaTabela";
			}else{
				echo "NADA|recepcionar";
			}
			break;
		case "cancelar":
			$select = "SELECT DISTINCT agexl_status,
							  agexl_data
						 FROM agendamento_exame_lista
						WHERE agex_codigo = $agex_codigo";
			$executa = pg_query($select);
			$linha = pg_fetch_row($executa);
			$status = $linha[0];
			$data = $linha[1];
			if ($data == date('Y-m-d')){
				if ($linha[0] == "A"){
					$status = 'C';
				}
			}else{
				echo "NADA|cancelar";
				exit;
			}
			$updt = "UPDATE agendamento_exame_lista
					    SET agexl_status = '$status'
					  WHERE agex_codigo = $agex_codigo";
			$exec = pg_query($updt);
			$num = pg_affected_rows($exec);
			if ($num > 0){
				echo "$status|$linhaTabela";
			}else{
				echo "NADA|cancelar";
			}
			break;
	}
?>