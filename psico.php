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
	 reglog($id_login,"Entrando em PSICO");
//------------------------------------------------------------------>

// if(empty($acao)) {
 if(empty($acao) OR $acao=='form_psico') {

//
//-> Botoes
  echo "<fieldset>
	    <legend>Opções</legend>
	       <a href=farmacia.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>
	       ".ChmodBtn($id_login,'adicionar','psico.php?acao=form_add')."

	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>";
			if(chmodbtn($id_login, "buscar_if", "psico.php"))
			{
			  echo "<form method=post action=$PHP_SELF>";
			}

				echo "<input type=hidden name=acao value=busca>
				<input type=hidden name=id_login value=$id_login>
				<td width=30>Buscar:</td>
				<td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
				<td>".ChmodBtn($id_login,'procurar','psico.php')."</td>
		   
		   </form>
	      </tr>
	     </table>
	   </fieldset>
	  <br>";

//
//-> Listando
  if (chmodbtn($id_login,"listar_if","psico.php"))
  {
	echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	       <tr>
		<td>
		 <fieldset>
		  <legend>Listando Últimos <b>15</b> Psicotropicos Cadastrados</legend>
		   <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
		    <tr bgcolor=F9f9f9>
		      <td width=10 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Codigo</td>
		      <td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Descricao</td>
		      <td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
      
	 $sql=pg_query("select * from psicotropicos order by psico_codigo desc limit 15");
	   while($row=pg_fetch_array($sql)) {
	     echo "<tr>
		     <td width=10 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[psico_codigo]</td>
		     <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[psico_nome]</td>
		     <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','psico.php?acao=form_edit&psico_codigo='.$row[psico_codigo])."</td>
		     <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','psico.php?acao=del&psico_codigo='.$row[psico_codigo])."</td>
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
//
//-> Verificando Busca
 reglog($id_login,"Buscando em PSICO: $palavra_chave ");

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
	       <td width=200>".ChmodBtn($id_login,'adicionar','psico.php?acao=form_add')."</td>
	       <form method=post action=$PHP_SELF>
		<input type=hidden name=acao value=busca>
	<input type=hidden name=id_login value=$id_login>
	       <td width=30>Buscar:</td>
	       <td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	       <td>".ChmodBtn($id_login,'procurar','psico.php')."</td></form>
	       <td width=107><a href='logoff.php?id_login=$id_login' target='_parent'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif border=0></a></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

$sql=pg_query("select * from psicotropicos where (psico_nome like '%$palavra_chave%')");
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
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[psico_codigo]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[psico_nome]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','psico.php?acao=form_edit&psico_codigo='.$row[psico_codigo])."</td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','psico.php?acao=del&psico_codigo='.$row[psico_codigo])."</td>
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
	 reglog($id_login,"Formulario de ADICAO PSICO");


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
	    <legend>Cadastro de Psicotropico</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
			<td width=110>Descricao do Psicotropico:</td>
			<td><input type=text name=psico_nome class=box size=70></td>
	      </tr>
		   <tr>
			<td width=110>Exige cod. da receita?:</td>
			<td>
				<input type=radio name=psico_exige_codigo class=box size=70 value='t'>Sim
				<input type=radio name=psico_exige_codigo class=box size=70 value='f'>Não
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
	 reglog($id_login,"Formulario de EDICAO PSICO");

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
 $sqlpsico =                       "select *  
                                      from psicotropicos where psico_codigo='$psico_codigo'";
 $row=pg_fetch_array(pg_query($sqlpsico));

  echo "<form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=psico_codigo value=$psico_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de Psicotropico</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td width=110>Descricao do Psicotropico:</td>
		<td><input type=text name=psico_nome class=box size=70 value='$row[psico_nome]'></td>
	      </tr>
		  <tr>
			<td width=110>Exige cod. da receita?:</td>
			<td>
				<input type=radio name=psico_exige_codigo ".($row[psico_exige_codigo] == "t" ? "checked=checked" : "")." class=box size=70 value='t'>Sim
				<input type=radio name=psico_exige_codigo ".($row[psico_exige_codigo] == "f" ? "checked=checked" : "")." class=box size=70 value='f'>Não
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
	 reglog($id_login,"Adicionando Registro em PSICO");


         $sql = pg_query("insert into psicotropicos ( " .
            "psico_nome,  " .
			"psico_exige_codigo  " .
            ") values ( " .
            "upper('$psico_nome'),  " .
            "'$psico_exige_codigo'".
            ")") or die(pg_last_error());

msg($id_login,$acao,$sql);
}

//
//-> EDIT <--------------------------------------------------------->

 if($acao=="edit") {
	 reglog($id_login,"Editando PSICO $psico_codigo");

  $sql = pg_query("update psicotropicos set " .
            "psico_nome=upper('$psico_nome'), " .
			"psico_exige_codigo='$psico_exige_codigo'".
            "where psico_codigo='$psico_codigo'");

msg($id_login,$acao,$sql);
}

//
//-> DEL <---------------------------------------------------------->

 if($acao=="del") {
	 reglog($id_login,"Exluindo Registro de PSICO $psico_codigo");

  $sql = pg_query("delete from psicotropicos where psico_codigo='$psico_codigo'");
msg($id_login,$acao,$sql);
}

?>

