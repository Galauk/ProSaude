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
	    <a href=$PHP_SELF?acao=form_add&id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></a>
   	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<form method='post' action='$PHP_SELF?$_SERVER[QUERY_STRING]'>
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
	    <legend>Listando Material Cadastrados</legend>
	     <table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
	      <tr bgcolor=FFFFFF>
		   <td width='50'>Código</td>
		   <td width='*'>Material</td>
		   <td colspan=2 width='65' align='center'>&nbsp;</td>
		  </tr>";
if($action=="busca") {
    $palavra_chave = strtoupper($palavra_chave);
	   $query = pg_query("SELECT tp.tma_tipo,m.mex_codigo from tipodematerial as tp left join materialexame as m on tp.tma_codigo = m.tma_codigo where p.tma_tipo like '%$palavra_chave%'"); 
} else {	   
    $query = pg_query("select *from materialexame");
}
	   while($row=pg_fetch_array($query)) {
	   $tp = pg_fetch_array(pg_query("select *from tipodematerial where tma_codigo = $row[tma_codigo]"));
	   echo "<tr>
			   <td width='100' align='center'>$row[mex_codigo]</td>
			   <td width='100%'>$tp[tma_tipo]</td>
			   <td width='65' align='center'>
			   <a href='$PHP_SELF?acao=form_edit&mex_codigo=$row[mex_codigo]&id_login=$id_login'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a></td>
			   <td width='65' align='center'>
			   <a href='$PHP_SELF?acao=del&mex_codigo=$row[mex_codigo]&id_login=$id_login' onClick=\"if (!confirm('Realmente deseja apagar esta consulta?'))  return false\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
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


  echo "<form name='form_adicao' method='post' action='$PHP_SELF?id_login=$id_login' onSubmit='return verifica_campos_inserir()'>";

  if($acao=="form_add") {
      echo "<input type=hidden name=acao value=add>";
      $titulo = "Cadastro";
      $Btn = "adicionar";
  } else {
     $row = pg_fetch_array(pg_query("select *from materialexame where mex_codigo = '$mex_codigo'"));
      echo "<input type=hidden name=acao value=edit>";
      echo "<input type=hidden name=mex_codigo value=$mex_codigo>";
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
	    <legend>$titulo de Tipo de Metodo </legend>
	     <table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
	      <tr>
		<td align=right>Tipo do Material:</td>
		<td><select name=tma_codigo class=box>";
   echo "<option value=''>..:: Selecione o Tipo de Material ::..</option>";
  $sql = pg_query("select *from tipodematerial");
 while($rw=pg_fetch_array($sql)) {
   echo ($rw[tma_codigo]==$row[tma_codigo])?"<option value='$rw[tma_codigo]' selected>$rw[tma_tipo]</option>":"<option value='$rw[tma_codigo]'>$rw[tma_tipo]</option>";
 }
  echo "</select></td>
	      </tr>
	      <tr>
		<td>&nbsp;</td>
		<td>Instru&ccedil;oes de coleta: </td>
	      </tr>
	      <tr>
		<td>&nbsp;</td>
		<td><textarea name='mex_instucoescoleta' class='box' cols='66' rows='3'>$row[mex_instucoescoleta]</textarea></td>
	      </tr>
	      <tr>
		<td>&nbsp;</td>
		<td>Instru&ccedil;oes de encaminhamento do material: </td>
	      </tr>
	      <tr>
		<td>&nbsp;</td>
		<td><textarea name='mex_instrucoesdeencaminhamento' class='box' cols='66' rows='3'>$row[mex_instrucoesdeencaminhamento]</textarea></td>
	      </tr>
	      <tr>
		<td>&nbsp;</td>
		<td>Instru&ccedil;oes de conservacao e armazenamento: </td>
	      </tr>
	      <tr>
		<td>&nbsp;</td>
		<td><textarea name='mex_instrucoesdearmazenamento' class='box' cols='66' rows='3'>$row[mex_instrucoesdearmazenamento]</textarea></td>
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
	$sql = "INSERT INTO materialexame ( " .
			"tma_codigo, " .
			"mex_instucoescoleta, " .
			"mex_instrucoesdeencaminhamento, " .
			"mex_instrucoesdearmazenamento ".
               ") VALUES ( " .
			"'$tma_codigo'".
			",'$mex_instucoescoleta'".
			",'$mex_instrucoesdeencaminhamento'".
			",'$mex_instrucoesdearmazenamento' ) ";
	$query = pg_query($sql);
	msg($id_login,$acao,$query);
	
 }

if($acao=="edit") {
	$sql = 	"UPDATE materialexame SET " .
         	"tma_codigo='$tma_codigo' ". 
         	", mex_instucoescoleta='$mex_instucoescoleta' ". 
         	", mex_instrucoesdeencaminhamento='$mex_instrucoesdeencaminhamento' ". 
         	", mex_instrucoesdearmazenamento='$mex_instrucoesdearmazenamento' ". 
         	"where mex_codigo='$mex_codigo'" ;
	$query = pg_query($sql);
	msg($id_login,$acao,$query);
}

if($acao=="del") {
	$sql = 	"DELETE FROM materialexame " .
         	"where mex_codigo='$mex_codigo'" ;
			
	$query = pg_query($sql);
	msg($id_login,$acao,$sql);
}

?>
