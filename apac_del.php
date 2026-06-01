<?php
/**
 * @Modulo: Autorização de Internação Hospitalar ( AIH )
 * @Arquivos Relacionados: aih_apac.inc.php
 * @Tabelas: apac, aih_apac_numero_resto
 * @Acao: Deleta as APAC's.
*/ 

	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

	$sql	= "INSERT INTO aih_apac_numeros_resto (aan_numero_resto, aan_tipo) VALUES ('$numero', '$tipo')";
	$qry	= db_query($sql);
	//print $sql;
	//echo "<br><br>";
	
	$stm	= "DELETE FROM apac_procedimento WHERE apac_codigo=$apac_codigo";
	$que	= db_query($stm);
	//print $stm;
	//echo "<br><br>";
	
	$stmt 	= "DELETE FROM apac WHERE apac_codigo=$apac_codigo ";
	$query 	= db_query($stmt);
	//print $stmt;
	
	echo "
		<link href='estilo.css' rel='stylesheet' type='text/css'>	
        <div class='aviso ok' align='center'>APAC Apagada com Sucesso !</div>
        <script type='text/javascript'>
           setTimeout(\"document.location.href='apac.php?id_login=$id_login'\",3000);
        </script>
	";


?>