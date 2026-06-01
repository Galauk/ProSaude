<script language=javascript>

function imprimir() {
       window.print();
}

</script>

<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

$Tit="RELATORIO DA AIH PARA VINCULACAO DOS NUMEROS DA AIH";
$dtFin = date("d/m/Y");
include "cabecalho.php";

if($numeros_livres == 'livre' && $numeros_usados == 'usado')
{

    $stmt   = 'SELECT aih_numero_aih FROM aih';
    $res    = pg_query($stmt);
    
    $stmt_livre = "SELECT aan_numero_resto FROM aih_apac_numeros_resto WHERE aan_tipo = 'AIH'";
    $res_livre  = pg_query($stmt_livre);

}
elseif($numeros_livres == 'livre' && $numeros_usados == 'aaa')
{
    $stmt_livre = "SELECT aan_numero_resto FROM aih_apac_numeros_resto WHERE aan_tipo = 'AIH'";
    $res_livre  = pg_query($stmt_livre);
}
elseif($numeros_livres == 'bbb' && $numeros_usados == 'usado')
{
    $stmt   = 'SELECT aih_numero_aih FROM aih';
    $res    = pg_query($stmt);
}


echo "<table style=\"font-size:12px;font-family:courier,vardana,arial;\" width=100% align=center cellspacing=0 cellpadding=0 border=0 topmargin=0 leftmargin=0>\n";
if( pg_num_rows($res) > 0 )
{
    echo "<tr>";
    echo "<th align='left'>N&Uacute;MEROS USADOS</th>";
    echo "</tr>";
    while( $row = pg_fetch_array($res) )
    {
            echo "<tr>";
            echo "<td align=left>".$row[0]."</td>";
            echo "</tr>";
    }
    echo "<tr>";
    echo "<td><br /><b>Total de N&uacute;meros usados: ".pg_num_rows($res)."</b></td>";
    echo "</tr>";
}
if( pg_num_rows($res_livre) > 0 )
{
    if( pg_num_rows($res) > 0 )
    {
        echo "<tr><td><hr></td></tr>";
    }
    echo "<tr>";
    echo "<th align='left'>N&Uacute;MEROS LIVRES</th>";
    echo "</tr>";
    while( $row_livre = pg_fetch_array($res_livre) )
    {
            echo "<tr>";
            echo "<td align=left>".$row_livre[0]."</td>";
            echo "</tr>";
    }
    echo "<tr>";
    echo "<td><br /><b>Total de N&uacute;meros dispon&iacute;veis: ".pg_num_rows($res_livre)."</b></td>";
    echo "</tr>";
}
echo "</table>";
?>