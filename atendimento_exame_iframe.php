<?php
/**
@brief  Inclusao principal para montagem do sistema
*/
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
//verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

cabecario();

//$data       = $_POST['data'];
$data       = $_GET['data'];
$med_codigo = intval( $_GET['med_codigo'] );

//print '<pre>';var_dump($_GET['agt_codigo'],$_SERVER['QUERY_STRING']);print '</pre>';
//if( ! $data || ! $med_codigo || ! $agt_codigo )
if( ! $data || ! $med_codigo )
{
	print ("Escolha <strong>todos</strong> os campos antes de prosseguir !");
    exit(1);
}


reglog($id_login,"Acesando Atendimento de Exames (iframe)");
?>

<script language="javascript" type="text/javascript">
var URL = 'atendimento_exame_popup.php?<?="id_login={$id_login}&data={$data}&med_codigo={$med_codigo}&usu_codigo=";?>';

function init_proc( proc  )
{
    window.open( URL + proc, 'proc',  'width=500,height=200,left=150,top=150,scrollbars=yes,resizable=yes' );
}
</script>



<?php

// -------
/*$stmt = "SELECT DISTINCT ael.usu_codigo, usu_nome, COUNT(ael.proc_codigo) AS qtde, usu_mae, usu_prontuario
        FROM agendamento_exame_lista  AS ael
        INNER JOIN agendamento_exame AS ae ON ae.agex_codigo = ael.agex_codigo
        INNER JOIN usuario AS u ON u.usu_codigo = ael.usu_codigo
        WHERE ael.agexl_data = '{$data}' AND ael.med_codigo = {$med_codigo} 
            AND ae.agt_codigo = {$agt_codigo}
        GROUP BY ael.usu_codigo, usu_nome, usu_mae, usu_prontuario
        ORDER BY 2";*/
$stmt = "SELECT DISTINCT ael.usu_codigo, usu_nome, COUNT(ael.proc_codigo) AS qtde, usu_mae, usu_prontuario
        FROM agendamento_exame_lista  AS ael
        INNER JOIN agendamento_exame AS ae ON ae.agex_codigo = ael.agex_codigo
        INNER JOIN usuario AS u ON u.usu_codigo = ael.usu_codigo
        WHERE ael.agexl_data = '{$data}' AND ael.med_codigo = {$med_codigo} 
        GROUP BY ael.usu_codigo, usu_nome, usu_mae, usu_prontuario
        ORDER BY 2";


$qry = db_query($stmt);

if( pg_num_rows($qry) == 0 ) print "<p>Nenhum procedimento/exame agendado !</p>";

print "
<table class='lista'>
<tr style='background:#fff;'>
    <th>Paciente</th>
    <th>No. Prontu&aacute;rio</th>
    <th>Nome M&atilde;e</th>
    <th width='150' style='text-align:center;'>Qtde. Proc. Agendados</th>
</tr>
";

while( $row = pg_fetch_array($qry) )
{
    print "
    <tr>
        <td>
            <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/detalhes.png' onclick='init_proc({$row[usu_codigo]})' style='cursor:pointer;vertical-align:bottom; ' />
            {$row[usu_nome]}</td>
        <td>{$row[usu_prontuario]}</td>
        <td>{$row[usu_mae]}</td>
        <td class='c'>{$row[qtde]}</td>
    </tr>";
}

//------------------------------------------------------------------>
print "
</body>
</html>";
