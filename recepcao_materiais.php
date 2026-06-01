<?php
	session_start();
?>
<script>
function desativa()
{
  document.getElementById('consolidall').src = '<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/recepcao_todos_materiais_off.jpg';
  document.getElementById('consolidcam').href = '#';
  document.getElementById('consolidcam').onclick = function() {
    alert("Um ou mais material nao tem estoque.");
    return false;
  }
}
function imprimi(cod)
{
  url = 'relatorio/ReqExibirRel.php?req_nr_nota='+cod;
  window.open(url,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
}
</script>
<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);

	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
           
    $stmt = "SELECT uni_codigo FROM usuarios WHERE usr_codigo = $id_login";
	$stmt = pg_query($stmt);
    $uni = pg_fetch_array($stmt);
    
    
	$uni[0]=="" ? $decisao = "" : $decisao = " AND setor.uni_codigo = ".$uni[0];
	$sql = pg_query("select set_codigo,set_nome from setor where set_estoque = 'S' and set_distribuidor = 'S' $decisao");
	$t = pg_fetch_array($sql);
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

reglog($id_login,"Acessando Recepcao Materiais");
echo "<fieldset><legend>RECEPCAO DA REQUISICAO DE MATERIAIS</legend>";

if(empty($acao) || ($acao == 'form_consolid'))
{
//-> Botoes
  echo "
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	  <tr>
		<td>
		  <fieldset>
			<legend>Opções</legend>
			<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
			  <tr>
				<form method=post action='recepcao_materiais.php?id_login=$id_login'>
				<input type=hidden name=acao value=busca>
				<input type=hidden name=id_login value=$id_login>
				<td width=180 align=right>Buscar </td>
				<td width=90><select name=palavra_chave class='box'>";
                $uni[0]=="" ? $decisao = "" : $decisao = " AND setor.uni_codigo = ".$uni[0];
                $sql = pg_query("select set_codigo,set_nome from setor where set_estoque = 'S' and set_distribuidor = 'S' $decisao ORDER BY set_nome");
                while ($temp = pg_fetch_array($sql))
                {
                  echo "<option value=\"$temp[set_codigo]\">$temp[set_nome]</option>";
                }
			  echo "
				</select></td>
				<td>".ChmodBtn($id_login,'procurar','recepcao_materiais.php')."</td></form>
				<td width=79><a href=movimentacao.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
			  </tr>
			</table>
		  </fieldset>
		</td>
	  </tr>
	</table><br>";

//
//-> Listando

  if (chmodbtn($id_login,"listar_if","recepcao_materiais.php"))
  {
      echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	     <tr>
	      <td>
	       <fieldset>
		<legend>Listando Movimentacoes nao Consolidadas</legend>
		 <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
		  <tr bgcolor=F9f9f9>
		    <td width=30 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Data</td>
		    <td width=60 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Numero Mov.</td>
		    <td width=150 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Tipo Movim.</td>
		    <td width=150 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Centro Estoc.</td>
		    <td width=150 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Setor Solic.</td>
		    <td width=150 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Finalizado</td>
		    <td width=150 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
    
    
	 $sql="select distinct a.data, a.req_data, a.req_nr_nota, a.setor, a.codsetor, 
		a.codsetorsolicit, a.nomesetorsolicit, a.desc_movimentacao, a.req_tipo, a.operacao, 
		a.req_codigo, c.req_finalizado from req_naoconsolid a, setor b, requisicao c 
		WHERE a.req_codigo = c.req_codigo 
		AND b.set_codigo = c.set_entrada 
		AND c.req_finalizado = 'S'
		and (c.set_entrada = $t[set_codigo] or c.set_saida = $t[set_codigo])";
	    //$sql .= " and c.req_tipo = 'T' ";
	$sql .= "order by a.req_data";
    
	if(isset($_GET['sql']))
    	echo $sql;
 
	    $sql = pg_query($sql);
	    
	 while($row=pg_fetch_array($sql)) {
               $finalizado = 'NAO';
	       if ($row['req_finalizado'] == 'S') $finalizado = 'SIM';
	   echo "<tr>
		   <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[data]</td>
		   <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[req_nr_nota]</td>
		   <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[desc_movimentacao]</td>
		   <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[setor]</td>
		   <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[nomesetorsolicit]</td>
		   <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$finalizado</td>
		   <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".
		       ChmodBtn($id_login,'editar','recepcao_materiais.php?acao=form_edit&req_codigo='.$row[req_codigo].'&set_codigo='.$t[set_codigo])."&nbsp;
		       <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/imprimir.jpg' onclick='imprimi(".$row[req_codigo].")'></td>
		 </tr>";
	 }
  }	 
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
        
echo "
<script type='text/javascript'>
    var acao = \"document.location.href = '{$PHP_SELF}?{$QUERY_STRING}'\";
//    setTimeout( acao, 1000 * 60 );
</script>
";
}


//------------------------------------------------------------------>
//-> Mostrando o resultado da busca
//------------------------------------------------------------------>

if($acao=="busca") 
{
	reglog($id_login,"Buscando em Recepcao Materiais $palavra_chave");
//
//-> Verificando Busca
//if(strlen($palavra_chave)<"1") {
//        echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
//                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
//                 <tr bgcolor=f9f9f9>
//                   <td align=center><font color=red size=2><b>ERRO</b></font><br>Busca com menos de <b>3</b> caracteres não permitida</td>
//                 </tr>
//                </table><br>";
//        echo "<SCRIPT LANGUAGE=\"JavaScript\">
//                  setTimeout(\"location='$PHP_SELF?id_login=$id_login'\", 2000);
//              </SCRIPT>";
// exit;
//}

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
	       <form method=post action='recepcao_materiais.php?id_login=$id_login'>
		<input type=hidden name=acao value=busca>
		<input type=hidden name=id_login value=$id_login>
	       <td width=180 align=right>Buscar</td>
	       <td width=90><select name=palavra_chave class='box'>";
                $uni[0]=="" ? $decisao = "" : $decisao = " AND setor.uni_codigo = ".$uni[0];
                $sql = pg_query("select set_codigo,set_nome from setor where set_estoque = 'S' and set_distribuidor = 'S' $decisao order by set_nome");
                while ($temp = pg_fetch_array($sql))
                {
                  echo "<option value=\"$temp[set_codigo]\">$temp[set_nome]</option>";
                }
  echo      "</select></td>
	       <td>".ChmodBtn($id_login,'procurar','recepcao_materiais.php')."</td></form>
	       <td width=79><a href=movimentacao.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

  $sql="select distinct a.data, a.req_data, a.req_nr_nota, a.setor, a.codsetor, 
          a.codsetorsolicit, a.nomesetorsolicit, a.desc_movimentacao, a.req_tipo, a.operacao, 
          a.req_codigo, c.req_finalizado from req_naoconsolid a, setor b, requisicao c 
          WHERE a.req_codigo = c.req_codigo 
          AND (b.set_codigo = c.set_entrada or b.set_codigo = c.set_saida)
          AND c.req_finalizado = 'S'
          and (c.set_entrada = $palavra_chave or c.set_saida = $palavra_chave)";

  //adicionados por renato para deixa apenas consolidar o movimento que é do tipo T => Transferencia		  
  //$sql .= " and c.req_tipo = 'T' ";
  $sql .= "order by a.req_data";

  /*$sql = "select c.req_codigo, c.req_data as data, c.req_nr_nota
	from  setor b, requisicao c 
          WHERE b.set_codigo = c.set_entrada 
          and (c.set_entrada = $palavra_chave or c.set_saida = $palavra_chave)";*/

	if(isset($_GET['sql']))
    	echo $sql;
    	
  $sql = pg_query($sql);
  $num=pg_num_rows($sql);
  if($num=="0") { $resp = "Nenhum Registro encontrado "; }
  if($num=="1") { $resp = "Encontrado <b>$num</b> Registro "; }
  if($num>"1") { $resp = "Encontrados <b>$num</b> Registros "; }

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>$resp</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
		<td width=30 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Data</td>
		<td width=60 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Numero Mov.</td>
		<td width=150 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Tipo Movim.</td>
		<td width=150 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Centro Estoc.</td>
		<td width=150 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Setor Solic.</td>
		<td width=150 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Finalizado</td>
		<td width=150 colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

     while($row=pg_fetch_array($sql)) {
               $finalizado = 'NAO';
	       if ($row['req_finalizado'] == 'S') $finalizado = 'SIM';
       echo "<tr>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[data]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[req_nr_nota]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[desc_movimentacao]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[setor]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[nomesetorsolicit]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$finalizado</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','recepcao_materiais.php?acao=form_edit&req_codigo='.$row[req_codigo])."&nbsp;<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/imprimir.jpg' onclick='imprimi(".$row[req_codigo].")'></td>
	     </tr>";
     }
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
 </tr>
        </table>";
echo "	
<script type='text/javascript'>
    var acao = \"document.location.href = 'recepcao_materiais.php?acao=form_consolid&id_login=$id_login'\";
    setTimeout( acao, 1000 * 60 );
</script>
";
}

//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>

 if($acao=="form_edit") {
reglog($id_login,"Formulario de Edicao de Recepcao Materiais");
//
//-> Formulario de edicao do cadastro SIMPLES
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
           <fieldset>
            <legend>Opções de Cadastro</legend>
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>
               <td width=79><a href=recepcao_materiais.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
               <td width=79><a id='consolidcam' href='recepcao_materiais.php?id_login=$id_login&acao=todos&req_codigo=$req_codigo'><img id='consolidall' src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/recepcao_todos_materiais_on.jpg border=0></a></td>
               <td>&nbsp;</td>
              </tr>
             </table>
           </fieldset>
          </td>
         </tr>
        </table><br>";

//
//-> Pegando as informcoes do banco pra mostrar no formulario
  /*  $sql = "select pro_codigo, pro_nome, coalesce(ireq_quantidade,0) as ireq_quantidade,
		  coalesce(ireq_qtde_solicitada,0) as ireq_qtde_solicitada,
		  calcula_estoque(pro_codigo, codsetor, date(now())) as estoqueatual, ireq_codigo, desc_status,
		  qtde_dispensado(pro_codigo) as qtdedisp
		  from req_naoconsolid
		  where  req_codigo = '$req_codigo'
		  order by  pro_nome";*/
  $sql = "select   p.pro_codigo,
										pro_nome, coalesce(ireq_quantidade,0) as ireq_quantidade, 
										coalesce(ireq_qtde_solicitada,0) as ireq_qtde_solicitada, 
       									coalesce(sum(sal_qtde),0) AS sal_qtde, 
										ireq_codigo, 
										ireq_consolidado 
									 from itens_requisicao AS ite 
  									 join requisicao AS req 
  									   ON req.req_codigo=ite.req_codigo
									 left join saldo as s 
									   ON ite.pro_codigo = s.pro_codigo
   									  AND s.set_codigo=req.set_saida
									 join produto as p
									   ON ite.pro_codigo = p.pro_codigo
									where req.req_codigo = '$req_codigo' 
									group by p.pro_codigo,
											 pro_nome, 
											 ireq_quantidade, 
											 ireq_qtde_solicitada, 
											 ireq_codigo, 
											 ireq_consolidado 
											 order by pro_nome";
 
//die($sql);
  $sqlmovimento =  pg_query($sql);
  
  //echo "<pre>$sql</pre>";


  echo "<br><br><form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=req_codigo value=$req_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Recepcao Materiais de Materiais</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
		<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Produto</td>
		<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Qtde. a Baixar</td>
		<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Qtde.  Solic.</td>
		<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Estoque Atual</td>
		";/*<td width=60 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Dispensado</td>
		<td width=60 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Estoque Liquido</td>
		<td width=60 align=center style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Status</td>*/		
		echo"<td width=250 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
        while($row=pg_fetch_array($sqlmovimento)) {
	    $temp = true;
        $intquantidade = formata_valor0($row['ireq_quantidade']);
        $intquantidadesol = formata_valor0($row['ireq_qtde_solicitada']);
        $estoque = formata_valor0($row['sal_qtde']);
        $totdisp = formata_valor0($row['qtdedisp']);
	$totliq = $estoque - $row['qtdedisp'];
	  if ( ($estoque <= 0) and ($estoque < $row['ireq_quantidade']) )
	    echo "<script>desativa();</script>";
        echo "
	       <tr>
	          <td align=left style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[pro_nome]</td>
	          <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$intquantidade</td>
	          <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$intquantidadesol</td>
	          <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9; font-weight:bold'> $estoque</td>";
	          /*<td width=10% align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9; font-weight:bold'>$totdisp</td>
	          <td width=10% align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9; font-weight:bold'>$totliq</td>
	          <td width=10% align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9; '>$row[desc_status]</td>*/		  
		 	 echo"<td  style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>";
          if (($row['ireq_quantidade'] <= $row['estoqueatual'] && $row['ireq_quantidade'] > 0)) {
               echo "
	       <a href=$PHP_SELF?id_login=$id_login&acao=edit&ireq_codigo=$row[ireq_codigo]&req_codigo=$req_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/recepcao_materiais_on.jpg border=0></a> &nbsp; &nbsp;";
           }
       echo "
	       <a href=$PHP_SELF?id_login=$id_login&acao=form_altera_item&ireq_codigo=$row[ireq_codigo]&req_codigo=$req_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a>
	       <a href=$PHP_SELF?id_login=$id_login&acao=cancela&ireq_codigo=$row[ireq_codigo]&req_codigo=$req_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
	      </tr>";
        }
         echo"
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br></form>";
}

if ($acao == 'form_altera_item') {
   $sql=pg_query("select ireq_codigo, itens_requisicao.pro_codigo, pro_nome,  ireq_quantidade, ireq_qtde_solicitada
                  from itens_requisicao, produto
                  where itens_requisicao.pro_codigo = produto.pro_codigo
                  and   req_codigo = $req_codigo
                  and   ireq_codigo = $ireq_codigo");

   $row = pg_fetch_array($sql);
   $intquantidade = formata_valor0($row['ireq_quantidade']);
   echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Alteracao do Item </legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	       <form name=altera_item method=post action=$PHP_SELF>
		<input type=hidden name=action value=edit>
		<input type=hidden name=acao value=edit_item>
		<input type=hidden name=id_login value=$id_login>
		<input type=hidden name=req_codigo value=$req_codigo>
		<input type=hidden name=ireq_codigo value=$ireq_codigo>
	      <tr>
		<td width=20>Produto:</td>
    		<td><input type=text name=pro_nome readonly class=box size=70 value='$row[pro_nome]'></td>
	      </tr>
	     <tr>
     		<td width=20>Quantidade:</td>
    		<td><input type=text name=ireq_quantidade class=box size=20 value='$intquantidade'></td>
         </tr>
	     <tr>
	       <td width=79><a href=recepcao_materiais.php?id_login=$id_login&req_codigo=$req_codigo&ireq_codigo=$ireq_codigo&acao=form_edit><input type=image  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td width=95><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
	      </tr></form>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
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
//-> EDIT <--------------------------------------------------------->

 if($acao=="edit") {
 	//echo "<pre>".print_r($_GET)."</pre>";
 	//-------------------------------------------------------------->
 	//-> TENTATIVA
 	//-------------------------------------------------------------->
 $sqlmovimento =pg_query(" select   p.pro_codigo,
										pro_nome, coalesce(ireq_quantidade,0) as ireq_quantidade, 
										coalesce(ireq_qtde_solicitada,0) as ireq_qtde_solicitada, 
										sum(sal_qtde) AS sal_qtde , 
										ireq_codigo, 
										ireq_consolidado 
									 from itens_requisicao AS ite 
									 join saldo as s 
									   ON ite.pro_codigo = s.pro_codigo
									 join produto as p
									   ON ite.pro_codigo = p.pro_codigo
									where req_codigo = '$req_codigo' 
									group by p.pro_codigo,
											 pro_nome, 
											 ireq_quantidade, 
											 ireq_qtde_solicitada, 
											 ireq_codigo, 
											 ireq_consolidado 
											 order by pro_nome
										 ");  
 	
  $vereq = pg_fetch_array(pg_query("select mov_codigo from movimento
            where  req_codigo = $req_codigo"));
	    
  $req = pg_fetch_array(pg_query("select req_data, set_entrada, set_saida, req_tipo, req_saida, current_date
                   from requisicao where  req_codigo = $req_codigo"));

  $ireq = pg_fetch_array(pg_query("SELECT pro_codigo, 
  										  usr_codigo, 
  										  ireq_status, 
  										  ireq_quantidade, 
  										  ireq_qtde_solicitada
                                     FROM itens_requisicao 
                                    WHERE ireq_codigo = $ireq_codigo"));
  $sql = "begin; ";
  $data=date("d_m_Y");

  if ($req[3] == 'T') {
     $tipo = 'T';
     $saida = 'T';
     $entrada = 'T';
  }
  else {
     $tipo='S';
     $saida = 'S';
     $entrada = '';
  }
     
  if ( empty ($vereq[0]) ) {
    $sqlcodmov = pg_fetch_array (pg_query("select nextval('seq_mov_codigo')"));    
       $sql .= " INSERT INTO movimento 
         						 (mov_codigo, 
         						  mov_data, 
         						  mov_tipo, 
         						  set_entrada, 
         						  set_saida, 
         						  mov_nr_nota, 
         						  mov_dt_nota, 
         						  mov_saida, 
         						  req_codigo, 
         						  mov_entrada)VALUES 
								  ($sqlcodmov[0], '$req[5]', '$tipo', $req[1], $req[2], 
								   '$sqlcodmov[0]', '$req[5]', '$saida', $req_codigo, '$entrada' ); ";
     $codmov = $sqlcodmov[0];	   
  }
  else {
        $codmov = $vereq[0];
     }
		$row=pg_fetch_array($sqlmovimento);         
         $sql .= " INSERT INTO itens_movimento
                   						(mov_codigo, 
                   						 pro_codigo, 
                   						 usr_codigo, 
                   						 ite_consolidado, 
                   						 ite_status, 
                   						 ite_quantidade, 
                   						 ite_qtde_solicitada)VALUES
								         ($codmov, $row[0], $id_login, 'D', 'A', $row[2], $row[3]); ";
 
  $sql .= "commit; ";

  $psql = pg_query($sql); 

//msg($id_login."&acao=form_edit&req_codigo=".$req_codigo,$acao,$sql);
$update = pg_query("update itens_requisicao set " .
                  "ireq_consolidado = 'D'," .
                  ($id_login ? "usr_codigo='$id_login'" : "usr_codigo=null") . "  " .
                  "where ireq_codigo ='$ireq_codigo'");
          
                  
reglog($id_login,"Consolidando Total Codigo $ireq_codigo");       
if($psql)
{
  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                                <tr bgcolor=f9f9f9>
                                        <td align=center style='font-weight:bold'>Editado com sucesso</td>
                                </tr>
                        </table><br>";
                echo "<SCRIPT LANGUAGE=\"JavaScript\">
                        setTimeout(\"location='recepcao_materiais.php?id_login=$id_login&req_codigo=$req_codigo&acao=form_edit'\", 2000);
                </SCRIPT>";
} else {
  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                                <tr bgcolor=F9f9f9>
                                        <td align=center style='font-weight:bold'>Editado com sucesso</td>
                                </tr>
                        </table><br>";
                echo "<SCRIPT LANGUAGE=\"JavaScript\">
                        setTimeout(\"location='recepcao_materiais.php?id_login=$id_login&req_codigo=$req_codigo&acao=form_edit'\", 2000);
                </SCRIPT>";
}
 	
 	//###############################################################
  /*$sql = pg_query("update itens_requisicao set " .
                  "ireq_consolidado = 'D'," .
                  ($id_login ? "usr_codigo='$id_login'" : "usr_codigo=null") . "  " .
                  "where ireq_codigo ='$ireq_codigo'");
reglog($id_login,"Editando Recepcao Materiais $ireq_codigo");
//msg($id_login."&acao=form_edit&req_codigo=".$req_codigo,$acao,$sql);
if($sql)
{
  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                                <tr bgcolor=f9f9f9>
                                        <td align=center style='font-weight:bold'>Editado com sucesso</td>
                                </tr>
                        </table><br>";
                echo "<SCRIPT LANGUAGE=\"JavaScript\">
                        setTimeout(\"location='recepcao_materiais.php'\", 2000);
                </SCRIPT>";
} else {
  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                                <tr bgcolor=F9f9f9>
                                        <td align=center style='font-weight:bold'>Editado com sucesso</td>
                                </tr>
                        </table><br>";
                echo "<SCRIPT LANGUAGE=\"JavaScript\">
                        setTimeout(\"location='recepcao_materiais.php'\", 2000);
                </SCRIPT>";
}*/
}

 if($acao=="cancela") {
 	$updateReq =  "UPDATE itens_requisicao SET 
  						  ireq_consolidado = 'C',
  						  ireq_quantidade = 0,
                  		  ".($id_login ? "usr_codigo='$id_login'" : "usr_codigo=null") ."
                    WHERE ireq_codigo ='$ireq_codigo'";
  $sql = pg_query($updateReq);
reglog($id_login,"Editando Recepcao Materiais $ireq_codigo");
//msg($id_login."&acao=form_edit&req_codigo=".$req_codigo,$acao,$sql);
if($sql)
{
  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                                <tr bgcolor=f9f9f9>
                                        <td align=center style='font-weight:bold'>Editado com sucesso</td>
                                </tr>
                        </table><br>";
                echo "<SCRIPT LANGUAGE=\"JavaScript\">
                        setTimeout(\"location='recepcao_materiais.php?acao=form_edit&req_codigo=$req_codigo&id_login=$id_login'\", 2000);
                </SCRIPT>";
} else {
  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                                <tr bgcolor=F9f9f9>
                                        <td align=center style='font-weight:bold'>Editado com sucesso</td>
                                </tr>
                        </table><br>";
                echo "<SCRIPT LANGUAGE=\"JavaScript\">
                        setTimeout(\"location='recepcao_materiais.php?acao=form_edit&req_codigo=$req_codigo&id_login=$id_login'\", 2000);
                </SCRIPT>";
}
} //acao==cancela


 if($acao=="edit_item") {
  $sql = pg_query("update itens_requisicao set " .
                  ($ireq_quantidade ? "ireq_quantidade='$ireq_quantidade'" : "ireq_quantidade=null") . ", " .
                  ($id_login ? "usr_codigo='$id_login'" : "usr_codigo=null") . "  " .
                  "where ireq_codigo ='$ireq_codigo'");
reglog($id_login,"Alterando quantidade do produto na Recepcao Materiais $ireq_codigo");
//msg($id_login."&acao=form_edit&req_codigo=".$req_codigo,$acao,$sql);
if($sql)
{
  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                                <tr bgcolor=f9f9f9>
                                        <td align=center style='font-weight:bold'>Editado com sucesso</td>
                                </tr>
                        </table><br>";
                echo "<SCRIPT LANGUAGE=\"JavaScript\">
                      setTimeout(\"location='recepcao_materiais.php?id_login=$id_login&req_codigo=$req_codigo&acao=form_edit'\", 2000);
                </SCRIPT>";
} else {
  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                                <tr bgcolor=F9f9f9>
                                        <td align=center style='font-weight:bold'>Editado com sucesso</td>
                                </tr>
                        </table><br>";
                echo "<SCRIPT LANGUAGE=\"JavaScript\">
                        setTimeout(\"location='recepcao_materiais.php?id_login=$id_login&req_codigo=$req_codigo&acao=form_edit'\", 2000);
                </SCRIPT>";
}
//recepcao_materiais.php?id_login=$id_login'
}

if($acao=="todos") {
//echo"<pre>".print_r($_GET)."</pre>";
//############################################################
//  Saida de todos os pedidos
//############################################################

$set_saida = "SELECT set_saida 
					FROM requisicao
	               WHERE req_codigo = '$req_codigo'";

	$exeSet = pg_query($set_saida);
	$resSet = pg_fetch_array($exeSet);
	$setor = $resSet[set_saida];
	
  /*$sqlmovimento =  pg_query("SELECT pro_codigo, 
  									pro_nome, 
  									ireq_quantidade, 
  									ireq_qtde_solicitada, 
  									ireq_status, 
                          calcula_estoque(pro_codigo, codsetor, date(now())) as estoqueatual, ireq_codigo
                    from req_dispensado
                    where  req_codigo = '$req_codigo'
		    and    ireq_quantidade is not null");*/
	$sqlDoMovimento = " select   p.pro_codigo,
										pro_nome, coalesce(ireq_quantidade,0) as ireq_quantidade, 
										coalesce(ireq_qtde_solicitada,0) as ireq_qtde_solicitada, 
										sum(sal_qtde) AS sal_qtde , 
										ireq_codigo, 
										ireq_consolidado 
									 from itens_requisicao AS ite 
									 join saldo as s 
									   ON ite.pro_codigo = s.pro_codigo
									 join produto as p
									   ON ite.pro_codigo = p.pro_codigo
									where req_codigo = '$req_codigo' 
									group by p.pro_codigo,
											 pro_nome, 
											 ireq_quantidade, 
											 ireq_qtde_solicitada, 
											 ireq_codigo, 
											 ireq_consolidado 
											 order by pro_nome
										 ";
	 $sqlmovimento =pg_query($sqlDoMovimento);
	 
	 
	 while ($validaQuantidade=pg_fetch_array($sqlmovimento)){
	 	if($validaQuantidade[sal_qtde] < $validaQuantidade[ireq_quantidade])
	 	{
	 		
	 		 //echo "<script>desativa();</script>";
	 		
	 		echo"<script LANGUAGE=\"JavaScript\">
	 				alert('A Quantidade a baixar do Produto $validaQuantidade[pro_nome] é maior que a quantidade em estoque');
	 			     setTimeout(\"location='recepcao_materiais.php?id_login=$id_login&req_codigo=$req_codigo&acao=form_edit'\", 0);	 			 	
	 			 </script>";
	 		exit();
	 		
	 	}
	 }  
  $vereq = pg_fetch_array(pg_query("SELECT mov_codigo 
  									  FROM movimento
            					     WHERE req_codigo = $req_codigo"));
	    
  $req = pg_fetch_array(pg_query("SELECT req_data,
  										 set_entrada, 
  										 set_saida, 
  										 req_tipo, 
  										 req_saida, 
  										 current_date as dataatual
                   				    FROM requisicao 
                   				   WHERE req_codigo = $req_codigo"));

  $ireq = pg_fetch_array(pg_query("SELECT pro_codigo, 
  										  usr_codigo, 
  										  ireq_status, 
  										  ireq_quantidade, 
  										  ireq_qtde_solicitada
			                   		 FROM itens_requisicao 
			                   		WHERE req_codigo = $req_codigo"));
  $sql = "begin; ";

  $data=date("d_m_Y");

  if ($req[3] == 'T') {
     $tipo = 'T';
     $saida = 'T';
     $entrada = 'T';
  }
  else {
     $tipo='S';
     $saida = 'S';
     $entrada = '';
  }

  if ( empty ($vereq[0]) ) {
  	
     $sqlcodmov = pg_fetch_array (pg_query("select nextval('seq_mov_codigo')"));
      $update = pg_query("update itens_requisicao set " .
                  "ireq_consolidado = 'D'," .
                  ($id_login ? "usr_codigo='$id_login'" : "usr_codigo=null") . "  " .
                  "where req_codigo ='$req_codigo'");     
     $sql .= " INSERT INTO movimento 
         						 (mov_codigo, 
         						  mov_data, 
         						  mov_tipo, 
         						  set_entrada, 
         						  set_saida, 
         						  mov_nr_nota, 
         						  mov_dt_nota, 
         						  mov_saida, 
         						  req_codigo, 
         						  mov_entrada)VALUES 
								  ($sqlcodmov[0], '$req[5]', '$tipo', $req[1], $req[2], 
								   '$sqlcodmov[0]', '$req[5]', '$saida', $req_codigo, '$entrada' ); ";
   
     $codmov = $sqlcodmov[0];	   
  
  }
  else {
        $codmov = $vereq[0];
     }
 $sqlmovimento2 =pg_query($sqlDoMovimento);
  while($row=pg_fetch_array($sqlmovimento2)) {         
         $sql .= " INSERT INTO itens_movimento
                   						(mov_codigo, 
                   						 pro_codigo, 
                   						 usr_codigo, 
                   						 ite_consolidado, 
                   						 ite_status, 
                   						 ite_quantidade, 
                   						 ite_qtde_solicitada)VALUES
								         ($codmov, $row[0], $id_login, 'D', 'A', $row[2], $row[3]); ";
         reglog($id_login,"Consolidando Total Codigo $ireq_codigo");
       
         
                  
  }
 
             
  $sql .= "commit; ";

  $psql = pg_query($sql);
  
reglog($id_login,"Consolidando Total Codigo $ireq_codigo");
 //############################################################
//  até aki
//############################################################
/*  $sqlmovimento =  pg_query("select pro_codigo, pro_nome, ireq_quantidade, ireq_qtde_solicitada,
                          calcula_estoque(pro_codigo, codsetor, date(now())) as estoqueatual, ireq_codigo
                    from req_naoconsolid
                    where  req_codigo = '$req_codigo'
                    and    calcula_estoque(pro_codigo, codsetor, date(now())) > 0");

  while($row=pg_fetch_array($sqlmovimento)) {
         $sql = pg_query("update itens_requisicao set " .
                  "ireq_consolidado = 'D'," .
                  ($id_login ? "usr_codigo='$id_login'" : "usr_codigo=null") . "  " .
                  "where ireq_codigo ='$row[ireq_codigo]'");
        reglog($id_login,"Consolidando Total Codigo $ireq_codigo");
  }*/

//msg($id_login."&acao=form_edit&req_codigo=".$req_codigo,$acao,$sql);
if($sql)
{
  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                                <tr bgcolor=f9f9f9 style='font-weight:bold'>
                                        <td align=center>Editado com sucesso</td>
                                </tr>
                        </table><br>";
                echo "<SCRIPT LANGUAGE=\"JavaScript\">
                        setTimeout(\"location='recepcao_materiais.php'\", 2000);
                </SCRIPT>";
} else {
  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                                <tr bgcolor=F9f9f9 style='font-weight:bold'>
                                        <td align=center>Editado com sucesso</td>
                                </tr>
                        </table><br>";
                echo "<SCRIPT LANGUAGE=\"JavaScript\">
                        setTimeout(\"location='recepcao_materiais.php'\", 2000);
                </SCRIPT>";
}
}
//
//-> DEL <---------------------------------------------------------->

?>
</fieldset>
