<script>
  function verifica_campos_inserir() {
 
	   if(document.form_adicao.cli_cod.value == '') {
		alert("Por favor Preencha o Código do Clínica");
		document.form_adicao.cli_cod.focus();
		return false;
	   }
	   if(document.form_adicao.cli_descricao.value == '') {
		alert("Por favor Preencha a Descrição do Clínica");
		document.form_adicao.cli_descricao.focus();
		return false;
	   }	   

	return true;
	
}
</script>
<?

/**
@Modulo: Clínica
@Responsavel: André Filipe
@Tabelas: clinica
@Criacao: 2007-02-24
@Acao: Adiciona os tipos de Clínica
*/ 

//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

 if(empty($acao)) {

//
//-> Botoes
  echo "<fieldset>
	    <legend>Opções</legend>
		  
			<a href=aih.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>
			".ChmodBtn($id_login,'adicionar',"clinica.php?acao=form_add&id_login=$id_login")."
		   		
			<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
				<tr>";
					if(chmodbtn($id_login, "procurar_if", "clinica.php"))
					{
					  echo "<form method='post' action='$PHP_SELF?$_SERVER[QUERY_STRING]'>";
					}
					echo "<input type=hidden name=acao value=busca>
						<td width=30>Buscar:</td>
						<td width=120><input type='text' name=palavra_chave class=box /></td>
						<td>".ChmodBtn($id_login,'procurar','clinica.php')."</td>
					</form>
				</tr>
			</table>
		 
	   </fieldset>
	  <br>";

//
//-> Listando

echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Listando Cl�nicas Cadastradas</legend>
	     <table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
	      <tr bgcolor=FFFFFF>
		   <th width='50'>Código</th>
		   <th width='*'>Descrição</th>
		   <th width='65' align='center'>&nbsp;</th>
		  </tr>";
	if(chmodbtn($id_login, "listar_if", "clinica.php"))
	{
	   $sql		=	"SELECT cli_codigo, cli_cod, cli_descricao ".
	   				"FROM clinica ".
					"ORDER BY  cli_cod, cli_descricao ASC"; 
	   $query 	= 	db_query($sql);
	}
	   
	   while($row=pg_fetch_array($query)) {

	   echo "<tr>
			   <td width='50' align='center'>$row[cli_cod]</td>
			   <td width='*'>$row[cli_descricao]</td>
			   <td width='65' align='center'>
			   ".ChmodBtn($id_login,'editar',"clinica.php?acao=form_edit&cli_codigo=$row[cli_codigo]&id_login=$id_login")."
			   <td>
			 </tr>";
		 }
		 
	   echo "</table>
	   </fieldset>
	  </td>
	 </tr>
      </table>";
}


//------------------------------------------------------------------>
//-> Mostrando o resultado da busca
//------------------------------------------------------------------>

 if($acao=="busca") {

//-> Subistituindo o + por porcentagem na busca
	$str = str_replace("+","%",$palavra_chave);
	$pos = strpos($palavra_chave,"+");

	if($pos=="0") {
	 $v1=1;
	} else {
	 $v1=2;
	}

//-> Botoes
  echo "<fieldset>
	    <legend>Opções</legend>
		  
			<a href=aih.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>
			".ChmodBtn($id_login,'adicionar',"clinica.php?acao=form_add&id_login=$id_login")."
		   		
			<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
				<tr>";
					if(chmodbtn($id_login, "procurar_if", "clinica.php"))
					{
					  echo "<form method='post' action='$PHP_SELF?$_SERVER[QUERY_STRING]'>";
					}
					echo "<input type=hidden name=acao value=busca>
						<td width=30>Buscar:</td>
						<td width=120><input type='text' name=palavra_chave class=box /></td>
						<td>".ChmodBtn($id_login,'procurar','clinica.php')."</td>
					</form>
				</tr>
			</table>
		 
	   </fieldset>
	  <br>";

if(chmodbtn($id_login, "listar_if", "clinica.php"))
{
   $sql		=	"SELECT cli_codigo, cli_cod, cli_descricao ".
				"FROM clinica ".
				"WHERE (cli_descricao ilike '%$palavra_chave%') ".
				"ORDER BY  cli_cod, cli_descricao ASC"; 
	$query 	=	db_query($sql);
}
	$num	=	pg_num_rows($query);

	if($num=="0") { $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
	if($num=="1") { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
	if($num>"1") { $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }

echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>".$resp."</legend>
	     <table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
	      <tr bgcolor=FFFFFF>
		   <th width='50'>Código</th>
		   <th width='*'>Descrição</th>
		   <th width='65' align='center'>&nbsp;</th>
		  </tr>";

	   while($row=pg_fetch_array($query)) {

	   echo "<tr>
			   <td width='50'>$row[cli_cod]</td>
			   <td width='*'>$row[cli_descricao]</td>
			   <td width='65' align='center'>
			   ".ChmodBtn($id_login,'editar',"clinica.php?acao=form_edit&cli_codigo=$row[cli_codigo]&id_login=$id_login")."
			</td>
			 </tr>";
		 }
		 
	   echo "</table>
	   </fieldset>
	  </td>
	 </tr>
      </table>";
}

//------------------------------------------------------------------>
//-> Formulario de Adicao de Conteudo
//------------------------------------------------------------------>

 if($acao=="form_add") {

//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  echo "<fieldset>
	    <legend>Opções de Cadastro</legend>
	       <a href=clinica.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>
	    </fieldset>
	  <br>";


  echo "<form name='form_adicao' method='post' action='$PHP_SELF?id_login=$id_login' onSubmit='return verifica_campos_inserir()'>
		<input type=hidden name=acao value=add>

	   <fieldset>
	    <legend>Cadastro de Clínica </legend>
	     <table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
	      <tr>
			<td>Código:</td>
			<td><input type='text' name='cli_cod' class='box' size='69' maxlength='5'></td>
	      </tr>
	      <tr>
		<td width=70>Descrição: </td>
		<td><textarea name='cli_descricao' class='box' cols='66' rows='3'></textarea></td>
	      </tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg></td>
	      </tr>
	     </table>
	   </fieldset>
	   <br />
		</form>";

}

//}

//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>

 if($acao=="form_edit") {
//
//-> Botoes
  echo "<fieldset>
	    <legend>Opções</legend>
	        
			<a href=clinica.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>
			<a href=$PHP_SELF?acao=form_add&id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></a>
		 
	   </fieldset>
	  <br>";

//-> Pegando as informcoes do banco pra mostrar no formulario
 $sqlci = "SELECT * FROM clinica WHERE cli_codigo='$cli_codigo'";
 $row=pg_fetch_array(pg_query($sqlci));



  echo "<form name='form_adicao' method='post' action='$PHP_SELF?id_login=$id_login' onSubmit='return verifica_campos_inserir()'>
		<input type=hidden name=acao value=edit>
		<input type=hidden name=cli_codigo value=$cli_codigo>

	   <fieldset>
	    <legend>Alteração de Clinica </legend>
	     <table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
	      <tr>
			<td>Código:</td>
			<td><input type='text' name='cli_cod' class='box' size='69' maxlength='5' value='$row[cli_cod]' /></td>
	      </tr>
	      <tr>
		<td width=70>Descrição: </td>
		<td><textarea name='cli_descricao' class='box' cols='66' rows='3'>$row[cli_descricao]</textarea></td>
	      </tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
	      </tr>
	     </table>
	   </fieldset>
	   <br />
		</form>";

}

//------------------------------------------------------------------>
//-> SQL's
//------------------------------------------------------------------>
//
//-> ADD <---------------------------------------------------------->

 if($acao=="add") {
 
		$sql = "INSERT INTO clinica ( " .
				"cli_cod, " .
				"cli_descricao ".
            ") VALUES ( " .
				"'$cli_cod'".
				", '$cli_descricao' ) ";

	//echo $sql;
	$query = db_query($sql);
	msg($id_login,$acao,$sql);
	
 }

//
//-> EDIT <--------------------------------------------------------->

 if($acao=="edit") {
 	
 
	$sql = 	"UPDATE clinica SET " .
         	"cli_cod='$cli_cod' ". 
         	", cli_descricao='$cli_descricao' ". 
         	"where cli_codigo='$cli_codigo'" ;
			
	//echo $sql;
	$query = db_query($sql);
	msg($id_login,$acao,$sql);
}

?>