<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	cabecario();
?>

<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>

<?

//Deve ser feita uma rotina para conferencia do total da nota em JavaScript, quando o usuario finalizar a nota
//termina, mas avisar o usuario e gravar no log este aviso.

echo "<script>\n

function notnull()
{
    if (document.inclui_item.rprod_quantidade.value == '') {
       alert ('A quantidade deve ser digitada');
       document.inclui_item.rprod_quantidade.focus();
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

$stmt = "SELECT uni_codigo FROM usuarios WHERE usr_codigo = $id_login";
$stmt = db_query($stmt);
$dados = pg_fetch_array($stmt);



//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

 if (empty($action) OR ($acao == 'form_inclui_item')) {

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	       <form name=dados_nota method=post action=''>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Dados da Requisicao de Compra</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
    $sql =  pg_query("select rcom_codigo, to_char(rcom_data, 'dd/mm/yyyy') as rcom_data,set_codigo
             from requisicao_compra
             where  rcom_codigo = '$rcom_codigo'");

   $row=pg_fetch_array($sql);
   $sqlsetor = pg_query("select * from setor where set_estoque = 'S' and set_codigo = '$row[set_codigo]'");
   $rowsetor = pg_fetch_array($sqlsetor);
 echo "
                <tr>
	          <td width=70>Dados do Centro Estocador</td>
	          <td width=150 colspan=5><input type=text readonly name=set_nome size=100 value='$rowsetor[set_nome]'></td>
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
	    <legend>Digitacao dos Itens da Requisicao de Compra </legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	       <form onSubmit=\"return notnull()\"  name=inclui_item method=post action=''>
		<input type=hidden name=action value=form_insert>
		<input type=hidden name=acao value=>
		<input type=hidden name=rcom_codigo value=$rcom_codigo>
	      <tr>
		<td width=20>Produto: </td>
		<td colspan=4>
		 <select name='pro_codigo' class='box' id='pro_codigo'>";
	    //
	    //-> SQL do produto
			$sql = "select distinct a.pro_codigo, a.pro_nome
					from produto a, setor b, produto_setor c
					where a.pro_codigo = c.pro_codigo
					and b.set_codigo = c.set_codigo "
					.($dados[0]=="" ? "" : " and b.uni_codigo = ".$dados[0]).
					" order by pro_nome";
			$query = pg_query($sql);
			while($produto=pg_fetch_array($query))
			{
				echo "<option value='$produto[pro_codigo]'>$produto[pro_nome]</option>";
			}
	   echo "</select>
	      </tr>
	     <tr>
     		<td width=20>Quantidade:</td>
    		<td>
    			<input type='text' name='rprod_quantidade' id='rprod_quantidade' class='box' size='20' onkeypress='return Bloqueia_Caracteres(event);' />
    		</td>
	       <td width=60><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif '></td>
	       <td width=60><a href=reqcompra.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/finalizar_on.jpg onclick=\"return verificatotal()\" border=0></a></td>
           <td width=60><a href=reqcompra.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
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
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

   $sql=pg_query("select rprod_codigo, reqcompra_produto.pro_codigo, pro_nome,  rprod_quantidade
                  from reqcompra_produto, produto
                  where reqcompra_produto.pro_codigo = produto.pro_codigo
                  and   rcom_codigo = $rcom_codigo
                  order by rcom_codigo desc limit 15");
     while($row=pg_fetch_array($sql)) {
     $intquantidade = formata_valor0($row['rprod_quantidade']);
       echo "<tr>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[pro_nome]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$intquantidade</td>
	       <td width=100 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?id_login=$id_login&acao=form_edit&rprod_codigo=$row[rprod_codigo]&action=form_altera_item&rcom_codigo=$rcom_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a></td>
	       <td width=100 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?id_login=$id_login&acao=del&rprod_codigo=$row[rprod_codigo]&action=form_exclui_item&rcom_codigo=$rcom_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
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
	    <legend>Dados da Requisicao de Entrada</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
    $sql =  pg_query("select rcom_codigo, to_char(rcom_data, 'dd/mm/yyyy') as rcom_data, set_codigo
             from requisicao_compra
             where  rcom_codigo = '$rcom_codigo'");
   $row=pg_fetch_array($sql);
   $sqlsetor = pg_query("select * from setor where set_estoque = 'S' and set_codigo = '$row[set_codigo]'");
   $rowsetor = pg_fetch_array($sqlsetor);
 echo "
                <tr>
	          <td width=70>Dados do Centro Estocador</td>
	          <td width=150 colspan=5><input type=text readonly name=set_nome size=100 value='$rowsetor[set_nome]'></td>
               </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
//
//-> Botoes
   $sql=pg_query("select reqcompra_produto.*, pro_nome
                  from reqcompra_produto, produto
                  where reqcompra_produto.pro_codigo = produto.pro_codigo
                  and   rcom_codigo = $rcom_codigo
                  and   rprod_codigo = $rprod_codigo
                  order by rcom_codigo desc limit 15");
    $row = pg_fetch_array($sql);
  $intquantidade = formata_valor0($row['rprod_quantidade']);

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Alteracao do Item da Requisicao de Compra</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	       <form name=altera_item method=post action=$PHP_SELF>
		<input type=hidden name=action value=edit>
		<input type=hidden name=acao value=>
		<input type=hidden name=id_login value=$id_login>
		<input type=hidden name=rcom_codigo value=$rcom_codigo>
		<input type=hidden name=rprod_codigo value=$rprod_codigo>
	      <tr>
		<td width=20>Produto:</td>
		<td colspan=4>
		 <select name=pro_codigo class=box>";
	    //
	    //-> SQL do produto
			$sql = "select distinct a.pro_codigo, a.pro_nome
					from produto a, setor b, produto_setor c
					where a.pro_codigo = c.pro_codigo
					and b.set_codigo = c.set_codigo "
					.($dados[0]=="" ? "" : " and b.uni_codigo = ".$dados[0]).
					" order by pro_nome";            
	    $query = pg_query($sql);
	      while($produto=pg_fetch_array($query)) {
	       echo ($produto[pro_codigo]==$row[pro_codigo])?"<option value='$produto[pro_codigo]' selected>$produto[pro_nome]</option>":"<option value='$produto[pro_codigo]'>$produto[pro_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	     <tr>
     		<td width=20>Quantidade:</td>
    		<td><input type=text name=rprod_quantidade class=box size=20 value='$intquantidade' ></td>
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
    $sql = pg_query("insert into reqcompra_produto ( " .
            "pro_codigo, " .
            "rprod_quantidade, " .
            "rcom_codigo " .
            ") values ( " .
            ($pro_codigo ? "'$pro_codigo'" : "null") . ", " .
            ($rprod_quantidade ? "'$rprod_quantidade'" : "null") . ", " .
            ($rcom_codigo ? "'$rcom_codigo'" : "null") . "  " .
            ")") or die(pg_last_error());

msg($id_login,$acao,$sql);

       echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='itens_reqcompra.php?id_login=$id_login&rcom_codigo=$rcom_codigo&acao=form_inclui_item&action='\", 0);
           </SCRIPT>";
}

//
//-> EDIT <--------------------------------------------------------->

 if($action=="edit") {
  $sql = pg_query("update reqcompra_produto set " .
            ($pro_codigo ? "pro_codigo='$pro_codigo'" : "pro_codigo=null") . ", " .
            ($rprod_quantidade ? "rprod_quantidade='$rprod_quantidade'" : "rprod_quantidade=null") . ", " .
            ($rcom_codigo ? "rcom_codigo='$rcom_codigo'" : "rcom_codigo=null") . "  " .
            "where rprod_codigo='$rprod_codigo'");

msg($id_login,$acao,$sql);
       echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='itens_reqcompra.php?id_login=$id_login&rcom_codigo=$rcom_codigo&acao=form_inclui_item&action='\", 0);
           </SCRIPT>";
}

//
//-> DEL <---------------------------------------------------------->

 if($action=="form_exclui_item") {
  $sql = pg_query("delete from reqcompra_produto where rprod_codigo='$rprod_codigo'");
msg($id_login,$acao,$sql);
       echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='itens_reqcompra.php?id_login=$id_login&rcom_codigo=$rcom_codigo&acao=form_inclui_item&action='\", 0);
          </SCRIPT>";
}

?>

