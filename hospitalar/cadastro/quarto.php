<?
	session_start();
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
//-> Botoes
	$acao = $_POST['acao'];
if ($acao == ''){
	  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
		 <tr>
		  <td>
		   <fieldset>
			<legend>Op&ccedil;&otilde;es</legend>
			 <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
			  <tr>
			   <td width=79><a href=leito.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>";
					  
			  echo "
			  </tr>
			 </table>
		   </fieldset>
		  </td>
		 </tr>
			</table><br>";
		 echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
		 <tr>
		  <td>
		   <fieldset>
			<legend>Op&ccedil;&otilde;es</legend>
			 <form method=post action=$PHP_SELF>
			 <input type='hidden' name='acao' value='add'>";		
			 echo" <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
			  <tr>
				<td>Quarto:</td>
				<td><input type='text' name='leito' size='27'></td>
			  </tr>
			  <tr>
				<td width='50'>Descri&ccedil;&atilde;o:</td>
				<td><input type='text' name='descricao' size='40'></td>
			  </tr>
				<tr>
				<td>Ala:</td>
				<td>
					<select>
						<option value='1'>    ...      </option>
						<option value='2'>Térrio - Enfermaria</option>
						<option value='3'>UTI - 2º Andar</option>										
					</select>
				</td>
			  </tr>
			  <tr>
				<td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></td>
			  </tr>
			
			 </table>
			 </form>
		   </fieldset>
		  </td>
		 </tr>
			</table>";
}
else if($acao == 'add'){
		$insert = "insert into quarto
					(qua_quarto,qua_desc,ala_codigo)
				VALUES
				    ('$_POST[quarto]','$_POST[descricao]','57712120') ";
		//echo $insert;
		$query = pg_query($insert);
		msg($id_login,$acao,$query);
	}
		
?>