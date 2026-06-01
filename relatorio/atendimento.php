<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>
echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";

echo "<form name=\"frm_rel_atendimento\" method=\"post\" action=\"post\">\n";
echo "<fieldset>";
echo "<legend>Relat&oacute;rio de Atendimento</legend> \n";
echo "<table border='0' width=98% align=center cellspacing='2' cellpadding='1'>\n";
echo    "<tr>\n";
echo        "<td valign='bottom'>Data Incial</td>\n";
echo        "<td><input type='text' class='box'  name='dt_inicial' size='10'  value='' />  </td>\n";
echo    "</tr>\n";

echo    "<tr>\n";
echo        "<td valign='bottom'>Data Final</td>\n";
echo        "<td><input class='box' type='text' name='dt_final' size='10'  value='' /> </td>\n";
echo    "</tr>\n";

echo    "<tr>\n";
echo        "<td valign='bottom'>M&eacute;dico </td>\n";
echo        "<td><select name='id_medico' class=box>\n";
echo             "<option value=''>---SELECIONE UM M&Eacute;DICO---</option>";

echo         "</select>\n";
echo        "</td>\n";
echo    "</tr>\n";
echo    "<tr>\n";
echo        "<td valign='bottom'>Especializa&ccedil;&atilde;o</td>\n";
echo        "<td><select name='id_medico' class=box>\n";
echo             "<option value=''>---SELECIONE UMA ESPECIALIZA&Ccedil;&Atilde;O---</option>";

echo         "</select>\n";
echo        "</td>\n";
echo    "</tr>\n";

echo    "<tr>\n";
echo        "<td valign='bottom'>Unidade</td>\n";
echo        "<td><select name='id_medico' class=box>\n";
echo             "<option value=''>---SELECIONE UMA UNIDADE ---</option>";

echo         "</select>\n";
echo        "</td>\n";
echo    "</tr>\n";
echo   "<tr>\n";
echo  "<td>&nbsp;</td>";
echo        "<td> <input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg' onclick='rel_atendimento()'  name='enviar' value='ENVIAR'> </td>\n";
echo  "</tr>\n";
echo "</table>\n";
echo "</fieldset>\n";
echo "</form>\n";

