<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="ajax_motor.js"></script>
<!--<script language="JavaScript" type="text/javascript">
var validade;
var controle = 0;
function verificar()
{
	/*controle++;
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
	}*/
	pro_codigo =  document.inclui_item.pro_codigo.value;
	url = "buscarValidade.php?pro_codigo="+pro_codigo;
	alert(url);
	ajax_tudo(url, escrever_validade);
	/*ajax.open("GET", url, true);
	if(ajax)
	{
		ajax.onreadystatechange = function()
		{
			if(ajax.readyState == 4)
			{
				validade = ajax.responseText;
				if(controle == 1 && (document.inclui_item.ite_lote.value == '' || document.inclui_item.ite_validade.value == '') && document.inclui_item.ite_quantidade.value != '' && document.inclui_item.ite_vlrunit.value != '')
				{
					ob = new notnull();
				}
			}
		}
		ajax.send(null);
	}*/
}
	function escrever_validade(txt)
	{
		document.getElementById("validade").value = txt;
	}
</script>
--><!--<script language="JavaScript" type="text/javascript" src="ajax.js"></script> -->
<?
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

//Função que adiciona os campos;
function CampoValidade(event) { 
	function criarCampo(txt){	
		d = document.getElementById('lote_validade');
		if (txt == 'S'){
			d.style.display = '';
		}
		else {
			d.style.display = 'none';
		}
		document.getElementById('validade').value = txt;
	}

	var codigoProduto = document.getElementById('pro_codigo').value;
	url = 'buscaValidade.php?codProduto='+codigoProduto;
	ajax_tudo(url, criarCampo);
}


function ver_estoque(setor)
{   
	var produto = document.inclui_item.pro_codigo.value; 
    url = 've_estoque.php?prod='+produto+'&str='+setor ;
    ajax_tudo(url, trataEstoque);
}

function trataEstoque(txt){
	if (txt != 1){
		document.getElementById('ahu').innerHTML = '<strong>Estoque Atual:</strong>';
		var elemento = document.getElementById('hau');
		elemento.innerHTML = txt;
	}else{
		document.getElementById('ahu').innerHTML = '&nbsp;';
		var elemento = document.getElementById('hau');
		elemento.innerHTML = '&nbsp;';
	}
}

function notnull() 
{ 
	if (document.inclui_item.pro_codigo.value == '') {
       alert ('O produto deve ser digitado');
       document.inclui_item.pro_codigo.focus();
       return false;
    }
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
	if(document.getElementById('validade').value == 'S')
	{
		if(document.inclui_item.ite_lote.value == '')
		{
			alert('O lote deve ser digitado');
			document.inclui_item.ite_lote.focus();
			return false;
		}
		if(document.inclui_item.ite_validade.value == '')
		{
			alert('A validade deve ser digitada');
			document.inclui_item.ite_validade.focus();
			return false;
		} else {
			validade = 'N';
		}
	}
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
 if ($action == 'form_inclui_item') {
  

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Dados da Entrada</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
    $sql =  pg_query("select mov_codigo, to_char(mov_data, 'dd/mm/yyyy') as mov_data, mov_tipo, for_codigo, 
                          mov_desconto, mov_observacao, 
                          set_saida, set_entrada, mov_nr_nota, to_char(mov_dt_nota, 'dd/mm/yyyy') as mov_dt_nota,
                  'Entrada de Devolucao por Setor' as tiposaida     
             from movimento
             where  mov_codigo = '$mov_codigo'");
   $row=pg_fetch_array($sql);
   $sqlcentestoc=pg_query("select set_codigo, set_nome
                         from setor
                         where set_codigo = '$row[set_entrada]'");
   $rowcentestoc = pg_fetch_array($sqlcentestoc);        
   $r_estoque = $rowcentestoc['set_codigo'];
   $sqlsetor=pg_query("select set_codigo, set_nome
                         from setor
                         where set_codigo = '$row[set_saida]'");
   $rowsetor = pg_fetch_array($sqlsetor);        
   $rowdata = $row['mov_data'];

 echo "
      <tr> 
         <td width=70>Centro Estocador</td>
         <td width=70 ><input type=text readonly name=centroestoc size=40 value='$rowcentestoc[set_nome]'></td> 
               </tr>
         <tr> 
             <td width=70>Setor</td>
             <td width=70 ><input type=text readonly name=setor_nome size=40 value='$rowsetor[set_nome]'></td> 
             <td width=70>Tipo de Entrada</td>
             <td width=70 ><input type=text readonly name=tiposaida size=40 value='$row[tiposaida]'></td> 
         </tr>

	      <tr>
		<td width=70>Numero Mov.:</td>
		<td><input type=text readonly name=mov_nr_nota class=box size=20 value='$row[mov_nr_nota]'></td>
		<td width=70>Data da entrada:</td>
		<td><input type=text readonly name=mov_data class=box size=20 value='$row[mov_data]'></td>
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
	    <legend>Digitacao dos Itens da Entrada</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	    <form onSubmit=\"return notnull()\" name='inclui_item' method=post action=$PHP_SELF>
		<input type=hidden name=action value=form_insert>
		<input type=hidden name=acao value=>
		<input type=hidden name=id_login value=$id_login>
		<input type=hidden name=mov_codigo value=$mov_codigo>
		<input type=hidden name=setor value=$r_estoque>
		<input type=hidden name=dataestoque value=$rowdata>
		<input type=hidden id=validade>
	      <tr>
		<td width=20>Produto:</td>
		<td colspan=4>
		 <select name=pro_codigo id=pro_codigo class=box onChange=\"CampoValidade(this);\">
		 	<option value='0'>...</option>";
	    //
	    //-> SQL do produto
	    $query = pg_query("select pro_codigo, pro_nome from produto 
                           order by pro_nome");
	      //echo "<option value=''>-------</option>";
	      while($produto=pg_fetch_array($query)) {
	       echo "<option value='$produto[pro_codigo]'>$produto[pro_nome]</option>";
	      }
//              <td><b><div style='width:20px; text-align:left' id='qtd_estoque'> </div> </b></td>
	   echo "</select>
       </tr>
       <tr>
	        </td>
     		<td width=50>Quantidade:</td>
    		<td><input type=text name=ite_quantidade class=box size=20 '></td>
			<td width=95><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif></td>
			<td width=79><a href=entradevolucao.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/finalizar_on.jpg border=0></a></td>
			<td width=79><a href=entradevolucao.php?id_login=$id_login&mov_codigo=$mov_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
		</tr>
		<tr>
			<td colspan=2>
				<div id='lote_validade' style='display:none'>
					<table cellspacing=0 cellpadding=0 border=0>
						<tr>
				     		<td width=120>Lote:</td>
				    		<td><input type=text name=ite_lote class=box size=20></td>
				     		<td width=90>Validade:</td>
				    		<td><input type=text name=ite_validade class=box size=20  onKeypress=\"return Ajusta_Data(this, event);\"></td>
				    	</tr>
				    </table>
				</div>
			 </td>

	      </tr>
	      <tr>
	      	<td id='ahu' width=120 align=right>&nbsp;</td>
            <td id='hau'>&nbsp;</td>
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
	    <legend>Listando Itens Cadastradros para o Movimento</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9>
		<td width=400 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Produto</td>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Quantidade</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td> ";

   $sql=pg_query("select ite_codigo, itens_movimento.pro_codigo, pro_nome,  ite_quantidade, ite_vlrunit, ite_vlrdesc, 
                         ite_lote, ite_validade, (ite_quantidade * ite_vlrunit) as valortotal
                  from itens_movimento, produto
                  where itens_movimento.pro_codigo = produto.pro_codigo
                  and   (select mov_tipo    from movimento where mov_codigo = itens_movimento.mov_codigo) = 'E' 
                  and   (select mov_entrada from movimento where mov_codigo = itens_movimento.mov_codigo) = 'V' 
                  and   mov_codigo = $mov_codigo
                  order by mov_codigo desc ");
     while($row=pg_fetch_array($sql)) {
     $intquantidade = formata_valor0($row['ite_quantidade']);
       echo "<tr>
	       <td width=400 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[pro_nome]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$intquantidade</td>
	       <td width=100 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?acao=form_edit&ite_codigo=$row[ite_codigo]&action=form_altera_item&id_login=$id_login&mov_codigo=$mov_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a></td>
	       <td width=100 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?acao=del&ite_codigo=$row[ite_codigo]&action=form_exclui_item&id_login=$id_login&mov_codigo=$mov_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
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
	     <table cellspacing=3 cellpadding=0 border=0>";
    $sql =  pg_query("select mov_codigo, to_char(mov_data, 'dd/mm/yyyy') as mov_data, mov_tipo, for_codigo, 
                          mov_desconto, mov_observacao, set_saida,
                          set_entrada, mov_nr_nota, to_char(mov_dt_nota, 'dd/mm/yyyy') as mov_dt_nota,
                  'Entrada por Devolucao de Setor' as tiposaida     
             from movimento
             where  mov_codigo = '$mov_codigo'");
   $row=pg_fetch_array($sql);
   $sqlcentestoc=pg_query("select set_codigo, set_nome
                         from setor
                         where set_codigo = '$row[set_entrada]'");
   $rowcentestoc = pg_fetch_array($sqlcentestoc);        
   $r_estoque = $rowcentestoc['set_codigo'];
   $sqlsetor=pg_query("select set_codigo, set_nome
                         from setor
                         where set_codigo = '$row[set_saida]'");
   $rowsetor = pg_fetch_array($sqlsetor);        
   $rowdata = $row['mov_data'];
 echo "
                <tr> 
         <td width=100>Centro Estocador</td>
         <td width=70 ><input type=text readonly name=centroestoc size=40 value='$rowcentestoc[set_nome]'></td> 
               </tr>
         <tr> 
            <td width=100>Setor</td>
            <td width=70 ><input type=text readonly name=setor_nome size=40 value='$rowsetor[set_nome]'></td>
         </tr>
         <tr> 
            <td width=70>Tipo de Entrada: </td>
            <td>Devolucao de Setor</td>
         </tr>

	      <tr>
		<td width=70>Numero Mov.:</td>
		<td><input type=text name=mov_nr_nota class=box size=20 value='$row[mov_nr_nota]' readonly></td>
		</tr>
         <tr>
		<td width=70>Data da Entrada:</td>
		<td><input type=text name=mov_data class=box size=20 value='$row[mov_data]'></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
//
//-> Botoes
   $sql=pg_query("select ite_codigo, itens_movimento.pro_codigo, pro_nome,  ite_quantidade, ite_vlrunit, ite_vlrdesc, 
                         ite_lote, to_char(ite_validade, 'dd/mm/yyyy') as ite_validade,
                         (ite_quantidade * ite_vlrunit) as valortotal
                  from itens_movimento, produto
                  where itens_movimento.pro_codigo = produto.pro_codigo
                  and   (select mov_tipo from movimento where mov_codigo = itens_movimento.mov_codigo) = 'E' 
                  and   (select mov_entrada from movimento where mov_codigo = itens_movimento.mov_codigo) = 'V' 
                  and   mov_codigo = $mov_codigo
                  and   ite_codigo = $ite_codigo
                  order by mov_codigo desc limit 15");
    /*echo "select ite_codigo, itens_movimento.pro_codigo, pro_nome,  ite_quantidade, ite_vlrunit, ite_vlrdesc, 
                         ite_lote, to_char(ite_validade,'dd/mm/yyyy') as ite_validade, 
                         (ite_quantidade * ite_vlrunit) as valortotal
                  from itens_movimento, produto
                  where itens_movimento.pro_codigo = produto.pro_codigo
                  and   (select mov_tipo from movimento where mov_codigo = itens_movimento.mov_codigo) = 'E' 
                  and   (select mov_entrada from movimento where mov_codigo = itens_movimento.mov_codigo) = 'V' 
                  and   mov_codigo = $mov_codigo
                  and   ite_codigo = $ite_codigo
                  order by mov_codigo desc limit 15";*/
  $row = pg_fetch_array($sql);                
  $sqlprod=pg_query("select pro_codigo, pro_nome
                         from produto
                         where pro_codigo = '$row[pro_codigo]'");
   $rowprod = pg_fetch_array($sqlprod);        
   $intquantidade = formata_valor0($row['ite_quantidade']);
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Alteracao do Item da Entrada</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	       <form name=altera_item method=post action=$PHP_SELF>
		<input type=hidden name=action value=edit>
		<input type=hidden name=acao value=form_inclui_item>
		<input type=hidden name=id_login value=$id_login>
		<input type=hidden name=mov_codigo value=$mov_codigo>
		<input type=hidden name=ite_codigo value=$ite_codigo>
		<input type=hidden name=pro_codigo value=$row[pro_codigo]>
		<input type=hidden name=setor value=$r_estoque>
		<input type=hidden name=dataestoque value=$rowdata>
		<input type=hidden id=validade>
	      <tr>
		    <td width=20>Produto:</td>
            <td><input type=text name=pro_nome readonly class=box size=100 value='$row[pro_nome]'></td>
	    </tr>
	     <tr>
     		<td width=20>Quantidade:</td>
    		<td><input type=text name=ite_quantidade class=box size=20 value='$intquantidade'></td>
         </tr>
         <tr>
     		<td width=20>Lote:</td>
    		<td><input type=text name=ite_lote class=box size=20 value='$row[ite_lote]'></td>
         </tr>
         <tr>
     		<td width=20>Data de Validade:</td>
    		<td><input type=text name=ite_validade class=box size=20 value='$row[ite_validade]' onkeypress=\"return Ajusta_Data(this,event);\" maxlength=\"10\"></td>
         </tr>
	     <tr>
	       <td width=95><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
               <td width=79><a href=itens_entradevolucao.php?id_login=$id_login&mov_codigo=$mov_codigo&acao=form_incluir_item&action=edit><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
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
	$ite_consolidado = 'S';
	$select = "INSERT INTO itens_movimento 
						   (    pro_codigo, 
							    ite_quantidade,
							    ite_vlrunit, 
							    mov_codigo,
							    ite_lote,
							    ite_validade,
							    ite_consolidado) 
            		VALUES ( " .
								($pro_codigo ? "'$pro_codigo'" : "null") . ", " .
								($ite_quantidade ? "'$ite_quantidade'" : "null") . ", " . 
								($ite_vlrunit ? "'$ite_vlrunit'" : "null") . ", " .
								($mov_codigo ? "'$mov_codigo'" : "null") . ", " .
								($ite_lote ? "'$ite_lote'" : "'SEM_LOTE'") . ", " .
								($ite_validade ? "'$ite_validade'" : "'31/12/2900'") . ", " .
								"'$ite_consolidado'" . "  " .  //tipo da movimentacao = E - Entrada
            			  ")";
	$sql = pg_query($select);

	echo "<SCRIPT LANGUAGE=\"JavaScript\">
				setTimeout(\"location='itens_entradevolucao.php?id_login=$id_login&mov_codigo=$mov_codigo&action=form_inclui_item'\", 0);
		  </SCRIPT>";
}

//
//-> EDIT <--------------------------------------------------------->

 if($action=="edit") {
    $ite_consolidado = 'S';
    $update = "UPDATE itens_movimento 
    			  SET " .
						($ite_quantidade ? "ite_quantidade = '$ite_quantidade'" : "ite_quantidade = null") . ", " .
						($ite_vlrunit ? "ite_vlrunit = '$ite_vlrunit'" : "ite_vlrunit = null") . ", " .
						($mov_codigo ? "mov_codigo = '$mov_codigo'" : "mov_codigo = null") . ", " .
						($ite_lote ? "ite_lote = '$ite_lote'" : "ite_lote = 'SEM_LOTE'") . ", " .
						($ite_validade ? "ite_validade = '$ite_validade'" : "ite_validade = '31/12/2900'") . ",  " .
						"ite_consolidado = '$ite_consolidado'" . "  " .  //tipo da movimentacao = E - Entrada
				 "WHERE ite_codigo='$ite_codigo'";
    $sql = pg_query($update);

       echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='itens_entradevolucao.php?id_login=$id_login&mov_codigo=$mov_codigo&action=form_inclui_item'\", 0);
           </SCRIPT>";
}

//
//-> DEL <---------------------------------------------------------->

 if($action=="form_exclui_item") {
  $sql = pg_query("delete from itens_movimento where ite_codigo='$ite_codigo'");
       echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='itens_entradevolucao.php?id_login=$id_login&mov_codigo=$mov_codigo&action=form_inclui_item'\", 0);
          </SCRIPT>";
}

?>

