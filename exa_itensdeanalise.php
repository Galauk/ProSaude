<?
/**
	@Modulo: EXAME
	@Tabelas: tipodemetodo
	@Acao: Adiciona os tipos de Métodos de Exame
*/ 

/*
 @
 @ INCLUDES
 @
*/	

	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);

	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();


/*
 @
 @ BOTOES DE ADICIONAR E BUSCA
 @
*/	

 if(empty($acao)) {

//
//-> Botoes
  echo "<fieldset>
	    <legend>Opções</legend>
	    <a href=".$_SERVER['PHP_SELF']."?acao=form_add&id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></a>
   	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<form method='post' action='".$_SERVER['PHP_SELF']."?$_SERVER[QUERY_STRING]'>
		  <input type=hidden name=action value=busca>
		<td width=30>Buscar:</td>
		<td width=120><input type='text' name=palavra_chave class=box /></td>
		<td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td>
	       </form>
	      </tr>
	     </table>
       </fieldset>
      <br>";

/*
 @
 @ LISTANDO DADOS DA SQL
 @
*/	
echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Listando Itens de Analise Cadastrados</legend>
	     <table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
	      <tr bgcolor=FFFFFF>
		   <td width='50'>Código</td>
		   <td width='*'>SubExame</td>
		   <td width='*'>Item</td>
		   <td width='*'>Observacao</td>
		   <td colspan=2 width='65' align='center'>&nbsp;</td>
		  </tr>";
if($action=="busca") {
    $query = pg_query("select sub.sex_observacao, sub.sex_subexame,i.ite_codigo,i.ite_itemdoexame,i.ite_observacao from itensanalise as i left join subexame as sub on sub.sex_codigo = i.sex_codigo where sub.sex_subexame like '%$palavra_chave%'");
} else {	   
    $query = pg_query("select sub.sex_observacao, sub.sex_subexame,i.ite_codigo,i.ite_itemdoexame,i.ite_observacao from itensanalise as i left join subexame as sub on sub.sex_codigo = i.sex_codigo");
}
	   while($row=pg_fetch_array($query)) {
	   echo "<tr>
			   <td width='100' align='center'>$row[ite_codigo]</td>
			   <td width='30%'>$row[sex_subexame] - $row[sex_observacao]</td>
			   <td width='30%'>$row[ite_itemdoexame]</td>
			   <td width='30%'>$row[ite_observacao]</td>
			   <td width='65' align='center'>
			   <a href='".$_SERVER['PHP_SELF']."?acao=form_edit&ite_codigo=$row[ite_codigo]&id_login=$id_login'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a></td>
			   <td width='65' align='center'>
			   <a href='$".$_SERVER['PHP_SELF']."?acao=del&ite_codigo=$row[ite_codigo]&id_login=$id_login' onClick=\"if (!confirm('Realmente deseja apagar esta consulta?'))  return false\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
			 </tr>";
		 }
		 
	   echo "</table>
	   </fieldset>
	  </td>
	 </tr>
      </table>";
}


/*
 @
 @ FORMULARIO DE ADICAO E EDICAO
 @
*/	

 if(($acao=="form_add" OR $acao=="form_edit")) {


  echo "<fieldset>
	    <legend>Opções de Cadastro</legend>
	       <a href=".$_SERVER["SCRIPT_NAME"]."?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>
	    </fieldset>
	  <br>";


  echo "<form name='form_adicao' method='post' action='".$_SERVER['PHP_SELF']."?id_login=$id_login' onSubmit='return verifica_campos_inserir()'>";

  if($acao=="form_add") {
      echo "<input type=hidden name=acao value=add>";
      $titulo = "Cadastro";
      $Btn = "adicionar";
  } else {
     $row = pg_fetch_array(pg_query("select *from itensanalise where ite_codigo = '$ite_codigo'"));
      echo "<input type=hidden name=acao value=edit>";
      echo "<input type=hidden name=ite_codigo value=$ite_codigo>";
      $titulo = "Editar";
      $Btn = "editar";
  }

  echo "<fieldset>
	    <legend>$titulo de Itens de Analise </legend>
	     <table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
	      <tr>
		<td align=right>SubExame:</td>
		<td><select name=sex_codigo class=box>";
   echo "<option value=''>..:: Selecione o SubExame ::..</option>";
  $sql = pg_query("select *from subexame");
 while($rw=pg_fetch_array($sql)) {
   echo ($rw[sex_codigo]==$row[sex_codigo])?"<option value='$rw[sex_codigo]' selected>$rw[sex_subexame]::$rw[sex_observacao]</option>":"<option value='$rw[sex_codigo]'>$rw[sex_subexame]::$rw[sex_observacao]</option>";
 }
  echo "</select></td>
	      </tr>
	      <tr>
			<td align=right>Tipo do Exame</td>
			<td>
				<select name='txa_codigo' class='box'>
					<option value=''>.....</option>";
  					$sqlTipoExame = pg_query("SELECT * FROM tipodeexame as t JOIN procedimento as p on p.proc_codigo = t.proc_codigo ORDER BY proc_nome");
  					while($regTipoExame = pg_fetch_array($sqlTipoExame)){
  						echo "<option value='$regTipoExame[txa_codigo]' ".($regTipoExame[txa_codigo] == $row[txa_codigo] ? "selected=selected" : "").">$regTipoExame[proc_nome]</option>";
  					}
  echo "		</select>
			</td>
	      </tr>
	      <tr>
		<td align=right>Item do Exame</td>
		<td><input type=text name=ite_itemdoexame value='$row[ite_itemdoexame]' class=box></td>
	      </tr>
	      <tr>
	       <tr>
		<td align=right>Tipo de Medida:</td>
		<td><input type=text name=ite_tipo_medida value='$row[ite_tipo_medida]' class=box></td>
	      </tr>
	      <tr>
		<td>&nbsp;</td>
		<td>Observacao: </td>
	      </tr>
	      <tr>
		<td>&nbsp;</td>
		<td><textarea name='ite_observacao' class='box' cols='66' rows='3'>$row[ite_observacao]</textarea></td>
	      </tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$Btn."_on.jpg></td>
	      </tr>
	     </table>
	   </fieldset>
	   <br />
	  </form>";

}


/*
 @
 @ SQLs PARA INSERCAO, EDICAO E EXCLUSAO
 @
*/	

 if($acao=="add") {
	$sql = "INSERT INTO itensanalise ( " .
			"sex_codigo," .
			"ite_itemdoexame, ".
			"ite_observacao, ".
			"ite_tipo_medida,".
			"txa_codigo".
               ") VALUES ( " .
			($sex_codigo == "" ? "null" : "'$sex_codigo'").
			",'$ite_itemdoexame'".
			",'$ite_observacao'".
			",'$ite_tipo_medida' ,".
			($txa_codigo == "" ? "null" : "'$txa_codigo'").") ";
	$query = pg_query($sql) or die(pg_last_error());
	msg($id_login,$acao,$query);
	
 }

if($acao=="edit") {
	$sql = 	"UPDATE itensanalise SET " .
         	"sex_codigo=".($sex_codigo == "" ? "null" : "'$sex_codigo'"). 
         	", ite_itemdoexame='$ite_itemdoexame' ". 
         	", ite_observacao='$ite_observacao' ".
			", ite_tipo_medida = '$ite_tipo_medida',".
			"txa_codigo =".($txa_codigo == "" ? "null" : "'$txa_codigo'"). 
         	"where ite_codigo='$ite_codigo'" ;
	$query = pg_query($sql);
	msg($id_login,$acao,$query);
}

if($acao=="del") {
	$sql = 	"DELETE FROM itensanalise " .
         	"where ite_codigo='$ite_codigo'" ;
			
	$query = pg_query($sql);
	msg($id_login,$acao,$sql);
}

?>
