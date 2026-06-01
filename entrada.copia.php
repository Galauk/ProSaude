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

 if (empty($acao) OR ($acao == 'form_entrada')) {

//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=95><a href=$PHP_SELF?acao=form_add><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></a></td>
	       <form method=post action=$PHP_SELF>
		<input type=hidden name=acao value=busca>
	       <td width=180 align=right>Buscar (por Fornecedor):</td>
	       <td width=90><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td></form>
	       <td width=107><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif></td>
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
	    <legend>Listando Últimas <b>15</b> Entradas Cadastradas</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Data</td>
		<td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Fornecedor</td>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Nr. Nota</td>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Tipo Entrada</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

   $sql=pg_query("select mov_codigo, for_nome, to_char(mov_data,'DD/MM/YYYY') as mov_data , mov_nr_nota,
                  case when mov_entrada = 'E' then 'Nota Fiscal'
                       when mov_entrada = 'A' then 'Ajuste'
                       when mov_entrada = 'M' then 'Emprestimo'
                       when mov_entrada = 'I' then 'Inventario'
                       when mov_entrada = 'D' then 'Doacao'
                       when mov_entrada = 'P' then 'Permuta'
                       when mov_entrada = 'O' then 'Outras Entradas'
                  end as tipoentrada     
                  from movimento, fornecedor
                  where movimento.for_codigo = fornecedor.for_codigo
                  and   mov_tipo = 'E' 
                  order by mov_codigo desc limit 15");
     while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mov_data]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[for_nome]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mov_nr_nota]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[tipoentrada]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?acao=form_edit&mov_codigo=$row[mov_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a></td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?acao=del&mov_codigo=$row[mov_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
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
	       <td width=95><a href=$PHP_SELF?acao=form_add><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></a></td>
	       <form method=post action=$PHP_SELF>
		<input type=hidden name=acao value=busca>
	       <td width=180 align=right>Buscar (por Fornecedor):</td>
	       <td width=90><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td></form>
	       <td width=107><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

   $sql=pg_query("select mov_codigo, for_nome, to_char(mov_data, 'dd/mm/yyyy') as mov_data, mov_data as mov_data2 , mov_nr_nota
                  from movimento, fornecedor
                  where movimento.for_codigo = fornecedor.for_codigo
                  and   mov_tipo = 'E' 
                  and   for_nome like '$palavra_chave%'
                  order by for_nome, mov_data2 ");
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
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Data</td>
		<td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Fornecedor</td>
		<td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Num. Nota</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

     while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mov_data]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[for_nome]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mov_nr_nota]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?acao=form_edit&mov_codigo=$row[mov_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a></td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?acao=del&mov_codigo=$row[mov_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
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
	       <td width=79><a href=entrada.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
            <form name=formbusca method=post action=$PHP_SELF>
             <input type=hidden name=acao value=form_add>
             <input type=hidden name=action value=buscar>
             <input type=hidden name=id_login value=$id_login>
             <td width=125 align=right>Procurar Fornecedor</td>
             <td width=30><input type=text name=palavra class=box></td>
             <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td>
	   </fieldset></form>
	  </td>
	 </tr>
        </table></table><br>";

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
	    <legend>Cabecalho da Nota</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
     if ($action == "addfornecedor") {
        $sqlfornec=pg_query("select for_codigo, for_nome
                from fornecedor where for_codigo = {$_POST['fornecedor']}");
        $rowfornec = pg_fetch_array($sqlfornec);        
         echo "<input type=hidden name=for_codigo value='$rowfornec[for_codigo]'>
                <tr> 
		          <td width=70>Dados do Fornecedor</td>
		          <td width=70><input type=text readonly name=for_nome   size=70 value='$rowfornec[for_nome]'></td>
               </tr>";   
        $sqlnota=pg_query("select nextval('seq_mov_codigo'::text) as novo_codigo");       
        $rownota = pg_fetch_array($sqlnota);
        echo "<input type=hidden name=mov_codigo value=$rownota[novo_codigo]>";
     }
     echo "
	      <tr>
		<td width=70>Unidade:</td>
		<td>
		 <select name=uni_entrada class=box>";
	    //
	    //-> SQL da Unidade
	    $query = pg_query("select * from unidade where uni_movimenta_estoque = 'S' order by uni_desc");
	      while($unidade=pg_fetch_array($query)) {
	       echo "<option value='$unidade[uni_codigo]'>$unidade[uni_desc]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>";
     $sqldata_hora = pg_query("select to_char(current_date, 'dd/mm/yyyy') as data, extract(hour from current_time) || ':' || extract(minute from current_time) as hora");
     $rowdata_hora = pg_fetch_array($sqldata_hora);
     $mov_data = $rowdata_hora['data'];
     $mov_dt_nota = $rowdata_hora['data'];
          echo "
	     <tr>
     		<td width=40>Data de Entrada:</td>
    		<td><input type=text name=mov_data class=box size=20 value=$mov_data></td>
         </tr>   
	     <tr>
     		<td width=40>Data da Emissao da Nota:</td>
    		<td><input type=text name=mov_dt_nota class=box size=20 value=$mov_dt_nota></td>
         </tr>   
	     <tr>
    		<td width=40>Desconto:</td>
    		<td><input type=text name=mov_desconto class=box size=20></td>
         </tr>   
	     <tr>
    		<td width=40>Numero da Nota:</td>
    		<td><input type=text name=mov_nr_nota class=box size=20></td>
        </tr>
	      <tr>
		<td width=110>Doacao:</td>
		<td>
		 <select name=mov_doacao class=box>
		  <option value=N>Nao</option>
		  <option value=S>Sim</option>
		 </select>
	      </tr>
	     <tr>
		<td width=40>Observacao:</td>
            <td ><textarea name=mov_observacao class=box cols=100 rows=2></textarea></td> 
	      </tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg></td>
	      </tr>

	  </td>
	 </tr>
        </table><br></form>";
 }//fechamento do if - acao = simples
#-------------------------------------------------------------
# BUSCA DE FORNECEDORES                                     >>
#-------------------------------------------------------------
if($action =="buscar") {
 $sql=pg_query("select for_nome, for_nome_fantasia, for_codigo
                from fornecedor where for_nome like '$palavra%' order by for_nome");
 if(pg_num_rows($sql)=="0") {
   $results="<b><font color=red>Nenhum Fornecedor encontrado com: <font color=blue>\"$palavra\"</font></font></b>";
   }
 if(pg_num_rows($sql)=="1") {
   $results="<b><font color=red>Encontrado ".pg_num_rows($sql)." Fornecedor com o Nome: <font color=blue>\"$palavra\"</font></font></b>";
   }
 if(pg_num_rows($sql)>"1") {
   $results="<b><font color=red>Encontrados ".pg_num_rows($sql)." Fornecedores com o Nome: <font color=blue>\"$palavra\"</font></font></b>";
   }
  echo "<fieldset>
	<legend><font size=2>Lista de Fornecedores $results</font></legend>";
  echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>";
 if($palavra=="") {
     echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
            <tr>
              <td align=center><font size=2 color=blue>Nenhum Fornecedor Localizado</font></td>
            </tr>
           </table>";
   }
     echo "<table width=100% cellspacing=2 cellpadding=3 border=0>
            <tr bgcolor=c9c9c9>
             <td>Codigo</td>
             <td width=40%>Nome</td>
             <td width=40%>Nome Fantasia</td>
             <td>&nbsp;</td>
            </tr>";
 while($row=pg_fetch_array($sql)) {
      echo "
	   </fieldset>
       <tr>
              <form name=forfornecedor method=post action=$PHP_SELF>
              <input type=hidden name=id_login value=$id_login>
              <input type=hidden name=acao value=form_add>
              <input type=hidden name=action value=addfornecedor>
              <input type=hidden name=fornecedor value='$row[for_codigo]'>
             <td width=11% style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'> <font color=red></font>&nbsp;<b>$row[for_codigo]</b></td>
             <td width=40% style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'><font color=red><b>$row[for_nome]</b></font></td>
             <td width=40% style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'><font color=red><b>$row[for_nome_fantasia]</b></font></td>";
       echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif></td>";
       echo "</tr></form>";

      } //-> FIM DO WHILE 
    echo "</table>";

    echo "</td>";
  } //FIM DO IF - BUSCAR (FORNECEDOR)
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
               <td width=79><a href=entrada.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
               <td>&nbsp;</td>
              </tr>
             </table>
           </fieldset>
          </td>
         </tr>
        </table><br>";

//
//-> Pegando as informcoes do banco pra mostrar no formulario
 $sqlmovimento =  "select mov_codigo, to_char(mov_data, 'dd/mm/yyyy') as mov_data, mov_tipo, for_codigo, 
                          mov_desconto, mov_observacao, 
                          uni_entrada, mov_nr_nota, to_char(mov_dt_nota, 'dd/mm/yyyy') as mov_dt_nota,
                          mov_doacao from movimento
                   where  mov_codigo = '$mov_codigo'";
 $row=pg_fetch_array(pg_query($sqlmovimento));
 if ($row[mov_doacao] == 'N' ) {
    $vl1 = 'selected ';
    $vl2 = '';
    }
  else {
    $vl1 = '';
    $vl2 = 'selected';
    }

  echo "<br><br><form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=mov_codigo value=$mov_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Entrada de Materiais</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
        $sqlfornecedor=pg_query("select for_codigo, for_nome, for_nome_fantasia 
                from fornecedor where for_codigo = '$row[for_codigo]'");
        $rowfornecedor = pg_fetch_array($sqlfornecedor);        
         echo "<input type=hidden name=for_codigo value='$rowfornecedor[for_codigo]'>
                <tr> 
		          <td width=70>Dados do Fornecedor</td>
		          <td width=10><input type=text readonly name=for_nome size=10 value='$rowfornecedor[for_codigo]'></td> 
		          <td width=70><input type=text readonly name=for_nome size=70 value='$rowfornecedor[for_nome]'></td> 
               </tr>
               <tr>
		          <td width=70>&nbsp;</td>
		          <td width=20><input type=text readonly name=usu_datanasc size=20 value='$rowfornecedor[for_nome_fantasia]'></td>
               </tr>";   
        echo "       
	      <tr>
		<td width=70>Unidade:</td>
		<td>
		 <select name=uni_entrada class=box>";
	    //
	    //-> SQL da Unidade
	    $query = pg_query("select * from unidade where uni_movimenta_estoque = 'S' order by uni_desc");
	      while($unidade=pg_fetch_array($query)) {
	       echo ($unidade[uni_codigo]==$row[uni_codigo])?"<option value='$unidade[uni_codigo]' selected>$unidade[uni_desc]</option>":"<option value='$unidade[uni_codigo]'>$unidade[uni_desc]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>Data de Entrada:</td>
		<td><input type=text name=mov_data class=box size=20 value='$row[mov_data]'></td>
	      </tr>
	      <tr>
		<td width=70>Data Emissao da Nota:</td>
		<td><input type=text name=mov_dt_nota class=box size=20 value='$row[mov_dt_nota]'></td>
	      </tr>
	      <tr>
		<td width=70>Desconto:</td>
		<td><input type=text name=mov_desconto class=box size=20 value='$row[mov_desconto]'></td>
	      </tr>
	      <tr>
		<td width=110>Doacao:</td>
		<td>
		 <select name=mov_doacao class=box>
		  <option value=N $vl1>Nao</option>
		  <option value=S $vl2>Sim</option>
		 </select>
	      </tr>
	      <tr>
		<td width=70>Numero da Nota:</td>
		<td><input type=text name=mov_nr_nota class=box size=20 value='$row[mov_nr_nota]'></td>
	      </tr>
	     <tr>
		<td width=40>Observacao:</td>
            <td ><textarea name=mov_observacao class=box cols=100 rows=2>$row[mov_observacao]</textarea></td> 
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
    $tipo_movimento = 'E';
    $sql = pg_query("insert into movimento ( " .
            "mov_codigo, " .
            "mov_data, " .
            "mov_tipo, " .
            "for_codigo, " .
            "usu_codigo, " .
            "mov_desconto, " .
            "mov_observacao, " .
            "cond_codigo, " .
            "ate_codigo, " .
            "uni_entrada, " .
            "uni_saida, " .
            "mov_nr_nota, " .
            "mov_dt_nota, " .
            "mov_doacao, " .
            "usr_codigo, " .
            "mov_data_inclusao, " .
            "mov_ip, " .
            "mov_total_nota  " .
            ") values ( " .
            "$mov_codigo" . ", " .  //grava o codigo do movimento para que possa passar posteriormente para o outro formulÃ¡rio
            ($mov_data ? "'$mov_data'" : "null") . ", " .
            "'{$tipo_movimento}'" . ", " .  //tipo da movimentaÃ§Ã£o = E - Entrada
            ($for_codigo ? "'$for_codigo'" : "null") . ", " . 
            ($usu_codigo ? "'$usu_codigo'" : "null") . ", " .
            ($mov_desconto ? "'$mov_desconto'" : "null") . ", " .
            ($mov_observacao ? "'$mov_observacao'" : "null") . ", " .
            ($cond_codigo ? "'$cond_codigo'" : "null") . ", " .
            ($ate_codigo ? "'$ate_codigo'" : "null") . ", " .
            ($uni_entrada ? "'$uni_entrada'" : "null") . ", " .  //Quando o tipo for entrada - a entrada e a saida recebem o mesmo valor da Unidade
            ($uni_entrada ? "'$uni_entrada'" : "null") . ", " .
            ($mov_nr_nota ? "'$mov_nr_nota'" : "null") . ", " .
            ($mov_dt_nota ? "'$mov_dt_nota'" : "null") . ", " .
            ($mov_doacao ? "'$mov_doacao'" : "null") . ", " .
            ($usr_codigo ? "'$usr_codigo'" : "null") . ", " . //Mover o login do usuario que esta usando
            "date(now())" . ", " .
            ($mov_ip ? "'$mov_ip'" : "null") . ", " . //Mover o ip do micro do que o usuario esta usando
            ($mov_total_nota ? "'$mov_total_nota'" : "null") . "  " . //Fazer update na gravacao da nota
            ")");

msg($acao,$sql);
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='itens_entrada.php?id_login=$id_login&mov_codigo=$mov_codigo'\", 0);
              </SCRIPT>";
}

//
//-> EDIT <--------------------------------------------------------->

 if($acao=="edit") {
  $sql = pg_query("update movimento set " .
            ($mov_data ? "mov_data='$mov_data'" : "mov_data=null") . ", " .
            ($for_codigo ? "for_codigo='$for_codigo'" : "for_codigo=null") . ", " .
            ($usu_codigo ? "usu_codigo='$usu_codigo'" : "usu_codigo=null") . ", " .
            ($mov_desconto ? "mov_desconto='$mov_desconto'" : "mov_desconto=null") . ", " .
            ($mov_observacao ? "mov_observacao='$mov_observacao'" : "mov_observacao=null") . ", " .
            ($cond_codigo ? "cond_codigo='$cond_codigo'" : "cond_codigo=null") . ", " .
            ($ate_codigo ? "ate_codigo='$ate_codigo'" : "ate_codigo=null") . ", " .
            ($uni_entrada ? "uni_entrada='$uni_entrada'" : "uni_entrada=null") . ", " .
            ($uni_entrada ? "uni_saida='$uni_entrada'" : "uni_saida=null") . ", " .
            ($mov_nr_nota ? "mov_nr_nota='$mov_nr_nota'" : "mov_nr_nota=null") . ", " .
            ($mov_dt_nota ? "mov_dt_nota='$mov_dt_nota'" : "mov_dt_nota=null") . ", " .
            ($mov_doacao ? "mov_doacao='$mov_doacao'" : "mov_doacao=null") . ", " .
            ($usr_codigo ? "usr_codigo='$usr_codigo'" : "usr_codigo=null") . ", " .
            "mov_data_inclusao = date(now()),  " . 
            ($mov_ip ? "mov_ip='$mov_ip'" : "mov_ip=null") . ", " .
            ($mov_total_nota ? "mov_total_nota='$mov_total_nota'" : "mov_total_nota=null") . "  " .
            "where mov_codigo='$mov_codigo'");

msg($acao,$sql);
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='itens_entrada.php?id_login=$id_login&mov_codigo=$mov_codigo'\", 0);
              </SCRIPT>";
}

//
//-> DEL <---------------------------------------------------------->

 if($acao=="del") {
  $sql = pg_query("delete from atendimento where mov_codigo='$mov_codigo'");
msg($acao,$sql);
}

?>

