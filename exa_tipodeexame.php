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
	    <legend>Listando Tipo de Exames Cadastrados</legend>
	     <table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
	      <tr bgcolor=FFFFFF>
		   <td width='50'>Código</td>
		   <td width='*'>Procedimento</td>
		   <td width='*'>Categoria do Exame</td>
		   <td width='*'>Tipo de Material</td>
		   <td colspan=2 width='65' align='center'>&nbsp;</td>
		  </tr>";
if($action=="busca") {
    $palavra_chave = strtoupper($palavra_chave);
	   $query = pg_query("SELECT tp.tma_codigo,cat.cte_cargo,tp.txa_codigo,p.proc_nome,p.proc_classificacao_sus from tipodeexame as tp left join procedimento as p on tp.proc_codigo = p.proc_codigo left join categoriadeexames as cat on tp.cte_codigo = cat.cte_codigo where p.proc_nome like '%$palavra_chave%' order by proc_nome"); 
} else {	   
	   $query = pg_query("SELECT tp.tma_codigo,cat.cte_cargo,tp.txa_codigo,p.proc_nome,p.proc_classificacao_sus from tipodeexame as tp left join procedimento as p on tp.proc_codigo = p.proc_codigo left join categoriadeexames as cat on tp.cte_codigo = cat.cte_codigo order by proc_nome"); 
}
	   while($row=pg_fetch_array($query)) {
		 $tp = pg_fetch_array(pg_query("select *from tipodematerial where tma_codigo = '$row[tma_codigo]'"));
	   echo "<tr>
			   <td width='100' align='center'>$row[proc_classificacao_sus]</td>
			   <td width='*'>$row[proc_nome]</td>
			   <td width='*'>$row[cte_cargo]</td>
			   <td width='*'>$tp[tma_tipo]</td>
			   <td width='65' align='center'>
			   <a href='".$_SERVER['PHP_SELF']."?acao=form_edit&txa_codigo=$row[txa_codigo]&id_login=$id_login'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a></td>
			   <td width='65' align='center'>
			   <a href='".$_SERVER['PHP_SELF']."?acao=del&txa_codigo=$row[txa_codigo]&id_login=$id_login' onClick=\"if (!confirm('Realmente deseja apagar esta consulta?'))  return false\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
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
     $row = pg_fetch_array(pg_query("select *from tipodeexame where txa_codigo = '$txa_codigo'"));
      echo "<input type=hidden name=acao value=edit>";
      echo "<input type=hidden name=txa_codigo value=$txa_codigo>";
      $titulo = "Editar";
      $Btn = "editar";
      if(trim($row[txa_subdivisoes])=="S") {
	 $vse_1 = "selected";
 	 $vse_2 = "";
      } else {
	 $vse_1 = "";
 	 $vse_2 = "selected";
      }

  }

  echo "<fieldset>
	    <legend>$titulo de Tipo de Exame </legend>
	     <table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
	      <tr>
		<td align=right>Procedimento:</td>
		<td><select name=proc_codigo class=box>";
   echo "<option value=''>..:: Selecione o Procedimento ::..</option>";
  $sql = pg_query("select *from procedimento order by proc_nome");
 while($rw=pg_fetch_array($sql)) {
   echo ($rw[proc_codigo]==$row[proc_codigo])?"<option value='$rw[proc_codigo]' selected>$rw[proc_nome]</option>":"<option value='$rw[proc_codigo]'>$rw[proc_nome]</option>";
 }
  echo "</select></td>
	      </tr>
	      <tr>
		<td align=right>Tipo de Material:</td>
		<td><select name=tma_codigo class=box>";
   echo "<option value=''>..:: Selecione o Tipo de Material ::..</option>";
  $sql = pg_query("select *from tipodematerial order by tma_tipo");
 while($rw=pg_fetch_array($sql)) {
   echo ($rw[tma_codigo]==$row[tma_codigo])?"<option value='$rw[tma_codigo]' selected>$rw[tma_tipo]</option>":"<option value='$rw[tma_codigo]'>$rw[tma_tipo]</option>";
 }
  echo "</select></td>
	      </tr>
	      <tr>
		<td align=right>Cat. Exame:</td>
		<td><select name=cte_codigo class=box>";
   echo "<option value=''>..:: Selecione a Categoria ::..</option>";
  $query = pg_query("select *from categoriadeexames");
 while($rr=pg_fetch_array($query)) {
   echo ($rr[cte_codigo]==$row[cte_codigo])?"<option value='$rr[cte_codigo]' selected>$rr[cte_cargo]</option>":"<option value='$rr[cte_codigo]'>$rr[cte_cargo]</option>";
 }
  echo "</select></td>
	      </tr>
	      <tr>
		<td>&nbsp;</td>
		<td>Instru&ccedil;oes de preparo do paciente: </td>
	      </tr>
	      <tr>
		<td>&nbsp;</td>
		<td><textarea name='txa_preparo' class='box' cols='66' rows='3'>$row[txa_preparo]</textarea></td>
	      </tr>
	      <tr>
		<td>&nbsp;</td>
		<td>Interferentes: </td>
	      </tr>
	      <tr>
		<td>&nbsp;</td>
		<td><textarea name='txa_interferentes' class='box' cols='66' rows='3'>$row[txa_interferentes]</textarea></td>
	      </tr>
	      <tr>
		<td>&nbsp;</td>
		<td>Interpretacao: </td>
	      </tr>
	      <tr>
		<td>&nbsp;</td>
		<td><textarea name='txa_interpretacao' class='box' cols='66' rows='3'>$row[txa_interpretacao]</textarea></td>
	      </tr>
              <tr>
                <td align=right>Prazo de Execucao:</td>
                <td><input type='text' name='txa_prazoexecucao' class='box' size='5' value='$row[txa_prazoexecucao]'></td>
              </tr>
              <tr>
                <td align=right>Sub Divisoes:</td>
                <td><select name=txa_subdivisoes class=box>
		<option value='N' $vse_1>NAO</option>
		<option value='S' $vse_2>SIM</option>
		</select></td>
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
	$sql = "INSERT INTO tipodeexame ( " .
			"tma_codigo, " .
			"proc_codigo, " .
			"cte_codigo, " .
			"txa_preparo, " .
			"txa_interferentes, " .
			"txa_interpretacao, " .
			"txa_prazoexecucao, " .
			"txa_subdivisoes ".
               ") VALUES ( " .
			"'$tma_codigo'".
			",'$proc_codigo'".
			",'$cte_codigo'".
			",'$txa_preparo'".
			",'$txa_interferentes'".
			",'$txa_interpretacao'".
			",'$txa_prazoexecucao'".
			",'$txa_subdivisoes' ) ";

	$query = pg_query($sql) or die(pg_last_error());
	msg($id_login,$acao,$query);
 }

if($acao=="edit") {
	$sql = 	"UPDATE tipodeexame SET " .
         	"tma_codigo='$tma_codigo' ". 
         	", proc_codigo='$proc_codigo' ". 
         	", cte_codigo='$cte_codigo' ". 
         	", txa_preparo='$txa_preparo' ". 
         	", txa_interferentes='$txa_interferentes' ". 
         	", txa_interpretacao='$txa_interpretacao' ". 
         	", txa_prazoexecucao='$txa_prazoexecucao' ". 
         	", txa_subdivisoes='$txa_subdividoes' ". 
         	"where txa_codigo='$txa_codigo'" ;
			
	$query = pg_query($sql);
	msg($id_login,$acao,$query);
}

if($acao=="del") {
	$sql = 	"DELETE FROM tipodeexame " .
         	"where txa_codigo='$txa_codigo'" ;
			
	$query = pg_query($sql);
	msg($id_login,$acao,$sql);
}

?>
