<script src="ajax_motor.js"></script>
<script src="funcoes.js"></script>
<script src="ajax.js"></script>
<script language="JavaScript" type="text/javascript">
var resposta;
function escolher(parametro){
	document.inclui_item.ite_lote.value = '';
	document.inclui_item.ite_validade.value = '';
	v = document.inclui_item.select_lote.value.split(':');
	document.inclui_item.ite_lote.value = v[0];
	document.inclui_item.ite_validade.value = v[1];
}
not_lote = true;
function buscar()
{
	document.inclui_item.ite_quantidade.value = '';
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
	set_codigo = document.inclui_item.setor.value;
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

function verificar_estoque()
{
	ite_qtde = document.inclui_item.ite_quantidade.value;
	set_codigo = document.inclui_item.setor.value;
	pro_codigo = document.inclui_item.pro_codigo.value;
	sal_lote = document.inclui_item.select_lote.value;
	if (sal_lote != ''){
		url = "buscarEstoque.php?qtde="+ite_qtde+"&set_codigo="+set_codigo+"&pro_codigo="+pro_codigo+"&sal_lote="+sal_lote;
		//ajax_tudo(url, verificarEstoque);
	}
}
trava = false;
trava2 = false;
function verificarEstoque(txt)
{
	if(txt == 'N')
	{
		alert("Este produto nao contem estoque! Nao sera possivel fazer a saida");
		trava2 = true;
	} else {
		ite_qtde = document.inclui_item.ite_quantidade.value;
		txt = txt.split("###");
		if((txt[1] - ite_qtde) < 0)
		{
			trava = true;
			trava2 = false;
			//alert("A quantidade digitada ultrapassa a quantidade em estoque. \nPor favor corriga a quantidade digitada.")
		} else {
			trava = false;
		}
	}
}
function validaF(){
	produto = document.getElementById('pro_codigo').value;
	quantidade = document.getElementById('ite_quantidade').value;
	mov_codigo = document.getElementById('mov_codigo').value;
	select_lote = document.getElementById('select_lote').value;
	id_login = document.getElementById('id_login').value
	url = "verificaSeTemItem.php?pro_codigo="+produto+"&select_lote="+select_lote+"&ite_quantidade="+quantidade+"&mov_codigo="+mov_codigo+"&acao=verificaf";
	//alert(url);
	ajax_tudo(url,respostaV);



	
	
}
function respostaV(txt){
	if(txt == 1){
		window.location.href="saida.php?mov_codigo="+mov_codigo+"&id_login="+id_login+"&link_extra=";
		return false;
	}
	else{
		alert('Adicione pelo menos um produto para finalizar.');
		return false;
	}
//	if(produto != "" && quantidade){
//		alert('O produto deve ser preenchido!');
//		document.getElementById('pro_codigo').focus;
//		return false;
//	}
//	if(quantidade == ""){
//		alert('A quantidade  deve ser preenchida!');
//		document.getElementById('ite_quantidade').focus;
//		return false;
//	}
	
}
</script>
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
	/*alert(not_lote);
	return false;*/
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
    if(document.getElementById('qtd_estoque') && (new Number(document.getElementById('qtd_estoque').value) < new Number(document.inclui_item.ite_quantidade.value)))
    {
        trava = true;
    }
	if(trava)
	{
		alert('A quantidade digitada ultrapassa a quantidade em estoque. \\nPor favor corrija a quantidade digitada.');
		return false;
	}
	if(trava2)
	{
		alert('Este produto nao contem estoque! Nao sera possivel fazer a saida');
		return false;
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

/*echo "<pre>";
    print_r($_REQUEST);
echo "</pre>";
*/
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>
 if ($action == 'form_inclui_item') {

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Dados da Saida</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
    $sql =  pg_query("select m.mov_codigo, to_char(m.mov_data, 'dd/mm/yyyy') as mov_data, mov_tipo, for_codigo,
                          mov_desconto, mov_observacao,
                          set_saida, *,set_entrada, mov_nr_nota, to_char(mov_dt_nota, 'dd/mm/yyyy') as mov_dt_nota,
                  case when mov_saida = 'S' then 'Saida de Consumo'
                       when mov_saida = 'R' then 'Perdas'
                       when mov_saida = 'M' then 'Emprestimo'
                       when mov_saida = 'I' then 'Inventario'
                       when mov_saida = 'D' then 'Doacao'
                       when mov_saida = 'P' then 'Permuta'
                       when mov_saida = 'O' then 'Outras Saidas'
                  end as tiposaida
             from movimento m
             
             where  m.mov_codigo = '$mov_codigo'");
    
   $row=pg_fetch_array($sql);
   $sqlcentestoc=pg_query("select set_codigo, set_nome
                         from setor
                         where set_codigo = '$row[set_saida]'");
   $rowcentestoc = pg_fetch_array($sqlcentestoc);
   $r_estoque = $rowcentestoc['set_codigo'];
   
   if(!empty($row['set_entrada'])){
	   $sqlsetor=pg_query("select set_codigo, set_nome
	                         from setor
	                         where set_codigo = '$row[set_entrada]'");
	   $rowsetor = pg_fetch_array($sqlsetor);
   }
   $rowdata = $row['mov_data'];
   

 echo "
                <tr>
         <td width=70>Centro Estocador</td>
         <td width=70 ><input type=text readonly name=centroestoc size=40 value='$rowcentestoc[set_nome]' class=box></td>";
 
 if(!empty($row['set_entrada'])){
 	echo "
               </tr>
         <tr>
         <td width=70>Setor</td>
         <td width=70 ><input type=text readonly name=setor_nome size=70 value='$rowsetor[set_nome]' class=box></td>";
 }
 
 echo "  <td width=70>Tipo de Saida</td>
         <td width=70 ><input type=text readonly name=tiposaida size=30 value='$row[tiposaida]' class=box></td>

         </tr>

	      <tr>
		<td width=70>Numero Mov.:</td>
		<td><input type=text name=mov_nr_nota class=box size=20 value='$row[mov_nr_nota]' readonly></td>
		<td width=70>Data da Saida:</td>
		<td><input type=text name=mov_data class=box size=20 value='$row[mov_data]' readonly></td>
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
	    <legend>Digitacao dos Itens da Saida</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	       <form onsubmit=\"return notnull();\" name='inclui_item' method=post action=$PHP_SELF>
		<input type=hidden name=action value=form_insert>
		<input type=hidden name=acao value=>
		<input type=hidden id=id_login name=id_login value=$_GET[id_login]>
		<input type=hidden id=mov_codigo name=mov_codigo value=$mov_codigo>
		<input type=hidden name=setor value=$r_estoque>
		<input type=hidden name=dataestoque value=$rowdata>
		<input type=hidden name='link_extra' value='$link_extra'>
		<input type=hidden name=ite_lote value=>
		<input type=hidden name=ite_validade value=>
	      <tr>
		<td width=20 align=right>Produto:</td>
		<td>
		<select name=pro_codigo id=pro_codigo class=box onChange=\"ver_estoque($r_estoque);buscar();\" >";
	    //
	    //-> SQL do produto
	    /*$query = pg_query("select pro_codigo, pro_nome from produto
         --                  where calcula_estoque(pro_codigo, $r_estoque, date(now())) > 0
                           order by pro_nome");*/
		
		$sql = "select * from produto
				where pro_codigo in (select pro_codigo
									from produto_setor
									where set_codigo = $rowcentestoc[set_codigo]) AND
						pro_situacao = 'A'
				order by pro_nome";
				
		$query = db_query($sql);
		
	      echo "<option value=''>-------</option>";
	      while($produto=pg_fetch_array($query)) {
	       echo "<option value='$produto[pro_codigo]' style='width:500px;' title='$produto[pro_nome]'>$produto[pro_nome]</option>";
	      }
//           echo "  <td><b><div style='width:20px; text-align:left' id='qtd_estoque'> </div> </b></td>";
	   echo "</select> 
	        </td>
			</tr>
			<tr>
			<td width=50 align=right>
				Lote:
			</td>
			<td>
			<select class=box name=select_lote id=select_lote  onchange='escolher(this)'><option value=''>Escolha</option></select>
			</td>
			</tr>
			<tr>
     		<td width=50 align=right>Quantidade:</td>
    		<td colspan=2><input type=text name=ite_quantidade  id=ite_quantidade class=box size=20 onchange='verificar_estoque();' onkeypress='return Bloqueia_Caracteres(event)' autocomplete='off'></td>
            </tr>
            <tr>";
              //<td width=50 style='display:none;'><b>Estoque Atual:</b></td>
              //<td style='display:none;'><b><input type=text name=qtd_estoque id=qtd_estoque readonly> </b></td>
	     echo "
              <td id='ahu' width=120 align=right>&nbsp;</td>
              <td id='hau'>&nbsp;</td>
	       <td width=95>";
		   //adicionado esta restrição conforme pedido da os número 166.
		   //alteração desfeita
		   $data_setor = pg_query("SELECT setp_data_inicial, setp_data_final
					FROM setor_periodo WHERE set_codigo = $rowcentestoc[set_codigo]
					ORDER BY setp_codigo DESC LIMIT 1");
		   $data_setor = pg_fetch_array($data_setor);
		   $sel = "SELECT mov_data
					FROM movimento
					WHERE mov_data between '$data_setor[0]' and '$data_setor[1]'
					AND mov_codigo = $mov_codigo";
			//echo $sel;
			$exec_sel = pg_query($sel);
			if(pg_num_rows($exec_sel) > 0)
			{
				echo "<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif'>";
			} else {
				echo "&nbsp;";
			}
			echo "</td>
	       <td width=79>";
			if(pg_num_rows($exec_sel) > 0)
			{
				//echo "<span style=\"cursor:pointer;\" onClick='return validaF()'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/finalizar_on.jpg border=0></span>";
			} else {
				echo "&nbsp;";
			}
		   echo "</td>
           <td width=79>";
				echo "<a href=saida.php?id_login=$id_login&mov_codigo=$mov_codigo&link_extra=$link_extra><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>";
			echo "</td>
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
		<td width=400 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Produto</td>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Quantidade</td>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Lote</td>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Validade</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td> ";

   $sql=pg_query("select ite_codigo, itens_movimento.pro_codigo, pro_nome,  ite_quantidade, ite_vlrunit, ite_vlrdesc,
                         ite_lote, ite_validade, (ite_quantidade * ite_vlrunit) as valortotal
                  from itens_movimento, produto
                  where itens_movimento.pro_codigo = produto.pro_codigo
                  and   (select mov_tipo from movimento where mov_codigo = itens_movimento.mov_codigo) = 'S'
                  and   mov_codigo = $mov_codigo
                  order by mov_codigo desc ");
     while($row=pg_fetch_array($sql)) {
     $intquantidade = formata_valor0($row['ite_quantidade']);
       echo "<tr>
	       <td width=400 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[pro_nome]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$intquantidade</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[ite_lote]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[ite_validade]</td>
	       <td width=100 aling=right  style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>";
			if(pg_num_rows($exec_sel) > 0)
			{
				echo "<a href=$PHP_SELF?acao=del&ite_codigo=$row[ite_codigo]&action=form_exclui_item&id_login=$id_login&mov_codigo=$mov_codigo onclick=\"if (!confirm('Realmente deseja apagar este registro?')) return false\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0 aling=rigth></a>&nbsp;";
			} else {
				echo "&nbsp;";
			}
			echo "</td>
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
	    <legend>Dados da Saida</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
    $sql =  pg_query("select mov_codigo, to_char(mov_data, 'dd/mm/yyyy') as mov_data, mov_tipo, for_codigo,
                          mov_desconto, mov_observacao, set_saida,
                          set_entrada, mov_nr_nota, to_char(mov_dt_nota, 'dd/mm/yyyy') as mov_dt_nota,
                  case when mov_saida = 'S' then 'Saida de Consumo'
                       when mov_saida = 'R' then 'Perdas'
                       when mov_saida = 'M' then 'Emprestimo'
                       when mov_saida = 'I' then 'Inventario'
                       when mov_saida = 'D' then 'Doacao'
                       when mov_saida = 'P' then 'Permuta'
                       when mov_saida = 'O' then 'Outras Saidas'
                  end as tiposaida
             from movimento
             where  mov_codigo = '$mov_codigo'");
   $row=pg_fetch_array($sql);
   $sqlcentestoc=pg_query("select set_codigo, set_nome
                         from setor
                         where set_codigo = '$row[set_saida]'");
   $rowcentestoc = pg_fetch_array($sqlcentestoc);
   $r_estoque = $rowcentestoc['set_codigo'];
   
    if(!empty($row['set_entrada'])){
	   $sqlsetor=pg_query("select set_codigo, set_nome
	                         from setor
	                         where set_codigo = '$row[set_entrada]'");
	   $rowsetor = pg_fetch_array($sqlsetor);
   }
   
   $rowdata = $row['mov_data'];
 echo "
                <tr>
         <td width=70>Centro Estocador</td>
         <td width=70 ><input type=text readonly name=centroestoc size=40 value='$rowcentestoc[set_nome]' class='box'></td>";
 
 if(!empty($row['set_entrada'])){
 	echo "
               </tr>
         <tr>
            <td width=70>Setor</td>
            <td width=70 ><input type=text readonly name=setor_nome size=40 value='$rowsetor[set_nome]'></td>";
 }
  echo "    <td width=70>Tipo de Saida</td>
            <td width=70 ><input type=text readonly name=tiposaida size=40 value='$row[tiposaida]'></td>
         </tr>

	      <tr>
		<td width=70>Numero Mov.:</td>
		<td><input type=text name=mov_nr_nota class=box size=20 value='$row[mov_nr_nota]'></td>
		<td width=70>Data da Saida:</td>
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
                         ite_lote, to_char(ite_validade,'dd/mm/yyyy') as ite_validade,
                         (ite_quantidade * ite_vlrunit) as valortotal
                  from itens_movimento, produto
                  where itens_movimento.pro_codigo = produto.pro_codigo
                  and   (select mov_tipo from movimento where mov_codigo = itens_movimento.mov_codigo) = 'S'
                  and   mov_codigo = $mov_codigo
                  and   ite_codigo = $ite_codigo
                  order by mov_codigo desc ");
  $row = pg_fetch_array($sql);
  $sqlprod=pg_query("select pro_codigo, pro_nome
                         from produto
                         where pro_codigo = '$row[pro_codigo]'");
   $rowprod = pg_fetch_array($sqlprod);
   $produto = $rowprod['pro_codigo'];
   $intquantidade = formata_valor0($row['ite_quantidade']);
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Alteracao do Item da Saida</legend>
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
	      <tr>
              <td width=20>Produto:</td>
              <td><input type=text name=pro_nome readonly class=box size=100 value='$row[pro_nome]'></td>
	    </tr>
	     <tr>
     		<td width=20>Quantidade:</td>
    		<td><input type=text name=ite_quantidade class=box size=20 value='$intquantidade'  onchange='verificar_estoque();' onkeypress='return Bloqueia_Caracteres(event)' autocomplete='off'></td>
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
                                   calcula_estoque_lote_validade(produto.pro_codigo,  $r_estoque, 
                                                        '$data', ite_lote, ite_validade) as estoque
                            from produto, itens_movimento
                            where produto.pro_codigo = itens_movimento.pro_codigo
                            and ite_lote is not null
                            and produto.pro_codigo = $produto
                            group by produto.pro_codigo, produto.pro_nome, ite_lote, ite_validade
                            having calcula_estoque_lote_validade(produto.pro_codigo,  $r_estoque, 
                                                   '$data', ite_lote, ite_validade) > 0
                            order by ite_lote, ite_validade";
                   $query = pg_query($select);

	           while($setor=pg_fetch_array($query)) {
	            //     echo "<option value='$setor[ite_lote]'>$setor[valor]</option>";
                    echo ($setor[ite_lote]==$row[ite_lote])?"<option value='$setor[ite_lote]' selected>
                        $setor[valor]</option>":"<option value='$setor[ite_lote]'>$setor[valor]</option>";
	           }
	   echo "</select>
	        </td>
         </tr>
	     <tr>
	       <td width=95><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
               <td width=79><a href=itens_saida.php?id_login=$id_login&mov_codigo=$mov_codigo&action=form_inclui_item><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
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
if($acao == "verificaf"){
	echo "teste";
}

//------------------------------------------------------------------>
//-> SQL's
//------------------------------------------------------------------>
//
//-> ADD <---------------------------------------------------------->

 if($action=="form_insert") {
    $sqlmovimento = pg_query("select set_saida, to_char(mov_data,'dd/mm/yyyy') as mov_data from movimento where mov_codigo = $mov_codigo");
    $rowmovim=pg_fetch_array($sqlmovimento);
    $sqlestoque = pg_query("select calcula_estoque($pro_codigo, $rowmovim[set_saida], '$rowmovim[mov_data])') as estoque");
    $row=pg_fetch_array($sqlestoque);
    $sqlusuario = pg_query("select usr_consolidacao_automatica from usuarios where usr_codigo = $id_login");
    $rowusuario=pg_fetch_array($sqlusuario);
    if ($rowusuario['usr_consolidacao_automatica'] == 'S') {
       if ($ite_quantidade > $row['estoque']) {
          $ite_consolidado = 'N';
       }
       else {
            $ite_consolidado = 'S';
       }
    }
    else {
       $ite_consolidado = 'N';
    }
    $ite_qtde_solicitada = $ite_quantidade;
    $sql = pg_query("insert into itens_movimento ( " .
            "pro_codigo, " .
            "ite_quantidade, " .
            "ite_qtde_solicitada, " .
            "ite_vlrunit, " .
            "mov_codigo, " .
            "ite_lote, " .
            "ite_validade, " .
            "ite_consolidado  " .
            ") values ( " .
            ($pro_codigo ? "'$pro_codigo'" : "null") . ", " .
            ($ite_quantidade ? "'$ite_quantidade'" : "null") . ", " .
            ($ite_qtde_solicitada ? "'$ite_qtde_solicitada'" : "null") . ", " .
            ($ite_vlrunit ? "'$ite_vlrunit'" : "null") . ", " .
            ($mov_codigo ? "'$mov_codigo'" : "null") . ", " .
            ($ite_lote ? "'$ite_lote'" : "null") . ", " .
            ($ite_validade ? "'$ite_validade'" : "null") . ", " .
            "'{$ite_consolidado}'" . "  " .  //tipo da movimentação = E - Entrada
            ")");

     
      $calcestoque = pg_fetch_row(pg_query("select mov_data, set_entrada
                                             from movimento where mov_codigo = $mov_codigo"));
      $atualiza_preco = pg_query("select calcula_preco($pro_codigo,  $calcestoque[1], '$calcestoque[0]')");


       echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='itens_saida.php?id_login=$id_login&mov_codigo=$mov_codigo&action=form_inclui_item'\", 0);
           </SCRIPT>";
}

//
//-> EDIT <--------------------------------------------------------->

 if($action=="edit") {
    $sqlmovimento = pg_query("select set_saida, to_char(mov_data,'dd/mm/yyyy') as mov_data from movimento where mov_codigo = $mov_codigo");
    $rowmovim=pg_fetch_array($sqlmovimento);
    $sqlestoque = pg_query("select calcula_estoque($pro_codigo, $rowmovim[set_saida], '$rowmovim[mov_data])') as estoque");
    $row=pg_fetch_array($sqlestoque);
    $sqlusuario = pg_query("select usr_consolidacao_automatica from usuarios where usr_codigo = $id_login");
    $rowusuario=pg_fetch_array($sqlusuario);
    if ($rowusuario['usr_consolidacao_automatica'] == 'S') {
       if ($ite_quantidade > $row['estoque']) {
          $ite_consolidado = 'N';
       }
       else {
            $ite_consolidado = 'S';
       }
    }
    else {
       $ite_consolidado = 'N';
    }
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
            "ite_consolidado = '{$ite_consolidado}'" . "  " .  //tipo da movimentação = E - Entrada
            "where ite_codigo='$ite_codigo'");

      $calcestoque = pg_fetch_row(pg_query("select mov_data, set_entrada
                                             from movimento where mov_codigo = $mov_codigo"));
      $atualiza_preco = pg_query("select calcula_preco($pro_codigo,  $calcestoque[1], '$calcestoque[0]')");

       echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='itens_saida.php?id_login=$id_login&mov_codigo=$mov_codigo&action=form_inclui_item'\", 0);
           </SCRIPT>";
}

//
//-> DEL <---------------------------------------------------------->

if($action=="form_exclui_item") {
 	$delete = "delete 
 	  			 from itens_movimento 
 	  			where ite_codigo = '$ite_codigo'";
	$sql = pg_query($delete);
  
	$sqlcalcestoque = "select mov_data, 
							  set_entrada
                         from movimento 
                        where mov_codigo = $mov_codigo";
	$calcestoque = pg_fetch_row(pg_query($sqlcalcestoque));
	
	$selectdetelepreco = "delete 
							from precomedio 
						   where pro_codigo = $pro_codigo 
						     and mov_data = $calcestoque[0]";
	$deletepreco = pg_query($selectdetelepreco);
	
	$selectatualiza_preco = "select calcula_preco($pro_codigo,  $calcestoque[1], $calcestoque[0])";
	$atualiza_preco = pg_query($selectatualiza_preco);

	echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='itens_saida.php?id_login=$id_login&mov_codigo=$mov_codigo&action=form_inclui_item'\", 0);
          </SCRIPT>";

}

?>

