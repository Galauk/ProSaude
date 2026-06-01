<head>
	<script language=javascript>
		function imprimir() {
			window.print();
		}
	</script>

	<style type="text/css">
		.bordas {
			border:1px solid;
		}
	</style>
</head>


<?php
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
global $movimento_tipo;
//----------------  Dados Recebidos  ---------------->
$nr_movimento = $mov_nr_nota;
//$titulo="Exibição de Movimentação";    //       NOME DO RELATÓRIO

$sql = "SELECT movimento.mov_codigo, 
			   TO_CHAR(movimento.mov_data, 'DD/MM/YYYY') as mov_data,
			   case when mov_tipo = 'E' then 'Entrada'
	         	    when mov_tipo = 'S' then 'Saida'
	         	    when mov_tipo = 'T' then 'Transferencia'
	           end as tipomovimento ,
			   movimento.for_codigo, 
			   movimento.usu_codigo,
			   movimento.mov_desconto,
			   movimento.mov_observacao,
			   movimento.cond_codigo,
			   movimento.ate_codigo,
			   movimento.set_entrada,
			   movimento.set_saida,
			   movimento.mov_nr_nota,
			   TO_CHAR(movimento.mov_dt_nota, 'DD/MM/YY'),
			   movimento.usr_codigo,
			   TO_CHAR(movimento.mov_total_nota, '999,999,999D99') as mov_total_nota,
			   TO_CHAR(movimento.mov_data_inclusao, 'DD/MM/YY') as mov_data_inclusao,
			   case when mov_entrada = 'E' then 'Nota Fiscal'
				    when mov_entrada = 'A' then 'Ajuste'
				    when mov_entrada = 'M' then 'Emprestimo'
				    when mov_entrada = 'I' then 'Inventario'
				    when mov_entrada = 'D' then 'Doacao'
				    when mov_entrada = 'P' then 'Permuta'
				    when mov_entrada = 'O' then 'Outras Entradas'
				    when mov_entrada = 'V' then 'Devol. Setor'
	           end as tipoentrada,
			   case when mov_saida = 'S' then 'Saida de Consumo'
					when mov_saida = 'R' then 'Perdas'
					when mov_saida = 'M' then 'Emprestimo'
					when mov_saida = 'I' then 'Inventario'
					when mov_saida = 'P' then 'Permuta'
					when mov_saida = 'O' then 'Outras Saidas'
			   end as tiposaida, 
			   req_codigo, 
			   mov_requisitante
		  FROM movimento
		 WHERE movimento.mov_codigo = $mov_nr_nota";
$query = pg_query($sql);
//echo $sql;
while($row = pg_fetch_array($query)) {
	$pessoa = ' ';
	$mov_codigo	= $row['mov_codigo'];
	$mov_data	= $row['mov_data'];
	$mov_tipo	= $row['tipomovimento'];
	if (($row['for_codigo'] != null) and ($mov_tipo != 'Saida')){
		$for_codigo = $row['for_codigo'];
	}
	$usu_codigo	= $row['usu_codigo'];
	
	if ($usu_codigo != null)
	{
		$sqlpessoa = "SELECT u.usu_nome
						FROM usuario u
					   WHERE u.usu_codigo = $usu_codigo";
		$query=pg_query($sqlpessoa);
		$row4 = pg_fetch_array($query);
		$pessoa='Usuario: ' . $row4['usu_nome'];
	}
	$set_saida	= $row['set_saida'];

	if ($set_saida != null) {
		$sqlsetor = "SELECT s.set_nome
					   FROM setor s
					  WHERE s.set_codigo = $set_saida";
		$query=pg_query($sqlsetor);
		$row10=pg_fetch_array($query);
		$pessoa='C. Estocador Origem:  ' . $row10['set_nome'];
	}

	if ($for_codigo != null) {
		$sqlpessoa = "SELECT f.for_nome
						FROM fornecedor f
					   WHERE f.for_codigo = $for_codigo";
		$query = pg_query($sqlpessoa);
		$row3 = pg_fetch_array($query);
		if ($pessoa) {
			$pessoa .= '    /   Fornecedor: ' . $row3['for_nome'];
		}
		else {
			$pessoa='Fornecedor: ' . $row3['for_nome'];
		}
		
	}

	$mov_desconto	= $row['mov_desconto'];
	$mov_observacao	= $row['mov_observacao'];
	$cond_codigo	= $row['cond_codigo'];

	if ($cond_codigo != null) {
		$sqlcond = "SELECT c.cond_descricao
					  FROM condpagto c
					 WHERE c.cond_codigo = $cond_codigo";
		$query		= pg_query($sqlcond);
		$row7		= pg_fetch_array($query);
		$condicao	= $row7['cond_descricao'];
	}

	$ate_codigo		= $row['ate_codigo'];
	$set_entrada	= $row['set_entrada'];

	if ($set_entrada != null) {
		$sqlsetor = "SELECT s.set_nome
					   FROM setor s
					  WHERE s.set_codigo = $set_entrada";
		$query	= pg_query($sqlsetor);
		$row9	= pg_fetch_array($query);
		$setor	=$row9['set_nome'];
	}

	$mov_nr_nota	= $row['mov_nr_nota'];
	$mov_dt_nota	= $row['mov_data'];
	$usr_codigo		= $row['usr_codigo'];

	if ($usr_codigo != null) {
		$sqlusuario = "SELECT u.usr_nome
						 FROM usuarios u
						WHERE u.usr_codigo = $usr_codigo";
		$query		= pg_query($sqlusuario);
		$row13		= pg_fetch_array($query);
		$usuario	= $row13['usr_nome'];
	}

	$mov_total_nota    = $row['mov_total_nota'];
	$mov_data_inclusao = $row['mov_data_inclusao'];
	$mov_entrada       = $row['tipoentrada'];
	$mov_saida         = $row['tiposaida'];
	$req_codigo        = $row['req_codigo'];
	$mov_requisitante  = $row['mov_requisitante'];
	if ($mov_entrada != null){
		$ent_saida = $mov_entrada; 
	}
	else {
		$ent_saida = $mov_saida ;
	}

	//---------  Cabeçalho do Relatorio  ----------------->
	//echo "<body >";
	$Tit = "Relatório de Movimento";
	$dt_fim = $mov_data;
//	include "cabecalho.php";
	
	// cabeçalho
	  include_once '../global.php';
	  include_once COMUM.'/library/php/funcoes.inc.php';
	  cabecario_rel($Tit,false, false, "$mov_tipo em $mov_data");
	
	if ( ($mov_tipo == 'Saida') and ( ! empty($row[3])) )  {
		$sqlpessoa = "SELECT f.for_nome
						FROM fornecedor f
					   WHERE f.for_codigo = $row[for_codigo]";
		$query = pg_query($sqlpessoa);
		$row3 = pg_fetch_array($query);
		if ($pessoa) {
			$pessoa .= '    /   Doado para : ' . $row3['for_nome'];
		}			 
		else {
			$pessoa = 'Doado para : ' . $row3['for_nome'];
		}
	}
	   
	echo "
	<table  width='100%' cellspacing=0 cellpadding=0 border=0 style='font-size:12px;font-family:Tahoma,Arial;'>
		<tr>
			<td colspan='2'>Cod. Movimento:&nbsp; <b>$mov_nr_nota</b>&nbsp;&nbsp;&nbsp; em&nbsp;&nbsp;
		  		$mov_data&nbsp;&nbsp;&nbsp;&nbsp; <b>- $ent_saida</b></td>
			<td>Tipo:&nbsp; <b>$mov_tipo</b></td>
		</tr>
		<tr>
			<td>$pessoa</td>
			<td>".( $mov_tipo == "Entrada" ? "Atendimento: $ate_codigo" : "&nbsp;" )."</td>
			<td>setor: $setor&nbsp;</td>
		</tr>
		<tr>
			<td>OBS.:&nbsp; $mov_observacao&nbsp;</td>
			<td>&nbsp;</td>
			<td>Usuário: $usuario</td>
		</tr>
	</table>
	<!-- <p>Dados da NF/Docum:</p> -->
	<table border=0 width='100%' style='font-size:12px;font-family:Tahoma,Arial;'>
		<tr>
			<td width='40%'>NF/Doc. nr. <b>$mov_nr_nota</b>&nbsp; "; 
			if (! empty($req_codigo)) {
			echo " - Requis. nr. : " . $req_codigo . " ";
			}   
			echo "
			</td>
			<td width='30%'>Dt. Inclusao: $mov_data_inclusao  </td>
			<td width='10%'>".( $mov_tipo == "Entrada" ? "Desc.:&nbsp; $mov_desconto" : "&nbsp;" )."</td>
			<td width='10%'>".( $mov_tipo == "Entrada" ? "Valor: R$ $mov_total_nota" : "&nbsp;" )."</td>
			<td width='10%'>".( $mov_tipo == "Entrada" ? "Condição:&nbsp; $condicao" : "&nbsp;" )."</td>
		</tr>
		";
		if ($mov_tipo == 'Saida'){
			echo "<tr> <td width='30%'> Requisitante: $mov_requisitante </td> </tr> ";
		}
		echo "
	</table>
	";
}
	//------------------------------------------------------------------>
	// -> Exibição dos itens
	//------------------------------------------------------------------>
	
	$cabec = 1;
	$sqlit = "SELECT itens_movimento.ite_codigo,
					 itens_movimento.mov_codigo,
					 itens_movimento.pro_codigo,
					 produto.pro_nome,
					 itens_movimento.ite_vlrdesc,
					 itens_movimento.ite_lote,
					 ite_validade,
					 itens_movimento.ite_status,
					 to_char(coalesce(itens_movimento.ite_quantidade,0),'9999999') as qtde,
					 coalesce(itens_movimento.ite_vlrunit,0) as ite_vlrunit,
					coalesce(itens_movimento.ite_vlrunit,0) * coalesce(itens_movimento.ite_quantidade,0) as tot_item,
					 produto.pro_custo as custo_saida,
					 to_char(coalesce(produto.pro_custo,0) * coalesce(itens_movimento.ite_quantidade,0), '99,999,999.99') as vlr_tt_sai,
					 movimento.mov_tipo as movimentacao,
					 itens_movimento.ite_vlrtotal
				FROM itens_movimento, 
					 produto, 
					 movimento
			   WHERE produto.pro_codigo = itens_movimento.pro_codigo
			     AND itens_movimento.mov_codigo = movimento.mov_codigo
				 AND movimento.mov_codigo = '$nr_movimento'
			   ORDER BY itens_movimento.ite_codigo";
	//------------------------------------------------------------------>
	// -> Cálculo do total de Entrada
	//------------------------------------------------------------------>
	
	
	$sqlttit ="SELECT to_char(sum(itens_movimento.ite_vlrunit * itens_movimento.ite_quantidade), '99,999,999D99'),
					  to_char(sum(itens_movimento.ite_vlrdesc * itens_movimento.ite_quantidade), '99,999,999D99')
				 FROM itens_movimento, 
				 	  produto, 
					  movimento
				WHERE produto.pro_codigo = itens_movimento.pro_codigo
				  AND itens_movimento.mov_codigo = movimento.mov_codigo
				  AND movimento.mov_codigo = '$nr_movimento' ";
	
	
	//------------------------------------------------------------------>
	// -> Cálculo do total de Saída
	//------------------------------------------------------------------>
	
	
	
	$sqlttit_sai ="SELECT to_char(sum(produto.pro_custo * itens_movimento.ite_quantidade), '99,999,999D99') as vlr_tt_sai
					 FROM itens_movimento, 
					 	  produto, 
						  movimento
					WHERE produto.pro_codigo = itens_movimento.pro_codigo
					  AND itens_movimento.mov_codigo = movimento.mov_codigo
					  AND movimento.mov_codigo = '$nr_movimento' ";
	
	$query = pg_query($sqlit);
	$movimento_tipo = $mov_tipo;
	$total_it = 0;
	while($rowit = pg_fetch_array($query))
	{
		if( $rowit["movimentacao"] != 'T' ){
//			$movimento_tipo = $mov_tipo;

			$ite_codigo     =  $rowit['ite_codigo'];
			$mov_codigo     =  $rowit['mov_codigo'];
			$pro_codigo     =  $rowit['pro_codigo'];
			$pro_nome       =  $rowit['pro_nome'];
			$ite_vlrdesc    =  $rowit['ite_vlrdesc'];
			if ($ite_vlrdesc == null )
			{
				$ite_vlrdesc = '0.00';
			}
			$ite_lote       =  $rowit['ite_lote'];
			$ite_validade   =  $rowit['ite_validade'];
			$ite_status     =  $rowit['ite_status'];
			$ite_quantidade =  $rowit['qtde'];
			$ite_vlrunit    =  number_format($rowit['ite_vlrunit'],2);
			$tot_item       =  $rowit['tot_item'];
			if ($movimento_tipo != 'Entrada')
			{
				$ite_vlrunit = $rowit['custo_saida'];
				$tot_item    = $rowit['vlr_tt_sai'];
			}
			$ite_vlrtotal    =  $rowit['ite_vlrtotal'];
			$vlrunit = $ite_vlrtotal / $ite_quantidade;
			$totalitem = $vlrunit * $ite_quantidade;
	
			if ($cabec == 1)
			{
				echo "
				<table width='100%' cellpadding='1' cellspacing='1'>
					<tr>
						<td width='100%' align='center' colspan='8' class='bordas'><b>Itens do Movimento</b></td>
					</tr>
					<tr>
						<td width='7%' class='bordas'><strong>Codigo</strong></td>
						<td width='31%' class='bordas'><strong>Descricao</strong></td>
						<td width='8%' class='bordas'><strong>Lote</strong></td>
						<td width='12%' class='bordas'><strong>Validade</strong></td>
						<td width='6%' align = 'right' class='bordas'><strong>Qtde</strong></td>
						<td width='10%' align = 'right' class='bordas'><strong>Vlr. Unit</strong></td>
						<td width='13%' align = 'right' class='bordas'><strong>Vlr. Total</strong></td>
						<td width='13%' align = 'right' class='bordas'><strong>Vlr. Desconto</strong></td>
					</tr> ";
				$cabec = 0;
			}
			
			//alteracao <td width='13%' align = 'right'>$tot_item</td>
			echo "
				<tr>
					<td width='7%' class='bordas'>$pro_codigo</td>
					<td width='31%' class='bordas'>$pro_nome</td>
					<td width='8%' class='bordas'>$ite_lote</td>
					<td width='12%' class='bordas'>".formatarData($ite_validade)."</td>
					<td width='6%' align = 'right' class='bordas'>$ite_quantidade</td>
					<td width='10%' align = 'right' class='bordas'>".$ite_vlrunit."</td>
					<td width='13%' align = 'right' class='bordas'>".$ite_vlrtotal."</td>
					<td width='13%' align = 'right' class='bordas'>".moeda($ite_vlrdesc)."</td>
				</tr>" ;
			$valorTotal = $valorTotal + $ite_vlrtotal;
			$total_it = $total_it + ($ite_quantidade * numero($ite_vlrunit));
			$total_desc += moeda($ite_vlrdesc);
		}
		else
		{
			$movimento_tipo = $mov_tipo;
			
			$ite_codigo     =  $rowit['ite_codigo'];
			$mov_codigo     =  $rowit['mov_codigo'];
			$pro_codigo     =  $rowit['pro_codigo'];
			$pro_nome       =  $rowit['pro_nome'];
			$ite_vlrdesc    =  $rowit['ite_vlrdesc'];
			if ($ite_vlrdesc == null )
			{
				$ite_vlrdesc = 0.00;
			}
			$ite_lote       =  $rowit['ite_lote'];
			$ite_validade   =  $rowit['ite_validade'];
			$ite_status     =  $rowit['ite_status'];
			$ite_quantidade =  $rowit['qtde'];
			$ite_vlrunit    =  $rowit['ite_vlrunit'];
			$tot_item       =  $rowit['tot_item'];
			if ($movimento_tipo != 'Entrada')
			{
				$ite_vlrunit = $rowit['custo_saida'];
				$tot_item    = $rowit['vlr_tt_sai'];
			}
	
			if ($cabec == 1)
			{
				echo "
				<table width='100%' style='font-size:12px;font-family:Tahoma,Arial;'>
					<tr>
						<td width='100%' align='center' colspan='8' class='bordas'><b>Itens do Movimento</b></td>
					</tr>
					<tr>
						<td width='7%' class='bordas'>Codigo</td>
						<td width='60%' class='bordas' colspan='3'>Descricao</td>
						<td width='8%' class='bordas'>Lote</td>
						<td width='10%' align = 'center' class='bordas'>Validade</td>
						<td width='8%' align = 'center' class='bordas'>Qtde</td>
						<td width='8%' align = 'center' class='bordas'>Entregue</td>
					</tr>";
				$cabec = 0;
			}
			echo "
				<tr>
					<td width='7%' class='bordas'>$pro_codigo</td>
					<td width='60%' class='bordas' colspan='3'>$pro_nome</td>
					<td width='8%' class='bordas'>$ite_lote</td>
					<td width='10%' class='bordas'>".formatarData($ite_validade)."</td>
					<td width='8%' align = 'right' class='bordas'>$ite_quantidade&nbsp;&nbsp;&nbsp;</td>
					<td width='8%' align = 'right' class='bordas'>[<u>&#175;</u>]&nbsp;</td>
				</tr>" ;
		}//fim do if
	}//fim do while
	if ($mov_tipo != 'Entrada')
	{
		$query = pg_query($sqlttit_sai);
		while($row_total_sai = pg_fetch_array($query))
		{
			//$total_it = $row_total_sai['vlr_tt_sai'];
			if ($total_desc == null )
			{
				$total_desc = 0;
			}
		}
	
	}
	else
	{
		//$query = pg_query($sqlttit);
		//while($row_total = pg_fetch_array($query))
		//{
			//$total_it       =  $row_total[0];
			//$total_desc     =  $row_total[1];
			//if ($total_desc == null )
			//{
				//$total_desc = 0;
			//}
		//}
	}

//	$total_it = number_format($total_it, 2, ',', '.');

	echo " 
		<tr>
			<td width='74%' align = 'right' colspan='6' class='bordas'><b>Total dos Itens</td>
			<td width='13%' align = 'right' class='bordas'><b>".moeda($valorTotal)."</b></td>
			<td width='13%' align = 'right' class='bordas'><b>".moeda($total_desc)."</b></td>
		</tr>
	</table>
</body> ";
?>