<?php
/**
 * @version Renato 5/7/2007 - 16:15
 * @version	Leandro 21/05/07 14:10
 * @author	Anderson
 * @brief	
*/
?>
<SCRIPT Language="Javascript">
function imprimir()
{
	window.print() ;
}
</script>
<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

echo "<style type='text/css'>
        tr{
			font-size:12px;
        }
		th
		{
			text-align: left;
		}
        </style>";
echo "<link href=\"".$_SESSION[rootlink].$_SESSION[modulo]."estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";

$cod_pro  = $_GET['cod_pro'];
$cod_ce	  = $_GET['cod_ce'];
$data_ini = $_GET['data_ini'];
$data_fim = $_GET['data_fim'];

$titulo_rel = "Pacientes Por Programa";

$Tit = "RELATÓRIO DE PACIENTES POR PROGRAMA";
$dtIni = $data_ini;
$dtFin = $data_fim;
include 'cabecalho.php';
?>
<head>
    <title><?=$titulo_rel?></title>
</head>

<body>
<?

	/*echo "<pre>";
		print_R($_REQUEST);
	echo "</pre>";*/

	$sql = "select prg_nome, sum(qtde) as qtde
		from (
			select distinct on ( a.prg_codigo ,f.usu_codigo ) a.prg_nome, count(distinct f.usu_codigo) as qtde
			from programa_atendimento a, programa_produto b, produto c, setor d,
			cota_paciente e, usuario f, produto_setor g
			where a.prg_codigo = b.prg_codigo
			and b.prgp_codigo = e.prgp_codigo
			and e.usu_codigo = f.usu_codigo
			and b.pro_codigo = c.pro_codigo
			and c.pro_codigo = g.pro_codigo
			and g.set_codigo = d.set_codigo
			and d.set_farmacia = 'S'
			and d.set_estoque = 'S'
			and e.ctp_data_de_cadastro between '$dtIni'::timestamp and '$dtFin'::timestamp";
	$sql .= (empty($_GET[cod_pro]) ? "" : " and a.prg_codigo = $_GET[cod_pro] ");
	$sql .= (empty($_GET[cod_ce]) ? "" : " and d.set_codigo = $_GET[cod_ce] ");
	$sql .=		"
			group by set_nome, a.prg_codigo, a.prg_nome, f.usu_nome, f.usu_codigo
			) as rta
			group by prg_nome
			order by prg_nome";

	/*echo "<pre>";
		echo $sql;
	echo "</pre>";*/

	echo "
	<table width=100%>
		<tr>
			<th>
				Programa
			</th>
			<th>
				Quantidade
			</th>
		</tr>";

	$exec = db_query($sql);

	$x = 0;
    while($row = pg_fetch_array($exec))
    {
		$total_geral += $row[qtde];
		echo "
		<tr>
			<td>
				$row[prg_nome]
			</td>
			<td>
				$row[qtde] pacientes
			</td>
		</tr>
		";
    }
	echo "
		<tr>
			<td colspan='2'>
				<hr/>
			</td>
		</tr>
		<tr>
			<th>
				Total Geral
			</th>
			<td>
				$total_geral pacientes
			</td>
		</tr>
	</table>";

?>
</body>
