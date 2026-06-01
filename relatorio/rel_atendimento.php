<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

 if(empty($acao)) {

//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
 <tr>
  <td>
   <fieldset>
    <legend>Opções</legend>
    <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
      <tr>
        <td width=200><a href=$PHP_SELF?acao=form_add><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></a></td>
        <form method=post action=$PHP_SELF>
          <input type=hidden name=acao value=busca>
	        <td width=30>Buscar: </td>
	        <td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"> </td>
	        <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td>
        </form>
	    <td width=107><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif></td>
	  </tr>
	</table>
   </fieldset>
  </td>
 </tr>
        </table><br>";

//
//-> Listando
  
echo "
<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
 <tr>
  <td>
   <fieldset>
    <legend>Listando Últimos <b>15</b> Unidades Cadastrados</legend>
     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
      <tr bgcolor=F9f9f9>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Codigo</td>
		<td width=270 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Descricao</td>
		<td width=180 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Localizacao</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
        $sql=pg_query("select uni_codigo, uni_desc, uni_localizacao, uni_responsavel ". 
                      "from unidade ".
                      "order by uni_codigo desc limit 15");
        while($row=pg_fetch_array($sql)) {
        echo "<tr>
	     <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[uni_codigo]</td>
	     <td width=270 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[uni_desc]</td>
	     <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[uni_localizacao]</td>
         <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?acao=form_edit&med_codigo=$row[med_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a></td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?acao=del&med_codigo=$row[med_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
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
//-> Mostrando o resultado da busca
//------------------------------------------------------------------>

 if($acao=="busca") {
//
//-> Verificando Busca
if(strlen($palavra_chave)<="3") {
        echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font color=red size=2><b>ERRO</b></font><br>Busca com menos de <b>3</b> caracteres não permitida</td>
                 </tr>
                </table><br>";
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='$PHP_SELF?id_login=$id_login'\", 2000);
              </SCRIPT>";
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
	       <td width=200><a href=$PHP_SELF?acao=form_add><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></a></td>
	       <form method=post action=$PHP_SELF>
		<input type=hidden name=acao value=busca>
	       <td width=30>Buscar:</td>
	       <td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td></form>
	       <td width=107><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

$sql=pg_query("select uni_codigo, uni_desc, uni_localizacao from unidade where (uni_desc like '%$palavra_chave%')");
   $num=pg_num_rows($sql);
   if($num=="0") { $resp = "Nenhum Registro encontrado com \"       $palavra_chave\""; }
   if($num=="1") { $resp = "Encontrado <b>$num</b> Registro com \"  $palavra_chave\""; }
   if($num>"1")  { $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }

   echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>$resp</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9>
	    	<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Codigo</td>
    		<td width=270 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Nome</td>
    		<td width=120 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Localizacao</td>
    		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

     while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[uni_codigo]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[uni_desc]</td>
	       <td align=left style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[uni_localizacao]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?acao=form_edit&uni_codigo=$row[uni_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a></td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?acao=del&uni_codigo=$row[uni_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
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

//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções de Cadastro</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=unidade.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
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
    echo "<form method=post action=$PHP_SELF>
  	  <input type=hidden name=acao value=add>
	  <input type=hidden name=type value=simples>
	  <table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	   <tr>
	    <td>
	     <fieldset>
	      <legend>Cadastro de Unidade</legend>
	       <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	        <tr>
		       <td width=70>Descricao:</td>
		       <td><input type=text name=uni_desc class=box size=10></td>
	        </tr>
	        <tr>
		       <td width=70>Localizacao:</td>
		       <td><input type=text name=uni_localizacao class=box size=100></td>
	        </tr>
	        <tr>
		       <td width=70>Responsavel:</td>
		       <td><input type=text name=uni_responsavel class=box size=70></td>
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
  } 
 }


//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>

 if($acao=="form_edit") {
//
//-> Formulario de edicao do cadastro SIMPLES

//
//-> Pegando as informcoes do banco pra mostrar no formulario
 $sqlunidade = "select * from unidade where uni_codigo='$uni_codigo'";
 $row=pg_fetch_array(pg_query($sqlunidade));

  echo "<br><br><form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=uni_codigo value=$uni_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de Unidades</legend>
	      <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	        <tr>
  		      <td width=70>Unidade:</td>
		      <td><input type=text name=uni_desc class=box size=70 value='$row[uni_desc]'></td>
	      </tr>
	      <tr>
		      <td width=70>Localizacao:</td>
		      <td><input type=text name=uni_localizacao class=box size=100 value='$row[uni_localizacao]'></td>
	      </tr>
	      <tr>
		      <td width=70>Reponsavel:</td>
		      <td><input type=text name=uni_responsavel class=box size=60 value='$row[uni_responsavel]'></td>
	      </tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><a href=unidade.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>&nbsp;&nbsp;<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
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
    $sql = pg_query("insert into unidade ( " .
            "uni_desc, " .
            "uni_localizacao, " .
            "uni_responsavel " .
            ") values ( " .
            ($uni_desc        ? "upper('$uni_desc') "        : "null ") . ", " .
            ($uni_localizacao ? "upper('$uni_localizacao') " : "null ") . ", " .
            ($uni_responsavel ? "upper('$uni_responsavel') " : "null ") . 
            ")");

 msg($acao,$sql);
}

//
//-> EDIT <--------------------------------------------------------->

 if($acao=="edit") {
  $sql = pg_query("update unidade set " .
            ($uni_desc        ? "uni_desc=upper('$uni_desc') "               : "uni_desc='NAOTEM' ")    . ", " .
            ($uni_localizacao ? "uni_localizacao=upper('$uni_localizacao') " : "uni_localizacao=null ") . ", " .
            ($uni_responsavel ? "uni_responsavel=upper('$uni_responsavel') " : "uni_responsavel=null ") . 
            " where uni_codigo='$uni_codigo'");

msg($acao,$sql);
}

//
//-> DEL <---------------------------------------------------------->

 if($acao=="del") {
  $sql = pg_query("delete from unidade where uni_codigo='$uni_codigo'");
msg($acao,$sql);
}

?>

