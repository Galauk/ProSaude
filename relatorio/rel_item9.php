<?php

function data_dif( $df, $di )
{
    
    $df_arr = split( '[\/\-]', $df );
    $di_arr = split( '[\/\-]', $di );
    
    $time_f = mktime( 0, 0, 0, $df_arr[1], $df_arr[0], $df_arr[2] );
    $time_i = mktime( 0, 0, 0, $di_arr[1], $di_arr[0], $di_arr[2] );

    $r = (int) ( ( $time_f - $time_i ) / 86400 );

    //echo "<p>diferenca $df - $di = $r dias</p>";
    
    return $r;
}

echo "
<style type=\"text/css\">
tr { font-size:12px; }
.relatorio { width: 100%; }
.relatorio th { text-align:left; border-bottom: 1px solid #000; }

</style>
"
;

	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";

$Tit = "Reinternacoes";

// pegando as vars...
$data_ini   = @ $_GET['data_ini'];
$data_fin   = @ $_GET['data_fin'];
$mes_compet = (int) @ $_GET['mes_compet'];
$ano_compet = (int) @ $_GET['ano_compet'];
$municipio  = (int) @ $_GET['municipio'];

// arrumando o sql das datas, periodos e afins
if( ! empty($data_ini) )
{
    $dados_compet = "PERIODO : ".$data_ini." A ".$data_fin;
    $where2 = " AND aih_dataini BETWEEN '{$data_ini}' AND '{$data_fin}' ";
}
else
{
    $dados_compet = "COMPETENCIA ". mes($mes_compet) ." / ". $ano_compet;
    $where2 = " AND aih_ano_compet = {$ano_compet} AND aih_mes_compet = {$mes_compet}";
}

// escolheu algum municipio? todos ?
if( $municipio > 0 )
{
    $where3a = " AND muni_cd_cod_ibge_resid = {$municipio}";
    $where3b = " AND pac_ibge_codigo = {$municipio}";
}
else
    $where3a = $where3b = '';
    

include_once $_SESSION[root].$_SESSION[modulo]."relatorio/cabecalho.php";

// descobrir quais pacientes tem mais de 2 ocorrencias na tabela AIH
// pode ser um 'usu_codigo' ou um 'pac_aih_codigo'

//echo
$stmt =
    "
    (SELECT usu_codigo as usu_codigo,
            'usu_codigo' As tipo,
            usu_nome as usu_nome,
            COUNT(aih_codigo) AS total,
            COALESCE( c.cid_nome || ' - ' || c.uf_sigla, '(SEM CIDADE)' ) AS cid_nome,
            TO_CHAR( usu_datanasc, 'dd/mm/yyyy') as nasc,
            COALESCE(usu_end_rua,'') || ' ' || COALESCE(usu_end_nr,'') AS endereco,
            u.muni_cd_cod_ibge_resid AS ibge
        
        FROM aih 
        
        NATURAL JOIN usuario AS u
        
        LEFT JOIN cidade AS c ON c.cid_codigo_ibge = u.muni_cd_cod_ibge_resid

        WHERE aih_ativo = 'S' $where2 $where3a
        
        GROUP BY usu_codigo, usu_nome, cid_nome, uf_sigla, nasc, endereco, muni_cd_cod_ibge_resid
        
        HAVING count( usu_codigo ) >= 2)
    
    UNION ALL
    
    (SELECT pac_aih_codigo as usu_codigo,
            'pac_aih_codigo' AS tipo,
            pac_nome as usu_nome,
            COUNT(aih_codigo) AS total, 
            COALESCE( c.cid_nome || ' - ' || c.uf_sigla, '(SEM CIDADE)' ) AS cid_nome,
            TO_CHAR( pac_dt_nasc, 'dd/mm/yyyy') as nasc,
            COALESCE(pac_end_rua,'') || ' ' || COALESCE(pac_end_nr,'') AS endereco,
            p.pac_ibge_codigo AS ibge
            
        FROM aih AS a
        
        INNER JOIN aih_paciente AS p ON p.pac_codigo = a.pac_aih_codigo
        
        LEFT JOIN cidade AS c ON c.cid_codigo_ibge = p.pac_ibge_codigo
        
        WHERE aih_ativo = 'S' {$where2} {$where3b}
        
        GROUP BY pac_aih_codigo, pac_nome, cid_nome, uf_sigla, nasc, endereco, pac_ibge_codigo
        
        HAVING count( pac_aih_codigo ) >= 2)
    
    ORDER BY 5, 3";
    
$qry = db_query( $stmt, $LOG = false );

// iniciando vars
$cid_nome   = -1;
$total_cid  = 0;
$cont       = 0;
$total      = 0;

while( $linha = pg_fetch_array($qry) )
{
    // trocou de idade ou 1a iteração
    if( $cid_nome != $linha['cid_nome'] )
    {
        
        if( $cont > 0 )
        {
            echo "<p>Total da cidade: <strong>$total_cid</strong>";
            $total_cid = 0;
        }    
        
        echo "<p> Cidade: <strong>{$linha[cid_nome]}</strong></p>";
    }
    
    $cid_nome   = $linha['cid_nome'];
    $total_cid  += $linha['total'];
    $total      += $linha['total'];
    $cont++;
    
    echo "
    <table class='relatorio'>
        <tr>
            <th width='30%'>Procedimento</th>
            <th width='30%'>Paciente</th>
            <th width='10%'>Data Nasc.</th>
            <th width='20%'>Endere&ccedil;o</th>
            <th width='10%'>Reinterna&ccedil;&atilde;o</th>
        </tr>
    ";
    
    //echo
    $stmt2 = "SELECT aih_codigo,
                    aih_desc_proc_soli,
                    aih_dataini,
                    TO_CHAR(aih_dataini,'dd/mm/yyyy') AS data,
                    proc_classificacao_sus,
                    proc_nome
                FROM aih AS a
                LEFT JOIN procedimento AS p ON p.proc_codigo = aih_desc_proc_soli
                WHERE aih_ativo = 'S' AND {$linha[tipo]} = {$linha[0]} {$where2}
                ORDER BY aih_dataini ASC";
    
    $qry2 = db_query( $stmt2 );
    
    $dt_ant = null;
    
    $mostra = true;
    
    while( $linha2 = pg_fetch_array($qry2) )
    {
        
        if( ! $dt_ant )
            $reint = 'N&atilde;o';
        else
            $reint = ( data_dif( $linha2['data'], $dt_ant ) < 30 ? 'Sim' : 'N&atilde;o' );
            
        $dt_ant = $linha2['data'];
        
        echo "
        <tr>
            <td>{$linha2[proc_classificacao_sus]} - {$linha2[proc_nome]}</td>
            <td>".( $mostra ? $linha['usu_nome'] : "&nbsp;" )."</td>
            <td>".( $mostra ? $linha['nasc'] : "&nbsp;" )."</td>
            <td>".( $mostra ? $linha['endereco'] : "&nbsp;" )."</td>
            <td>{$reint}</td>
        </td>
        ";
        
        $mostra = false;
    }
    
    echo "
    </table>
    Total : <strong>{$linha[total]}</strong>
    <br /><br />
    ";
}


echo "
<p>Total da cidade: <strong>$total_cid</strong>

<hr/>

<p> Total Geral: <strong>{$total}</strong>.</p>

</body>
</html>
";