<?php

/**
 *b rief  Inclusao principal para montagem do sistema
*/
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
//verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

cabecario();

$data       = $_GET['data'];
$agt_codigo = intval( $_GET['agt_codigo'] );
$uni_codigo = intval( $_GET['uni_codigo'] );
$med_codigo = intval( $_GET['med_codigo'] );

?>

<script language="javascript" type="text/javascript">
function teste()
{
    var C = document.getElementsByTagName('input');
    for( var i = 0; i < C.length; i ++ )
    {
        if( C[ i ].type != 'checkbox' ) continue;
        if( C[i].checked ) return true;
    }
    alert( 'Selecione um Procedimento antes de continuar !' );
    return false;

}

function t()
{
    var C = document.getElementsByTagName('input');
    var L = document.getElementById('opcoes');
    var marca = ( L.innerHTML == 'todos' );
    L.innerHTML = ( L.innerHTML == 'todos' ? 'nenhum' : 'todos' );

    for( var i = 0; i < C.length; i ++ )
    {
        if( C[ i ].type != 'checkbox' ) continue;
        C[i].checked = marca;
    }
}


</script>

<?php

reglog($id_login,"Acesando Atendimento de Exames (popup)");

// EXECUTANDO O FORM
if( ! empty($_POST['acao']) && count($_POST['agexl']) > 0 )
{
    foreach( $_POST['agexl'] as $k => $val )
    {
        switch( strtoupper($acao) )
        {
            case 'ATENDER':     $status_sql = 'D'; break;
            case 'FALTOU':      $status_sql = 'F'; break;
            case 'TRANSFERIR':  $status_sql = 'T'; break;
            default:            $status_sql = 'A'; break;
        }

        $stmt = "UPDATE agendamento_exame_lista SET agexl_status='{$status_sql}', 
            agexl_status_time = NOW() WHERE agexl_codigo = {$val}";
        db_query($stmt);
    }
    db_query('COMMIT');
    print "<p class='aviso ok'>Atualizado !</p>";
}

// EXIBINDO OS PROCEDIMENTOS
/*$stmt = "SELECT ael.agexl_codigo, p.proc_nome, agexl_status_atend, 
        COALESCE(to_char(agexl_status_time,'dd/mm/yy hh24:mi'), '--' ) as data
    FROM agendamento_exame_lista  AS ael
    INNER JOIN agendamento_exame AS ae ON ae.agex_codigo = ael.agex_codigo
    INNER JOIN procedimento AS p ON p.proc_codigo = ael.proc_codigo
    WHERE ael.agexl_data = '{$data}' AND ael.med_codigo = {$med_codigo} AND ae.agt_codigo = {$agt_codigo} 
        AND ae.usu_codigo = {$usu_codigo}
    ORDER BY 2";*/

$stmt = "SELECT ael.agexl_codigo, p.proc_nome, agexl_status, 
        COALESCE(to_char(agexl_status_time,'dd/mm/yy hh24:mi'), '--' ) as data
    FROM agendamento_exame_lista  AS ael
    INNER JOIN agendamento_exame AS ae ON ae.agex_codigo = ael.agex_codigo
    INNER JOIN procedimento AS p ON p.proc_codigo = ael.proc_codigo
    WHERE ael.agexl_data = '{$data}' AND ael.med_codigo = {$med_codigo} AND ae.usu_codigo = {$usu_codigo}
    ORDER BY 2    ";



$qry = db_query($stmt);

if( pg_num_rows($qry) == 0 ) print "<p>Nenhum procedimento/exame agendado !</p>";


print "
<form aciton='$PHP_SELF?$QUERY_STRING' method='post' onsubmit='return teste()'>

<fieldset>
<legend> Op&ccedil;&otilde;es </legend>

<!--<input type='submit' name='acao' value='Atender' />
<input type='submit' name='acao' value='Faltou' />
<input type='submit' name='acao' value='Transferir' />-->

<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/atender_on.jpg' name='acao' value='Atender' />
<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/faltou_on.jpg' name='acao' value='Faltou' />
<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/transferir_on.jpg' name='acao' value='Transferir' />

</fieldset>

<table class='lista'> 
<tr style='background:#fff;'> 
    <th width='55' style='text-align:center;'>
        <a href='javascript:t()' style='font-size:0.85em;' id='opcoes'>todos</a>
    </th> 
    <th>Procedimento</th> 
    <th width='80' style='text-align:center;'>Status</th>
    <th width='120' style='text-align:center;'>Atualiza&ccedil;&atilde;o</th>
</tr> 
"; 
while( $row = pg_fetch_array($qry) ) 
{ 
    switch( $row['agexl_status'] )
    {
        case 'D': $status = 'Atendido'; break;
        case 'F': $status = 'Faltou';break;
        case 'T': $status = 'Transferido';break;
        default: $status = 'Agendado'; break;
    }

    
    print " 
    <tr> 
        <td class='c'><input type='checkbox' name='agexl[]' value='{$row[0]}' /></td>
        <td>{$row[proc_nome]}</td>
        <td class='c'>{$status}</td>
        <td class='c'>{$row[data]}</td>
    </tr>"; 
} 

print
"</table>
</form>
</body>
</html>
";

?>
