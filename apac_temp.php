<?php
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

Cabecario( );
verauth($id_login);

?>
<script type="text/ajvascript">
function buscar()
{
    try
    {
        
    }
    catch( ex )
    {
        alert( ex );
    }
}
</script>
<fieldset>
    <legend>CADASTRO AUXILIAR </legend>
    
    <table>
        <tr>
            <td>Data Validade Inicio</td>
            <td><input type="text" id="dt_ini" class="box"/></td>
        </tr>
        <tr>
            <td>Data Validade Fim</td>
            <td><input type="text" id="dt_fim" class="box"/></td>
        </tr>
        <tr>
            <td>Numeros das APACs</td>
            <td><textarea rows="5" cols="60" class="box"></textarea></td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="button" value="Buscar APACs" onclick="buscar()"/>
            </td>
        </tr>
    </table>
    
</fieldset>

<table class="lista">
    <tr>
        <th width="200">Numero da APAC</th>
        <th>Nome do Paciente</th>
        <th width="200">Confirmar ?</th>
    <tr>
</table>