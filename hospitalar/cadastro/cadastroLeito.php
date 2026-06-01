<?php
	session_start();
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
//-> Botoes
	$acao = $_REQUEST['acao'];
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
				<td>Leito:</td>
				<td><input type='text' name='leito' size='27'></td>
			  </tr>
			  <tr>
				<td width='50'>Descri&ccedil;&atilde;o:</td>
				<td><input type='text' name='descricao' size='40'></td>
			  </tr>
				<tr>
				<td>Quarto:</td>
				<td>
					<select>
						<option value='1'>    ...      </option>
						<option value='2'>Quarto 1</option>
						<option value='3'>Quarto 2</option>										
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
		$insert = "insert into leito
					(lei_leito,lei_desc,qua_codigo)
				VALUES
				    ('$_POST[leito]','$_POST[descricao]','512120') ";
		//echo $insert;
		$query = pg_query($insert);
		msg($id_login,$acao,$query);
	}
else if($acao== 'edit')
{
	$sql = "select * from leito where lei_codigo = $_GET[lei_codigo]";
	$exec_sql = pg_query($sql);
	$res_exec_sql = pg_fetch_array($exec_sql);
	$leito = $res_exec_sql['lei_leito'];
	$desc_leito = $res_exec_sql['lei_desc'];
	echo"<pre>".print_r($_POST);"</pre>";
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
			 <form method=post action=$PHP_SELF>lei_codigo
			 <input type='hidden' name='acao' value='editar'>
			  <input type='hidden' name='lei_codigo' value='$_GET[lei_codigo]'>
			 <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
			  <tr>
				<td>Leito:</td>
				<td><input type='text' name='leito' size='27' value='$leito'></td>
			  </tr>
			  <tr>
				<td width='50'>Descri&ccedil;&atilde;o:</td>
				<td><input type='text' name='descricao' size='40' value='$desc_leito'></td>
			  </tr>
				<tr>
				<td>Quarto:</td>
				<td>
					<select>
						<option value='1'>    ...      </option>
						<option value='2'>Quarto 1</option>
						<option value='3'>Quarto 2</option>										
					</select>
				</td>
			  </tr>
			  <tr>
			  	<td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></td>
			  </tr>
			
			 </table>
			 </form>
		   </fieldset>
		  </td>
		 </tr>
			</table>";
}
if($acao == "editar")
{
	$update = "update leito 
				  set lei_leito = '$_POST[leito]', 
				      lei_desc = '$_POST[descricao]', 
				  	  qua_codigo = '512120'				
				WHERE lei_codigo = $_POST[lei_codigo]";
	
	$exe = pg_query($update);
	msg($id_login,'edit',$exe);
}
if($acao=="del") {
  $sql = pg_query("delete from leito where lei_codigo='$_GET[lei_codigo]'");
msg($id_login,$acao,$sql);
}
		
?>