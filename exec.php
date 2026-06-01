<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

    //Header para evitar cahe
    header("Content-type: text/html; charset=iso-8859-1");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    echo "<select  name=\"cidade\">";
    //$result = pg_query("SELECT * FROM cidades WHERE uf='$_GET[ID]' ORDER BY 'nome'");
    $sql = pg_query("select medico_especialidade.esp_codigo,esp_nome from medico_especialidade, 
                     especialidade where medico_especialidade.esp_codigo=especialidade.esp_codigo 
                     and medico_especialidade.med_codigo='{$_GET['med_codigo']}'");
         echo "<option value=\"\">-------</option>";

         while($row = pg_fetch_array($sql)){
            
            echo "<option value=\"$row[0]\">$row[1]</option>";
    }

    echo "</select>";

    ?>
