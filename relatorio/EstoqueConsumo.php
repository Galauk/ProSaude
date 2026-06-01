<!-- --------------------------------------------------------------
       Funções javascript
------------------------------------------------------------------ -->

<SCRIPT Language="Javascript">
	function imprimir() {
		window.print();
	}
</script>

<body>

<?php

//------------------------------------------------------------------>
// -> Includes
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

echo "<link href='../estilo.css' rel='stylesheet' type='text/css'>";

//----------------  Monta Dados Recebidos  ---------------->

//echo "Data INICIAL->".$dt_inicial."<br>";
//echo "Data FINAL  ->".$dt_final."<br>";
//echo "CentroEstoq->".$CE_codigo."<br>";
//echo "Setor  ->".$set_codigo."<br>";
//echo "Grupo  ->".$gru_codigo."<br>";
//echo "Produto->".$pro_codigo."<br>";


$titulo="RELATÓRIO DE CONSUMO";    //       NOME DO RELATÓRIO

if ($CE_codigo) {
    $sql = "SELECT setor.set_nome " .
           "  FROM setor                      " .
           " WHERE setor.set_estoque = 'S'    " .
           "   AND setor.set_codigo = $CE_codigo";
    $row=pg_fetch_row(pg_query($sql));
    $CENome=$row[0];
} else {  $CENome = "TODOS";  }

if ($set_codigo) {
    $sql = "SELECT setor.set_nome " .
           "  FROM setor                      " .
           " WHERE setor.set_codigo = $set_codigo";
    $row=pg_fetch_row(pg_query($sql));
    $SetorNome=$row[0];
} else {  $SetorNome = "TODOS";  }

if ($gru_codigo) {
    $sql = "SELECT grupo.gru_nome " .
           "  FROM grupo                      " .
           " WHERE grupo.gru_codigo = $gru_codigo";
    $row=pg_fetch_row(pg_query($sql));
    $GrupoNome=$row[0];
} else {  $GrupoNome = "TODOS";  }

if ($pro_codigo) {
    $sql = "SELECT pro_nome " .
           "  FROM produto                    " .
           " WHERE produto.pro_codigo = $pro_codigo";
    $row=pg_fetch_row(pg_query($sql));
    $ProdutoNome=$row[0];
} else {  $ProdutoNome = "TODOS";  }

//------------------------------------------------------------------>
// -> Funções php
//------------------------------------------------------------------>

function cabeca($Tit, $dtIni, $dtFin, $CE, $Setor, $Grupo, $Produto, $Cab) {

//--->        Cabeçalho do Sistema
	if ($Cab == 0) {
		include "cabecalho.php";
		echo "<table style=\"font-size:11px;font-family:courier,vardana,arial;\" width=100% align=center cellspacing=0 cellpadding=1 border=0 topmargin=0 leftmargin=0>\n";
	}

//--->        Cabeçalho dos Dados

	if ($Cab == 1) {
		//echo "<table style=\"font-size:11px;font-family:courier,vardana,arial;\" width=100% align=center cellspacing=0 cellpadding=0 border=0 topmargin=0 leftmargin=0>\n";
		echo " <tr  style=\"font-weight:bold\">\n";
		echo "  <td width= 40px>Produto   </td>\n";
		echo "  <td colspan='2'>Tipo de Consumo   </td>\n";
		echo "  <td align=right>Qtde&nbsp;&nbsp; </td>\n";
		echo "  <td>&nbsp;&nbsp;          </td>\n";
		echo "  <td align=center>Pr Médio </td>\n";
		echo "  <td align=center>Total    </td>\n";
		echo " </tr>\n";
	}
}

//----------------  Rotina de Impressão  ---------------->

if ($CE_codigo) {
	$LclPrMedio=$CE_codigo;
} else {
	$row=pg_fetch_row(pg_query("select * from conf_estoque"));
	$LclPrMedio=$row[1];
}

if ($SetorNome == "TODOS" && $set_junto != 1 )
{
	$sql_set = "SELECT setorconsumo.set_codigo, 
					   setorconsumo.set_nome
				  FROM setor AS setorconsumo, 
				  	   v_movimentacao
				 WHERE v_movimentacao.codsetorsolicit = setorconsumo.set_codigo 
				 GROUP BY setorconsumo.set_codigo, 
				 	   setorconsumo.set_nome";
	$res_set = pg_query($sql_set);

	cabeca($titulo, $dt_inicial, $dt_final, $CENome, $SetorNome, $GrupoNome, $ProdutoNome, '0');
	$sem_consumo = "";
	
	// Zebragem
	$controle = 0;

	while( $row=pg_fetch_row($res_set) )
	{
		$set_codigo=$row[0];
		$set_nome=$row[1];
		
		$sql = "SELECT distinct produto.pro_nome, 
					   sum(ite_quantidade), 
					   produto.pro_codigo,
					   CASE v_movimentacao.tipomovim
							WHEN 'S' THEN 'Saida de Consumo'
							WHEN 'I' THEN 'Inventario'
							WHEN 'M' THEN 'Emprestimo'
							WHEN 'P' THEN 'Permuta'
							WHEN 'R' THEN 'Perdas'
							WHEN 'O' THEN 'Outras Saidas'
							WHEN 'E' THEN 'Nota Fiscal de Compra'
							WHEN 'A' THEN 'Ajuste'
							WHEN 'D' THEN 'Dispensação'
							WHEN 'V' THEN 'Devol. Setor'
							WHEN 'T' THEN 'Transferência'
					   		ELSE 'Indefinido'
					   END AS tipo_consumo
				  FROM v_movimentacao, 
				  	   produto " ;
		if ( !empty($gru_codigo) ) {  $sql.= ", grupo " ; }
		if ( !empty($set_codigo) ) {  $sql.= ", setor as setorconsumo"; }
		if ( !empty($CE_codigo) )  { $sql.= ", setor  as centroestoc" ; }
		$sql.=" WHERE v_movimentacao.pro_codigo = produto.pro_codigo ";
		if ( !empty($CE_codigo) )  {  $sql.=" AND v_movimentacao.codsetor = centroestoc.set_codigo ";}
		if ( !empty($set_codigo) ) {  $sql.=" AND v_movimentacao.codsetorsolicit = setorconsumo.set_codigo ";}
		if ($gru_codigo) {  $sql.=" AND produto.gru_codigo = grupo.gru_codigo ";}
		$sql.=" AND v_movimentacao.sinal = '-' " ;
		if ( !empty($dt_inicial) ) {  $sql.=" AND v_movimentacao.mov_data  between to_date('$dt_inicial', 'dd/mm/yyyy') and  to_date('$dt_final', 'dd/mm/yyyy')  " ; }
		if ( !empty($set_codigo) ) { $sql .= " AND v_movimentacao.codsetorsolicit= $set_codigo " ; }
		if ( !empty($gru_codigo) ) { $sql .= " AND grupo.gru_codigo   = $gru_codigo " ; }
		if ( !empty($CE_codigo) )  { $sql .= " AND v_movimentacao.codsetor= $CE_codigo  " ; }
		if ( !empty($pro_codigo) ) {  $sql.="  AND produto.pro_codigo = $pro_codigo " ; }
		//	if ( !empty($tipomovim) ) {  $sql.="  AND v_movimentacao.tipomovim = '$tipomovim' " ; }
		if ( !empty($tipomovim) and ($tipomovim <> 'X') ) {  $sql.="  AND v_movimentacao.tipomovim = '$tipomovim' " ; }
		if ( !empty($tipomovim) and ($tipomovim == 'X') ) {  $sql.="  AND (v_movimentacao.tipomovim = 'S' OR 
	                                                                   v_movimentacao.tipomovim = 'T' )" ; }
		//else
		$sql.=" GROUP BY produto.pro_nome, 
					  produto.pro_codigo, 
					  v_movimentacao.tipomovim  
				ORDER BY produto.pro_nome  ";
                //echo "<pre>".$sql."</pre>";

		$query = pg_query($sql);
		
		$lin=999;
		$total = 0;
		
		$qtd_prod = 0;
		$prod_aux = "";
		while($row = pg_fetch_row($query)) 
		{
			if ($lin == 999)
			{
				echo "<tr><td colspan='8'><b>*".$set_nome."</b></td></tr>";
				cabeca($titulo, $dt_inicial, $dt_final, $CENome, $SetorNome, $GrupoNome, $ProdutoNome, '1');
				$lin=9;
			}
			$PrMed=pg_fetch_array(pg_query("select verifica_preco($row[2], $LclPrMedio, '$dt_final')"));
			if( $prod_aux != $row[0] )
			{
				$qtd_prod++;
			}
			$prod_aux = $row[0];
			
			$c1 = "";
			$c2 = "#A6A6A6";
			
			if ($controle == 0) {
			  $cor = $c1;
			  $controle++;
			} else {
			  $cor = $c2;
			  $controle = 0;
			}
			echo " <tr bgcolor='$cor'>\n";
			echo "  <td>".substr($row[0],0,40)."</td>\n";
			echo "  <td colspan='2'>".$row[3]."</td>\n";
			echo "  <td align=right>".number_format($row[1],0,",","")."     </td>\n";
			echo "  <td>&nbsp;&nbsp;            </td>\n";
			echo "  <td align=center>".number_format($PrMed[0],4,",",".")."    </td>\n";
			echo "  <td align=right>".number_format($PrMed[0]*$row[1],2,",",".")."</td>\n";
			echo " </tr>\n";
			$lin++;
			$total = $total + ($row[1] * $PrMed[0]);
		}
		if (pg_num_rows($query) != 0) 
		{
			echo " <tr>\n";
			echo "  <td>Quantidade de Itens: ".number_format($qtd_prod,0,",",".")."</td>\n";
			echo "  <td>                        </td>\n";
			echo "  <td>                        </td>\n";
			echo "  <td>Sub-Total                 </td>\n";
			echo "  <td>                        </td>\n";
			echo "  <td>                            </td>\n";
			echo "  <td colspan='2' align='right'>".number_format($total,2,",",".")."</td>\n";
			echo " </tr>\n";
			echo "<tr><td colspan='8'><hr></td></tr>";
			$total_geral += $total;
		}
	}
	
	echo " <tr>\n";
	echo "  <td>                        </td>\n";
	echo "  <td>                        </td>\n";
	echo "  <td>                        </td>\n";
	echo "  <td><b>Total Geral</b>      </td>\n";
	echo "  <td>                        </td>\n";
	echo "  <td>                            </td>\n";
	echo "  <td colspan='2' align='right'><b>".number_format($total_geral,2,",",".")."</b></td>\n";
	echo " </tr>\n";
	echo "</table><br />";
	//echo "<b>Setores sem consumo:</b><br />".$sem_consumo;
}
else
{
	$sql = "SELECT distinct produto.pro_nome, 
				   sum(ite_quantidade), 
				   produto.pro_codigo,
				   CASE
						WHEN v_movimentacao.tipomovim = 'S' THEN 'Saida de Consumo'
						WHEN v_movimentacao.tipomovim = 'I' THEN 'Inventario'
						WHEN v_movimentacao.tipomovim = 'M' THEN 'Emprestimo'
						WHEN v_movimentacao.tipomovim = 'P' THEN 'Permuta'
						WHEN v_movimentacao.tipomovim = 'R' THEN 'Perdas'
						WHEN v_movimentacao.tipomovim = 'O' THEN 'Outras Saidas'
						WHEN v_movimentacao.tipomovim = 'E' THEN 'Nota Fiscal de Compra'
						WHEN v_movimentacao.tipomovim = 'A' THEN 'Ajuste'
						WHEN v_movimentacao.tipomovim = 'D' THEN 'Dispensação'
						WHEN v_movimentacao.tipomovim = 'V' THEN 'Devol. Setor'
						WHEN v_movimentacao.tipomovim = 'T' THEN 'Transferência'
						WHEN v_movimentacao.tipomovim = 'X' THEN 'Saida de Consumo + Transferência'
						ELSE 'Indefinido'
				   END AS tipo_consumo
			  FROM v_movimentacao, 
			  	   produto " ;
	if ( !empty($gru_codigo) ) {  $sql.= ", grupo " ; }
	if ( !empty($set_codigo) ) {  $sql.= ", setor as setorconsumo"; }
	if ( !empty($CE_codigo) )  { $sql.= ", setor  as centroestoc" ; }
	$sql.=" WHERE v_movimentacao.pro_codigo = produto.pro_codigo ";
	if ( !empty($CE_codigo) )  {  $sql.=" AND v_movimentacao.codsetor = centroestoc.set_codigo ";}
	if ( !empty($set_codigo) ) {  $sql.=" AND v_movimentacao.codsetorsolicit = setorconsumo.set_codigo ";}
	if ( !empty($gru_codigo) ) {  $sql.=" AND produto.gru_codigo = grupo.gru_codigo ";}
	$sql.=" AND v_movimentacao.sinal = '-' " ;
	if ( !empty($dt_inicial) ) {  $sql.=" AND v_movimentacao.mov_data  between to_date('$dt_inicial', 'dd/mm/yyyy') and  to_date('$dt_final', 'dd/mm/yyyy')  " ; }
	//if ( !empty($set_junto) ) { $sql .= " /* " ; }
	if ( !empty($set_codigo) ) { $sql .= " AND v_movimentacao.codsetorsolicit= $set_codigo " ; }
	//if ( !empty($set_junto) ) { $sql .= " */ " ; }
	if ( !empty($gru_codigo) ) { $sql .= " AND grupo.gru_codigo   = $gru_codigo " ; }
	if ( !empty($CE_codigo) )  { $sql .= " AND v_movimentacao.codsetor= $CE_codigo  " ; }
	if ( !empty($pro_codigo) ) {  $sql.="  AND produto.pro_codigo = $pro_codigo " ; }
	if ( !empty($tipomovim) and ($tipomovim <> 'X') ) {  $sql.="  AND v_movimentacao.tipomovim = '$tipomovim' " ; }
	if ( !empty($tipomovim) and ($tipomovim == 'X') ) {  $sql.="  AND (v_movimentacao.tipomovim = 'S' OR 
	                                                                   v_movimentacao.tipomovim = 'T' )" ; }
	//else
	$sql.=" GROUP BY produto.pro_nome,
                produto.pro_codigo, v_movimentacao.tipomovim  ";
	$sql.=" ORDER BY produto.pro_nome  ";
	echo "<pre>".$sql."</pre>";
	
	$query=pg_query($sql);
	
	if (pg_num_rows($query) == 0) 
	{
		echo "<table width=100% cellspacing=0 cellpadding=3 border=0 align=center>";
		echo " <div><div style='padding-top:122px;'>";
		echo "  <tr><td colspan=2 align=center>NÃO TEM DADOS PARA ESTES PARÂMETROS</td></tr>";
		echo "  <tr><td colspan=2>&nbsp;</td></tr><tr><td colspan=2>&nbsp;</td></tr>";
		echo "  <tr><td align=right width=50%>".str_pad('INICIAL', 15  , '___')."</td><td width=50%>$dt_inicial </td></tr>";
		echo "  <tr><td align=right width=50%>".str_pad('FINAL', 15  , '___')."</td><td width=50%>$dt_final     </td></tr>";
		echo "  <tr><td align=right width=50%>".str_pad('CentroEstoq', 16  , '___')."</td><td width=50%>$CENome </td></tr>";
		echo "  <tr><td align=right width=50%>".str_pad('Setor', 16  , '___')."</td><td width=50%>$SetorNome    </td></tr>";
		echo "  <tr><td align=right width=50%>".str_pad('Grupo', 15  , '___')."</td><td width=50%>$GrupoNome    </td></tr>";
		echo "  <tr><td align=right width=50%>".str_pad('Produto', 15  , '___')."</td><td width=50%>$ProdutoNome</td></tr>";
		echo " </div>";
		echo "</table>";
	}
	$lin=999;
	$qtd_prod = 0;
	$prod_aux = "";
	
        // Zebragem
	$controle = 0;
        
        while($row=pg_fetch_row($query)) 
	{
		if ($lin== 999)
		{
			cabeca($titulo, $dt_inicial, $dt_final, $CENome, $SetorNome, $GrupoNome, $ProdutoNome, '0');
			cabeca($titulo, $dt_inicial, $dt_final, $CENome, $SetorNome, $GrupoNome, $ProdutoNome, '1');
			$lin=9;
		}
		$PrMed=pg_fetch_array(pg_query("select verifica_preco($row[2], $LclPrMedio, '$dt_final')"));
		
		if( $prod_aux != $row[0] )
		{
			$qtd_prod++;
		}
                
                $c1 = "";
                $c2 = "#A6A6A6";
                
                if ($controle == 0) {
                  $cor = $c1;
                  $controle++;
                } else {
                  $cor = $c2;
                  $controle = 0;
                }
                
		$prod_aux = $row[0];
		echo " <tr bgcolor='$cor'>\n";
		echo "  <td>".substr($row[0],0,40)."</td>\n";
		echo "  <td colspan='2'>".$row[3]."</td>\n";
		echo "  <td align=right>".number_format($row[1],0,",",".")."     </td>\n";
		echo "  <td>&nbsp;&nbsp;            </td>\n";
		echo "  <td align=center>".number_format($PrMed[0],4,",",".")."    </td>\n";
		echo "  <td align=right>".number_format($PrMed[0]*$row[1],2,",",".")."</td>\n";
		echo " </tr>\n";
		$lin++;
		$total = $total + ($row[1] * $PrMed[0]);
	}
	echo " <tr><td colspan='7'><hr></td></tr>\n";
	echo " <tr>\n";
	echo "  <td>Quantidade de Itens: ".number_format($qtd_prod,0,",",".")."</td>\n";
	echo "  <td>                        </td>\n";
	echo "  <td>                        </td>\n";
	echo "  <td>Total Geral                 </td>\n";
	echo "  <td>                        </td>\n";
	echo "  <td>                            </td>\n";
	echo "  <td colspan='2' align='right'>".number_format($total,2,",",".")."</td>\n";
	echo " </tr>\n";
	echo "</table>";
}

echo "</body>";
?>
