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
			  <td width=82><a href=$PHP_SELF?acao=form_add><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></a></td>
			  <td width=60><a href=grupo.php?acao=form_grupo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/grupo_on.jpg border=0></a></td>
			  <td width=89><a href=fornecedor.php?acao=form_forn><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/fornecedor_on.jpg border=0></a></td>
			  <td width=89><a href=entrada.php?acao=form_entrada><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/entrada_on.jpg border=0></a></td>
			  <td width=89><a href=saida.php?acao=form_saida>Saida</a></td>
			  <td width=89><a href=remanejamento.php?acao=form_remanejamento>Remanejamento</a></td>
			  <td width=89><a href=dispensacao.php?acao=form_dispensa>Dispensacao</a></td>
			 </tr >
			 <tr>
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



//
//-> Opções de Impressão
//


  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Listando os Produtos Cadastrados</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Codigo</td>
		<td width=500 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Nome</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

   $sql=pg_query("select pro_codigo, pro_nome from produto order by pro_nome ");
     while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[pro_codigo]</td>
	       <td width=300 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[pro_nome]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?acao=form_edit&pro_codigo=$row[pro_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a></td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?acao=del&pro_codigo=$row[pro_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
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
echo $v1;
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

$sql=pg_query("select pro_codigo, pro_nome from produto where (pro_nome like '%$palavra_chave%')");
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
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Codigo</td>
		<td width=270 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Nome</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

     while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[pro_codigo]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[pro_nome]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?acao=form_edit&pro_codigo=$row[pro_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a></td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?acao=del&pro_codigo=$row[pro_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
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
	       <td width=79><a href=materiais.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
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
	    <legend>Cadastro de Produto</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td width=70>Nome:</td>
		<td><input type=text name=pro_nome class=box size=60></td>
	      </tr>
	      <tr>
		<td width=70>Grupo:</td>
		<td>
		 <select name=gru_codigo class=box>";
	    //
	    //-> SQL do Estado
	    $query = pg_query("select gru_codigo, gru_nome from grupo order by gru_nome");
	      while($grupo=pg_fetch_array($query)) {
	       echo "<option value='$grupo[gru_codigo]'>$grupo[gru_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>Unidade:</td>
		<td><input type=text name=pro_unidade class=box size=03></td>
	      </tr>
	      <tr>
		<td width=70>Codigo Barras: td>
		<td><input type=text name=pro_barcode class=box size=15></td>
	      </tr>
	      <tr>
		<td width=70>Custo de Referencia: </td>
		<td><input type=text name=pro_custo class=box size=20></td>
	      </tr>
	      <tr>
		<td width=70>Embalagem:</td>
		<td><input type=text name=pro_embalagem class=box size=60></td>
	      </tr>
	      <tr>
		<td width=70>Descricao Tecnica:</td>
		<td><input type=textarea name=pro_descricao_tecnica class=box size=40></td>
	      </tr>
	      <tr>
		<td width=70>Observacao:</td>
		<td><input type=textarea name=pro_observacao class=box size=40></td>
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
//
//-> Formulario de edicao do cadastro SIMPLES

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
 $sqlproduto =                       "select *
                                      from produto where pro_codigo ='$pro_codigo '";
 $row=pg_fetch_array(pg_query($sqlproduto));

  echo "<br><br><form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=pro_codigo value=$pro_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de Produto</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>

	      <tr>
		<td width=70>Nome:</td>
		<td><input type=text name=pro_nome class=box size=60 value='$row[pro_nome]'></td>
	      </tr>
	      <tr>
		<td width=70>Grupo:</td>
		<td>
		 <select name=gru_codigo class=box>";
	    //
	    //-> SQL do Estado
	    $query = pg_query("select gru_codigo, gru_nome from grupo order by gru_nome");
	      while($grupo=pg_fetch_array($query)) {
	       echo ($grupo[gru_codigo]==$row[gru_codigo])?"<option value='$grupo[gru_codigo]' selected>$grupo[gru_nome]</option>":"<option value='$grupo[gru_codigo]'>$grupo[gru_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>Unidade:</td>
		<td><input type=text name=pro_unidade class=box size=03 value='$row[pro_unidade]'></td>
	      </tr>
	      <tr>
		<td width=70>Codigo Barras: td>
		<td><input type=text name=pro_barcode class=box size=15 value='$row[pro_barcode]'></td>
	      </tr>
	      <tr>
		<td width=70>Custo de Referencia: </td>
		<td><input type=text name=pro_custo class=box size=20 value='$row[pro_custo]'></td>
	      </tr>
	      <tr>
		<td width=70>Embalagem:</td>
		<td><input type=text name=pro_embalagem class=box size=60 value='$row[pro_embalagem]'></td>
	      </tr>
	      <tr>
		<td width=70>Descricao Tecnica:</td>
		<td><input type=textarea name=pro_descricao_tecnica class=box size=40 value='$row[pro_descricao_tecnica]'></td>
	      </tr>
	      <tr>
		<td width=70>Observacao:</td>
		<td><input type=textarea name=pro_observacao class=box size=40 value='$row[pro_observacao]'></td>
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
    $sql = pg_query("insert into produto ( " .
            "pro_nome, " .
            "gru_codigo, " .
            "pro_unidade, " .
            "pro_barcode, " .
            "pro_custo, " .
            "pro_embalagem, " .
            "pro_descricao_tecnica, " .
            "pro_observacao " .
            ") values ( " .
            "upper('$pro_nome'), " .
            ($gru_codigo ? "'$gru_codigo'" : "null") . ", " .
            ($pro_unidade ? "'$pro_unidade'" : "null") . ", " .
            ($pro_barcode ? "'$pro_barcode'" : "null") . ", " .
            ($pro_custo ? "'$pro_custo'" : "null") . ", " .
            ($pro_embalagem ? "'$pro_embalagem'" : "null") . ", " .
            ($pro_descricao_tecnica ? "'$pro_descricao_tecnica'" : "null") . ", " .
            ($pro_observacao ? "'$pro_observacao'" : "null") . "  " .
            ")");

msg($acao,$sql);
}

//
//-> EDIT <--------------------------------------------------------->

 if($acao=="edit") {
  $sql = pg_query("update produto set " .
            "pro_nome=upper('$pro_nome'), " .
            "gru_codigo='$gru_codigo', " .
            ($pro_unidade ? "pro_unidade ='$pro_unidade '" : "pro_unidade =null") . ", " .
            ($pro_barcode ? "pro_barcode='$pro_barcode'" : "pro_barcode=null") . ", " .
            ($pro_custo ? "pro_custo='$pro_custo'" : "pro_custo=null") . ", " .
            ($pro_embalagem ? "pro_embalagem='$pro_embalagem'" : "pro_embalagem=null") . ", " .
            ($pro_descricao_tecnica ? "pro_descricao_tecnica='$pro_descricao_tecnica'" : "pro_descricao_tecnica=null") . ", " .
            ($pro_observacao ? "pro_observacao='$pro_observacao'" : "pro_observacao=null") . " " .
            "where pro_codigo='$pro_codigo'");
#            echo $sql;
#            exit(0);

msg($acao,$sql);
}

//
//-> DEL <---------------------------------------------------------->

 if($acao=="del") {
  $sql = pg_query("delete from produto where pro_codigo='$pro_codigo'");
msg($acao,$sql);
}

?>
