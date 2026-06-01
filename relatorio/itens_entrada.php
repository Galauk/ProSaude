<?
	session_start();
//Deve ser feita uma rotina para conferencia do total da nota em JavaScript, quando o usuario finalizar a nota
//termina, mas avisar o usuario e gravar no log este aviso.

echo "<script>\n
function calcula() 
{ 
    document.inclui_item.totalitem.value = document.inclui_item.ite_quantidade.value * \n
                                      document.inclui_item.ite_vlrunit.value; \n
}                                      
function calcula_altera() 
{ 
    document.altera_item.totalitem.value = document.altera_item.ite_quantidade.value * \n
                                      document.altera_item.ite_vlrunit.value; \n
}                                      

function verifica_total_inclusao() 
{ 
    document.altera_item.totalitem.value = document.altera_item.ite_quantidade.value * \n
                                      document.altera_item.ite_vlrunit.value; \n
}                                      

function notnull() 
{ 
    if (document.inclui_item.ite_quantidade.value == '') {
       alert ('A quantidade deve ser digitada');
       document.inclui_item.ite_quantidade.focus();
       return false;
    }
    if (document.inclui_item.ite_quantidade.value == 0) {
       alert ('A quantidade nao pode ser zero');
       document.inclui_item.ite_quantidade.focus();
       return false;
    }
    if (document.inclui_item.ite_vlrunit.value == '') {
       alert ('O valor unitario  deve ser digitado');
       document.inclui_item.ite_vlrunit.focus();
       return false;
    }
    if (document.inclui_item.ite_vlrunit.value == 0) {
       alert ('O valor unitario  nao pode ser zero');
       document.inclui_item.ite_vlrunit.focus();
       return false;
    }
    return true;
}                                      

function verificatotal() 
{ 
    if (document.dados_nota.vlrtotal.value != document.dados_nota.vlrtotalinfo.value) {
       alert ('Valor total digitado diferente do valor total informado');
       return false;
    }
    return true;
}

</script>\n";
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

	include_once $_SESSION[root].$_SESSION[comum]."funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

 if (empty($action) OR ($acao == 'form_inclui_item')) {
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	       <form name=dados_nota method=post action=''>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Dados da Entrada</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
    $sql =  pg_query("select mov_codigo, to_char(mov_data, 'dd/mm/yyyy') as mov_data, mov_tipo, for_codigo, 
                          mov_desconto, mov_observacao, 
                          case when mov_entrada = 'E' then 'Nota Fiscal'
                               when mov_entrada = 'A' then 'Ajuste'
                               when mov_entrada = 'M' then 'Emprestimo'
                               when mov_entrada = 'I' then 'Inventario'
                               when mov_entrada = 'D' then 'Doacao'
                               when mov_entrada = 'P' then 'Permuta'
                               when mov_entrada = 'O' then 'Outras Entradas'
                          end as tipoentrada,
                          set_entrada, mov_nr_nota, to_char(mov_dt_nota, 'dd/mm/yyyy') as mov_dt_nota,
                          mov_total_nota
             from movimento
             where  mov_codigo = '$mov_codigo'");
   $row=pg_fetch_array($sql);
   $sqlfornecedor=pg_query("select for_codigo, for_nome, for_nome_fantasia 
                from fornecedor where for_codigo = '$row[for_codigo]'");
   $rowfornecedor = pg_fetch_array($sqlfornecedor);        
   $sqlsetor = pg_query("select * from setor where set_estoque = 'S' and set_codigo = '$row[set_entrada]'");
   $rowsetor = pg_fetch_array($sqlsetor);        
   $sqltotal=pg_query("select (sum(ite_vlrunit * ite_quantidade) - coalesce(mov_desconto,0)) as total 
                from movimento, itens_movimento
                where movimento.mov_codigo = itens_movimento.mov_codigo
                and   movimento.mov_codigo = '$mov_codigo'
                group by mov_desconto");
   $rowtotal = pg_fetch_array($sqltotal);        
   $vlrtotal = formata_valor($rowtotal['total']);
   $vlrdesconto = formata_valor4($row['mov_desconto']);
   $vlrtotaldigitado = formata_valor($row['mov_total_nota']);
 echo "
                <tr> 
	          <td width=70>Dados do Fornecedor</td>
	          <td width=70 colspan=5><input type=text readonly name=for_nome size=40 value='$rowfornecedor[for_nome]'></td> 
               </tr>
          <tr> 
	          <td width=70>Unidade</td>
	          <td width=70><input type=text readonly name=uni_desc size=40 value='$rowsetor[set_nome]'></td> 
	          <td width=20>Valor Total Calculado</td>
	          <td width=20><input type=text readonly name=vlrtotal size=20 value='$vlrtotal'></td> 
	          <td width=20>Valor Total Informado</td>
	          <td width=20><input type=text readonly name=vlrtotalinfo size=20 value='$vlrtotaldigitado'></td> 
	      </tr>
	      <tr>
		<td width=70>Numero NF:</td>
		<td><input type=text name=mov_nr_nota class=box size=20 value='$row[mov_nr_nota]'></td>
		<td width=70>Data Emissao da Nota:</td>
		<td><input type=text name=mov_dt_nota class=box size=20 value='$row[mov_dt_nota]'></td>
		<td width=70>Desconto:</td>
		<td><input type=text name=mov_desconto class=box size=20 value='$vlrdesconto'></td>
	      </tr>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
     </form>
        </table><br>";
//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Digitacao dos Itens da Entrada</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	       <form onSubmit=\"return notnull()\"  name=inclui_item method=post action=''>
		<input type=hidden name=action value=form_insert>
		<input type=hidden name=acao value=>
		<input type=hidden name=mov_codigo value=$mov_codigo>
	      <tr>
		<td width=20>Produto:</td>
		<td colspan=4>
		 <select name=pro_codigo class=box>";
	    //
	    //-> SQL do produto
	    $query = pg_query("select * from produto order by pro_nome");
	      while($produto=pg_fetch_array($query)) {
	       echo "<option value='$produto[pro_codigo]'>$produto[pro_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	     <tr>
     		<td width=20>Quantidade:</td>
    		<td><input type=text name=ite_quantidade class=box size=20 onchange='calcula()'></td>
     		<td width=20>Valor Unitario:</td>
    		<td><input type=text name=ite_vlrunit class=box size=20 onchange='calcula()'></td>
     		<td width=20 >Valor Total:</td>
    		<td colspan=2><input type=text readonly name=totalitem class=box size=20></td>
         </tr>   
	     <tr>
     		<td width=20>Lote:</td>
    		<td><input type=text name=ite_lote class=box size=20></td>
     		<td width=20>Validade:</td>
    		<td><input type=text name=ite_validade class=box size=20></td>
	       <td width=60><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif '></td>
	       <td width=60><a href=entrada.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/finalizar_on.jpg onclick=\"return verificatotal()\" border=0></a></td>
           <td width=60><a href=entrada.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	      </tr></form>
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
	    <legend>Listando Itens Cadastradros para o Movimento</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9>
		<td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Produto</td>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Quantidade</td>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Vlr.Unit.</td>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Vlr. Total</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

   $sql=pg_query("select ite_codigo, itens_movimento.pro_codigo, pro_nome,  ite_quantidade, ite_vlrunit, ite_vlrdesc, 
                         ite_lote, ite_validade, (ite_quantidade * ite_vlrunit) as valortotal
                  from itens_movimento, produto
                  where itens_movimento.pro_codigo = produto.pro_codigo
                  and   (select mov_tipo from movimento where mov_codigo = itens_movimento.mov_codigo) = 'E' 
                  and   mov_codigo = $mov_codigo
                  order by mov_codigo desc limit 15");
     while($row=pg_fetch_array($sql)) {
     $intquantidade = formata_valor0($row['ite_quantidade']);
     $vlrunit = formata_valor4($row['ite_vlrunit']);
     $valor=explode('.', $row['ite_vlrunit']);
     $vlrtotal = formata_valor($row['valortotal']);
       echo "<tr>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[pro_nome]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$intquantidade</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$vlrunit</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$vlrtotal</td>
	       <td width=100 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?acao=form_edit&ite_codigo=$row[ite_codigo]&action=form_altera_item&mov_codigo=$mov_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a></td>
	       <td width=100 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?acao=del&ite_codigo=$row[ite_codigo]&action=form_exclui_item&mov_codigo=$mov_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
	     </tr>";
     }
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
}


 if ($acao == 'form_edit') {

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Dados da Entrada</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
    $sql =  pg_query("select mov_codigo, to_char(mov_data, 'dd/mm/yyyy') as mov_data, mov_tipo, for_codigo, 
                          mov_desconto, mov_observacao, 
                          uni_entrada, mov_nr_nota, to_char(mov_dt_nota, 'dd/mm/yyyy') as mov_dt_nota,
                          mov_doacao 
             from movimento
             where  mov_codigo = '$mov_codigo'");
   $row=pg_fetch_array($sql);
   $sqlfornecedor=pg_query("select for_codigo, for_nome, for_nome_fantasia 
                from fornecedor where for_codigo = '$row[for_codigo]'");
   $rowfornecedor = pg_fetch_array($sqlfornecedor);        
   $sqlunidade = pg_query("select * from unidade where uni_movimenta_estoque = 'S' and uni_codigo = '$row[uni_entrada]'");
   $rowunidade = pg_fetch_array($sqlunidade);        
   $sqltotal=pg_query("select (sum(ite_vlrunit * ite_quantidade) - coalesce(mov_desconto,0)) as total 
                from movimento, itens_movimento
                where movimento.mov_codigo = itens_movimento.mov_codigo
                and   movimento.mov_codigo = '$mov_codigo'
                group by mov_desconto");
   $rowtotal = pg_fetch_array($sqltotal);        
   $vlrtotal = formata_valor4($rowtotal['total']);
   $vlrdesconto = formata_valor4($row['mov_desconto']);
 echo "
                <tr> 
	          <td width=70>Dados do Fornecedor</td>
	          <td width=70 colspan=5><input type=text readonly name=for_nome size=40 value='$rowfornecedor[for_nome]'></td> 
               </tr>
          <tr> 
	          <td width=70>Unidade</td>
	          <td width=70><input type=text readonly name=uni_desc size=40 value='$rowunidade[uni_desc]'></td> 
	          <td width=20>Valor Total</td>
	          <td width=20 colspan=3><input type=text readonly name=vlrtotal size=20 value='$vlrtotal'></td> 
	      </tr>
	      <tr>
		<td width=70>Numero NF:</td>
		<td><input type=text name=mov_nr_nota class=box size=20 value='$row[mov_nr_nota]'></td>
		<td width=70>Data Emissao da Nota:</td>
		<td><input type=text name=mov_dt_nota class=box size=20 value='$row[mov_dt_nota]'></td>
		<td width=70>Desconto:</td>
		<td><input type=text name=mov_desconto class=box size=20 value='$vlrdesconto'></td>
	      </tr>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
//
//-> Botoes
   $sql=pg_query("select ite_codigo, itens_movimento.pro_codigo, pro_nome,  ite_quantidade, ite_vlrunit, ite_vlrdesc, 
                         ite_lote, to_char(ite_validade,'dd/mm/yyyy') as ite_validade, 
                         (ite_quantidade * ite_vlrunit) as valortotal
                  from itens_movimento, produto
                  where itens_movimento.pro_codigo = produto.pro_codigo
                  and   (select mov_tipo from movimento where mov_codigo = itens_movimento.mov_codigo) = 'E' 
                  and   mov_codigo = $mov_codigo
                  and   ite_codigo = $ite_codigo
                  order by mov_codigo desc limit 15");
  $row = pg_fetch_array($sql);                
  $intquantidade = formata_valor0($row['ite_quantidade']);

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Alteracao do Item da Entrada</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	       <form name=altera_item method=post action=$PHP_SELF>
		<input type=hidden name=action value=edit>
		<input type=hidden name=acao value=>
		<input type=hidden name=mov_codigo value=$mov_codigo>
		<input type=hidden name=ite_codigo value=$ite_codigo>
	      <tr>
		<td width=20>Produto:</td>
		<td colspan=4>
		 <select name=pro_codigo class=box>";
	    //
	    //-> SQL do produto
	    $query = pg_query("select * from produto order by pro_nome");
	      while($produto=pg_fetch_array($query)) {
	       echo ($produto[pro_codigo]==$row[pro_codigo])?"<option value='$produto[pro_codigo]' selected>$produto[pro_nome]</option>":"<option value='$produto[pro_codigo]'>$produto[pro_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	     <tr>
     		<td width=20>Quantidade:</td>
    		<td><input type=text name=ite_quantidade class=box size=20 value='$intquantidade' onchange='calcula_altera()'></td>
     		<td width=20>Valor Unitario:</td>
    		<td><input type=text name=ite_vlrunit class=box size=20 value='$row[ite_vlrunit]' onchange='calcula_altera()'></td>
     		<td width=20>Valor Total:</td>
    		<td><input type=text readonly name=totalitem class=box size=20></td>
         </tr>   
	     <tr>
     		<td width=20>Lote:</td>
    		<td><input type=text name=ite_lote class=box size=20 value='$row[ite_lote]'></td>
     		<td width=20>Validade:</td>
    		<td><input type=text name=ite_validade class=box size=20 value='$row[ite_validade]'></td>
	       <td width=95><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
	      </tr></form>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

//
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
}

//------------------------------------------------------------------>
//-> SQL's
//------------------------------------------------------------------>
//
//-> ADD <---------------------------------------------------------->

 if($action=="form_insert") {
    $ite_consolidado = 'S'; //Toda entrada e consolidada automaticamente.
    

    $sql = pg_query("insert into itens_movimento ( " .
            "pro_codigo, " .
            "ite_quantidade, " .
            "ite_vlrunit, " .
            "mov_codigo, " .
            "ite_consolidado, " .
            "ite_lote, " .
            "ite_validade  " .
            ") values ( " .
            ($pro_codigo ? "'$pro_codigo'" : "null") . ", " .
            ($ite_quantidade ? "'$ite_quantidade'" : "null") . ", " . 
            ($ite_vlrunit ? "'$ite_vlrunit'" : "null") . ", " .
            ($mov_codigo ? "'$mov_codigo'" : "null") . ", " .
            "'{$ite_consolidado}'" . ", " .             
            ($ite_lote ? "'$ite_lote'" : "null") . ", " .
            ($ite_validade ? "'$ite_validade'" : "null") . "  " .
            ")");

msg($id_login,$acao,$sql);


       echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='itens_entrada.php?id_login=$id_login&mov_codigo=$mov_codigo&acao=form_inclui_item&action='\", 0);
           </SCRIPT>";
}

//
//-> EDIT <--------------------------------------------------------->

 if($action=="edit") {	
 	$sqlUpdate = "update itens_movimento set " .
            ($pro_codigo ? "pro_codigo='$pro_codigo'" : "pro_codigo=null") . ", " .
            ($ite_quantidade ? "ite_quantidade='$ite_quantidade'" : "ite_quantidade=null") . ", " .
            ($ite_vlrunit ? "ite_vlrunit='$ite_vlrunit'" : "ite_vlrunit=null") . ", " .
            ($mov_codigo ? "mov_codigo='$mov_codigo'" : "mov_codigo=null") . ", " .
            ($ite_lote ? "ite_lote='$ite_lote'" : "ite_lote=null") . ", " .
            ($ite_validade ? "ite_validade='$ite_validade'" : "ite_validade=null") . "  " .
            "where ite_codigo='$ite_codigo'";
           
  $sql = pg_query($sqlUpadate);

msg($id_login,$acao,$sql);
       echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='itens_entrada.php?id_login=$id_login&mov_codigo=$mov_codigo&acao=form_inclui_item&action='\", 0);
           </SCRIPT>";
}

//
//-> DEL <---------------------------------------------------------->

 if($action=="form_exclui_item") {
  $sql = pg_query("delete from itens_movimento where ite_codigo='$ite_codigo'");
  echo "delete from itens_entrada where ite_codigo='$ite_codigo'";
msg($id_login,$acao,$sql);
       echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='itens_entrada.php?id_login=$id_login&mov_codigo=$mov_codigo&acao=form_inclui_item&action='\", 0);
          </SCRIPT>";
}

?>

