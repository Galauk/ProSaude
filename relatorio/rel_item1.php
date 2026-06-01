<?
/**
 * @brief	Alteração na query para se adaptar as novas opções do filtro
 * retirada quantidade do LIMIT e adicionado qtd de aih's
 * db_query( $stmt, $LOG = false )
 * alteracao do titulo do relatorio que estava como "Dispensacao por programa de controle por paciente" para "N de AIH utilizado por prestador"
 * refeito
 */
?>
<script language=javascript>

function imprimir() {
       window.print();
}

</script>
<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";

echo "<style type=\"text/css\">
    tr{
    font-size   :12px;
    }
    </style>";

$Tit = "Numeros de AIH utilizados por prestador";
$mes_compt = (int)$_GET[mes_comp];
$ano_compt = (int)$_GET[ano_comp];
$dtIni = $_GET[data_ini];
$dtFin = $_GET[data_fim];
$dados_compet = ( $mes_compt && $ano_compt ? "COMPETENCIA: ".$mes_compt."/".$ano_compt : "" );



include_once $_SESSION[root].$_SESSION[modulo]."cabecalho.php";
list($d_ini,$m_ini,$a_ini) = explode("/",$dtIni);
list($d_fim,$m_fim,$a_fim) = explode("/",$dtFin);


$data_ini = date('Y-m-d',mktime(0,0,0,$m_ini,$d_ini,$a_ini));
$data_fim = date('Y-m-d',mktime(0,0,0,$m_fim,$d_fim,$a_fim));

$btprint = 0;

$med_codigo = (int)$_GET["prestador"];



$select = "select sa.aih_numero_aih, sa.med_codigo_solicitante,sm.med_nome as med_nome from aih as sa
                left join medico as sm on sm.med_codigo = sa.med_codigo_solicitante";
$where1 = " where sa.med_codigo_solicitante =";
$where2 = " where sa.aih_mes_compet =";
$and1   = " and sa.aih_mes_compet =";
$and2   = " and sa.aih_ano_compet =";
$and3   = " and sa.med_codigo_solicitante =";
$and4   = " and sa.aih_ativo = 'S'";
$orderby= " order by sa.med_codigo_solicitante, sa.aih_numero_aih";
$periodo1= " where aih_dataini between '$data_ini' and '$data_fim'";
$periodo2= " and aih_dataini between '$data_ini' and '$data_fim'";
$duplic = " and sa.aih_numero_aih not in ( select aih_numero_aih 
                                                from aih
                                                where aih_ativo = 'S'
                                                group by aih_numero_aih
                                                having count(aih_numero_aih) > 1 
                                                order by aih_numero_aih
		)";
/*
$select.=$where1.$med_codigo.$and1.$mes_compt.$and2.$ano_compt.$orderby; // query com prestador e competencia selecionado
$select.=$where1.$med_codigo.$periodo2.$orderby; // query com prestador e com periodo
$select.=$periodo1.$orderby; // query com periodo apenas
$select.=$where2.$mes_compt.$and2.$ano_compt.$orderby; //query com competencia selecionada apenas



 competencia = cp
 periodo = pe
 prestador = pr
 
 pr cp  pe
 0  0   0 ->nao existe (ou 'cp' ou 'pe' devem estar selecionados)
 0  0   1 $select.=$periodo1.$orderby; // query com periodo apenas
 0  1   0 $select.=$where2.$mes_compt.$and2.$ano_compt.$orderby; //query com competencia selecionada apenas
 0  1   1 -> nao existe (nao podem estar selecionados ao mesmo tempo)
 1  0   0 -> nao existe (idem ao primeiro caso)
 1  0   1 $select.=$where1.$med_codigo.$periodo2.$orderby; // query com prestador e com periodo
 1  1   0 $select.=$where1.$med_codigo.$and1.$mes_compt.$and2.$ano_compt.$orderby; // query com prestador e competencia selecionado
 1  1   1 ->nao existe (idem ao quarto caso)
*/



if (($med_codigo == -1) && ($mes_compt==0)) // todos os prestadores com periodo
{
//    echo "todos os prestadores com periodo";
    $select.=$periodo1.$duplic.$orderby; // query com periodo apenas
}
else if(($med_codigo == -1) && ($mes_compt!=0)) // todos os prestadores com competencia
{
//    echo "todos os prestadores com competencia<br>";
    $select.=$where2.$mes_compt.$and2.$ano_compt.$and4.$duplic.$orderby; //query com competencia selecionada apenas    
}
else if (($med_codigo != -1) && ($mes_compt==0)) // prestador especifico com periodo
{
//    echo "prestador especifico com periodo";
    $select.=$where1.$med_codigo.$periodo2.$and4.$duplic.$orderby; // query com prestador e com periodo
}
else if(($med_codigo != -1) && ($mes_compt!=0)) // presatdor especifico com competencia
{
//    echo "presatdor especifico com competencia";
    $select.=$where1.$med_codigo.$and1.$mes_compt.$and2.$ano_compt.$and4.$duplic.$orderby; // query com prestador e competencia selecionado    
}
echo "<table><tr><td>Prestador</td><td>Inicio</td><td>Fim</td><td>Qtde</td></tr>";
        
$cont=0;

$exec               = db_query($select,false);
$t                  = pg_fetch_array($exec);
$temp               = $t[aih_numero_aih];
$guarda_inicio      = $temp;
$guarda_fim         = $temp;
$medSolic           = $t[med_nome];
$medCod             = $t[med_codigo_solicitante];
$qtde               = 0;

while ($reg = pg_fetch_array($exec))
{
    $cont++;
    $dif = $reg[aih_numero_aih] - $temp;
    //if (($dif == 1) && ($medCod == $reg[med_codigo_solicitante])) // se for sequencia do mesmo prestador, armazena o numero final da sequencia
    if (($medCod == $reg[med_codigo_solicitante])) // se for sequencia do mesmo prestador, armazena o numero final da sequencia
    {
        $guarda_fim= $reg[aih_numero_aih];
        $qtde++;
    }
    else // senao, imprime o inicio e o final e a quantidade podendo o inicio e o fim serem numeros iguais.
    {
        $qtde+=1;
        $total+=$qtde;        
        echo "<tr>
                <td width='60%'>$medSolic</td><td width='25%'>$guarda_inicio</td><td width='25%'>$guarda_fim</td><td width='10%'>$qtde</td>
            </tr>";
        $qtde=0;
        $guarda_inicio = $reg[aih_numero_aih];
        $guarda_fim= $reg[aih_numero_aih];                
    }
    $medSolic   = $reg[med_nome];
    $medCod     = $reg[med_codigo_solicitante];
    $temp       = $reg[aih_numero_aih];
}

    $qtde+=1;    
    $total+=$qtde;            
        echo "<tr>
                <td width='60%'>$medSolic</td><td width='25%'>$guarda_inicio</td><td width='25%'>$guarda_fim</td><td width='10%'>$qtde</td>
            </tr>";

echo "<tr><td colspan='2'></td><td><b>Total geral = </b></td><td><b>".$total."</b></td></tr>";

echo "</table>";


























/* o sql abaixo seleciona a faixa de numeros AIH utilizada por prestador - item 1 da lista pedida*/

/*
if (isset($med_codigo) and ($med_codigo==-1)) {
    $sql_statement = "SELECT COUNT(b.aih_numero_aih) AS total, a.med_nome, MIN(b.aih_numero_aih) AS x,
		    MAX(b.aih_numero_aih) AS y 
		    FROM medico a, aih b 
		    WHERE b.med_codigo_solicitante = a.med_codigo 
		    ".( !empty($mes_compt) ? "AND b.aih_mes_compet = $mes_compt AND b.aih_ano_compet = $ano_compt" : "" )."
                    ".( !empty($dtIni) ? "AND aih_dataini BETWEEN '$a_ini$m_ini$d_ini' AND '$a_fim$m_fim$d_fim'" : "" )."
		    GROUP BY a.med_nome";
    $sql = db_query($sql_statement, $LOG = false);
    include "cabeca_relatorio.php";
    echo "<table width='100%' border=0 style=\"font-size:12px;font-family:Tahoma,Arial;\">
                <tr style=\"font-weight:bold;\"><td width='50%'>Prestador</td><td width='25%'>Inicio</td><td width='25%'>Final</td><td width='25%'>Qtd. AIH</td></tr>
            </table>
        ";
        
    echo "<table width='100%' border=0 style=\"font-size:12px;font-family:Tahoma,Arial;\">";		
        $total = 0;
        while ($reg=pg_fetch_array($sql)){
            echo "<tr>
                    <td width='50%'>$reg[med_nome]</td>
                    <td width='25%'>$reg[x]</td>
                    <td width='25%'>$reg[y]</td>
		    <td width='25%'>$reg[0]</td>
                </tr>";
            $total += $reg[0];
        }
    echo "<tr>
            <td><b>Total: ".$total."</b></td>
        </tr>
    </table>";
    pg_close($db);
} else {
    $sql_statement = "SELECT COUNT(b.aih_numero_aih) AS total, a.med_nome,
		    MIN(b.aih_numero_aih) AS x, MAX(b.aih_numero_aih) AS y 
		    FROM medico a, aih b 
		    WHERE b.med_codigo_solicitante = a.med_codigo
		    AND b.med_codigo_solicitante = $med_codigo
		    ".( !empty($mes_compt) ? "AND b.aih_mes_compet = $mes_compt AND b.aih_ano_compet = $ano_compt" : "" )."
                    ".( !empty($dtIni) ? "AND aih_dataini BETWEEN '$a_ini$m_ini$d_ini' AND '$a_fim$m_fim$d_fim'" : "" )."
		    GROUP BY a.med_nome";
    $sql = db_query($sql_statement, $LOG = false);
    include "cabeca_relatorio.php";
    echo "<table width='100%' border=0 style=\"font-size:12px;font-family:Tahoma,Arial;\">
                <tr style=\"font-weight:bold;\"><td width='50%'>Prestador</td><td width='25%'>Inicio</td><td width='25%'>Final</td><td width='25%'>Qtd. AIH</td></tr>
            </table>
            ";
        
    echo "<table width='100%' border=0 style=\"font-size:12px;font-family:Tahoma,Arial;\">";
    $total = 0;
    while ($reg = pg_fetch_array($sql)){
        echo "<tr>
                <td width='50%'>$reg[med_nome]</td>
                <td width='25%'>$reg[x]</td>
                <td width='25%'>$reg[y]</td>
		<td width='25%'>$reg[0]</td>
            </tr>";
            $total += $reg[0];
    }
    echo "<tr>
            <td>Total: ".$total."</td>
        </tr>
    </table>";
    pg_close($db);
}
                                                                                                 */	
?>
