<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
echo "<style type='text/css'>
        tr{
        font-size : 10px;
        } 
    </style>";
$med_codigo = $_GET['codmed'];
$dtIni = $_GET['dtini'];
$dtFin = $_GET['dtfim'];
$and_medico = ($med_codigo != -1 ) ? " and sm.med_codigo = '$med_codigo'":"";
$and_periodo = (strlen($dtIni) > 0) ? " and sa.age_data between '$dtIni' and '$dtFin'" :"";
$select = "select sm.med_nome,se.esp_nome,to_char(sa.age_data, 'dd/mm/yyyy') as age_data ,count(distinct(sa.age_data)) as totdia
            from agendamento sa
            left join medico sm on sm.med_codigo = sa.med_codigo
            left join especialidade se on se.esp_codigo = sa.esp_codigo
            where age_falta_medico is not null $and_medico $and_periodo
            group by sa.age_data,sm.med_nome,se.esp_nome
            order by 1";
            
include_once $_SESSION[root].$_SESSION[modulo]."relatorio/cabecalho.php";

$exec = db_query($select);

$cont   = 1;
$data   = 0;
$ct     = 0;


echo "
    <table width = 100% >
        <tr><td><strong>M&eacute;dico</strong></td><td><strong>Especialidade</td><td><strong>Data</strong></td></tr>";
while ($reg = pg_fetch_array($exec))
{
    $nome_do_medico = ($reg[med_nome] != $nome_do_medico) ? $reg[med_nome]:'';
    $especialidade = ($reg[esp_nome] != $especialidade) ? $reg[esp_nome]:'';
    $ambos = ($reg[med_nome].$reg[esp_nome] != $ambos) ? false : true;
    $data = ($reg[age_data]!=$data) ? true : false;
    if ($ambos)
    {
        $ct++;
        echo "<tr><td>$nome_do_medico</td><td>$especialidade</td><td>$reg[age_data]</td></tr>";
    }else{
        if ($ct > 0){
            echo "<tr><td colspan = 2><strong>Total de faltas do medico :$ct</strong></td></tr>";
            echo "<tr><td colspan = 3><hr></td></tr>";
            $ct = 0;
        }
        echo "<tr><td>$reg[med_nome]</td><td>$reg[esp_nome]</td><td>$reg[age_data]</td></tr>";
        $ct++;
    }
    $nome_do_medico = $reg[med_nome];
    $especialidade = $reg[esp_nome];
    $ambos=$reg[med_nome].$reg[esp_nome];
}
    echo "<tr><td colspan = 2><strong>Total de faltas do medico :$ct</strong></td></tr>";
    echo "<tr><td colspan = 3><hr></td></tr>";
echo "</table>"; 


?>