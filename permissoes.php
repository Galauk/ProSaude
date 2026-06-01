<?
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
	 reglog($id_login,"Entrando em PERMISSOES");
//------------------------------------------------------------------>
echo "<fieldset><legend>PERMISSÕES</legend>";

// if(empty($_REQUEST['acao'])) {
 if(empty($_REQUEST['acao']) OR $_REQUEST['acao']=='form_perm') {

//
//-> Botoes
  echo "<fieldset>
	    <legend>Opções</legend>
	       <a href=".$_SESSION[linkroot].$_SESSION[modulo]."zf/usuarios/usuarios><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>
	       ".ChmodBtn($id_login,'adicionar','permissoes.php?acao=form_add')."

			<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
			  <tr>
			
				<form method=post action=$PHP_SELF>
				
					<input type=hidden name=acao value=busca>
					<input type=hidden name=id_login value=$id_login>
					<td width=30>Buscar:</td>
					<td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
					<td>".ChmodBtn($id_login,'procurar','permissoes.php')."</td>
				
				</form>
			
			  </tr>
			</table>
	  
	   </fieldset>
	  <br>";

//
//-> Listando
  if (chmodbtn($id_login,"listar_if","permissoes.php"))
  {
      echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	     <tr>
	      <td>
	       <fieldset>
		<legend>Listando Últimas <b>15</b> Permissoes Cadastradas</legend>
		 <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
		  <tr bgcolor=F9f9f9>
		    <td width=10 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Codigo</td>
		    <td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Permissao</td>
		    <td width=65 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Programa</td>
		    <td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
    
       $sql=pg_query("select * from permissoes order by perm_codigo desc limit 15");
	 while($row=pg_fetch_array($sql)) {
	   echo "<tr>
		   <td width=10 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[perm_codigo]</td>
		   <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[perm_descricao]</td>
		   <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[perm_programa]</td>
		   <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','permissoes.php?acao=form_edit&perm_codigo='.$row[perm_codigo])."</td>
		   <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','permissoes.php?acao=del&perm_codigo='.$row[perm_codigo])."</td>
		 </tr>";
	 }
  }
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
}


//------------------------------------------------------------------>
//-> Mostrando o resultado da busca
//------------------------------------------------------------------>

 if($_REQUEST['acao']=="busca") {
//
//-> Verificando Busca
 reglog($id_login,"Buscando em PERMISSOES: $palavra_chave ");

if(strlen($palavra_chave)<="2") {
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
//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=200>".ChmodBtn($id_login,'adicionar','permissoes.php?acao=form_add')."</td>
	      ";
	      if (chmodbtn($id_login,"procurar_if","permissoes.php"))
	      {
		echo "<form method=post action=$PHP_SELF>";
	      }
	      echo "
		<input type=hidden name=acao value=busca>
		<input type=hidden name=id_login value=$id_login>
	       <td width=30>Buscar:</td>
	       <td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	       <td>".ChmodBtn($id_login,'procurar','permissoes.php')."</td></form>
	       <td width=107><a href='logoff.php?id_login=$id_login' target='_parent'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif border=0></a></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

// ~* significa busca pela string $palavra_chave case INsensitive, e valores a direita e esquerda
$sql=pg_query("SELECT * FROM permissoes 
		WHERE perm_descricao ~* '$palavra_chave' OR perm_programa ~* '$palavra_chave' ");
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
		<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Permissao</td>
		<td width=65 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Programa</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

     while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[perm_codigo]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[perm_descricao]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[perm_programa]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','permissoes.php?acao=form_edit&perm_codigo='.$row[perm_codigo])."</td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','permissoes.php?acao=del&perm_codigo='.$row[perm_codigo])."</td>
	     </tr>";
     }
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
}

//------------------------------------------------------------------>
//-> Formulario de Adicao de Conteudo
//------------------------------------------------------------------>

 if($_REQUEST['acao']=="form_add") {
	 reglog($id_login,"Formulario de ADICAO PERMISSOES");


//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções de Cadastro</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=$PHP_SELF?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td>&nbsp;</td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
//
//-> Este if esta apontando quando a acao for vazia ele vai mostrar o cadastro simples
//   no cadastro completo vc vai ter que passar a variavel acao para completo

 if(($type=="" OR $_REQUEST['acao']=="simples")) {
  echo "<form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=add>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=type value=simples>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de Permissoes</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td width=110>Descricao:</td>
		<td><input type=text name=perm_descricao class=box size=70 style='text-transform:upperCase'></td>
	      </tr>
	      <tr>
		<td width=110>Programa:</td>
		<td><input type=text name=perm_programa class=box size=100></td>
	      </tr>
	      <tr>
		<td width=110>Objeto:</td>
		<td><input type=text name=perm_objeto class=box size=70></td>
	      </tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br></form>";
 }//fechamento do if
}
//}

//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>

 if($_REQUEST['acao']=="form_edit") {
	 reglog($id_login,"Formulario de EDICAO PERMISSOES");

//
//-> Formulario de edicao do cadastro SIMPLES

//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções de Cadastro</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=$PHP_SELF?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td>&nbsp;</td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

//
//-> Pegando as informcoes do banco pra mostrar no formulario
 $sqlpermissoes =                       "select *  
                                      from permissoes where perm_codigo='$perm_codigo'";
 $row=pg_fetch_array(pg_query($sqlpermissoes));

  echo "<form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=perm_codigo value=$perm_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de Especialidade</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td width=110>Descricao da Permissao:</td>
		<td><input type=text name=perm_descricao class=box size=100 value='$row[perm_descricao]'></td>
	      </tr>
	      <tr>
		<td width=110>Programa:</td>
		<td><input type=text name=perm_programa class=box size=100 value='$row[perm_programa]'></td>
	      </tr>
	      <tr>
		<td width=110>Objeto:</td>
		<td><input type=text name=perm_objeto class=box size=100 value='$row[perm_objeto]'></td>
	      </tr>
	      <tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br></form>";
}

//------------------------------------------------------------------>
//-> SQL's
//------------------------------------------------------------------>
//
//-> ADD <---------------------------------------------------------->

 if($_REQUEST['acao']=="add") {
	 reglog($id_login,"Adicionando Registro em PERMISSOES");
    
    $sql_verif = "SELECT perm_programa FROM permissoes WHERE perm_programa = '".$perm_programa."' ";
    
    $res_verif = pg_query($sql_verif);
    $existe = 0;
    if( pg_num_rows($res_verif) > 0 )
    {
    	$existe = 1;
    }

    if( $existe != 1 && $perm_programa != "" )
    {
		
	/*$sl = pg_query("insert into permissoes ( " .
		"perm_descricao, " .
		"perm_programa, " .
		"perm_objeto  " .
		") values ( " .
		($perm_descricao ? "'".strtoupper($perm_descricao)."'" : "null") . ", " .
		($perm_programa ? "'$perm_programa'" : "null") . ", " .
		($perm_objeto ? "'$perm_objeto'" : "null") . "  " .
		")");*/
		$sql2= "insert into permissoes
							 (perm_descricao,
							  perm_programa,
							  perm_objeto)
					 values
							 (upper('$perm_descricao'),
							  '$perm_programa',
							  '$perm_objeto')";
    	$sl = pg_query($sql2);
	
		
	$sql = pg_query("SELECT * FROM usuarios");
	$nli = pg_fetch_array (pg_query("select nextval('seq_permissoes')"));
	$perm_codigo = ($nli[0]-1);
	while($usu=pg_fetch_array($sql)) 
	{
		$usr_codigo = $usu[usr_codigo];
		$qq = pg_query("insert into usuarios_permissoes (usr_codigo,perm_codigo,perm_set,nivel_i,nivel_a,nivel_d,nivel_l,nivel_b) values ('$usr_codigo','$perm_codigo','S','N','N','N','N','N')");
	}
	msg($id_login,$_REQUEST['acao'],$sql);
	}
	else
	{
		if( $existe == 1 )
		{
			$GetNameFile = $_SERVER["SCRIPT_NAME"];
			echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
			<table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
			<tr bgcolor=f9f9f9>
			<td align=center><font size=2 color=red><b>ERRO: Programa já existe.</b></font></td>
			</tr>
			</table><br>";
			echo "<SCRIPT LANGUAGE=\"JavaScript\">
			setTimeout(\"location='$GetNameFile?id_login=$id_login'\", 2000);
			</SCRIPT>";
		}
		else
		{
			msg($id_login,$_REQUEST['acao'],$sql);
		}
	}
}

//
//-> EDIT <--------------------------------------------------------->

 if($_REQUEST['acao']=="edit") {
	 reglog($id_login,"Editando PERMISSOES $perm_codigo");

  $sql = pg_query("update permissoes set " .
            ($perm_descricao ? "perm_descricao='$perm_descricao'" : "perm_descricao=null") . "," .
            ($perm_programa ? "perm_programa='$perm_programa'" : "perm_programa=null") . "," .
            ($perm_objeto ? "perm_objeto='$perm_objeto'" : "perm_objeto=null") . " " .
            "where perm_codigo='$perm_codigo'");

msg($id_login,$_REQUEST['acao'],$sql);
}

//
//-> DEL <---------------------------------------------------------->

 if($_REQUEST['acao']=="del") {	
 	
	 reglog($id_login,"Exluindo Registro de PERMISSOES $perm_codigo");
//	 $bloquea="SELECT *
//			 	 FROM permissoes p
//				 JOIN usuarios_permissoes up
//			   	   ON p.perm_codigo = up.perm_codigo
//			    WHERE p.perm_codigo = $perm_codigo";
//	 $queryB= pg_query($bloquea);
//	 if(pg_num_rows($queryB) > 0){
//	 	echo"<script>alert('Permissão ligada a um usuário.')</script>";
//	 	echo "SELECT *
//			 	 FROM permissoes p
//				 JOIN usuarios_permissoes up
//			   	   ON p.perm_codigo = up.perm_codigo
//			    WHERE p.perm_codigo = $perm_codigo";
//	 	return false;
//	 }
	
 $sql = pg_query("select *from usuarios");
while($usu=pg_fetch_array($sql)) {
   $sq = pg_query("delete from usuarios_permissoes where perm_codigo='$perm_codigo'");
}
  $sql = pg_query("delete from permissoes where perm_codigo='$perm_codigo'");
msg($id_login,$_REQUEST['acao'],$sql);
}

?>
</fieldset>