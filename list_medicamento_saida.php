<?php
/**
 * @brief       mostra o consumo de medicamentos nos últimos 6 meses
 * esta página é chamada pela funcao abre_hist() 
 * que está na página dispensa_medicamentos.php 
 * esta pagina recebe o id_login e usu_codigo por GET
 * 30/05 foi adicionado campos no resultado do histórico
*/
?>
<script>

function getpaciente(nome,codigo,nascimento,mae,cidade) {
   window.opener.pacientes(nome,codigo,nascimento,mae,cidade);
  window.close();
}
</script>
<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>


	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	cabecario();
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
	 reglog($id_login,"Entrando em LIST_PACIENTES");
//------------------------------------------------------------------>
//list_medicamento_saida.php?id_login=168&usu_codigo=962158

//formata data para 6 meses atrás
$ano_inicial = date("Y");
$mes_inicial = date("n");
$usu_codigo = $_GET["usu_codigo"];

for ($i=1;$i<=5;$i++) {
    $mes_inicial = $mes_inicial - 1;
    if ($mes_inicial == 0) {
        $mes_inicial = 12;
        $ano_inicial = $ano_inicial - 1;
    }
}
$dt_inicial = $ano_inicial.'-'.$mes_inicial."-01";

 if(empty($acao)) {

//
//-> Listando


  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Medicamentos Retirados pelo Paciente nos Últimos 6 Meses</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9>
		<td width=110 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Data</td>
		<td width=270 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Produto</td>
		<td width=50 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Qtd</td>
		<td width=90 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Setor</td>
		<td width=130 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Tipo de Consumo</td>
		<td width=90 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Qtd Dias</td>
		<td width=90 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Total de Dias</td>
		<td width=90 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Próxima Liberação</td>
	      </tr>";

   $sql="SELECT to_char(v_movimentacao.mov_data, 'DD/MM/YYYY'), v_movimentacao.pro_nome, 
		v_movimentacao.ite_quantidade, v_movimentacao.setor, 
		CASE WHEN v_movimentacao.tipomovim = 'S' THEN 'Saida de Consumo' 
		WHEN v_movimentacao.tipomovim = 'I' THEN 'Inventario' 
		WHEN v_movimentacao.tipomovim = 'M' THEN 'Emprestimo' 
		WHEN v_movimentacao.tipomovim = 'P' THEN 'Permuta' 
		WHEN v_movimentacao.tipomovim = 'R' THEN 'Perdas' 
		WHEN v_movimentacao.tipomovim = 'O' THEN 'Outras Saidas' 
		WHEN v_movimentacao.tipomovim = 'E' THEN 'Nota Fiscal de Compra' 
		WHEN v_movimentacao.tipomovim = 'A' THEN 'Ajuste' 
		WHEN v_movimentacao.tipomovim = 'D' THEN 'Doacao' 
		WHEN v_movimentacao.tipomovim = 'V' THEN 'Devol. Setor' 
		WHEN v_movimentacao.tipomovim = 'T' THEN 'Transferência' 
		ELSE 
			'Indefinido' 
		END AS tipo_consumo,
		itens_movimento.ite_qtde_dia, itens_movimento.ite_posologia,
		itens_movimento.ite_detalhes_tratamento, itens_movimento.ite_observacoes
		FROM v_movimentacao 
		LEFT JOIN itens_movimento 
			ON itens_movimento.mov_codigo = v_movimentacao.mov_codigo
	WHERE v_movimentacao.usu_codigo = ".$usu_codigo." 
	AND v_movimentacao.mov_data > '".$dt_inicial."' 
	AND itens_movimento.pro_codigo = v_movimentacao.pro_codigo ";
	//print $sql;
	$res_sql = pg_query($sql);
	$cor = 0;
	$cor1 = "F2F5F3";
     while($row=pg_fetch_array($res_sql)) {
        $cor++;
        //separa a data da movimentacao para criar a data final, 
        //de acordo com a qtd de dias que o medicamento sera utilizado
        $temp_dia = substr($row[0],0,2);
        $temp_mes = substr($row[0],3,2);
        $temp_ano = substr($row[0],6,4);
	$dt_liberacao = date("d/m/Y", mktime(0, 0, 0, $temp_mes, $temp_dia+((int)$row[2]/$row[5]), $temp_ano));
	
	echo "<tr ";
	if( $cor%2 == 0 ){ print "bgcolor='$cor1'"; }
	echo ">
		<td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[0]</td>
		<td width=270 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[1]</td>
		<td align=right style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".number_format($row[2],0,',','.')."</td>
		<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[3]</td>
		<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[4]&nbsp;</td>
		<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' align='right'>$row[5]&nbsp;</td>
		<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' align='right'>".(int)($row[2]/$row[5])."&nbsp;</td>
		<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$dt_liberacao</td>
	      </tr>";
	echo "<tr ";
	if( $cor%2 == 0 ){ print "bgcolor='$cor1'"; }
	echo ">
		<td align=left style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' colspan='2'><b>Posologia:</b><br />".$row[6]."</td>
		<td align=left style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' colspan='3'><b>Detalhes do tratamento:</b><br />".$row[7]."</td>
		<td align='left' style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' colspan='3'><b>Obsevações:</b><br />".$row[8]."</td>
	      </tr>";
     }
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
}

?>