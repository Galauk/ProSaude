<script>
 
function salvarProduto()
{	
	mov_codigo = document.getElementById("mov_codigo").value;
	set_codigo = document.getElementById('set_codigo').value;
	quantidade = document.getElementById('ite_quantidade').value;
	pro_codigo = document.getElementById('pro_codigo').value;
	if(quantidade == "")
	{
		alert("A quantidade deve ser preenchida!");
		document.getElementById('ite_quantidade').focus();
		return false;
	}
}
function respostaCota(resp,contagem,qtde,pro_codigo){
	
	mov_codigo = document.getElementById("mov_codigo").value;
	set_codigo = document.getElementById('set_codigo').value;
	quantidade = document.getElementById('ite_quantidade').value;
	
	url ="itens_dispensacao.php?resp="+resp+"&contagem="+contagem+"&ite_quantidade="+qtde+"&mov_codigo="+mov_codigo+"&set_codigo="+set_codigo+"&pro_codigo="+pro_codigo+"&action=form_insert";
	setTimeout("location='"+url+"'", 0);
	//window.open('escolherLoteValidade.php?quantidade='+quantidade+"&mov_codigo="+mov_codigo+"&set_codigo="+set_codigo+"&pro_codigo="+pro_codigo,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
	
}
</script>
<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
echo "<link type=\"text/css\" href=\"css/estiloForm.css\" rel=\"stylesheet\"/>";
//echo "<pre>".print_r($_REQUEST,true)."<pre>";
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
</script>\n";
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

cabecario();
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

 if (empty($_REQUEST['action']) OR ($_REQUEST['acao'] == 'form_inclui_item')) {

  $sql = pg_query("update movimento set mov_data = '".$_REQUEST['mov_data']."' where mov_codigo = ".$_REQUEST['mov_codigo']." ");

  echo "<form name=inclui_item method=post action='itens_dispensacao.php'>
  	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Dados da Dispensacao</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
    $sql =  pg_query("select usu_codigo,mov_codigo, to_char(mov_data, 'dd/mm/yyyy') as mov_data, mov_tipo, for_codigo,
                          mov_desconto, mov_observacao, usu_codigo,
                          set_saida, mov_nr_nota, to_char(mov_dt_nota, 'dd/mm/yyyy') as mov_dt_nota
             from movimento
             where  mov_codigo = '$mov_codigo'");
   $row=pg_fetch_array($sql);
   $sqlpaciente=pg_query("select usu_nome, to_char(usu_datanasc, 'dd/mm/yyyy') as usu_datanasc, usu_codigo, usu_mae
                from usuario where usu_codigo = $row[usu_codigo]");
   $rowpaciente = pg_fetch_array($sqlpaciente);
   echo "<input type=hidden name=usu_codigo value='$rowpaciente[usu_codigo]'>
   		<input type=hidden name=set_codigo id=set_codigo value='$row[set_saida]'>
                <tr>
		          <td width=70>Dados do Paciente</td>
		          <td width=10><input type=text readonly name=usu_codigo size=10 value='$rowpaciente[usu_codigo]' class=box></td>
		          <td colspan=2><input type=text readonly name=usu_nome   size=70 value='$rowpaciente[usu_nome]' class=box></td>
               </tr>
               <tr>
		          <td width=70>&nbsp;</td>
		          <td width=20><input type=text readonly name=usu_datanasc size=20 value='$rowpaciente[usu_datanasc]' class=box></td>
		          <td colspan=2><input type=text readonly name=usu_mae size=70 value='$rowpaciente[usu_mae]' class=box></td>
               </tr>";
   $sqlunidade=pg_query("select set_codigo, set_nome
                         from setor
                         where set_codigo = '$row[set_saida]'");
   $rowunidade = pg_fetch_array($sqlunidade);
 echo "
                <tr>
         <td width=70>Centro Estocador</td>
         <td width=70 colspan=5><input type=text class=box  readonly name=set_nome size=40 value='$rowunidade[set_nome]'></td>
               </tr>
	      <tr>
		<td width=70>Numero Mov.:</td>
		<td><input type=text readonly name=mov_nr_nota class=box size=20 value='$row[mov_codigo]' ></td>
		<td width=70>Data da Dispensacao:</td>
		<td ><input type=text name=mov_data class=box size=20 value='$row[mov_data]'></td>
	      </tr>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0 > 
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Digitacao dos Itens da Dispensacao</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	       
		<input type=hidden name=acao value=''>
		<input type=hidden name=action value='form_insert'>		
		<input type=hidden name=id_login value=$id_login>
		<input type=hidden name=mov_codigo id=mov_codigo value=$mov_codigo>
		
	      <tr>
		<td width=20>Produto:</td>
		<td colspan=4>
		 <select name=pro_codigo[] id=pro_codigo class=box>
		 <option value='0'> :: Selecionar :: </option>";
	    //
	    //-> SQL do produto
	    $query = pg_query("select to_char(sal_validade,'DD/MM/YYYY') as validade,* from saldo as s join produto as p on p.pro_codigo = s.pro_codigo where pro_situacao = 'A' and sal_qtde >=1 order by pro_nome");
	      while($produto=pg_fetch_array($query)) {
	       echo "<option value='$produto[pro_codigo]|$produto[sal_lote]|$produto[validade]'>$produto[pro_nome] :: Lote/Val: $produto[sal_lote] :: $produto[validade] :: Saldo: $produto[sal_qtde]</option>";
	      }
	   echo "</select>
	        </td>
     		<td width=20>Quantidade:</td>
    		<td ><input type=text name=ite_quantidade[] id=ite_quantidade  size=20 onchange='calcula()' class=inputForm></td>
            </tr>
            <tr>
	       <td width=95>&nbsp;</td>
	       <td width=95><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg  onclick='return salvarProduto()'></td>
	       <td width=79><a href=dispensacao.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/finalizar_on.jpg border=0></a></td>
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
	    <legend>Listando Itens Cadastrados para o Movimento</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0 class=lista>
	      <tr bgcolor=F9f9f9>
		<th width=400 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Produto</th>
		<th width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Quantidade</th>
		<th width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Lote</th>
		<th colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</th> ";

   $sql=pg_query("select ite_codigo, itens_movimento.pro_codigo, pro_nome,  ite_quantidade, ite_vlrunit, ite_vlrdesc,
                         itens_movimento.ite_lote, ite_validade, (ite_quantidade * ite_vlrunit) as valortotal
                  from itens_movimento, produto
                  where itens_movimento.pro_codigo = produto.pro_codigo
                  and   (select mov_tipo from movimento where mov_codigo = itens_movimento.mov_codigo
                         and mov_tipo = 'S' and mov_saida = 'D') = 'S'
                  and   mov_codigo = $mov_codigo
                  order by mov_codigo desc ");
   
     while($row=pg_fetch_array($sql)) {
       $intquantidade = formata_valor0($row['ite_quantidade']);
       echo "<tr>
	       <td width=400 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[pro_nome]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$intquantidade</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[ite_lote]</td>
	       <td width=100 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=".$_SERVER['PHP_SELF']."?id_login=$id_login&acao=del&ite_codigo=$row[ite_codigo]&action=form_exclui_item&mov_codigo=$mov_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
	     </tr>";
     }
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
}


 if ($_REQUERT['acao'] == 'form_edit') {
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Dados da Dispensacao</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
    $sql =  pg_query("select mov_codigo, to_char(mov_data, 'dd/mm/yyyy') as mov_data, mov_tipo,
                          mov_desconto, mov_observacao, usu_codigo, mov_saida,
                          set_saida, mov_nr_nota, to_char(mov_dt_nota, 'dd/mm/yyyy') as mov_dt_nota
             from movimento
             where  mov_codigo = '$mov_codigo'");
   $row=pg_fetch_array($sql);
   $sqlpaciente=pg_query("select usu_nome, to_char(usu_datanasc, 'dd/mm/yyyy') as usu_datanasc, usu_codigo, usu_mae
                from usuario where usu_codigo = $row[usu_codigo]");
   $rowpaciente = pg_fetch_array($sqlpaciente);
   echo "<input type=hidden name=usu_codigo value='$rowpaciente[usu_codigo]'>
                <tr>
		          <td width=70>Dados do Paciente</td>
		          <td width=10><input type=text readonly name=usu_same size=10 value='$rowpaciente[usu_same]' class=box></td>
		          <td colspan=2><input type=text readonly name=usu_nome   size=70 value='$rowpaciente[usu_nome]' class=box></td>
               </tr>
               <tr>
		          <td width=70>&nbsp;</td>
		          <td width=20><input type=text readonly name=usu_datanasc size=20 value='$rowpaciente[usu_datanasc]' class=box></td>
		          <td colspan=2><input type=text readonly name=usu_mae size=70 value='$rowpaciente[usu_mae]' class=box></td>
               </tr>";
   $sqlunidade = pg_query("select * from setor where set_estoque = 'S' and set_codigo = '$row[set_saida]'");
   $rowunidade = pg_fetch_array($sqlunidade);
 echo "
                <tr>
	          <td width=70>Centro Estocador</td>
	          <td width=70 colspan=5><input type=text class=box readonly name=for_nome size=40 value='$rowunidade[set_nome]'></td>
               </tr>
	      <tr>
		<td width=70>Numero Mov.:</td>
		<td><input type=text name=mov_nr_nota class=box size=20 value='$row[mov_nr_nota]'></td>
		<td width=70>Data da Dispensacao:</td>
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
                         (ite_quantidade * ite_vlrunit) as valortotal
                  from itens_movimento, produto
                  where itens_movimento.pro_codigo = produto.pro_codigo
                  and   (select mov_tipo from movimento where mov_codigo = itens_movimento.mov_codigo and
                         mov_tipo = 'S' and mov_saida = 'D') = 'S'
                  and   mov_codigo = $mov_codigo
                  and   ite_codigo = $ite_codigo
                  order by mov_codigo desc ");
  $row = pg_fetch_array($sql);
   $intquantidade = formata_valor0($row['ite_quantidade']);
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Alteracao do Item da Dispensacao</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	       <form name=altera_item method=post action=$PHP_SELF>
		<input type=hidden name=action value=edit>
		<input type=hidden name=acao value=>
		<input type=hidden name=id_login value=$id_login>
		<input type=hidden name=mov_codigo id=mov_codigo value=$mov_codigo>
		<input type=hidden name=ite_codigo value=$ite_codigo>
	      <tr>
		<td width=20>Produto:</td>
		<td colspan=4>
		 <select name='pro_codigo[]' class=box>";
	    //
	    //-> SQL do produto
	    $query = pg_query("select * from produto where pro_situacao = 'A' order by pro_nome");
	      while($produto=pg_fetch_array($query)) {
	       echo ($produto[pro_codigo]==$row[pro_codigo])?"<option value='$produto[pro_codigo]' selected>$produto[pro_nome]</option>":"<option value='$produto[pro_codigo]'>$produto[pro_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	     <tr>
     		<td width=20>Quantidade:</td>
    		<td><input type=text name=ite_quantidade[] id=ite_quantidade class=box size=20 value='$intquantidade'></td>
         </tr>
	     <tr>
	       <td width=95><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
	       <td width=79><a href=itens_dispensacao.php?id_login=$id_login&mov_codigo=$mov_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
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

 if($_REQUEST['action']=="form_insert") {
 $pro_codigo = $_REQUEST["pro_codigo"];
 $sql = '';
	for($i=0; $i<count($pro_codigo); $i++){		
	$pro = explode("|",$pro_codigo[$i]);
   $sql .= "insert into itens_movimento ( " .
            "pro_codigo, " .
            "ite_quantidade, " .
            "ite_qtde_solicitada, " .
            "ite_vlrunit, " .
            "mov_codigo, " .
            "ite_lote, " .
            "ite_validade  " .
            ") values ( " .
            ($pro[0] ? "'$pro[0]'" : "null") . ", " .
            ($ite_quantidade[$i] ? "'$ite_quantidade[$i]'" : "null") . ", " .
            ($ite_qtde_solicitada ? "'$ite_qtde_solicitada'" : "null") . ", " .
            ($ite_vlrunit ? "'$ite_vlrunit'" : "null") . ", " .
            ($mov_codigo ? "'$mov_codigo'" : "null") . ", " .
            ($pro[1] ? "'$pro[1]'" : "null") . ", " .
            ($pro[2] ? "'$pro[2]'" : "null") . "  " .
            ")";
//		 $exeinsertUmNovoItem = pg_query($insertUmNovoItem) or die ("ERRO ".$insertUmNovoItem);
	 	} 
	//	echo $sql;
		//die("asdfasdf");
		$ins = pg_query($sql) or die(pg_last_error());
	
	
       echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='itens_dispensacao.php?id_login=$id_login&mov_codigo=$mov_codigo&acao=form_inclui_item&action='\", 0);
           </SCRIPT>";
}

//
//-> EDIT <--------------------------------------------------------->

 if($_REQUEST['action']=="edit") {
  $sql = pg_query("update itens_movimento set " .
            ($pro_codigo ? "pro_codigo='$pro_codigo'" : "pro_codigo=null") . ", " .
            ($ite_quantidade ? "ite_quantidade='$ite_quantidade'" : "ite_quantidade=null") . ", " .
            ($ite_vlrunit ? "ite_vlrunit='$ite_vlrunit'" : "ite_vlrunit=null") . ", " .
            ($mov_codigo ? "mov_codigo='$mov_codigo'" : "mov_codigo=null") . ", " .
            ($ite_lote ? "ite_lote='$ite_lote'" : "ite_lote=null") . ", " .
            ($ite_validade ? "ite_validade='$ite_validade'" : "ite_validade=null") . "  " .
            "where ite_codigo='$ite_codigo'");

//msg($id_login,$acao,$sql);
      $calcestoque = pg_fetch_array(pg_query("select mov_data, set_entrada
                                             from movimento where mov_codigo = $mov_codigo"));
      $atualiza_preco = pg_query("select calcula_preco($pro_codigo,  $calcestoque[1], '$calcestoque[0]')");

       echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='itens_dispensacao.php?id_login=$id_login&mov_codigo=$mov_codigo&acao=form_inclui_item&action='\", 0);
           </SCRIPT>";
}

//
//-> DEL <---------------------------------------------------------->

 if($_REQUEST['action']=="form_exclui_item") {

 $sql = pg_query("delete from itens_movimento where ite_codigo='$ite_codigo'");

  $calcestoque = pg_fetch_row(pg_query("select mov_data, set_entrada
                                               from movimento where mov_codigo = $mov_codigo"));
  $deletepreco = pg_query("delete from precomedio where pro_codigo = $pro_codigo and
                                                                     mov_data = $calcestoque[0]");
  $atualiza_preco = pg_query("select calcula_preco($pro_codigo,  $calcestoque[1], $calcestoque[0])");


       echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='itens_dispensacao.php?id_login=$id_login&mov_codigo=$mov_codigo&acao=form_inclui_item&action='\", 0);
          </SCRIPT>";
}

?>

