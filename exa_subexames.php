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

	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	session_start();
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
	    <legend>Listando SubExames de Analise Cadastrados</legend>
	     <table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
	      <tr bgcolor=FFFFFF>
		   <td width='50'>Código</td>
		   <td width='*'>SubExame</td>
		   <td width='*'>Categoria</td>
		   <td width='*'>Exame</td>
		   <td colspan=2 width='65' align='center'>&nbsp;</td>
		  </tr>";
if($action=="busca") {
    $select = "SELECT cte.cte_cargo,
    				  proc.proc_nome,
    				  sub.sex_codigo,
    				  sub.txa_codigo,
    				  sub.sex_subexame,
    				  sub.sex_observacao 
    			 FROM subexame AS sub 
    			 LEFT JOIN tipodeexame AS txa 
    			   ON txa.txa_codigo = sub.txa_codigo 
    			 LEFT JOIN procedimento AS proc 
    			   ON proc.proc_codigo = txa.proc_codigo 
    			 LEFT JOIN categoriadeexames AS cte 
    			   ON cte.cte_codigo = txa.cte_codigo 
    			WHERE sub.sex_subexame 
    			 LIKE '%$palavra_chave%'";
} else {	   
    $select = "SELECT cte.cte_cargo,
    				  proc.proc_nome,
    				  sub.sex_codigo,
    				  sub.txa_codigo,
    				  sub.sex_subexame,
    				  sub.sex_observacao 
    			 FROM subexame AS sub 
    			 LEFT JOIN tipodeexame AS txa 
    			   ON txa.txa_codigo = sub.txa_codigo 
    			 LEFT JOIN procedimento AS proc 
    			   ON proc.proc_codigo = txa.proc_codigo 
    			 LEFT JOIN categoriadeexames AS cte 
    			   ON cte.cte_codigo = txa.cte_codigo";
}
	$query = pg_query($select);
	   while($row=pg_fetch_array($query)) {
	   echo "<tr>
			   <td width='100' align='center'>$row[sex_codigo]</td>
			   <td width='30%'>$row[sex_subexame]</td>
			   <td width='30%'>$row[cte_cargo]</td>
			   <td width='30%'>$row[proc_nome]</td>
			   <td width='65' align='center'>
			   <a href='".$_SERVER['PHP_SELF']."?acao=form_edit&sex_codigo=$row[sex_codigo]&id_login=$id_login'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a></td>
			   <td width='65' align='center'>
			   <a href='".$_SERVER['PHP_SELF']."?acao=del&sex_codigo=$row[sex_codigo]&id_login=$id_login' onClick=\"if (!confirm('Realmente deseja apagar esta consulta?'))  return false\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
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
     $row = pg_fetch_array(pg_query("select *from subexame where sex_codigo = '$sex_codigo'"));
      echo "<input type=hidden name=acao value=edit>";
      echo "<input type=hidden name=sex_codigo value=$sex_codigo>";
      $titulo = "Editar";
      $Btn = "editar";
  }

  echo "<fieldset>
	    <legend>$titulo de Tipo de Metodo </legend>
	     <table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
	      <tr>
		<td align=right>Exame:</td>
		<td><select name=txa_codigo class=box>";
   echo "<option value=''>..:: Selecione o Exame ::..</option>";
  $sql = pg_query("select txa.txa_codigo,cte.cte_cargo,p.proc_nome,txa.proc_codigo from tipodeexame as txa left join procedimento as p on p.proc_codigo = txa.proc_codigo left join categoriadeexames as cte on cte.cte_codigo = txa.cte_codigo order by p.proc_nome,cte.cte_cargo");
 while($rw=pg_fetch_array($sql)) {
   echo ($rw[txa_codigo]==$row[txa_codigo])?"<option value='$rw[txa_codigo]' selected>$rw[cte_cargo] :: $rw[proc_nome]</option>":"<option value='$rw[txa_codigo]'>$rw[cte_cargo] :: $rw[proc_nome]</option>";
 }
  echo "</select></td>
	      </tr>
	      <tr>
		<td align=right>Sub-Exame</td>
		<td><input type=text name=sex_subexame value='$row[sex_subexame]' class=box></td>
	      </tr>
	      <tr>
		<td>&nbsp;</td>
		<td>Observacao: </td>
	      </tr>
	      <tr>
		<td>&nbsp;</td>
		<td><textarea name='sex_observacao' class='box' cols='66' rows='3'>$row[sex_observacao]</textarea></td>
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
	$sql = "INSERT INTO subexame ( " .
			"txa_codigo, " .
			"sex_subexame, ".
			"sex_observacao ".
               ") VALUES ( " .
			"'$txa_codigo'".
			",'$sex_subexame'".
			",'$sex_observacao' ) ";
	$query = pg_query($sql);
	msg($id_login,$acao,$query);
	
 }

if($acao=="edit") {
	$sql = 	"UPDATE subexame SET " .
         	"txa_codigo='$txa_codigo' ". 
         	", sex_subexame='$sex_subexame' ". 
         	", sex_observacao='$sex_observacao' ". 
         	"where sex_codigo='$sex_codigo'" ;
	$query = pg_query($sql);
	msg($id_login,$acao,$query);
}

if($acao=="del") {
	$sql = 	"DELETE FROM subexame " .
         	"where sex_codigo='$sex_codigo'" ;
			
	$query = pg_query($sql);
	msg($id_login,$acao,$sql);
}

?>
