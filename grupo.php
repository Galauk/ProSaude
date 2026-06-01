<script>
function validaNome(){
	gru_nome = document.getElementById("gru_nome").value;
	if(gru_nome == ''){
		alert('Preencha o nome do grupo');
		exit;
	}
	document.forms['form'].submit();
	
}
</script>

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


reglog($id_login,"Acessando Grupo em Materiais");
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

// if(empty($acao)) {
 if(empty($acao) OR $acao=='form_grupo') {

//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Op&ccedil;&otildees</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=materiais.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td width=200>".ChmodBtn($id_login,'adicionar','grupo.php?acao=form_add')."</td>";
	       if (chmodbtn($id_login,"procurar_if","grupo.php"))
	       {	       
		  echo "<form method=post action=$PHP_SELF>";
	       }
		echo "<input type=hidden name=acao value=busca>
		      <input type=hidden name=id_login value=$id_login>
		      <td width=30>Buscar:</td>
		      <td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
		      <td>".ChmodBtn($id_login,'procurar','grupo.php')."</td></form>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

//
//-> Listando
  
  if (chmodbtn($id_login,"listar_if","grupo.php"))
  {
	echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	       <tr>
		<td>
		 <fieldset>
		  <legend>Listando &uacuteltimos <b>15</b> Grupos Cadastrados</legend>
		   <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
		    <tr bgcolor=F9f9f9>
		      <td width=10 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Codigo</td>
		      <td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Descricao</td>
		      <td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
      
	 $sql=pg_query("select * from grupo order by gru_codigo desc limit 15");
	   while($row=pg_fetch_array($sql)) {
	     echo "<tr>
		     <td width=10 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[gru_codigo]</td>
		     <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[gru_nome]</td>
		     <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','grupo.php?acao=form_edit&gru_codigo='.$row[gru_codigo])."</td>
		     <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','grupo.php?acao=del&gru_codigo='.$row[gru_codigo])."</td>
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

 if($acao=="busca") {
reglog($id_login,"Buscando Grupo em Materiais $palavra_chave");
//
//-> Verificando Busca
if(strlen($palavra_chave)<"1") {
        echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font color=red size=2><b>ERRO</b></font><br>Busca com menos de <b>3</b> caracteres n�o permitida</td>
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
	    <legend>Op��es</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=materiais.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td width=200>".ChmodBtn($id_login,'adicionar','grupo.php?acao=form_add')."</td>";
	       if (chmodbtn($id_login,"procurar_if","grupo.php"))
	       {
		  echo "<form method=post action=$PHP_SELF>";
	       }
	       echo "
		    <input type=hidden name=acao value=busca>
		    <input type=hidden name=id_login value=$id_login>
		    <td width=30>Buscar:</td>
		    <td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
		    <td>".ChmodBtn($id_login,'procurar','grupo.php')."</td></form>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

$sql=pg_query("select * from grupo where (gru_nome ilike upper('%$palavra_chave%'))");
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
		<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Descricao</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

     while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[gru_codigo]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[gru_nome]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','grupo.php?acao=form_edit&gru_codigo='.$row[gru_codigo])."</td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','grupo.php?acao=del&gru_codigo='.$row[gru_codigo])."</td>
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

 if($acao=="form_add") {
reglog($id_login,"Formulario de Adicao Grupo");
//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Op&ccedil&otildees de Cadastro</legend>
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

 if(($type=="" OR $acao=="simples")) {
  echo "<form method=post action='#' name=form>
	<input type=hidden name=acao value=add>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=type value=simples>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de Grupo</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td width=110>Descri&ccedil&atildeo do Grupo:</td>
		<td><input type=text name=gru_nome id=gru_nome class=box size=70></td>
	      </tr>
		<tr>
			<td> Possui Validade: </td>
			<td>
				<select name=gru_validade class=box>
					<option value='N'>N&Atilde;O</option>
					<option value='S'>SIM</option>
				</select>
			</td>
		</tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg  Onclick=validaNome()></td>
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

 if($acao=="form_edit") {
reglog($id_login,"Formulario de Edicao em Grupo");
//
//-> Formulario de edicao do cadastro SIMPLES

//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Op&ccedil&otildees de Cadastro</legend>
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
 $sqlgrupo =                       "select *  
                                      from grupo where gru_codigo='$gru_codigo'";
 $row=pg_fetch_array(pg_query($sqlgrupo));

  echo "<form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=gru_codigo value=$gru_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de Grupo</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td width=110>Descri&ccedil&atildeo do Grupo:</td>
		<td><input type=text name=gru_nome class=box size=70 value='$row[gru_nome]'></td>
	      </tr>
			<tr>
			<td> Possui Validade: </td>
			<td>
				<select name=gru_validade class=box>";
				if($row[gru_validade] != "S")
				{
					echo "<option value='N' selected>N&Atilde;O</option>
								<option value='S'>SIM</option>";
				} else {
					echo "<option value='N'>N&Atilde;O</option>
								<option value='S' selected>SIM</option>";
				}
				echo "</select>
			</td>
		</tr>
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

 if($acao=="add") {
    $sql = pg_query("insert into grupo ( " .
            "gru_nome, gru_validade  " .
            ") values ( " .
            "upper('$gru_nome'), ('$gru_validade')  " .
            ")");
reglog($id_login,"Adicionando Grupo $gru_nome");
msg($id_login,$acao,$sql);
}

//
//-> EDIT <--------------------------------------------------------->

 if($acao=="edit") {
	$sql = "update grupo set " .
            "gru_nome=upper('$gru_nome'), gru_validade = '$gru_validade' " .
            "where gru_codigo='$gru_codigo'"; 
	$sql = pg_query("update grupo set " .
            "gru_nome=upper('$gru_nome'), gru_validade = '$gru_validade' " .
            "where gru_codigo='$gru_codigo'");
reglog($id_login,"Editando Grupo $gru_nome");
msg($id_login,$acao,$sql);
}

//
//-> DEL <---------------------------------------------------------->

 if($acao=="del") {
  $sql = pg_query("delete from grupo where gru_codigo='$gru_codigo'");
reglog($id_login,"Excluindo Grupo $gru_codigo");
msg($id_login,$acao,$sql);
}

?>

