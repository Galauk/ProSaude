<?php
	session_start(); 
?>
<script>
function desativa()
{
  document.getElementById('consolidall').src = '<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/consolidar_todos_off.jpg';
  document.getElementById('consolidcam').href = '#';
  document.getElementById('consolidcam').onclick = function() {
    alert("Um ou mais material nao tem estoque.");
    return false;
  }
}
function imprimi(cod)
{
  url = 'relatorio/consolidacao.php?mov_codigo='+cod;
  window.open(url,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
}
</script>
<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
    verauth($id_login);
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
           
    $stmt = "SELECT uni_codigo FROM usuarios WHERE usr_codigo = $id_login";
	$stmt = pg_query($stmt);
    $uni = pg_fetch_array($stmt);
    
    
	$uni[0]=="" ? $decisao = "" : $decisao = " AND setor.uni_codigo = ".$uni[0];
	$sql = pg_query("select set_codigo,set_nome from setor where set_estoque = 'S' $decisao");
	$t = pg_fetch_array($sql);
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

reglog($id_login,"Acessando Consolidacao");
echo "<fieldset><legend>MOVIMENTAÇÃO/CONSOLIDAÇÃO</legend>";

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
				<form method=post action='consolidacao.php?id_login=$id_login'>
				<input type=hidden name=acao value=busca>
				<input type=hidden name=id_login value=$id_login>
				<td width=180 align=right>Buscar </td>
				<td width=90><select name=palavra_chave class='box'>";
                $uni[0]=="" ? $decisao = "" : $decisao = " AND setor.uni_codigo = ".$uni[0];
                $sql = pg_query("select set_codigo,set_nome from setor where set_estoque = 'S' $decisao");
                while ($temp = pg_fetch_array($sql))
                {
                  echo "<option value=\"$temp[set_codigo]\">$temp[set_nome]</option>";
                }
			  echo "
				</select></td>
				<td>".ChmodBtn($id_login,'procurar','consolidacao.php')."</td></form>
				<td width=79><a href=movimentacao.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
			  </tr>
			</table>
		  </fieldset>
		</td>
	  </tr>
	</table><br>";

//
//-> Listando

  if (chmodbtn($id_login,"listar_if","consolidacao.php"))
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
		    <td width=150 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
    
    
	 $sql="select distinct a.data, a.mov_data, a.mov_nr_nota, a.setor, a.codsetor, 
		a.codsetorsolicit, a.nomesetorsolicit, a.desc_movimentacao, a.mov_tipo, a.operacao, 
		a.mov_codigo from mov_naoconsolid a, setor b, movimento c 
		WHERE a.mov_codigo = c.mov_codigo 
		AND b.set_codigo = c.set_entrada 
		and (c.set_entrada = $t[set_codigo] or c.set_saida = $t[set_codigo])";
	    //$sql .= " and c.mov_tipo = 'T' ";
	$sql .= "order by a.mov_data";
    
	    $sql = pg_query($sql);
	    
	 while($row=pg_fetch_array($sql)) {
	   echo "<tr>
		   <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[data]</td>
		   <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mov_nr_nota]</td>
		   <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[desc_movimentacao]</td>
		   <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[setor]</td>
		   <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[nomesetorsolicit]</td>
		   <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".
		       ChmodBtn($id_login,'editar','consolidacao.php?acao=form_edit&mov_codigo='.$row[mov_codigo])."&nbsp;
		       <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/imprimir.jpg' onclick='imprimi(".$row[mov_codigo].")'></td>
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
	reglog($id_login,"Buscando em Consolidacao $palavra_chave");
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
	       <form method=post action='consolidacao.php?id_login=$id_login'>
		<input type=hidden name=acao value=busca>
		<input type=hidden name=id_login value=$id_login>
	       <td width=180 align=right>Buscar</td>
	       <td width=90><select name=palavra_chave class='box'>";
                $uni[0]=="" ? $decisao = "" : $decisao = " AND setor.uni_codigo = ".$uni[0];
                $sql = pg_query("select set_codigo,set_nome from setor where set_estoque = 'S' $decisao");
                while ($temp = pg_fetch_array($sql))
                {
                  echo "<option value=\"$temp[set_codigo]\">$temp[set_nome]</option>";
                }
  echo      "</select></td>
	       <td>".ChmodBtn($id_login,'procurar','consolidacao.php')."</td></form>
	       <td width=79><a href=movimentacao.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

  $sql="select distinct a.data, a.mov_data, a.mov_nr_nota, a.setor, a.codsetor, 
          a.codsetorsolicit, a.nomesetorsolicit, a.desc_movimentacao, a.mov_tipo, a.operacao, 
          a.mov_codigo from mov_naoconsolid a, setor b, movimento c 
          WHERE a.mov_codigo = c.mov_codigo 
          AND (b.set_codigo = c.set_entrada or b.set_codigo = c.set_saida)
          and (c.set_entrada = $palavra_chave or c.set_saida = $palavra_chave)";

  //adicionados por renato para deixa apenas consolidar o movimento que é do tipo T => Transferencia		  
  //$sql .= " and c.mov_tipo = 'T' ";
  $sql .= "order by a.mov_data";

  /*$sql = "select c.mov_codigo, c.mov_data as data, c.mov_nr_nota
	from  setor b, movimento c 
          WHERE b.set_codigo = c.set_entrada 
          and (c.set_entrada = $palavra_chave or c.set_saida = $palavra_chave)";*/

  //echo $sql;
		  
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
		<td width=150 colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

     while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[data]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mov_nr_nota]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[desc_movimentacao]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[setor]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[nomesetorsolicit]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','consolidacao.php?acao=form_edit&mov_codigo='.$row[mov_codigo])."&nbsp;<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/imprimir.jpg' onclick='imprimi(".$row[mov_codigo].")'></td>
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
    var acao = \"document.location.href = 'consolidacao.php?acao=form_consolid&id_login=$id_login'\";
    setTimeout( acao, 1000 * 60 );
</script>
";
}

//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>

 if($acao=="form_edit") {
reglog($id_login,"Formulario de Edicao de Consolidacao");
//
//-> Formulario de edicao do cadastro SIMPLES
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
           <fieldset>
            <legend>Opções de Cadastro</legend>
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>
               <td width=79><a href=consolidacao.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
               <td width=79><a id='consolidcam' href='consolidacao.php?id_login=$id_login&acao=todos&mov_codigo=$mov_codigo'><img id='consolidall' src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/consolidar_todos_on.jpg border=0></a></td>
               <td>&nbsp;</td>
              </tr>
             </table>
           </fieldset>
          </td>
         </tr>
        </table><br>";

//
//-> Pegando as informcoes do banco pra mostrar no formulario
/*    $sql = "select pro_codigo, pro_nome, coalesce(ite_quantidade,0) as ite_quantidade,
		  coalesce(ite_qtde_solicitada,0) as ite_qtde_solicitada,
		  calcula_estoque(pro_codigo, codsetor, date(now())) as estoqueatual, ite_codigo
		  from mov_naoconsolid
		  where  mov_codigo = '$mov_codigo'
		  order by mov_data desc";
   echo $sql;*/

   $sql = "SELECT pro_codigo, 
				  pro_nome, 
				  COALESCE(ite_quantidade,0) as ite_quantidade, 
				  COALESCE(ite_qtde_solicitada,0) as ite_qtde_solicitada, 
				  (SELECT sum(sal_qtde) as estoqueatual
				     FROM saldo s
				    WHERE m.pro_codigo = s.pro_codigo
				      AND m.codsetor = s.set_codigo) as estoqueatual,
				  ite_codigo 
			 FROM mov_naoconsolid m
			WHERE mov_codigo = '$mov_codigo' 
			ORDER BY mov_data DESC";
   
  $sqlmovimento =  pg_query($sql);
  
  //echo "<pre>$sql</pre>";


  echo "<br><br><form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=mov_codigo value=$mov_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Consolidacao de Materiais</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
		<td width=160 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Produto</td>
		<td width=60 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Quantidade a Baixar</td>
		<td width=60 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Quantidade Solic.</td>
		<td width=60 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Estoque Atual</td>
		<td width=140 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
        while($row=pg_fetch_array($sqlmovimento)) {
	    $temp = true;
        $intquantidade = formata_valor0($row['ite_quantidade']);
        $intquantidadesol = formata_valor0($row['ite_qtde_solicitada']);
        $estoque = formata_valor0($row['estoqueatual']);
	  if ($estoque <= 0)
	    echo "<script>desativa();</script>";
        echo "
	       <tr>
	          <td align=left style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[pro_nome]</td>
	          <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$intquantidade</td>
	          <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$intquantidadesol</td>
	          <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9; font-weight:bold'>$estoque</td>
		  <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>";
          if (($row['ite_quantidade'] <= $row['estoqueatual'] && $row['ite_quantidade'] > 0)) {
               echo "
	       <a href=$PHP_SELF?id_login=$id_login&acao=edit&ite_codigo=$row[ite_codigo]&mov_codigo=$mov_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif border=0></a> &nbsp; &nbsp;";
           }
       echo "
	       <a href=$PHP_SELF?id_login=$id_login&acao=form_altera_item&ite_codigo=$row[ite_codigo]&mov_codigo=$mov_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a></td>
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
   $sql=pg_query("select ite_codigo, itens_movimento.pro_codigo, pro_nome,  ite_quantidade, ite_qtde_solicitada
                  from itens_movimento, produto
                  where itens_movimento.pro_codigo = produto.pro_codigo
                  and   mov_codigo = $mov_codigo
                  and   ite_codigo = $ite_codigo");

   $row = pg_fetch_array($sql);
   $intquantidade = formata_valor0($row['ite_quantidade']);
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
		<input type=hidden name=mov_codigo value=$mov_codigo>
		<input type=hidden name=ite_codigo value=$ite_codigo>
	      <tr>
		<td width=20>Produto:</td>
    		<td><input type=text name=pro_nome readonly class=box size=70 value='$row[pro_nome]'></td>
	      </tr>
	     <tr>
     		<td width=20>Quantidade:</td>
    		<td><input type=text name=ite_quantidade class=box size=20 value='$intquantidade'></td>
         </tr>
	     <tr>
	       <td width=95><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
	       <td width=79><a href=consolidacao.php?id_login=$id_login&mov_codigo=$mov_codigo&ite_codigo=$ite_codigo&acao=form_edit<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
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
//-> EDIT <--------------------------------------------------------->

 if($acao=="edit") {
  $sql = pg_query("update itens_movimento set " .
                  "ite_consolidado = 'S'," .
                  ($id_login ? "usr_codigo='$id_login'" : "usr_codigo=null") . "  " .
                  "where ite_codigo ='$ite_codigo'");
reglog($id_login,"Editando Consolidacao $ite_codigo");
//msg($id_login."&acao=form_edit&mov_codigo=".$mov_codigo,$acao,$sql);
if($sql)
{
  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                                <tr bgcolor=f9f9f9>
                                        <td align=center style='font-weight:bold'>Editado com sucesso</td>
                                </tr>
                        </table><br>";
                echo "<SCRIPT LANGUAGE=\"JavaScript\">
                        setTimeout(\"location='consolidacao.php'\", 2000);
                </SCRIPT>";
} else {
  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                                <tr bgcolor=F9f9f9>
                                        <td align=center style='font-weight:bold'>Editado com sucesso</td>
                                </tr>
                        </table><br>";
                echo "<SCRIPT LANGUAGE=\"JavaScript\">
                        setTimeout(\"location='consolidacao.php'\", 2000);
                </SCRIPT>";
}
}

 if($acao=="edit_item") {
  $sql = pg_query("update itens_movimento set " .
                  ($ite_quantidade ? "ite_quantidade='$ite_quantidade'" : "ite_quantidade=null") . ", " .
                  ($id_login ? "usr_codigo='$id_login'" : "usr_codigo=null") . "  " .
                  "where ite_codigo ='$ite_codigo'");
reglog($id_login,"Alterando quantidade do produto na Consolidacao $ite_codigo");
//msg($id_login."&acao=form_edit&mov_codigo=".$mov_codigo,$acao,$sql);
if($sql)
{
  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                                <tr bgcolor=f9f9f9>
                                        <td align=center style='font-weight:bold'>Editado com sucesso</td>
                                </tr>
                        </table><br>";
                echo "<SCRIPT LANGUAGE=\"JavaScript\">
                        setTimeout(\"location='consolidacao.php'\", 2000);
                </SCRIPT>";
} else {
  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                                <tr bgcolor=F9f9f9>
                                        <td align=center style='font-weight:bold'>Editado com sucesso</td>
                                </tr>
                        </table><br>";
                echo "<SCRIPT LANGUAGE=\"JavaScript\">
                        setTimeout(\"location='consolidacao.php'\", 2000);
                </SCRIPT>";
}
}

if($acao=="todos") {
  $sqlmovimento =  pg_query("SELECT *
  							   FROM (SELECT m.pro_codigo, 
		  									m.pro_nome, 
		  									m.ite_quantidade, 
		  									m.ite_qtde_solicitada,
		  									(SELECT sum(sal_qtde) as estoqueatual
											   FROM saldo s
											  WHERE m.pro_codigo = s.pro_codigo
											    AND m.codsetor = s.set_codigo) as estoqueatual, 
		                          			m.ite_codigo
		                    		   FROM mov_naoconsolid m
		                    		  WHERE m.mov_codigo = '$mov_codigo') as x
                    			AND x.estoqueatual > 0");

  while($row=pg_fetch_array($sqlmovimento)) {
  	$sql = pg_query("UPDATE itens_movimento 
  						SET ite_consolidado = 'S'," .
              ($id_login ? "usr_codigo='$id_login'" : "usr_codigo=null") . "  " .
                     "WHERE ite_codigo ='$row[ite_codigo]'");
        reglog($id_login,"Consolidando Total Codigo $ite_codigo");
  }

//msg($id_login."&acao=form_edit&mov_codigo=".$mov_codigo,$acao,$sql);
if($sql)
{
  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                                <tr bgcolor=f9f9f9 style='font-weight:bold'>
                                        <td align=center>Editado com sucesso</td>
                                </tr>
                        </table><br>";
                echo "<SCRIPT LANGUAGE=\"JavaScript\">
                        setTimeout(\"location='consolidacao.php'\", 2000);
                </SCRIPT>";
} else {
  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                                <tr bgcolor=F9f9f9 style='font-weight:bold'>
                                        <td align=center>Editado com sucesso</td>
                                </tr>
                        </table><br>";
                echo "<SCRIPT LANGUAGE=\"JavaScript\">
                        setTimeout(\"location='consolidacao.php'\", 2000);
                </SCRIPT>";
}
}
//
//-> DEL <---------------------------------------------------------->

 if($acao=="del") {
  $sql = pg_query("delete from consolidacao where mov_codigo='$mov_codigo'");
reglog($id_login,"Excluindo Consolidacao $mov_codigo");
msg($id_login,$acao,$sql);
}

?>
</fieldset>
