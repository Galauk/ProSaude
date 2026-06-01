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
//    print "<p>Escolha <b>todos</b> os campos antes de prosseguir !</p>";
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


//  if( $med_row['proc_tipo_manut'] == 2 ) -----------------------------------------------------------------------------------------------
}
else if( $med_row['proc_tipo_manut'] == 2 ) 
{
	print "<p>Obs: Este laborat&oacute;rio est&aacute; configurado para manuten&ccedil;&atilde;o 
	<strong>por per&iacute;odo (valor)</strong>!</p>";

	if( empty($gex_periodo) ) exit(0);

}
?>
