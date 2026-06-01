<script language="JavaScript" type="text/javascript">
var resposta;
function escolher(parametro)
{
	document.inclui_item.ite_lote.value = '';
	document.inclui_item.ite_validade.value = '';
	document.inclui_item.ite_dose.value = '';
	v = document.inclui_item.select_lote.value.split(':');
	document.inclui_item.ite_lote.value = v[0];
	document.inclui_item.ite_validade.value = v[1];
	document.inclui_item.ite_dose.value = v[3]?v[3]:'1';
}
not_lote = true;
function buscar()
{
	document.inclui_item.ite_lote.value = '';
	document.inclui_item.ite_validade.value = '';
	document.getElementById('select_lote').innerHTML = "<option value=''>Carregando</option>";
	var ajax;
	try{
		 ajax = new XMLHttpRequest();
	}catch(ee){
		try{
			 ajax = new ActiveXObject("Msxml2.XMLHTTP");
		}catch(e){
			try{
				 ajax = new ActiveXObject("Microsoft.XMLHTTP");
			}catch(E){
				 ajax = false;
			}
		}
	}
	set_codigo = document.getElementById('setor_s').value;
	pro_codigo =  document.inclui_item.pro_codigo.value;
	url = "buscarLotes.php?pro_codigo="+pro_codigo+"&set_codigo="+set_codigo;
	ajax.open("GET", url, true);
	if(ajax)
	{
		ajax.onreadystatechange = function()
		{
			if(ajax.readyState == 4)
			{
				resp =ajax.responseText;
				//alert(resp);
				if(resp != "not")
				{
					document.getElementById('select_lote').innerHTML = "<option value=''>Escolha</option>";
					not_lote = false;
					resposta = resp.split(";");
					for(i = 0; i < resposta.length; i++)
					{
						document.getElementById('select_lote').innerHTML += resposta[i];
					}
				} else {
					document.getElementById('select_lote').innerHTML = "<option value=''>Sem Lote</option>";
					not_lote = true;
				}
			}
		}
		ajax.send(null);
	}
}
</script>
<?
echo "<script src=\"ajaxtrent.js\"></script>";
echo "<script src=\"ajaxtrsai.js\"></script>";
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

function ver_estoqueent(setorent, setorsai)
{
    var produto = document.inclui_item.pro_codigo.value;
    if(produto !=''){
        urlent = 've_estoquesai.php?prod='+produto+'&str='+setorent+'&strt='+setorsai;
        ajaxsai(urlent);
        //alert(urlent);
    }
}

function ver_estoquesai(setorsai)
{   var produto = document.inclui_item.pro_codigo.value;
    urlsai = 've_estoquesai.php?produto='+produto+'&setor='+setorsai ;
    ajaxsai(urlsai);
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
    if (document.inclui_item.pro_codigo.value == '') {
       alert ('O produto deve ser digitado');
       document.inclui_item.pro_codigo.focus();
       return false;
    }
	if(document.inclui_item.select_lote.value == '' && not_lote == false)
	{
		alert('O lote deve ser selecionado');
		document.inclui_item.select_lote.focus();
		return false;
	}
	v = document.inclui_item.select_lote.value.split(':');
	if(not_lote == false)
	{
		if(parseInt(document.inclui_item.ite_quantidade.value) > parseInt(v[2]))
		{
			alert('A quantidade ultrapassa o limite do lote');
			document.inclui_item.ite_quantidade.focus();
			return false;
		}
	}
    return true;
}
</script>\n";
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

 if (empty($action) OR ($acao == 'form_inclui_item')) {

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Dados da Transferencia</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
    $sql =  pg_query("select mov_codigo, to_char(mov_data, 'dd/mm/yyyy') as mov_data, mov_tipo,
                          mov_desconto, mov_observacao, set_entrada,
                          set_saida, mov_nr_nota, to_char(mov_dt_nota, 'dd/mm/yyyy') as mov_dt_nota
             from movimento
             where  mov_codigo = '$mov_codigo'");
   $row=pg_fetch_array($sql);
   $sqlunidadee=pg_query("select set_codigo, set_nome
                         from setor
                         where set_codigo = '$row[set_entrada]'");
   $rowunidadee = pg_fetch_array($sqlunidadee );
   $r_estoqueentrada = $rowunidadee['set_codigo'];
   
 echo "
                <tr>
         <td width=70>Centro Estoc. Entrada</td>
         <td width=70 colspan=5><input type=text readonly name=set_nomee size=40 value='$rowunidadee[set_nome]'><input type=hidden name=setor_e id=setor_e value='$rowunidadee[set_codigo]'></td>
               </tr>";
   $sqlunidades=pg_query("select set_codigo, set_nome
                         from setor
                         where set_codigo = '$row[set_saida]'");
   $rowunidades = pg_fetch_array($sqlunidades );
   $r_estoquesaida = $rowunidades['set_codigo'];
   
 echo "
                <tr>
         <td width=70>Centro Estoc. Saida</td>
         <td width=70 colspan=5><input type=text readonly name=set_nomes size=40 value='$rowunidades[set_nome]'><input type=hidden name=setor_s id=setor_s value='$rowunidades[set_codigo]'></td>
               </tr>

	      <tr>
		<td width=70>Numero Mov.:</td>
		<td><input type=text name=mov_nr_nota class=box size=20 value='$row[mov_nr_nota]'></td>
		<td width=70>Data da Transferencia:</td>
		<td><input type=text name=mov_data class=box size=20 value='$row[mov_data]'></td>
	      </tr>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Digitacao dos Itens da Transferencia</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	       <form onSubmit=\"return notnull()\" name=inclui_item method=post action=$PHP_SELF>
		<input type=hidden name=action value=form_insert>
		<input type=hidden name=acao value=>
		<input type=hidden name=id_login value=$id_login>
		<input type=hidden name=mov_codigo value=$mov_codigo>
		<input type=hidden name=ite_lote value=>
		<input type=hidden name=ite_validade value=>
		<input type=hidden name=ite_dose value=>
	      <tr>
		<td width=20>Produto:</td>
		<td >";

		$pegaSetor = "SELECT * FROM logon where id_login = $_SESSION[id_login]";
		$querySetor = pg_query($pegaSetor);
		$resSetor  = pg_fetch_array($querySetor);
		$set_codigo = $resSetor[cod_setor];
		 $sql = "select * from produto
                                where pro_codigo in (select pro_codigo from produto_setor where set_codigo =  $set_codigo) AND
								pro_situacao = 'A'
                                order by pro_nome";
echo"
		
		 <select name='pro_codigo' class=box onChange=\"ver_estoqueent($r_estoqueentrada, $r_estoquesaida);buscar();\">";
	    //
	    //-> SQL do produto
	    /*$query = pg_query("select pro_codigo, pro_nome from produto
--                           where calcula_estoque(pro_codigo, $r_estoquesaida, date(now())) > 0
                           order by pro_nome");*/
		
//		$sql = "select * from produto
//				where pro_codigo in (select pro_codigo from produto_setor where set_codigo = $rowunidadee[set_codigo]) and pro_codigo in (select pro_codigo from produto_setor where set_codigo = $rowunidades[set_codigo])
//				order by pro_nome";
		//echo $sql;		
		$query = db_query($sql);
		
	      echo "<option selected value=''>-------</option>";
	      while($produto=pg_fetch_array($query)) {
	       echo "<option value='$produto[pro_codigo]' style='width:500px;' title='$produto[pro_nome]'>$produto[pro_nome]</option>";
	      }
	   echo "</select>
	        </td>
			<tr>
			<td width=50>
				Lote:
			</td>
			<td>
			<select class=box name=select_lote id=select_lote  onchange='escolher(this)'><option value=''>Escolha</option></select>
			</td>
			</tr>
			<Tr>
     		<td width=20>Quantidade:</td>
    		<td><input type=text name=ite_quantidade class=box size=20 onchange='calcula()'></td>
            </tr>
            <tr>
                <td colspan='2'><div id='qtd_estoquesai' style='display:none'> </td>
                <!--   <td ><b><div id='qtd_estoquesai'></div></b></td> -->
	            <td width=95><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif></td>
	          <td width=79><a href=remanejamento.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/finalizar_on.jpg border=0></a></td>
	      </tr>
          </form>
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
	    <legend>Listando Itens Cadastrados para o Movimento</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9>
		<td width=400 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Produto</td>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Quantidade</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td> ";

   $sql=pg_query("select ite_codigo, itens_movimento.pro_codigo, pro_nome,  ite_quantidade, ite_vlrunit, ite_vlrdesc,
                         ite_lote, ite_validade, (ite_quantidade * ite_vlrunit) as valortotal
                  from itens_movimento, produto
                  where itens_movimento.pro_codigo = produto.pro_codigo
                  and   (select mov_tipo from movimento where mov_codigo = itens_movimento.mov_codigo) = 'T'
                  and   mov_codigo = $mov_codigo
                  order by mov_codigo desc ");
     while($row=pg_fetch_array($sql)) {
     $intquantidade = formata_valor0($row['ite_quantidade']);
       echo "<tr>
	       <td width=400 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[pro_nome]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$intquantidade</td>
	       <td width=100 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?acao=form_edit&ite_codigo=$row[ite_codigo]&action=form_altera_item&mov_codigo=$mov_codigo&id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a></td>
	       <td width=100 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?acao=del&ite_codigo=$row[ite_codigo]&action=form_exclui_item&mov_codigo=$mov_codigo&id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
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
	    <legend>Dados da Transferencia</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
    $sql =  pg_query("select mov_codigo, to_char(mov_data, 'dd/mm/yyyy') as mov_data, mov_tipo,
                          mov_desconto, mov_observacao, set_saida,
                          set_entrada, mov_nr_nota, to_char(mov_dt_nota, 'dd/mm/yyyy') as mov_dt_nota
             from movimento
             where  mov_codigo = '$mov_codigo'");
   $row=pg_fetch_array($sql);
   $setor = $row[5];
   $sqlunidadee= pg_query("select * from setor
                           where set_codigo = '$row[set_entrada]'");
   $rowunidadee= pg_fetch_array($sqlunidadee);
 echo "
                <tr>
	          <td width=70>Centro Estoc. Entrada</td>
         <td width=70 colspan=5><input type=text readonly name=for_nome size=40 value='$rowunidadee[set_nome]'></td>
               </tr>";
   $sqlunidades= pg_query("select * from setor
                           where set_codigo = '$row[set_saida]'");
   $rowunidades= pg_fetch_array($sqlunidades);
 echo "
                <tr>
	          <td width=70>Unidade de Saida</td>
         <td width=70 colspan=5><input type=text readonly name=for_nome size=40 value='$rowunidades[set_nome]'></td>
               </tr>
	      <tr>
		<td width=70>Numero Mov.:</td>
		<td><input type=text name=mov_nr_nota class=box size=20 value='$row[mov_nr_nota]'></td>
		<td width=70>Data da Transferencia:</td>
		<td><input type=text name=mov_data class=box size=20 value='$row[mov_data]'></td>
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
                         (ite_quantidade * ite_vlrunit) as valortotal, set_saida
                  from itens_movimento, produto, movimento
                  where itens_movimento.pro_codigo = produto.pro_codigo
                  and   itens_movimento.mov_codigo = movimento.mov_codigo
                  and   (select mov_tipo from movimento where mov_codigo = itens_movimento.mov_codigo) = 'T'
                  and   movimento.mov_codigo = $mov_codigo
                  and   ite_codigo = $ite_codigo
                  order by movimento.mov_codigo desc ");
  $row = pg_fetch_array($sql);
  $produto = $row[1];
  $intquantidade = formata_valor0($row['ite_quantidade']);
  $query = pg_query("select pro_codigo, pro_nome from produto where pro_codigo = '$row[1]' order by pro_nome");
  $rowprod = pg_fetch_array($query);
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Alteracao do Item da Transferencia</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	       <form name=altera_item method=post action=$PHP_SELF>
		<input type=hidden name=action value=edit>
		<input type=hidden name=acao value=>
		<input type=hidden name=id_login value=$id_login>
		<input type=hidden name=mov_codigo value=$mov_codigo>
		<input type=hidden name=ite_codigo value=$ite_codigo>
	      <tr>
              <td width=20>Produto:</td>
              <td><input type=text name=pro_nome readonly class=box size=100 value='$rowprod[1]'></td>
	    </tr>
	     <tr>
     		<td width=20>Quantidade:</td>
    		<td><input type=text name=ite_quantidade class=box size=20 value='$intquantidade'></td>
         </tr>
	 <tr>
            <td width=20>Lote:</td>
	     	<td>
     		 <select name=ite_lote class=box>
     		 ";
	         //
	         //$query = db_query("select * from setor
                  //              order by set_nome");
	         $data = date("d/m/Y");
                 $select = "select ite_lote, to_char(ite_validade, 'dd/mm/yyyy') as ite_validade,
                                   ite_lote || ' - ' || to_char(ite_validade, 'dd/mm/yyyy') as valor,
                                   calcula_estoque_lote_validade(produto.pro_codigo, $setor,  
                                                        '$data', ite_lote, ite_validade) as estoque
                            from produto, itens_movimento
                            where produto.pro_codigo = itens_movimento.pro_codigo
                            and ite_lote is not null
                            and produto.pro_codigo = $produto
                            group by produto.pro_codigo, produto.pro_nome, ite_lote, ite_validade
                            order by ite_lote, ite_validade";
                   $query = pg_query($select);

	           while($setor=pg_fetch_array($query)) {
	            //     echo "<option value='$setor[ite_lote]'>$setor[valor]</option>";
                    echo ($setor[ite_lote]==$row[ite_lote])?"<option value='$setor[ite_lote]' selected>
                        $setor[valor]</option>":"<option value='$setor[ite_lote]'>$setor[valor]</option>";
	           }
	   echo "</select>
	        </td>
	     <tr>
	       <td width=95><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
	       <td width=79><a href=itens_remanejamento.php?id_login=$id_login&mov_codigo=$mov_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
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
    $consolidado = 'N';
    
    $sql = pg_query("insert into itens_movimento ( " .
            "pro_codigo, " .
            "ite_quantidade, " .
            "ite_vlrunit, " .
            "mov_codigo, " .
            "ite_consolidado, ".
            "ite_lote, " .
            "ite_validade, " .
            "ite_dose, " .
            "ite_qtde_solicitada " .
            ") values ( " .
            ($pro_codigo ? "'$pro_codigo'" : "null") . ", " .
            ($ite_quantidade ? "'$ite_quantidade'" : "null") . ", " .
            ($ite_vlrunit ? "'$ite_vlrunit'" : "null") . ", " .
            ($mov_codigo ? "'$mov_codigo'" : "null") . ", " .
            ($consolidado ? "'$consolidado'" : "null") . ", " .
            ($ite_lote ? "'$ite_lote'" : "null") . ", " .
            ($ite_validade ? "'$ite_validade'" : "null") . ",  " .
            ($ite_dose ? "'$ite_dose'" : "null") . ",  " .
            ($ite_quantidade ? "'$ite_quantidade'" : "null") . "  " .
            ")");
            

      $calcestoque = pg_fetch_row(pg_query("select mov_data, set_entrada
                                             from movimento where mov_codigo = $mov_codigo"));
      $atualiza_preco = pg_query("select calcula_preco($pro_codigo,  $calcestoque[1], '$calcestoque[0]')");


       echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='itens_remanejamento.php?id_login=$id_login&mov_codigo=$mov_codigo&acao=form_inclui_item&action='\", 0);
           </SCRIPT>";
}

//
//-> EDIT <--------------------------------------------------------->

 if($action=="edit") {
    if ($ite_lote) {
       $select = "select distinct ite_lote, ite_validade
                  from produto, itens_movimento
                  where produto.pro_codigo = itens_movimento.pro_codigo
                  and ite_lote is not null
                  and produto.pro_codigo = $pro_codigo
                  and ite_lote = '$ite_lote'
                  group by produto.pro_codigo, produto.pro_nome, ite_lote, ite_validade
                  order by ite_lote, ite_validade";
       $sqllote = pg_query($select);
       $rowlote = pg_fetch_row($sqllote);
       $ite_validade = $rowlote[1];
    }
  $sql = pg_query("update itens_movimento set " .
            ($ite_quantidade ? "ite_quantidade='$ite_quantidade'" : "ite_quantidade=null") . ", " .
            ($ite_lote ? "ite_lote='$ite_lote'" : "ite_lote=null") . ", " .
            ($ite_validade ? "ite_validade='$ite_validade'" : "ite_validade=null") . ",  " .
            ($ite_dose ? "ite_dose='$ite_dose'" : "ite_dose=null") . ",  " .
            ($ite_quantidade ? "ite_qtde_solicitada='$ite_quantidade'" : "ite_qtde_solicitada=null") . "  " .
            "where ite_codigo='$ite_codigo'");

      $calcestoque = pg_fetch_row(pg_query("select mov_data, set_entrada
                                             from movimento where mov_codigo = $mov_codigo"));
      $atualiza_preco = pg_query("select calcula_preco($pro_codigo,  $calcestoque[1], '$calcestoque[0]')");

       echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='itens_remanejamento.php?id_login=$id_login&mov_codigo=$mov_codigo&acao=form_inclui_item&action='\", 0);
           </SCRIPT>";
}

//
//-> DEL <---------------------------------------------------------->

 if($action=="form_exclui_item") {
  $sql = pg_query("delete from itens_movimento where ite_codigo='$ite_codigo'") or die(pg_last_error());

  $calcestoque = pg_fetch_row(pg_query("select mov_data, set_entrada
                                             from movimento where mov_codigo = $mov_codigo"));
  
  //$deletepreco = $pg_query("delete from precomedio where pro_codigo = $pro_codigo and mov_data = $calcestoque[0]") or die(pg_last_error());
  $atualiza_preco = pg_query("select calcula_preco($pro_codigo,  $calcestoque[1], $calcestoque[0])");
       echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='itens_remanejamento.php?id_login=$id_login&mov_codigo=$mov_codigo&acao=form_inclui_item&action='\", 0);
          </SCRIPT>";
}

?>

