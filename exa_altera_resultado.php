<?php 
for($i=0; $i <= (count($vlr_valor)-1); $i++) {
$stmt = "update resultadoexame ( 
	sex_codigo, 
	id_login, 
	res_dataresultado, 
	res_horaresultado, 
	res_observacao, 
	res_conclusoes, 
	vlr_valor, 
	cad_exame, 
	proc_codigo
	 ) VALUES ( 
	".($vlr_codigo[$i] ? "'$vlr_codigo[$i]'" : "0" ).",
	".intval($id_login).", 
	".CURRENT_DATE.", 
	".CURRENT_TIME.", 
	'".trim(strtoupper($res_observacao[$i]))."', 
	'".trim(strtoupper($res_conclusoes[$i]))."', 
	'$vlr_valor[$i]', 
	".intval($cad_exame).", 
	".$proc_codigo." )";
echo $stmt;

# $sql = pg_query($stmt);
 }
        echo "<SCRIPT LANGUAGE=\'JavaScript\'>
                  setTimeout(\'location='exa_digitacaoresultado.php?proc_codigo=$proc_codigo&cad_exame=$cad_exame&id_login=$id_login'\', 0);
              </SCRIPT>";
?>