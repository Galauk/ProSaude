<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	$data = date("d/m/Y");
	$pro_codigo = $_GET['pro_codigo'];
	$set_codigo = $_GET['set_codigo'];
	
	$select = "select p.pro_codigo,
					  p.pro_nome,
					  s.sal_lote,
					  to_char(s.sal_validade, 'dd/mm/yyyy') as validade,
					  sal_qtde as estoque,
					  sal_dose_lote
				 from produto p
				 join saldo s
				   on p.pro_codigo = s.pro_codigo
				where p.pro_codigo = $pro_codigo
				  and s.set_codigo = $set_codigo
				  and s.sal_qtde > 0
				order by sal_validade ASC";
	$exec_select = pg_query($select);
	if(pg_num_rows($exec_select) > 0)
	{
		while($linha = pg_fetch_array($exec_select))
		{
			$aux .= "<option value='$linha[sal_lote]:$linha[validade]:$linha[estoque]:$linha[sal_dose_lote]' id='$linha[estoque]'>Lote: $linha[sal_lote] - Validade: $linha[validade] - Quantidade: $linha[estoque]</option>;";
		}
		echo $aux;
	} else {
		echo "not";
	}
?>