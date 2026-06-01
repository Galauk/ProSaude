<?php
/**
 * @Arquivos Relacionados: aih_apac.inc.php
 * @Tabelas: aih, aih_apac_numero_resto
 * @Acao: Deleta as AIH's.
*/ 

	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";


	$sql	= "INSERT INTO aih_apac_numeros_resto (aan_numero_resto, aan_tipo) VALUES ('$numero', '$tipo')";
	$qry	= db_query($sql);
	//print $sql;
	//echo "<br><br>";
	$stmt 	= "DELETE FROM aih WHERE aih_codigo=$aih_codigo AND aih_ativo='S' ";
	$query 	= db_query($stmt);
	//print $stmt;
	
	echo "
		<link href='estilo.css' rel='stylesheet' type='text/css'>	
        <div class='aviso ok' align='center'>AIH Apagada com Sucesso !</div>
        <script type='text/javascript'>
            setTimeout(\"document.location.href='aih.php?id_login=$id_login'\",3000);
        </script>
	";


?>