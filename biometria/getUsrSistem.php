<?php 

include("../global.php");

if($_GET[id]=="0") {
	echo '0';
} else {
   $sq = pg_query("select *from usuarios where idbio = '$_GET[id]'") or die(pg_last_error());
    $rr = pg_fetch_array($sq);   
	echo $rr[usr_login]."|".$rr[usr_senha]."|".bio."|".$rr[usr_tipo_medico]."|".$rr[usr_nome]."|".$rr[usr_codigo];
}



?>