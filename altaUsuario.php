<?php
    session_start();
    require_once 'global.php';
	include_once COMUM."/library/php/funcoes.inc.php";
	include_once SAUDE . '/__array.php';
	include "authlib.inc.php";
    $recebeUrl = explode('/',$_SERVER[REQUEST_URI]);
    
    $recebeUsuCodigo = intval($recebeUrl[4]);
    $recebeAgeCodigo = intval($recebeUrl[6]);

?>

<script>

    function closeWin() {
        console.log(this)
        window.close();
    }

</script>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <form action="#" method="post" style = 'padding: 13px;'>
        
        <div style = 'padding: 4px;'>

            <label for="dataAlta">Data da alta:</label>
            <input id = "dataAlta" name = "dataAlta"   type="date" value="">

        </div>

        <div style = 'padding: 4px;'>

            <label for="horaAlta">Hora da Alta:</label>
            <input id = "horaAlta" name = "horaAlta"   type="time" value="">

        </div>

        <div style = 'padding: 4px;'>
        
            <button type = "submit" onclick = "closeWin(this)">
                Salvar 
            </button>

        </div>

    </form>
</body>
</html>

<?php

    if(count($_POST) > 0){

        $query = pg_query("UPDATE agendamento SET data_alta_usuario = '$_POST[dataAlta]', hora_alta_usuario = '$_POST[horaAlta]' where age_codigo = $recebeAgeCodigo and usu_codigo = $recebeUsuCodigo") or die(pg_last_error());

    }
    
?>