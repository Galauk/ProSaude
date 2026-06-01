<link href="estilo.css" rel="stylesheet" type="text/css" />
<?php
session_start();

include_once $_SESSION[root] . $_SESSION[modulo] . "authlib.inc.php";
verauth($id_login);
include_once $_SESSION[root] . $_SESSION[comum] . "library/php/funcoes.inc.php";
cabecario();
echo "
<form name='upload' action='upload.php' method='post' enctype='multipart/form-data'>
    <fieldset>
        <fieldset>
            <legend>Importa&ccedil;&atilde;o do SIGTAP</legend>
            <input type='file' name='arquivo' class='boxTexto' size='60'>
        </fieldset>
        <input type=image src=" . $_SESSION[linkroot] . $_SESSION[comum] . "imgs/adicionar_on.jpg>
    </fieldset>
</form>";
?>

