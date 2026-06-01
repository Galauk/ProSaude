<?

/**
	@Modulo: EXAME
	@Tabelas: tipodemetodo
	@Acao: Adiciona os típos de Metodos de Exame
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
	    <a href=exa_valoresreferencia.php?acao=form_add&id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></a>
   	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<form method='post' action='exa_valoresreferencia.php?$_SERVER[QUERY_STRING]'>
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
	    <legend>Listando Valores de Referencia Cadastrados</legend>
	     <table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
	      <tr bgcolor=FFFFFF>
		   <td width='50'>Código</td>
		   <td width='*'>SubExame</td>
		   <td width='*'>Itens de Analise</td>
		   <td width='*'>Metodo</td>
		   <td width='*'>Valor de Referencia</td>
		   <td colspan=2 width='65' align='center'>&nbsp;</td>
		  </tr>";
if($action=="busca") {
    $query = pg_query("select sub.sex_subexame,i.ite_codigo,i.ite_itemdoexame,i.ite_observacao from itensanalise as i left join subexame as sub on sub.sex_codigo = i.sex_codigo where sub.sex_subexame like '%$palavra_chave%'");
} else {	   
    $query = pg_query("select vlr.sex_codigo,vlr.txa_codigo,vlr.man_codigo,ite.ite_itemdoexame,vlr.vlr_codigo,vlr.vlr_valordereferencia from valoresdereferencia as vlr left join itensanalise as ite on ite.ite_codigo = vlr.ite_codigo");
#$query = pg_query("select *from valoresdereferencia");
}
	   while($row=pg_fetch_array($query)) {
$tp = pg_fetch_array(pg_query("select *from tipodemetodos where tpm_codigo = $row[man_codigo]"));	
	  $rr = pg_fetch_array( pg_query("select tp.tpm_metodo,m.man_codigo,m.tpm_codigo,m.man_observacao from metododeanalize as m left join tipodemetodos as tp on tp.tpm_codigo = m.tpm_codigo where m.man_codigo = '$row[man_codigo]'"));
          $sub = pg_fetch_array(pg_query("select *from subexame where sex_codigo = '$row[sex_codigo]'"));
	   echo "<tr>
			   <td width='100' align='center'>$row[vlr_codigo]</td>
			   <td width='13%'>$sub[sex_subexame]</td>
			   <td width='20%'>$row[ite_itemdoexame]</td>
			   <td width='20%'>$tp[tpm_metodo]</td>
			   <td width='40%'>$row[vlr_valordereferencia]</td>
			   <td width='65' align='center'>
			   <a href='exa_valoresreferencia.php?acao=form_edit&vlr_codigo=$row[vlr_codigo]&id_login=$id_login'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a></td>
			   <td width='65' align='center'>
			   <a href='exa_valoresreferencia.php?acao=del&vlr_codigo=$row[vlr_codigo]&id_login=$id_login' onClick=\"if (!confirm('Realmente deseja apagar esta consulta?'))  return false\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
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


  echo "<form name='form_adicao' method='post' action='exa_valoresreferencia.php?id_login=$id_login' onSubmit='return verifica_campos_inserir()'>";

  if($acao=="form_add") {
      echo "<input type=hidden name=acao value=add>";
      $titulo = "Cadastro";
      $Btn = "adicionar";
  } else {
     $row = pg_fetch_array(pg_query("select *from valoresdereferencia where vlr_codigo = '$vlr_codigo'"));
      echo "<input type=hidden name=acao value=edit>";
      echo "<input type=hidden name=vlr_codigo value=$vlr_codigo>";
      $titulo = "Editar";
      $Btn = "editar";
  }

  echo "<fieldset>
	    <legend>$titulo de Valores de Referencia </legend>
	     <table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
	      <tr>
		<td align=right>Tipo de Exame:</td>
		<td><select name=txa_codigo class=box>";
   echo "<option value=''>..:: Selecione o Tipo de Exame ::..</option>";
  $sql = pg_query("select *from tipodeexame");
 while($rw=pg_fetch_array($sql)) {
  $proc = pg_fetch_array(pg_query("select *from procedimento where proc_codigo = '$rw[proc_codigo]'"));
  $proc2 = pg_num_rows(pg_query("select *from procedimento where proc_codigo = '$rw[proc_codigo]'"));
  if($proc2 == 0){
  	
  }else{
   echo ($rw[txa_codigo]==$row[txa_codigo])?"<option value='$rw[txa_codigo]' selected>$proc[proc_nome]</option>":"<option value='$rw[txa_codigo]'>$proc[proc_nome]</option>";
  }
 }
  echo "</select></td>
	      </tr>
	      <tr>
		<td align=right>Tipo de SubExame:</td>
		<td><select name=sex_codigo class=box>";
   echo "<option value=''>..:: Selecione o Tipo de SubExame ::..</option>";
  $sql = pg_query("select *from subexame");
 while($sub=pg_fetch_array($sql)) {
   echo ($sub[sex_codigo]==$row[sex_codigo])?"<option value='$sub[sex_codigo]' selected>$sub[sex_subexame]::$sub[sex_observacao]</option>":"<option value='$sub[sex_codigo]'>$sub[sex_subexame]::$sub[sex_observacao]</option>";
 }
  echo "</select></td>
	      </tr>
	      <tr>
		<td align=right>Itens de Analise:</td>
		<td><select name=ite_codigo class=box>";
   echo "<option value=''>..:: Selecione o Item de Analise ::..</option>";
  $sql = pg_query("select *from itensanalise");
 while($rw=pg_fetch_array($sql)) {
   echo ($rw[ite_codigo]==$row[ite_codigo])?"<option value='$rw[ite_codigo]' selected>$rw[ite_itemdoexame] :: $rw[ite_observacao]</option>":"<option value='$rw[ite_codigo]'>$rw[ite_itemdoexame]:: $rw[ite_observacao]</option>";
 }
  echo "</select></td>
	      </tr>
	      <tr>
		<td align=right>Metodo de Analise:</td>
		<td><select name=man_codigo class=box>";
   echo "<option value=''>..:: Selecione o Metodo de Analise ::..</option>";
  $sql = pg_query("select *from tipodemetodos");
 while($rw=pg_fetch_array($sql)) {
   echo ($rw[tpm_codigo]==$row[man_codigo])?"<option value='$rw[tpm_codigo]' selected>$rw[tpm_metodo]</option>":"<option value='$rw[tpm_codigo]'>$rw[tpm_metodo]</option>";
 }
  echo "</select></td>
	      </tr>
	      <tr>
	      	<td align=right>
	      		Faixa Etaria:
	      	</td>
	      	<td>
	      		<input type=text name=vlr_faixa_etaria class=box size=40 value=$row[vlr_faixa_etaria]>
	      	</td>
	      </tr>
	      <tr>
	      	<td align=right>
	      		Sexo:
	      	</td>
	      	<td>
	      		<select name=vlr_sexo class=box>
	      			<option></option>
	      			<option value='M' ".($row[vlr_sexo] == "M" ? "SELECTED='SELECTED'" : "").">Masculino</option>
	      			<option value='F' ".($row[vlr_sexo] == "F" ? "SELECTED='SELECTED'" : "").">Feminino</option>
	      		</select>
	      	</td>
	      </tr>
	      <tr>
		<td>&nbsp;</td>
		<td>Valor de Referencia: </td>
	      </tr>
	      <tr>
		<td>&nbsp;</td>
		<td><textarea name='vlr_valordereferencia' class='box' cols='66' rows='3'>$row[vlr_valordereferencia]</textarea></td>
	      </tr>
	      <tr>
		<td align=right>Alerta:</td>
		<td><input type=text name='vlr_limite_alerta_de' class='box' size='4' value='$row[vlr_limite_alerta_de]'>&nbsp;e&nbsp;<input type=text name='vlr_limite_alerta_ate' class='box' size='4' value='$row[vlr_limite_alerta_ate]'></td>
	      </tr>
	      <tr>
		<td align=right>Absurdo:</td>
		<td><input type=text name='vlr_limite_absurdo_de' class='box' size='4' value='$row[vlr_limite_absurdo_de]'>&nbsp;e&nbsp;<input type=text name='vlr_limite_absurdo_ate' class='box' size='4' value='$row[vlr_limite_absurdo_ate]'></td>
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
 if(empty($sex_codigo)) { $sex_codigo = "0"; }
 if(empty($ite_codigo)) { $ite_codigo = "0"; }
	$sql = "INSERT INTO valoresdereferencia ( " .
			"txa_codigo, " .
			"sex_codigo, " .
			"ite_codigo, " .
			"man_codigo, " .
			"vlr_valordereferencia, ".
			"vlr_limite_alerta_de, ".
			"vlr_limite_alerta_ate, ".
			"vlr_limite_absurdo_de, ".
			"vlr_limite_absurdo_ate, ".
			"vlr_faixa_etaria,".
			"vlr_sexo".
		   ") VALUES ( 
			'$txa_codigo'
			,'$sex_codigo'
			,'$ite_codigo'
			, '$man_codigo'
			,'$vlr_valordereferencia'
			,".($vlr_limite_alerta_de == "" ? "null" : "'$vlr_limite_alerta_de'")."
			,".($vlr_limite_alerta_ate == "" ? "null" : "'$vlr_limite_alerta_ate'")."
			,".($vlr_limite_absurdo_de == "" ? "null" : "'$vlr_limite_absurdo_de'")."
			,".($vlr_limite_absurdo_ate == "" ? "null" : "'$vlr_limite_absurdo_ate'")."
			,".($vlr_faixa_etaria== "" ? "null" : "'$vlr_faixa_etaria'")."
			,".($vlr_sexo == "" ? "null" : "'$vlr_sexo'").")";
	$query = pg_query($sql) or die(pg_last_error());
	msg($id_login,$acao,$query);
	
 }

if($acao=="edit") {
 if(empty($sex_codigo)) { $sex_codigo = "0"; }
 if(empty($ite_codigo)) { $ite_codigo = "0"; }
if(empty($man_codigo)) { $man_codigo = '1'; } else { $man_codigo = $man_codigo; }
	$sql = 	"UPDATE valoresdereferencia SET " .
         	"txa_codigo='$txa_codigo' ". 
         	", sex_codigo='$sex_codigo' ". 
         	", ite_codigo='$ite_codigo' ". 
         	", man_codigo='$man_codigo' ". 
         	", vlr_valordereferencia='$vlr_valordereferencia' ". 
         	", vlr_limite_alerta_de='$vlr_limite_alerta_de' ". 
         	", vlr_limite_alerta_ate='$vlr_limite_alerta_ate' ". 
         	", vlr_limite_absurdo_de='$vlr_limite_absurdo_de' ". 
         	", vlr_limite_absurdo_ate='$vlr_limite_absurdo_ate' ".
			", vlr_faixa_etaria='$vlr_faixa_etaria' ".
			", vlr_sexo='$vlr_sexo' ".
         	"where vlr_codigo='$vlr_codigo'" ;
	$query = pg_query($sql) or die(pg_last_error());
//	echo $sql;
	msg($id_login,$acao,$query);
}

if($acao=="del") {
	$sql = 	"DELETE FROM valoresdereferencia " .
         	"where vlr_codigo='$vlr_codigo'" ;
			
	$query = pg_query($sql);
	msg($id_login,$acao,$sql);
}

?>
