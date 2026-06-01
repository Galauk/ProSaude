<?php
	session_start();
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);

	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>
echo"
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td>
			<fieldset>
				<legend>Opc&otilde;es</legend>
				 <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
				 	<tr>
						<td width='200'><a href=../cadastro/leito.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/leito.jpg border='0'></a></td>
						<td width='200'><a href=../cadastro/quarto.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/leito.jpg border='0'></a></td>
						<td><a href=../cadastro/leito.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/leito.jpg border='0'></a></td>
					</tr>
			</fieldset>
			</td>
		</tr>";
?>