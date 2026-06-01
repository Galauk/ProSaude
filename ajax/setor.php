<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);

	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
	 reglog($id_login,"Entrando em SETOR");
//------------------------------------------------------------------>

// if(empty($acao)) {
 if(empty($acao) OR $acao=='form_setor') {

//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=materiais.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td width=200>".ChmodBtn($id_login,'adicionar','setor.php?acao=form_add')."</td>
	       <form method=post action=$PHP_SELF>
		<input type=hidden name=id_login value=$id_login>
		<input type=hidden name=acao value=busca>
	<input type=hidden name=id_login value=$id_login>
	       <td width=30>Buscar:</td>
	       <td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	       <td>".ChmodBtn($id_login,'procurar','setor.php')."</td></form>
	       <td width=107><a href='logoff.php?id_login=$id_login' target='_parent'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif border=0></a></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

//
//-> Listando
  
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Listar Setores Cadastrados</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9>
		<td width=10 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Codigo</td>
		<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Descricao</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

   //$sql=pg_query("select * from setor order by set_codigo desc limit 15");
     while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td width=10 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[set_codigo]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[set_nome]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','setor.php?acao=form_edit&set_codigo='.$row[set_codigo])."</td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','setor.php?acao=del&set_codigo='.$row[set_codigo])."</td>
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
 reglog($id_login,"Buscando em SETOR: $palavra_chave ");


/* FOI RETIRADO ESTE DADO A PEDIDO DA KEILA-APUCARANA 24/10/2005
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
*/

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
	       <td width=79><a href=materiais.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td width=200>".ChmodBtn($id_login,'adicionar','setor.php?acao=form_add')."</td>
	       <form method=post action=$PHP_SELF>
		<input type=hidden name=id_login value=$id_login>
		<input type=hidden name=acao value=busca>
	<input type=hidden name=id_login value=$id_login>
	       <td width=30>Buscar:</td>
	       <td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	       <td>".ChmodBtn($id_login,'procurar','setor.php')."</td></form>
	       <td width=107><a href='logoff.php?id_login=$id_login' target='_parent'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif border=0></a></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

$sql=pg_query("select * from setor where (set_nome like upper('$palavra_chave%')) order by set_nome");
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
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[set_codigo]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[set_nome]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','setor.php?acao=form_edit&set_codigo='.$row[set_codigo])."</td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','setor.php?acao=del&set_codigo='.$row[set_codigo])."</td>
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
	 reglog($id_login,"Formulario de ADICAO SETOR");


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

 if(($type=="" OR $acao=="simples")) {
  echo "<form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=add>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=type value=simples>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de Setor</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	    <tr>
		    <td width=110>Descricao do Setor:</td>
		    <td><input type=text name=set_nome class=box size=70></td>
	    </tr>
        <tr>
               <td width=70>Unidade:</td>
               <td>
                   <select name=uni_codigo class=boxr>";
                   $sql = pg_query("select * from unidade order by uni_desc");
                   echo "<option value=''>.........</option>";
                   while($uni=pg_fetch_array($sql)) {
                        echo "<option value='$uni[uni_codigo]'> $uni[uni_desc]</option>";
                   }
                  echo "</select>
              </td>
        </tr>
	    <tr>
		    <td width=110>Centro Estocador:</td>
		    <td>
		        <select name=set_estoque class=box>
          	         <option value=S>Sim</option>
    		         <option value=N>Nao</option>
		        </select>
            </td>    
	    </tr>
	    <tr>
		    <td width=110>Dispensa&ccedil;&atilde;o:</td>
		    <td>
		        <select name=set_farmacia class=box>
          	         <option value=S>Sim</option>
    		         <option value=N>Nao</option>
		        </select>
            </td>    
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

 if($acao=="form_edit") {
	 reglog($id_login,"Formulario de EDICAO SETOR");

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
 $sqlsetor =                       "select *  
                                      from setor where set_codigo='$set_codigo'";
 $row=pg_fetch_array(pg_query($sqlsetor));
 if ($row[set_estoque] == 'S' ) {
    $vlest1 = 'selected ';
    $vlest2 = '';
    }
  else {
    $vlest1 = '';
    $vlest2 = 'selected';
    }
 if ($row[set_farmacia] == 'S' ) {
    $vlfar1 = 'selected ';
    $vlfar2 = '';
    }
  else {
    $vlfar1 = '';
    $vlfar2 = 'selected';
    }

  echo "<form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=set_codigo value=$set_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de Setor</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td width=110>Descricao do Setor:</td>
		<td><input type=text name=set_nome class=box size=70 value='$row[set_nome]'></td>
	      </tr>
        <tr>
               <td width=70>Unidade:</td>
               <td>
                   <select name=uni_codigo class=boxr>";
                   $sql = pg_query("select * from unidade order by uni_desc");
                   echo "<option value=''>.........</option>";
                   while($unidade=pg_fetch_array($sql)) {
	                    echo ($unidade[uni_codigo]==$row[uni_codigo])?"<option value='$unidade[uni_codigo]' selected>$unidade[uni_desc]</option>":"<option value='$unidade[uni_codigo]'>$unidade[uni_desc]</option>";
                   }
                  echo "</select>
              </td>
        </tr>
	    <tr>
		    <td width=110>Centro Estocador:</td>
		    <td>
		        <select name=set_estoque class=box>
          	         <option value=S $vlest1>Sim</option>
    		         <option value=N $vlest2>Nao</option>
		        </select>
            </td>    
	    </tr>
	    <tr>
		    <td width=110>Dispensa&ccedil;&atilde;o:</td>
		    <td>
		        <select name=set_farmacia class=box>
          	         <option value=S $vlfar1>Sim</option>
    		         <option value=N $vlfar2>Nao</option>
		        </select>
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
	 reglog($id_login,"Adicionando Registro em SETOR");


    $sql = pg_query("insert into setor ( " .
            "set_nome ," .
            "set_estoque, " .
            "uni_codigo, " .
            "set_farmacia  " .
            ") values ( " .
            "upper('$set_nome'), " .
            "upper('$set_estoque'), " .
            ($uni_codigo  ? "'$uni_codigo'"   : "null") . "," .
            "upper('$set_farmacia')  " .
            ")");

 reglog($id_login,"Adicionando Setor $set_nome ");
msg($id_login,$acao,$sql);
}

//
//-> EDIT <--------------------------------------------------------->

 if($acao=="edit") {

  $sql = pg_query("update setor set " .
            "set_nome=upper('$set_nome')," .
            "set_estoque=upper('$set_estoque')," .
            "set_farmacia=upper('$set_farmacia') " .
            "where set_codigo='$set_codigo'");

 reglog($id_login,"Alterando Setor $set_nome ");
msg($id_login,$acao,$sql);
}

//
//-> DEL <---------------------------------------------------------->

 if($acao=="del") {
	 reglog($id_login,"Exluindo Registro de SETOR $set_codigo");

  $sql = pg_query("delete from setor where set_codigo='$set_codigo'");
msg($id_login,$acao,$sql);
}

?>

