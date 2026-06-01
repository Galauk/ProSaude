<?php

session_start();

include_once $_SESSION[root] . $_SESSION[modulo] . "authlib.inc.php";
verauth($id_login);
include_once $_SESSION[root] . $_SESSION[comum] . "library/php/funcoes.inc.php";
cabecario();
echo '<link href="estilo.css" rel="stylesheet" type="text/css" />';
echo "\n<script type='text/javascript' src='$_SESSION[linkroot]$_SESSION[comum]library/js/jquery-1.5.2.min.js'></script>\n";
?>

<script type="text/javascript">
    $(function () {
        $("#submit").click(function () {
            $("#loading").show();
        });


    });
</script>

<?php

echo "
    <form name='upload' action='" . $_SESSION[linkroot] . $_SESSION[modulo] . "dataSusUpdate/update2/upload.php?id_login=$id_login' method='post' enctype='multipart/form-data'>
        <fieldset>
            <fieldset>
                <legend>Importa&ccedil;&atilde;o do SIGTAP</legend>
                <input type='file' name='arquivo' class='boxTexto' size='60'>
            </fieldset>
            <input type=image src='" . $_SESSION[linkroot] . $_SESSION[comum] . "imgs/adicionar_on.jpg' id='submit'>
        </fieldset>
    </form>
    <div id=\"loading\">
        <div style=\"margin: 200px auto; width: 220px; text-align:center>\"><img src=\"" . $_SESSION['linkroot'] . $_SESSION['comum'] . "imgs/load.gif\" alt=\"Carregando...\" /></div>
    </div>  
    ";
?>

