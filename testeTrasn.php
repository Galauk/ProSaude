<?php
session_start();

include_once $_SESSION[root].$_SESSION[comum].'class/transactionControl.php';

include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

function insere21(){
	$u = "insert into doenca (cod_doenca,nome_doenca) values (21,'ddddd')";
	$q =  db_query_execute($u);
		
}


class  ExecuteTestTrans implements Command{

	private function insere22(){

		$u = "insert into doenca (cod_doenca,nome_doenca) values (22,'ddddd')";
		$q =  db_query_execute($u);

	}

	public function execute(){
		insere21();

		$this->insere22();
		
		$u = "insert into doenca (cod_doenca,nome_doenca) values (23,'ddddd')";
		$q =  db_query_execute($u);
		$u = "insert into doenca (cod_doenca,nome_doenca) values (20,'ddddd')";
		$q =  db_query_execute($u);

		$comm2 = new $executeTestTransVar();
		
		$comm2->execute();
		
		echo "teste ok";


	}

}

$executeTestTransVar = new ExecuteTestTrans();

$commandExecuteVar = new CommandExecute();

$commandExecuteVar->execute($executeTestTransVar);

?>