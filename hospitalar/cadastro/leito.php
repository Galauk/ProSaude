<?
	session_start();
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	cabecario();
	//------------------------------------------------------------------>
//-> Mostrando o resultado da busca
//------------------------------------------------------------------>

 if($acao=="busca") {
//echo "OLA MUNDO ";
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Op&ccedil;&otilde;es</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		  <td width=200><a href='leito.php?id_login=$id_login&acao=add'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></a></td>
	      <td><a href=cadastrosHospitalar.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>";
	              
		  echo "<form method=post action=$PHP_SELF?acao=busca&lei_codigo=$row[lei_codigo]>";
	       
		echo "<input type=hidden name=acao value=busca>
		      <input type=hidden name=id_login value=$id_login>
		      <td width=5>Buscar:</td>
		      <td width=5>
			    <td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
		    	<td><input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg' /></td>
			</form>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

//
//-> Verificando Busca
$palavra_chave = $_POST['palavra_chave'];
if(strlen($palavra_chave)<"1") {
        echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font color=red size=2><b>ERRO</b></font><br>Busca com menos de <b>3</b> caracteres não permitida</td>
                 </tr>
                </table><br>";
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='$PHP_SELF?id_login=$id_login'\", 2000);
              </SCRIPT>";
 exit;
}

//
//-> Subistituindo o + por porcentagem na busca

   $str = str_replace("+","%",$palavra_chave);
   $pos = strpos($palavra_chave,"+");
   
  if($pos=="0") {
     $v1=1;
  } else {
     $v1=2;
  }
  $select = "select * from leito where lei_leito like upper('%$palavra_chave%') or lei_desc = upper('%$palavra_chave%')";

 $sql=pg_query($select);
$num=pg_num_rows($sql);
  if($num=="0") { $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
  if($num=="1") { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
  if($num>"1") { $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>$resp</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9>
		<td width=10 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Codigo</td>
		<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Leito</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

		$sql=pg_query("select * from leito where lei_leito like upper('%$palavra_chave%') or lei_desc = upper('%$palavra_chave%')");
		// $select = "select * from leito where lei_leito like upper('%$palavra_chave%') or lei_desc = upper('%$palavra_chave%')";
	 $funcao = "<a href='leito.php?acao=del&lei_codigo=$row[lei_codigo]'></a>";
	   while($row=pg_fetch_array($sql)) {
	     echo "<tr>
		     <td width=10 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[lei_codigo]</td>
		     <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[lei_leito]</td>
			 <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>
			 	<a href='leito.php?acao=edit&lei_codigo=$row[lei_codigo]'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' border='0'/></a>
			</td>
			<td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>";
//			ChmodBtn($id_login,'apagar','leito.php?acao=del&lei_codigo=$row[lei_codigo]&id_login=$id_login')."
				echo "<a href=\"leito.php?acao=del&lei_codigo=$row[lei_codigo]&id_login=$id_login\" onClick=\"if (!confirm('Realmente deseja apagar este registro?')) return false\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg alt='Apagar' border=0></a>
			</td>
			<script>						
		  
			</script>
		   </tr>";
		   /*
		   <a href=\"leito.php?acao=del&lei_codigo=$row[lei_codigo]&id_login=$id_login\" onClick=\"if (!confirm('Realmente deseja apagar este registro?')) return false\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg alt='Apagar' border=0></a>
				
		   
		   <a href=\"javascript:;\" onClick=\"if (!confirm('Realmente deseja apagar este registro?')){ return false; } else { $funcao }\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$acao."_on.jpg border=0></a>*/
	   }
 }	   
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
		
		


//-> Botoes
if ($acao == ''){
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Op&ccedil;&otilde;es</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		  <td width=200><a href='leito.php?id_login=$id_login&acao=add'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></a></td>
	      <td><a href=cadastrosHospitalar.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>";
	              
		  echo "<form method=post action=$PHP_SELF?acao=busca&lei_codigo=$row[lei_codigo]>";
	       
		echo "<input type=hidden name=acao value=busca>
		      <input type=hidden name=id_login value=$id_login>
		      <td width=5>Buscar:</td>
		      <td width=5>
			    <td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
		    	<td><input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg' /></td>
			</form>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
		

		
		//-> Listando
  
/*  if (chmodbtn($id_login,"listar_if","grupo.php"))
  {*/
	echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	       <tr>
		<td>
		 <fieldset>
		  <legend>Listando &Uacute;ltimos <b>15</b> Leitos Cadastrados</legend>
		   <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
		    <tr bgcolor=F9f9f9>
		      <td width=10 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Codigo</td>
		      <td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Leito</td>
		      <td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
      
	 $sql=pg_query("select * from leito order by lei_codigo desc limit 15");
	 $funcao = "<a href='leito.php?acao=del&lei_codigo=$row[lei_codigo]'></a>";
	   while($row=pg_fetch_array($sql)) {
	     echo "<tr>
		     <td width=10 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[lei_codigo]</td>
		     <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[lei_leito]</td>
			 <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>
			 	<a href='leito.php?acao=edit&lei_codigo=$row[lei_codigo]'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' border='0'/></a>
			</td>
			<td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>";
//			ChmodBtn($id_login,'apagar','leito.php?acao=del&lei_codigo=$row[lei_codigo]&id_login=$id_login')."
				echo "<a href=\"leito.php?acao=del&lei_codigo=$row[lei_codigo]&id_login=$id_login\" onClick=\"if (!confirm('Realmente deseja apagar este registro?')) return false\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg alt='Apagar' border=0></a>
			</td>
			<script>						
		  
			</script>
		   </tr>";
		   /*
		   <a href=\"leito.php?acao=del&lei_codigo=$row[lei_codigo]&id_login=$id_login\" onClick=\"if (!confirm('Realmente deseja apagar este registro?')) return false\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg alt='Apagar' border=0></a>
				
		   
		   <a href=\"javascript:;\" onClick=\"if (!confirm('Realmente deseja apagar este registro?')){ return false; } else { $funcao }\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$acao."_on.jpg border=0></a>*/
	   }
 }	   
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";

	
//------------------------------------------------------------------>
//-> Formulario de Adicao de Conteudo
//------------------------------------------------------------------>
	if ($acao == 'add'){
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
			 <input type='hidden' name='acao' value='adicionar'>";		
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
else if($acao == 'adicionar'){
		$insert = "insert into leito
					(lei_leito,lei_desc,qua_codigo)
				VALUES
				    (upper('$_POST[leito]'),'$_POST[descricao]','512120') ";
		//echo $insert;
		$query = pg_query($insert);
		msg($id_login,$acao,$query);
	}
//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>
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
//DELETAR O REGSITO
if($acao=="del") {
  $sql = pg_query("delete from leito where lei_codigo='$_GET[lei_codigo]'");
msg($id_login,$acao,$sql);
}
		
	
?>
