<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Listagem de tabelas inconsistentes.</title>
<link rel="stylesheet" type="text/css" href="geral.css" />
</head>
<?
set_time_limit(100000000000);
$host_correto = $_POST["host_correto"];
$host_incosistente = $_POST["host_incosistente"];
$porta_correto = $_POST["porta_correto"];
$porta_incosistente = $_POST["porta_incosistente"];
$banco_correto = $_POST["banco_correto"];
$banco_incosistente = $_POST["banco_incosistente"];
$usuario_correto	= $_POST["usuario_correto"];
$usuario_incosistente = $_POST["usuario_incosistente"];
$senha_correto = $_POST["senha_correto"];
$senha_incosistente = $_POST["senha_incosistente"];
/*
// Conexão Banco antigo de atalaia
$db_correto = pg_connect("host='".$host_correto."' port='".$porta_correto."' dbname='".$banco_correto."' user='".$usuario_correto."' password='".$senha_correto."'") or die ("Não foi possivel conectar ao servidor 1");
// Conex?o banco atual de atalaia
$db_incosistente = pg_connect("host='".$host_incosistente."' port='".$porta_incosistente."' dbname='".$banco_incosistente."' user='".$usuario_incosistente."' password='".$senha_incosistente."'") or die ("Não foi possivel conectar ao servidor 2");
*/
include("db.inc.php");
// Percorre todas as tabelas, campos e tipos do banco em conexão e schema
$sqlDesenvolvimento = "SELECT
							c.schemaname as schema,
							c.relname as tabela,
							a.attname as coluna,
							d.adsrc as default,
							pg_catalog.format_type(a.atttypid, a.atttypmod) as tipo
						FROM 
							pg_catalog.pg_attribute a
						INNER JOIN 
							pg_stat_user_tables c on a.attrelid = c.relid
						LEFT JOIN 
							pg_catalog.pg_attrdef d on d.adrelid = c.relid and a.attnum = d.adnum
						WHERE 
							a.attnum > 0 AND
							c.schemaname = 'social' AND							
							NOT a.attisdropped
						ORDER BY 
							c.relname, a.attname";
$queryDesenvolvimento = pg_query($db_correto,$sqlDesenvolvimento);
// Array para impressão futuras
$arrayTabela = array(); 
$arrayTiposDiferente = array();
$arrayColunasFaltantes = array();
$cont01 = 0;
$cont02 = 0;
$cont03 = 0;
while($rowDesenvolvimento = pg_fetch_array($queryDesenvolvimento)) {
	// Verificando se tabela já foi criada no banco em que está sendo realizado a verificação
	$sqlTabela = "SELECT DISTINCT
					c.relname as tabela 
				FROM 
					pg_catalog.pg_attribute a 
				INNER JOIN 
					pg_stat_user_tables c on a.attrelid = c.relid 
				WHERE 
					a.attnum > 0 AND
					c.relname = '".$rowDesenvolvimento["tabela"]."'	AND
					NOT a.attisdropped 
				ORDER BY 
					c.relname";
	$queryTabela = pg_query($db_incosistente,$sqlTabela);
	$rowTabela = pg_fetch_array($queryTabela);
	$numTabela = pg_num_rows($queryTabela);
	if ($numTabela == 1) {
		// Se tabela exisiti entra aqui, Verifica se campo existe
		$sqlCampos = "SELECT 
						c.relname as tabela, 
						a.attname as coluna, 
						d.adsrc as default, 
						pg_catalog.format_type(a.atttypid, a.atttypmod) as tipo 
					FROM 
						pg_catalog.pg_attribute a 
					INNER JOIN 
						pg_stat_user_tables c on a.attrelid = c.relid 
					LEFT JOIN 
						pg_catalog.pg_attrdef d on d.adrelid = c.relid and a.attnum = d.adnum 
					WHERE 
						a.attnum > 0 AND 
						c.relname = '".$rowDesenvolvimento["tabela"]."' AND 
						a.attname = '".$rowDesenvolvimento["coluna"]."' AND  
						NOT a.attisdropped 
					order by c.relname, a.attname";
		$queryCampos = pg_query($db_incosistente,$sqlCampos);
		$rowCampos = pg_fetch_array($queryCampos);
		$numCampos = pg_num_rows($queryCampos);
		// Se campo existir, confere o tipo
		if ($numCampos > 0) {
			// Se o tipo for diferente do tipo atual, joga pro array de tipos que precisa ser arrumado
			if ($rowDesenvolvimento["tipo"] != $rowCampos["tipo"]) {
				$arrayTiposDiferente[$cont02]["tabela"] =  $rowDesenvolvimento["tabela"];
				$arrayTiposDiferente[$cont02]["coluna"] =  $rowDesenvolvimento["coluna"];
				$arrayTiposDiferente[$cont02]["tipoCorreto"] =  $rowDesenvolvimento["tipo"];
				$arrayTiposDiferente[$cont02]["tipoIncosistente"] =  $rowCampos["tipo"];
				$arrayTiposDiferente[$cont02]["possivelSolucao"] =  "ALTER TABLE ".$rowDesenvolvimento["tabela"]." ALTER COLUMN ".$rowDesenvolvimento["coluna"]." TYPE ".$rowDesenvolvimento["tipo"]."";
				$cont02++;
			}
			
		// Se não existir o campo, joga pro array de campos que precisa ser criado
		} else {
			$arrayColunasFaltantes[$cont03]["tabela"] = $rowDesenvolvimento["tabela"];
			$arrayColunasFaltantes[$cont03]["coluna"] = $rowDesenvolvimento["coluna"];
			$arrayColunasFaltantes[$cont03]["tipo"] = $rowDesenvolvimento["tipo"];
			$arrayColunasFaltantes[$cont03]["tabela"] = $rowDesenvolvimento["tabela"];
			$arrayColunasFaltantes[$cont03]["possivelSolucao"] = "ALTER TABLE ".$rowDesenvolvimento["tabela"]." ADD COLUMN ".$rowDesenvolvimento["coluna"]." ".$rowDesenvolvimento["tipo"]."";
			$cont03++;
		}	
	} else {
		if (!in_array($rowDesenvolvimento["tabela"], $arrayTabela)) { 
			$arrayTabela[$cont01] = $rowDesenvolvimento["tabela"];
			$cont01++;	
		}
	}
}
?>
<table>
    <tr style="height: 30px; background-color:#EAEAEA;">
        <td><span style="font-size: 14px; font-weight:bold; ba">TABELAS A CRIAR</span></td> 
    </tr>
    <tr style="height: 30px; background-color:#EAEAEA;">
        <td><span style="font-size: 14px; font-weight:bold;">NOME</span></td>
    </tr>
	<?php if (count($arrayTabela)>0) { ?>
		<?php foreach($arrayTabela as $valueTabela){ ?>
				<tr style="height: 22px;">
					<td><span style="font-size: 12px; font-style:normal"><?=$valueTabela?></span></td>
				</tr>
		<?php } ?>
	<?php } else { ?> 
			<tr>
				<td colspan="4">Nenhuma tabela inconsistente!</td>
			</tr>
	<?php } ?>
</table>
<br />
<table>
    <tr style="height: 30px; background-color:#EAEAEA;">
        <td colspan="4"><span style="font-size: 14px; font-weight:bold; ba">TABELAS COM COLUNAS INCONSISTENTES</span></td> 
    </tr>
	<tr style="height: 30px; background-color:#EAEAEA;">
		<td><span style="font-size: 14px; font-weight:bold;">TABELA</span></td>
		<td><span style="font-size: 14px; font-weight:bold;">COLUNA</span></td>
		<td><span style="font-size: 14px; font-weight:bold;">TIPO DE DADO</span></td>
		<td><span style="font-size: 14px; font-weight:bold;">POSS&Iacute;VEL SOLU&Ccedil;&Atilde;O</span></td>
    </tr>
	<?php if (count($arrayColunasFaltantes)>0) { ?>
		<?php foreach($arrayColunasFaltantes as $valueColuna){ ?>
				<tr>
					<td><?=$valueColuna["tabela"]?></td>
					<td><?=$valueColuna["coluna"]?></td>
					<td><?=$valueColuna["tipo"]?></td>
					<td><?=$valueColuna["possivelSolucao"]?></td>
				</tr>
		<?php } ?>
	<?php } else { ?> 
			<tr>
				<td colspan="4">Nenhum campo inconsistente!</td>
			</tr>
	<?php } ?>
</table>
<br />
<table>
    <tr style="height: 30px; background-color:#EAEAEA;">
        <td colspan="5"><span style="font-size: 14px; font-weight:bold; ba">TABELAS COM TIPOS INCONSISTENTES</span></td> 
    </tr>
	<tr style="height: 30px; background-color:#EAEAEA;">
		<td><span style="font-size: 14px; font-weight:bold;">TABELA</span></td>
		<td><span style="font-size: 14px; font-weight:bold;">COLUNA</span></td>
		<td><span style="font-size: 14px; font-weight:bold;">TIPO DE DADO CORRETO</span></td>
		<td><span style="font-size: 14px; font-weight:bold;">TIPO DE DADO INCONSISTENTE</span></td>
		<td><span style="font-size: 14px; font-weight:bold;">POSS&Iacute;VEL SOLU&Ccedil;&Atilde;O</span></td>
    </tr>
	<?php if (count($arrayTiposDiferente)>0) { ?>
		<?php foreach($arrayTiposDiferente as $valueTipos){ ?>
				<tr>
					<td><?=$valueTipos["tabela"]?></td>
					<td><?=$valueTipos["coluna"]?></td>
					<td><?=$valueTipos["tipoCorreto"]?></td>
					<td><?=$valueTipos["tipoIncosistente"]?></td>
					<td><?=$valueTipos["possivelSolucao"]?></td>
				</tr>
		<?php } ?>
	<?php } else { ?> 
			<tr>
				<td colspan="4">Nenhum campo inconsistente!</td>
			</tr>
	<?php } ?>
</table>
<body>
</body>
</html>
