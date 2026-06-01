<?php
/**
 * Arquivo responsavel pela manutencao "MENSAL" do agendamento
 * Ver arquivo manutenao_exame_mensal.php
 * Dependencias:
 * - manutencao_exame_mensal_iframe_ajax.php
*/
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
//verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

// variaveis
$med_codigo 	= intval($_GET['med_codigo']);
$uni_codigo 	= intval($_GET['uni_codigo']);
$proc_codigo 	= intval($_GET['proc_codigo']);
$gex_periodo 	= trim($_GET['gex_periodo']);

// vamos mostrar somente qdo tiver todos os campos
//if( empty($med_codigo) || empty($uni_codigo)  || empty($agt_codigo) || empty($gex_periodo) )
if( empty($med_codigo) || empty($uni_codigo)  || empty($proc_codigo) || empty($gex_periodo) )
{
    print "<p>Escolha <b>todos</b> os campos antes de prosseguir !</p>";
    exit(1);
}

?>
<script type="text/javascript" src="funcoes.js"></script>
<script type="text/javascript" src="ajax_motor.js"></script>
<script type="text/javascript" src="script.js"></script>
<script type="text/javascript">

// altera a grade de exames para manutencao tipo = 1
function altera_gex(id_login, gex_codigo, gex_tipo, valor, valor_antigo)
{ 
    // valida UNIDADE ou VALOR
    var re      = ( gex_tipo == 'Q' ? /^\d+$/           : /^(\d+|\d+\.\d+|\d+\,\d+)$/ ); 
	var aviso   = ( gex_tipo == 'Q' ?
                    'A quantidade deve ser um iteniro e maior que zero (0) !'   :
                    'O valor deve ser uma quantia (R$) e maior que zero (0)! ' );
    var Txt     = $('txt_valor_' + gex_codigo);
    
    if( ! re.test(valor) )
	{ 
		alert( aviso ); 
		Txt.value = valor_antigo; 
		Txt.focus(); 
		return false; 
	}

	var endereco = 'manutencao_exame_mensal_iframe_ajax.php?acao=upd_tipo_1&id_login='+id_login;
    endereco += '&codigo='+ gex_codigo +'&gex_tipo='+gex_tipo+'&valor='+ valor; 

    ajax_tudo( endereco, altera_callback );
}

// altera a grade de exames para manutencao tipo = 2
function altera_gem(id_login, gem_codigo, valor, valor_antigo, agt)
{
    var re  = /^(\d+|\d+\.\d+|\d+\,\d+)$/; 
    var Txt = $('txt_valor_' + gem_codigo);
    
    if( ! re.test(valor) )
	{ 
		alert( "O valor deve ser uma quantia (R$) e maior que zero (0)! "); 
		Txt.value = valor_antigo; 
		Txt.focus();  
		return false; 
	}
    
    var endereco = 'manutencao_exame_mensal_iframe_ajax.php?acao=upd_tipo_2&id_login='+id_login;
    endereco += '&codigo='+ gem_codigo +'&valor='+ valor;
    endereco += '&gex_periodo=<?=$gex_periodo;?>&med_codigo=<?=$med_codigo;?>&agt_codigo='+agt;

    ajax_tudo( endereco, altera_callback );
    //ajax_tudo( endereco, alert );
}

// Comportamento padrao do retorno do AJAX
function altera_callback( txt )
{
    //alert(txt);
    var Resp = eval(txt);
       
    if( Resp.ok )
    {
        $('upd').style.color = '#10d';
        $( 'usr_alt_' + Resp.codigo ).innerHTML  = Resp.usr;
    }
    else
    {
        $('upd').style.color = '#d01';
    }

    $('upd').innerHTML = Resp.msg;
    
    if( Resp.tipo == 2 )
    {
        $( 'gem_total_' + Resp.codigo ).innerHTML  = Resp.total;
        $( 'gem_dif_' + Resp.codigo ).innerHTML  = Resp.dif;
    }
}

</script>
</head>
<body>
<?

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>
echo "
	<div style='font-weight:bold;'>
		Atualiza&ccedil;&atilde;o: <label style='font-weight:bold;' id='upd'></label>
	</div>"; 

// informacoes adicionais...

// medico (laboratorio)
$med_row = db_getRow("SELECT proc_tipo_manut FROM medico WHERE med_codigo = $med_codigo");

// procedimento VS laboratorio
$proc_row = db_getRow(
        "SELECT proc_tipo FROM laboratorio_procedimento
        WHERE med_codigo = {$med_codigo}"  );

// (gex_tipo) V: Valor, Q:Quantidade
$gex_tipo       = trim( $proc_row['proc_tipo'] );
$gex_tipo_tit   = ( $gex_tipo == 'Q' ? 'Quantidade' : 'Valor (R$)' );

if( $med_row['proc_tipo_manut'] == 1 )
{

	print "<p>Obs: Este laborat&oacute;rio est&aacute; configurado para manuten&ccedil;&atilde;o 
	<strong>por procedimento (unit&aacute;rio)</strong> !</p>";

	if( empty($gex_periodo) ) exit(0);

	// percorrendo os agentes !
    // removido um JOIN
    // INNER JOIN laboratorio_procedimento AS lp ON
    //      (lp.proc_codigo = m.proc_codigo AND lp.med_codigo = m.med_codigo)
    
    //print
    $stmt_agt =
    "SELECT
        gex_codigo,
        TO_CHAR(gex_valor,'0.00') as gex_valor,
        gex_qtde,
        agt_descricao,
        COALESCE(u1.usr_nome,'--') as usr_cad,
        COALESCE(u2.usr_nome,'--') as usr_alt
    FROM grade_exame_mensal AS m
        INNER JOIN agente AS a ON a.agt_codigo = m.agt_codigo
        LEFT JOIN usuarios AS u1 ON u1.usr_codigo = m.usr_codigo_cad
        LEFT JOIN usuarios AS u2 ON u2.usr_codigo = m.usr_codigo_alt
    WHERE gex_periodo = '{$gex_periodo}'
        AND m.med_codigo = {$med_codigo}
        AND m.proc_codigo = {$proc_codigo}
        AND a.uni_codigo = {$uni_codigo}
    ORDER BY agt_descricao" ;

    print "
    <table class='lista'>
    <tr style='background:#ffffff'>
        <th>Agente</th>
        <th style='width:100px;text-align:center;'>{$gex_tipo_tit}</th>
        <th>Cadastrado por</th>
        <th>Alterado por</th>
    </tr>
    ";
    
    $qry = db_query($stmt_agt);

	echo "<form name='ff' method='get' action='$PHP_SELF'>
      <input type=hidden name='grm_codigo' id='grm_codigo' value='' \>";

	while( $row = pg_fetch_array($qry) )
	{
        
		echo "
		<tr>
			<td>{$row[agt_descricao]}</td>";

		if( $gex_tipo=="Q" )
		{
            $grm = (int)$row['gex_qtde'];
			echo "
			<td class='c'>
				<input type=text id='txt_valor_$row[gex_codigo]' name='gex_qtde' value='$grm' class='boxagente'
				onchange=\"altera_gex('$id_login', '$row[gex_codigo]', '$gex_tipo', this.value, '$grm')\" >
			</td>";
		}
		else if( $gex_tipo=="V" )
		{
            $grm = number_format( $row['gex_valor'], 2 );
			echo "
			<td class='c'>
    	    	<input type='text' id='txt_valor_$rr[gex_codigo]' name=gex_valor value='$gex_valor'
        		 class='boxagente' 
				onchange=\"altera_gex('$id_login', '$row[gex_codigo]', '$gex_tipo', this.value, '$grm')\" >
			</td>";
		}
	
		echo "
			<td>$row[usr_cad]</td>
			<td id='usr_alt_{$row[gex_codigo]}'>$row[usr_alt]</td>
		</tr>"; 

	} // fim do while($rr=pg_fetch_array($sql)

	echo "
		</form>
	</table>
	";

//  if( $med_row['proc_tipo_manut'] == 2 ) -----------------------------------------------------------------------------------------------
}
else if( $med_row['proc_tipo_manut'] == 2 ) 
{
	print "<p>Obs: Este laborat&oacute;rio est&aacute; configurado para manuten&ccedil;&atilde;o 
	<strong>por per&iacute;odo (valor)</strong>!</p>";

	if( empty($gex_periodo) ) exit(0);

	// percorrendo os agentes !
    //print
    $stmt_agt =
    "SELECT
        gem_codigo,
        gem_valor,
        agt_descricao,
        COALESCE(u1.usr_nome,'--') as usr_cad,
        COALESCE(u2.usr_nome,'--') as usr_alt,
        m.agt_codigo,
        ( SELECT laboratorio_calcula_custo_agt( {$med_codigo}::int8, '{$gex_periodo}'::date, 30::int2, m.agt_codigo ) ) AS total 
    FROM grade_exame_mensal_manut AS m
        INNER JOIN agente AS a ON a.agt_codigo = m.agt_codigo
        LEFT JOIN usuarios AS u1 ON u1.usr_codigo = m.usr_codigo_cad
        LEFT JOIN usuarios AS u2 ON u2.usr_codigo = m.usr_codigo_alt
    WHERE
        gem_periodo = '$gex_periodo'
        AND m.med_codigo = {$med_codigo}
        AND a.uni_codigo = {$uni_codigo}
    ORDER BY agt_descricao" ;

    print "
    <table class='lista'>
    <tr style='background:#ffffff'>
        <th>Agente</th>
        <th style='width:90px;text-align:center;'>Valor (R$)</th>
        <th style='width:120px;text-align:center;'>Total Gasto (R$)</th>
        <th style='width:90px;text-align:center;'>Restante (R$)</th>
        <th>Cadastrado por</th>
        <th>Alterado por</th>
    </tr>
    ";
    
    $qry = db_query($stmt_agt);
    
    while( $row = pg_fetch_array($qry) )
    {
        $row['gem_valor']   = number_format( $row['gem_valor'], 2 );
        $dif                = $row['gem_valor'] - $row['total'];
        $resto              = number_format( $dif, 2 );
        print "
        <tr>
            <td>{$row[agt_descricao]}</td>
            <td class='c'>
                <input type='text' class='boxagente' id='txt_valor_{$row[0]}' size='12'
                    maxlength='9' name='txt_valor_{$row[0]}'
                    onchange=\"altera_gem('$id_login', '$row[0]', this.value, '{$row[gem_valor]}', '{$row[agt_codigo]}')\" value='{$row[gem_valor]}' />
            </td>
            <td class='c' id='gem_total_{$row[gem_codigo]}'>{$row[total]}</td>
            <td class='c' id='gem_dif_{$row[gem_codigo]}'>{$resto}</td>
            <td>{$row[usr_cad]}</td>
            <td id='usr_alt_{$row[gem_codigo]}'>{$row[usr_alt]}</td>
        </tr>
        ";
    }
    
    print "\n\t\t</table>";

}
?>
