<?php
/**
 *  Visualiza uma lista completa das AIHs e APACs possibilitando sua busca e impressao da 2a via
*/
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."aih_apac.inc.php";

include_once $_SESSION[root].$_SESSION[modulo]."calendario.inc.php";

$Calendario = new Calendario;

//verauth($id_login);

Cabecario( $hotkey = false );

echo "
<form action='".(htmlentities($PHP_SELF."?id_login={$id_login}&acao="))."' method='post'>
<fieldset>
<legend>Visualiza&ccedil;&atilde;o de APACs e AIHs</legend>
<table>
<tr>
    <td width='120'>
        <label>Buscar
            <select name='busca' class='box'>
                <option value='1'".( $busca == 1 ? ' selected="selected"' : '' ).">Ambas</option>
                <option value='2'".( $busca == 2 ? ' selected="selected"' : '' ).">AIH</option>
                <option value='3'".( $busca == 3 ? ' selected="selected"' : '' ).">APAC</option>
             </select>    
        </label>
    </td>
    <td width='200'>
        <label>Texto
            <input type='text' name='palavra_chave' class='box' size='25'
                onchange='this.value=this.value.toUpperCase()' value='{$palavra_chave}' />
        </label>
    </td>
    <td width='120'>
        <label>Campo
            <select name='campo' class='box'>
                <option value='1'".( $campo == 1 ? ' selected="selected"' : '' ).">Paciente</option>
                <option value='2'".( $campo == 2 ? ' selected="selected"' : '' ).">N&uacute;mero</option>
             </select>    
        </label>
    </td>  
    <td><input type='image' alt='Procurar' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg' class='btn' /></td>
</tr>
</table>
</fieldset>
</form>
";

//// listagem !
if( empty($acao) )
{
/// montando os SQLs

	$str = str_replace("+","%%",strtoupper($palavra_chave));
    $max = 15;
   
    //var_dump($str,$campo);

    // se NAO for busca, limitar 
	$sql_f 	=  ( empty($palavra_chave) ? 
        " ORDER BY 1 DESC LIMIT $max" : 
        " ORDER BY 4,2" );

    if( ! empty($str) )
	{
		$where_c 	= "LIKE TO_ASCII('%$palavra_chave%')";
		$where_arr  = array( 'apac' => '', 'aih' => '' );

		switch( (int)$campo )
		{

            case 2:
				$where_arr['apac']  = "WHERE a.apac_num LIKE '%$palavra_chave%'";
                $where_arr['aih']   = "AND a.aih_numero_aih LIKE '%$palavra_chave%'";
                break;

			case 1:
            default:
				$where_arr['apac'] = "WHERE (CASE WHEN a.pac_codigo IS NOT NULL THEN p0.usu_nome $where_c
							WHEN a.pac_apac_codigo IS NOT NULL THEN p1.pac_nome $where_c END)";
                $where_arr['aih'] = "AND ( UPPER(p0.usu_nome) $where_c OR UPPER(p1.pac_nome) $where_c )";
				break;
        }
    }
    else
        $resp = "&Uacute;ltimos <b>$max</b> registros";

    // montando SQL APAC
	$stmt_apac = "SELECT 
    
        a.apac_codigo as codigo, 
        a.apac_num as numero, 
        TO_CHAR(apac_dt_cadastro,'dd/mm/yyyy') as data,

		(CASE WHEN a.pac_codigo IS NOT NULL THEN p0.usu_nome
		WHEN a.pac_apac_codigo IS NOT NULL THEN p1.pac_nome
		ELSE 'none' END) as nome,

        'APAC' as tipo

	FROM apac AS a
	
	LEFT JOIN usuario AS p0 ON p0.usu_codigo = a.pac_codigo
	LEFT JOIN apac_paciente AS p1 ON p1.pac_codigo = a.pac_apac_codigo
	
	LEFT JOIN unidade AS u0 ON u0.uni_codigo = a.uni_sol_codigo
	LEFT JOIN apac_unidade AS u1 ON u1.uni_codigo = a.uni_sol_apac_codigo
	
	LEFT JOIN medico AS m0 ON m0.med_codigo = a.med_sol_codigo
	LEFT JOIN apac_medico AS m1 ON m1.med_codigo = a.med_sol_apac_codigo
	
	LEFT JOIN unidade AS u2 on u2.uni_codigo = a.orgao_codigo
	LEFT JOIN apac_unidade AS u3 on u3.uni_codigo = a.orgao_apac_codigo
	
	LEFT JOIN unidade AS u4 ON u4.uni_codigo = a.uni_pres_codigo
	LEFT JOIN apac_unidade AS u5 ON u5.uni_codigo = a.uni_pres_apac_codigo
	
	LEFT JOIN medico AS m2 ON m2.med_codigo = a.med_aud_codigo
	LEFT JOIN apac_medico AS m3 ON m3.med_codigo = a.med_aud_apac_codigo	
	{$where_arr[apac]} ";


    $stmt_aih  = "SELECT 
        
            a.aih_codigo as codigo, 
            a.aih_numero_aih as numero, 
            to_char(aih_dt_cadastro, 'dd/mm/YYYY') as data,  
		
			( CASE WHEN a.usu_codigo IS NOT NULL THEN p0.usu_nome
			WHEN a.pac_aih_codigo IS NOT NULL THEN p1.pac_nome
			ELSE 'none' END ) as nome,

            'AIH' as tipo
			
		FROM aih AS a
		
		LEFT JOIN usuario AS p0 ON p0.usu_codigo = a.usu_codigo
		LEFT JOIN aih_paciente AS p1 ON p1.pac_codigo = a.pac_aih_codigo
	
		WHERE aih_ativo='S' {$where_arr[aih]} ";


    // sql final
    if( $busca == 1 || empty($busca) )
        $stmt = " ({$stmt_apac}) \nUNION \n({$stmt_aih}) {$sql_f}";
    else if( $busca == 2 )
        $stmt = $stmt_aih . $sql_f;
    else if( $busca == 3 )
        $stmt = $stmt_apac . $sql_f;

    //print "<pre>$stmt</pre>";

    $qry = db_query($stmt);

    $num = pg_num_rows($qry);

    if( ! $resp )
    {
    	if( $num == 0 ) { $resp = "Nenhum Registro encontrado"; }
	    else  { $resp = "<b>$num</b> Registro(s) na Base de Dados"; }
    }

print "
<fieldset>
<legend>Listagem</legend>

<p>{$resp}</p>

<table class='lista'>
<tr style='background:#fff'>
    <th>Nome do Paciente</th>
    <th width='50' style='text-align:center'>Tipo</th>
    <th width='100'>N&uacute;mero</th>
    <th width='75'>Dt Cadastro</th>
    <th width='180' colspan='2'>&nbsp;</th>
</tr>
";


    while( $row = pg_fetch_array($qry) )
    {

        if( $row['tipo'] == 'APAC' )
        {
            $func  = "document.location.href='apac.php?id_login={$id_login}&amp;acao=segundavia&amp;apac_codigo={$row[codigo]}'";
            $func2 = "document.location.href='{$PHP_SELF}?id_login={$id_login}&amp;acao=det_apac&amp;codigo={$row[codigo]}'";
        }
        else //if( $row['tipo'] == 'AIH' )
        {
            $func  = "document.location.href='aih.php?id_login={$id_login}&amp;acao=segundavia&amp;aih_codigo={$row[codigo]}'";
            $func2 = "document.location.href='{$PHP_SELF}?id_login={$id_login}&amp;acao=det_aih&amp;codigo={$row[codigo]}'";
        }
        
        print "
        <tr>
            <td>{$row[nome]}</td>
            <td class='c'>{$row[tipo]}</td>
            <td class='c'>{$row[numero]}</td>
            <td class='c'>{$row[data]}</td>
            <td class='c'>
                <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/detalhes.png' alt='Detalhes' style='cursor:pointer' onclick=\"{$func2}\"/> 
                <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/segunda_via_on.jpg' alt='Segunda via' style='cursor:pointer' onclick=\"{$func}\"/>
            </td>
        </tr>
        ";
    }

echo "
</table>
</fieldset>";
}
// Detalhes da APAC ------------------------------------------------------------
else if( $acao == 'det_apac' )
{
    
    // montando SQL APAC
	$stmt_apac = "SELECT 
        a.apac_num, 
        TO_CHAR(apac_dt_cadastro,'dd/mm/yyyy') as data_cad,
		(CASE WHEN a.pac_codigo IS NOT NULL THEN p0.usu_nome
    		WHEN a.pac_apac_codigo IS NOT NULL THEN p1.pac_nome
    		ELSE 'NENHUM' END) as nome,
        (CASE WHEN a.uni_sol_codigo IS NOT NULL THEN u0.uni_desc
            WHEN a.uni_sol_apac_codigo IS NOT NULL THEN u1.uni_desc
        	ELSE 'NENHUM' END) as uni_sol,
        (CASE WHEN a.med_sol_codigo IS NOT NULL THEN m0.med_nome
    		WHEN a.med_sol_apac_codigo IS NOT NULL THEN m1.med_nome
    		ELSE 'NENHUM' END) as med_sol,
        (CASE WHEN a.orgao_codigo IS NOT NULL THEN u2.uni_desc
    		WHEN a.orgao_apac_codigo IS NOT NULL THEN u3.uni_desc
    		ELSE 'NENHUM' END) as orgao,
        (CASE WHEN a.uni_pres_codigo IS NOT NULL THEN u4.uni_desc
    		WHEN a.uni_pres_apac_codigo IS NOT NULL THEN u5.uni_desc
    		ELSE 'NENHUM' END) as prestadora,
        (CASE WHEN a.med_aud_codigo IS NOT NULL THEN m2.med_nome
    		WHEN a.med_aud_apac_codigo IS NOT NULL THEN m3.med_nome
    		ELSE 'NENHUM' END) as auditor,
        COALESCE(cd10_descricao,'NENHUM') AS cd10,
        TO_CHAR(a.apac_periodo_validade,'dd/mm/yyyy') AS validade,
        TO_CHAR(a.apac_periodo_validade_fim,'dd/mm/yyyy') AS validade_fim, 
        COALESCE(apac_convenio_nome,'NENHUM') AS convenio,
        COALESCE(apac_hipotese,'NENHUM') AS apac_hipotese,
        COALESCE(apac_resumo_exame_fisico,'NENHUM') AS apac_resumo_exame_fisico,
        a.apac_mes_competencia, 
        TO_CHAR( COALESCE(a.apac_ano_competencia,0), '0009' ) AS ano_competencia,
        TO_CHAR(a.apac_dt_cadastro,'dd/mm/yyyy') AS dt_cad
    
    FROM apac AS a
	
    LEFT JOIN usuario AS p0 ON p0.usu_codigo = a.pac_codigo
	LEFT JOIN apac_paciente AS p1 ON p1.pac_codigo = a.pac_apac_codigo
	LEFT JOIN unidade AS u0 ON u0.uni_codigo = a.uni_sol_codigo
	LEFT JOIN apac_unidade AS u1 ON u1.uni_codigo = a.uni_sol_apac_codigo
	LEFT JOIN medico AS m0 ON m0.med_codigo = a.med_sol_codigo
	LEFT JOIN apac_medico AS m1 ON m1.med_codigo = a.med_sol_apac_codigo
	LEFT JOIN unidade AS u2 on u2.uni_codigo = a.orgao_codigo
	LEFT JOIN apac_unidade AS u3 on u3.uni_codigo = a.orgao_apac_codigo
	LEFT JOIN unidade AS u4 ON u4.uni_codigo = a.uni_pres_codigo
	LEFT JOIN apac_unidade AS u5 ON u5.uni_codigo = a.uni_pres_apac_codigo
	LEFT JOIN medico AS m2 ON m2.med_codigo = a.med_aud_codigo
	LEFT JOIN apac_medico AS m3 ON m3.med_codigo = a.med_aud_apac_codigo	
    LEFT JOIN cid10 AS cd10 ON cd10.cd10_codigo = a.cd10_codigo
  
    WHERE apac_codigo={$codigo}";
    
    $row = db_getRow($stmt_apac);
    
    $dados = array(
        'N&uacute;mero da Apac'                 => $row['apac_num'],
        'Paciente'                              => $row['nome'],
        'Unidade Solicitante'                   => $row['uni_sol'],
        'M&eacute;dico Solicitante'             => $row['med_sol'],
        '&Oacute;rg&atilde;o'                   => $row['orgao'],
        'Unidade Prestadora'                    => $row['prestadora'],
        'M&eacute;dico Auditor'                 => $row['auditor'],
        'CID10'                                 => $row['cd10'],
        'Per&iacute;odo de Validade'            => $row['validade'],
        'Per&iacute;odo de Validade (fim)'      => $row['validade_fim'],
        'Conv&ecirc;nio'                        => $row['convenio'],
        'Hip&oacute;tese'                       => $row['apac_hipotese'],
        'Resumo do Exame F&iacute;sico'         => $row['apac_resumo_exame_fisico'],
        'Compet&ecirc;ncia (m&ecirc;s / ano)'   =>
            $Calendario->mesExtenso[ $row['apac_mes_competencia'] ] .' / '.
            $row['ano_competencia'],
        'Data de Cadastro'                      => $row['dt_cad'],
        'Procedimentos'                         => "\n<ol style='margin:0;padding-left:16px;'>"
    );
    
    $stmt_proc = 
        "SELECT 
            ( CASE WHEN ap.proc_codigo IS NOT NULL THEN '(' || COALESCE( p0.proc_classificacao_sus, '000') || ') ' ||  p0.proc_nome 
              WHEN ap.proc_apac_codigo IS NOT NULL THEN '(' || COALESCE( p1.proc_numero, '000') || ') ' || p1.proc_nome END )
        FROM apac_procedimento AS ap
        LEFT JOIN procedimento AS p0 ON p0.proc_codigo = ap.proc_codigo
        LEFT JOIN apac_procedimento_cad AS p1 ON p1.proc_codigo = ap.proc_apac_codigo
        WHERE apac_codigo = {$codigo}";
        
        $qry_proc = db_query($stmt_proc);
        while( $proc = pg_fetch_row($qry_proc) )
            $dados['Procedimentos'] .= "\n<li>$proc[0]</li>";
        
        $dados['Procedimentos'] .= "\n</ol>";
    
    print "
    <fieldset>
    <legend>Detalhes da APAC</legend>
    <table>
    ";
   
    foreach( $dados as $tit => $cont )
    {
        print "
        <tr>
            <td style='width:155px;vertical-align:top'>{$tit}</td>
            <td style='font-weight:bold;'>{$cont}</td>
        </tr>";
    }
   
    print "
    </table>
    </fieldset>";
}
// Detalhes da AIH -------------------------------------------------------------
else if( $acao = 'det_aih' )
{
    
    $stmt_aih  =
        "SELECT 
            a.aih_codigo as codigo, 
            a.aih_numero_aih as numero, 
            to_char(aih_dt_cadastro, 'dd/mm/YYYY') as data,  
			( CASE WHEN a.usu_codigo IS NOT NULL THEN p0.usu_nome
                WHEN a.pac_aih_codigo IS NOT NULL THEN p1.pac_nome
                ELSE 'none' END ) as nome,
            to_char(aih_data_autorizacao, 'dd/mm/YYYY') as data_auto,   
            COALESCE( m.med_nome, 'NENHUM' ) as hosp_sol,
            COALESCE( aih_cnes_soli, '000' ) as cnes_sol,
			to_char(aih_dataini,  'dd/mm/YYYY') as data_ini,
            to_char(aih_data_alta, 'dd/mm/YYYY') as data_alta,
            to_char(aih_data_solicitacao, 'dd/mm/YYYY') as data_sol,
            aih_principais_sintomas as sintomas,
			aih_justificativa_internacao as justif,
            aih_principais_resultados as result,
            aih_diag_ini as diag,
			COALESCE( c1.cd10_descricao, 'NENHUM' ) as cid_pri,
            COALESCE( c2.cd10_descricao, 'NENHUM' ) as cid_sec,
            COALESCE( c3.cd10_descricao, 'NENHUM' ) as cid_ter,
			COALESCE( p.proc_classificacao_sus || ' - ' || p.proc_nome, 'NENHUM' ) as proc_nome,
            cli.cli_descricao,
            ci.ci_descricao,				
            '(' || COALESCE( UPPER(aih_tipo_doc_proc_soli), '--') || ') ' ||
                COALESCE( aih_n_doc_prof_solicitante, '000000') as doc,
			m1.med_nome as med_sol,
            aih_vinculo_previdencia,
            m2.med_nome as med_auto,
            '(' || COALESCE( UPPER(aih_tipo_doc_autorizacao), '--') || ') ' ||
                COALESCE( aih_n_doc_prof_autorizador, '000000') as doc_auto,    
			aih_tipo_acidente,
            aih_observacao,
            aih_cnpj_seguradora,
            aih_n_bilhete,
            aih_serie,
			aih_cnpj_da_empresa, 
			aih_cnae_da_empresa, 
			aih_mes_compet, 
			aih_ano_compet
			
		FROM aih AS a
		
		LEFT JOIN usuario AS p0 ON p0.usu_codigo = a.usu_codigo
		LEFT JOIN aih_paciente AS p1 ON p1.pac_codigo = a.pac_aih_codigo
        LEFT JOIN medico AS m ON m.med_codigo = a.med_codigo_solicitante
        LEFT JOIN cid10 AS c1 ON c1.cd10_codigo = a.aih_cid_cod_princ
        LEFT JOIN cid10 AS c2 ON c1.cd10_codigo = a.aih_cid_cod_secun
        LEFT JOIN cid10 AS c3 ON c1.cd10_codigo = a.aih_cid_cod_terc
        LEFT JOIN procedimento AS p ON p.proc_codigo = a.aih_desc_proc_soli
        LEFT JOIN clinica AS cli ON cli.cli_codigo = a.aih_clinica
        LEFT JOIN ci AS ci ON ci.ci_codigo = a.aih_ci
        LEFT JOIN medico AS m1 ON m1.med_codigo = a.med_solicitante_proc
        LEFT JOIN medico AS m2 ON m2.med_codigo = a.med_autorizador
        
		WHERE aih_ativo='S' AND aih_codigo = {$codigo} ";
    
    $row = db_getRow($stmt_aih);
    
    $dados = array(
        'N&uacute;mero da AIH'                  => $row['numero'],
        'Paciente'                              => $row['nome'],
        'Data de Cadastro'                      => $row['data'],
        'Data de Autoriza&ccedil;&atilde;o'     => $row['data_auto'],
        'Data de Solicita&ccedil;&atilde;o'     => $row['data_sol'],
        'Solicitante'                           => $row['hosp_sol'],
        'CNES'                                  => $row['cnes_sol'],
        'Data Interna&ccedil;&atilde;o'         => $row['data_ini'],
        'Data Alta'                             => $row['data_alta'],
        'Principais Sintomas'                   => $row['sintomas'],
        'Justificativa Interna&ccedil;&atilde;o'=> $row['justif'],
        'Principais Resultados'                 => $row['result'],
        'Diagn&oacute;stico'                    => $row['diag'],
        'CID10 (Prim&eacute;rio)'               => $row['cid_pri'],
        'CID10 (Secund&eacute;rio)'             => $row['cid_sec'],
        'CID10 (Terce&eacute;rio)'              => $row['cid_ter'],
        'Procedimento'                          => $row['proc_nome'],
        'Cl&iacute;nica'                        => $row['cli_descricao'],
        'C.I.'                                  => $row['ci_descricao'],
        'Doc. do Solicitante'                   => $row['doc'],
        'M&eacute;dico Solicitante'             => $row['med_sol'],
        'V&iacute;nculo Prev.'                  => $row['aih_vinculo_previdencia'],
        'M&eacute;dico Autorizador'             => $row['med_auto'],
        'Doc. M&eacute;d. Autorizador'          => $row['doc_auto'],
        'Tipo de Acidente'                      => $row['aih_tipo_acidente'],
        'CNPJ Seguradora'                       => $row['aih_cnpj_seguradora'],
        'N&uacute;mero do Bilhete'              => $row['aih_n_bilhete'],
        'S&eacute;rie'                          => $row['aih_serie'],
        'CNPJ da Empresa'                       => $row['aih_cnpj_da_empresa'],
        'CNAE da Empresa'                       => $row['aih_cnae_da_empresa'],
        'Compet&ecirc;ncia (m&ecirc;s / ano)'   =>
            $Calendario->mesExtenso[ $row['aih_mes_compet'] ] .' / '.
            $row['aih_ano_compet'],
        'Observa&ccedil;&atilde;o'              => nl2br( $row['aih_observacao'] )
    );
    
    //var_dump($row);
    
    print "
    <fieldset>
    <legend>Detalhes da AIH</legend>
    <table>
    ";
   
    foreach( $dados as $tit => $cont )
    {
        print "
        <tr>
            <td style='width:155px;vertical-align:top'>{$tit}</td>
            <td style='font-weight:bold;'>{$cont}</td>
        </tr>";
    }
   
    print "
    </table>
    </fieldset>";
}


//// fim -------------
echo "</body></html>";

