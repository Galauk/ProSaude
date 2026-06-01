<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	$common = new commonClass();
	echo "<link href=\"estilo.css\" rel=\"stylesheet\" type=\"text/css\">";
	$gru_codigo = ($_GET[gru_codigo] != 0 ? $_GET[gru_codigo] : "");
	$set_codigo = ($_GET[set_codigo] != 0 ? $_GET[set_codigo] : "");

	if($acao == "buscar")
	{
		if($texto == "atualizar")
		{
			$sql = "SELECT a.inv_codigo, 
						   to_char(a.inv_data, 'dd/mm/yyyy') as data, 
						   a.inv_responsavel, 
						   a.inv_equipe, 
						   b.gru_nome, 
						   c.set_nome, 
						   a.gru_codigo, 
						   a.set_codigo, 
						   a.usr_codigo, 
						   a.inv_data
					  FROM inventario a, 
					  	   grupo b, 
					  	   setor c
					 WHERE a.gru_codigo = b.gru_codigo
					   AND a.set_codigo = c.set_codigo
					   AND a.inv_data = '$data'
					   AND a.gru_codigo = $gru_codigo
					   AND a.set_codigo = $set_codigo
					   AND a.inv_codigo not in (SELECT inv_codigo 
					   							  FROM movimento 
					   							 WHERE inv_codigo = a.inv_codigo)";
		} else if($texto == "acuracia"){
			$sql = "SELECT a.inv_codigo, 
						   to_char(a.inv_data, 'dd/mm/yyyy') as data, 
						   a.inv_responsavel, 
						   a.inv_equipe, 
						   b.gru_nome, 
						   c.set_nome, 
						   a.gru_codigo, 
						   a.set_codigo, 
						   a.usr_codigo, 
						   a.inv_data
					  FROM inventario a, 
					  	   grupo b, 
					  	   setor c
					 WHERE a.gru_codigo = b.gru_codigo
					   AND a.set_codigo = c.set_codigo"
					   .($data == "" ? "" : " AND a.inv_data = '$data'")
					   .($gru_codigo == "" ? "" : " AND a.gru_codigo = $gru_codigo")
					   .($set_codigo == "" ? "" : " AND a.set_codigo = $set_codigo");
		}else {
			$sql = "SELECT a.inv_codigo, 
						   to_char(a.inv_data, 'dd/mm/yyyy') as data, 
						   a.inv_responsavel, 
						   a.inv_equipe, 
						   b.gru_nome, 
						   c.set_nome, 
						   a.gru_codigo, 
						   a.set_codigo, 
						   a.usr_codigo, 
						   a.inv_data
					  FROM inventario a, 
					  	   grupo b, 
						   setor c
					 WHERE a.gru_codigo = b.gru_codigo
					   AND a.set_codigo = c.set_codigo"
					   .($data == "" ? "" : " AND a.inv_data = '$data'")
					   .($gru_codigo == "" ? "" : " AND a.gru_codigo = $gru_codigo")
					   .($set_codigo == "" ? "" : " AND a.set_codigo = $set_codigo").
					  " AND a.inv_codigo not in (SELECT inv_codigo 
					   							  FROM movimento 
					   							 WHERE inv_codigo = a.inv_codigo)
					 ORDER BY a.inv_data DESC";
			/*$sql = "select a.inv_codigo, to_char(a.inv_data, 'dd/mm/yyyy') as data, a.inv_responsavel, a.inv_equipe, b.gru_nome, c.set_nome, a.gru_codigo, a.set_codigo, a.usr_codigo, a.inv_data
						from inventario a, grupo b, setor c
						where a.gru_codigo = b.gru_codigo
						and a.set_codigo = c.set_codigo
						and a.inv_data = '$data'
						and a.gru_codigo = $gru_codigo
						and a.set_codigo = $set_codigo
						and a.inv_codigo not in (select inv_codigo from movimento where inv_codigo = a.inv_codigo)";*/			
		}
		$exec = pg_query($sql);
		echo "<table class=\"lista\">";
			echo "<tr bgcolor=\"#ffffff\">";
				echo "<th>";
					echo "C&oacute;digo";
				echo "</th>";
				echo "<th>";
					echo "Data do Invent&aacute;rio";
				echo "</th>";
				echo "<th>";
					echo "Respons&aacute;vel";
				echo "</th>";
				echo "<th>";
					echo "Equipe";
				echo "<th>";
					echo "Grupo";
				echo "</th>";
				echo "<th>";
					echo "Setor";
				echo "</th>";
				echo "<th colspan='2'>&nbsp;</th>";
			echo "</tr>";
		while($linha = pg_fetch_array($exec))
		{
			echo "<tr>";
				for($i = 0; $i < (pg_num_fields($exec)-4); $i++)
				{
					echo "<td>";
						echo $linha[$i];
					echo "</td>";
				}
				echo "<td width=250>";
					if($texto == "relatorio")
					{
						echo $common->commonButton("Relat&oacute;rio Diferen&ccedil;as", null, "report.png", "onclick=\"passar($linha[0]);\"");
						echo "</td>
							  <td width=250>".
						$common->commonButton("Gerar Movimenta&ccedil;&atilde;o", null, "gerar.png", "onclick=\"atualizar($linha[0]);\"")
						."</td>";
					} else if($texto == "buscar"){
						echo "<input type=\"image\" src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg\" onclick=\"cadastro($linha[0]);\">";
					} else if($texto == "atualizar"){
						echo "<input type=\"image\" src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg\" onclick=\"atualizar($linha[0]);\">";
					} else if ($texto == "acuracia"){
						echo $common->commonButton("Relat&oacute;rio de Acur&aacute;cia", null, "report.png", "onclick=\"relAcuracia($linha[0]);\"");
						//echo "<input type=\"image\" src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg\" onclick=\"relAcuracia($linha[0]);\">";
					}
				echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
	if(pg_num_rows($exec) == 0)
	{
		echo "N&atilde;o existe registro!";		
	}
	
?>
