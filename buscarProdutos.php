<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
    cabecario();
echo "<body onLoad=\"document.getElementById('busca').focus()\">";
	echo "<fieldset>";
		echo "<legend>Op&ccedil;&otilde;es de Busca</legend>";
		echo "<table>";
			echo "<tr>";
				echo "<td>";
					echo "Produto";
				echo "</td>";
				echo "<td  width=\"380\">";
					echo "<input type=\"text\" name=\"busca\" id=\"busca\" size=\"60\" class=\"box\" onchange=\"pesqCad2(this.value,'buscarProdutos.php','produto',$_GET[parametro]);\">";
				echo "</td>";
				echo "<td>";
					echo "<input type=\"image\" src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg\" onclick=\"pesqCad2(document.getElementById('busca').value,'buscarProdutos.php','produto',$_GET[parametro])\">";
				echo "</td>";
			echo "</tr>";
		echo "</table>";
	echo "</fieldset>";
	echo "<fieldset>";
		echo "<legend>Lista</legend>";
		echo "<table>";
			//echo "&nbsp;";
			/*echo "<pre>";
				print_r($_REQUEST);
			echo "</pre>";*/
			if($_GET["palavra"])
			{
				$andWhere = " where pro_nome like '".strtoupper($_GET[palavra])."%' and pro_codigo in (select pro_codigo from produto_setor where set_codigo = $_GET[parametro]) order by pro_nome ";
			} else {
				$andWhere = " where pro_codigo in (select pro_codigo from produto_setor where set_codigo = $_GET[parametro]) order by pro_nome limit 15";
			}
			$select = "select * from produto $andWhere";
			$exec_select = pg_query($select);
			while($linha = pg_fetch_array($exec_select))
			{
				echo "<tr>";
					echo "<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>";
						echo $linha["pro_nome"];
					echo "<td>";
					echo "<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' width=90>";
						echo "<input type=\"image\" src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg\" onclick=\"passarDados('$linha[pro_codigo]', '$linha[pro_nome]')\">";
					echo "</td>";
				echo "</tr>";
			}
		echo "</table>";
	echo "</fieldset>";
?>