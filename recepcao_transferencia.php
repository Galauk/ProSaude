<?php
	session_start(); 
?>
<script type="text/javascript">

function validaQtde(form,set,id_login,req_codigo,ireq_codigo,pro_codigo) {	

	qtde2 = document.getElementById('ireq_quantidade').value;
	

	//*****************************************
	// Soma Todas as quantidades do estoque
		tudo = new Number(0);
		todos=document.getElementsByTagName('input');
		var i = 0;
		new Number(contagem = 0) ;
		for(x = 0; x < todos.length; x++){
			if(todos[x].checked){
				var teste = todos[x].value;
				recebeSplit = teste.split('|');
				tudo = tudo + new Number(recebeSplit[0]);				
				contagem = new Number(contagem) + 1;
				
										
			}
		}
		var array = new Array(todos.length);
		for(x = 0; x < todos.length; x++){
			if(todos[x].checked){
				var teste = todos[x].value;
				recebeSplit = teste.split('|');							
				array[i] = new Number(recebeSplit[0]);

				i++;	
				lote = recebeSplit[1];
				validade = recebeSplit[2];
				
							
			}
		
		}
		
	if (tudo == 0) {
		alert('Voce deve selecionar pelo menos um lote para ser dispensado!');
		return false;
	}
	qtde = new Number(document.getElementById('qtde').value);
	var cont = 0;
	for(x = 0; x < todos.length; x++){
		if(todos[x].checked){
			var teste = todos[x].value;
			recebeSplit = teste.split('|');
			qtdePrimeiroLote = new Number(recebeSplit[0]);						
			break;
			
		}
	}

	if ((qtdePrimeiroLote > qtde2) && (contagem > 1)){
		alert('Apenas o primeiro lote ja suficiente, voce nao deve selecionar mais.');
		return false;
	}
	
	if (qtde2 > tudo){
		alert('Quantidade selecionada insuficiente. Voce deve selecionar pelo menos mais um lote para ser dispensado!');
		return false;
	}
	var resultado = (tudo - qtde);
	i=0;
	resp = new Array(contagem);
	for(x = 0; x < todos.length; x++){
		if(todos[x].checked){
			var teste = todos[x].value;
			
			resp[i] = teste;
			recebeSplit = teste.split('|');							
			array[i] = new Number(recebeSplit[0]);
			i++;
		
		 }
	}
	window.location ="recepcao_transferencia.php?acao=edit_item&resp="+resp+"&set_codigo="+set+"&id_login="+id_login+"&req_codigo="+req_codigo+"&ireq_codigo="+ireq_codigo+"&contagem="+contagem+"&pro_codigo="+pro_codigo+"&ireq_quantidade="+document.getElementById('ireq_quantidade').value;;
	
	var qtde = document.getElementById("qtde").value;


}
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
  url = 'relatorio/ReqExibir.php?req_nr_nota='+cod;
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

reglog($id_login,"Acessando Recepcao Medicamentos");
echo "<fieldset><legend>RECEPCAO DA REQUISICAO DE MEDICAMENTOS</legend>";

if(empty($acao) || ($acao == 'form_consolid'))
{
//echo "<pre>".print_r($_GET,true)."</pre>";
	

//-> Botoesec
	echo "
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	  <tr>
		<td>
		  <fieldset>
			<legend>Opções</legend>
			<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
			  <tr>
				<form method=post action='recepcao_transferencia.php?id_login=$id_login'>
				<input type=hidden name=acao value=busca>
				<input type=hidden name=id_login value=$id_login>
				<td width=180 align=right>Buscar </td>
				<td width=90><select name=palavra_chave class='box'>";
                $uni[0]=="" ? $decisao = "" : $decisao = " AND setor.uni_codigo = ".$uni[0];
                $sql = pg_query("select set_codigo,set_nome from setor where set_estoque = 'S' and set_distribuidor = 'S' $decisao order by set_nome");
                while ($temp = pg_fetch_array($sql))
                {
                  echo "<option value=\"$temp[set_codigo]\">$temp[set_nome]</option>";
                }
			  echo "
				</select></td>
				<td>".ChmodBtn($id_login,'procurar','recepcao_transferencia.php')."</td></form>
				<td width=79><a href=movimentacao.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
			  </tr>
			</table>
		  </fieldset>
		</td>
	  </tr>
	</table><br>";

//
//-> Listando

  if (chmodbtn($id_login,"listar_if","recepcao_transferencia.php"))
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
    
    
	 $sql="SELECT DISTINCT a.data,
	 					   a.req_data, 
	 					   a.req_nr_nota, 
	 					   a.setor, 
	 					   a.codsetor, 
						   a.codsetorsolicit, 
						   a.nomesetorsolicit, 
						   a.desc_movimentacao, 
						   a.req_tipo, 
						   a.operacao, 
						   a.req_codigo, 
						   req_finalizado 
					  FROM reqtransf_naoconsolid a, 
				 		   setor b, 
				 		   requisicao c 
					 WHERE a.req_codigo = c.req_codigo"; 
		//echo $sql."OKAAKOOKA";
		/*AND b.set_codigo = c.set_saida";AND c.req_finalizado = 'S'
		and (c.set_entrada = $t[set_codigo] or c.set_saida = $t[set_codigo])";
	    //$sql .= " and c.req_tipo = 'T' ";*/
	//$sql .= " order by a.req_data";

    
	    $sql2 = pg_query($sql);
	    
	 while($row=pg_fetch_array($sql2)) {
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
       ChmodBtn($id_login,'editar','recepcao_transferencia.php?acao=form_edit&req_codigo='.$row[req_codigo].'&set_codigo='.$row[codsetor])."&nbsp;
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

	reglog($id_login,"Buscando em Recepcao Medicamentos $palavra_chave");
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
	       <form method=post action='recepcao_transferencia.php?id_login=$id_login'>
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
	       <td>".ChmodBtn($id_login,'procurar','recepcao_transferencia.php')."</td></form>
	       <td width=79><a href=movimentacao.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

  $sql="SELECT distinct a.data, 
  				a.req_data, 
  				a.req_nr_nota, 
  				a.setor, 
  				a.codsetor, 
          		a.codsetorsolicit, 
          		a.nomesetorsolicit, 
          		a.desc_movimentacao, 
          		a.req_tipo, 
          		a.operacao, 
          		a.req_codigo, 
          		req_finalizado 
          	FROM reqtransf_naoconsolid a, 
          		setor b, 
          		requisicao c 
          WHERE a.req_codigo = c.req_codigo 
          AND (b.set_codigo = c.set_entrada or b.set_codigo = c.set_saida)
          AND c.req_finalizado = 'S'
          AND (c.set_entrada = $palavra_chave or c.set_saida = $palavra_chave)";

  //adicionados por renato para deixa apenas consolidar o movimento que é do tipo T => Transferencia		  
  //$sql .= " and c.req_tipo = 'T' ";
  $sql .= "order by a.req_data";

  /*$sql = "select c.req_codigo, c.req_data as data, c.req_nr_nota
	from  setor b, requisicao c 
          WHERE b.set_codigo = c.set_entrada 
          and (c.set_entrada = $palavra_chave or c.set_saida = $palavra_chave)";*/

  //echo $sql;
  $exeSql = pg_query($sql);
  $resExe = pg_fetch_array($exeSql);

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
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','recepcao_transferencia.php?acao=form_edit&req_codigo='.$row[req_codigo].'&set_codigo='.$resExe['codsetor'])."&nbsp;<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/imprimir.jpg' onclick='imprimi(".$row[req_codigo].")'></td>
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
    var acao = \"document.location.href = 'recepcao_transferencia.php?acao=form_consolid&id_login=$id_login'\";
    setTimeout( acao, 1000 * 60 );
</script>
";
}

//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>

 if($acao=="form_edit") { 
	//echo"<pre>".print_r($_REQUEST,true)."</pre>";	exit;
	$buscaDados = "SELECT * FROM reqtransf_naoconsolid 
 					WHERE 
 				  req_codigo = '$req_codigo' 
 				  AND 
 				  codsetor = '$_GET[set_codigo]'";
 	$exeBusca = pg_query($buscaDados);
 	$resExeBusca = pg_fetch_array($exeBusca);
 	$req_data = $resExeBusca['req_data'];
 	$tipo = $resExeBusca['tipomovim'];
 	$cod_setor_solicitante = $resExeBusca['codsetorsolicit'];
 	$cod_setor = $resExeBusca['codsetor'];
 	$usr_codigo = $_GET['id_login'];
 	$data = $resExeBusca['data'];
 	$req_nr_nota = $resExeBusca['req_nr_nota'];
 	$lote = $_GET['lote'];
 	$validade = $_GET['validade'];
 	$quantidade = $_GET['quantidade'];
 	/* $insert ="INSERT INTO 
				movimento(
				mov_codigo,
				mov_data,
				mov_tipo,
				set_entrada,
				set_saida,
				usr_codigo,
				mov_data_inclusao,				
				mov_entrada,
				mov_saida,
				mov_nr_nota)
				VALUES(
				$mov_codigo,
				'$req_data',
				'$tipo',
				$cod_setor_solicitante,
				$cod_setor,
				$usr_codigo,
				'$data',
				'T',
				'T',
				$req_nr_nota)";		
	$exeInsert = pg_query($insert);*/
reglog($id_login,"Formulario de Edicao de Recepcao Medicamentos");
//
//-> Formulario de edicao do cadastro SIMPLES
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
           <fieldset>
            <legend>Opções de Cadastro</legend>
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>
               <td width=79><a href=recepcao_transferencia.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
               <td width=79><a id='consolidcam' href='recepcao_transferencia.php?id_login=$id_login&acao=todos&req_codigo=$req_codigo&req_data=$req_data&tipo=$tipo&cod_setor_solicitante=$cod_setor_solicitante&cod_setor=$cod_setor&data=$data&req_nr_nota=$req_nr_nota&lote=$lote&validade=$validade&qtde=$qtde'><img id='consolidall' src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/recepcao_todos_materiais_on.jpg border=0></a></td>
               <td> <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/imprimir.jpg' onclick='imprimi($req_codigo)'></td>
              </tr>
             </table>
           </fieldset>
          </td>
         </tr>
        </table><br>";

//
//-> Pegando as informcoes do banco pra mostrar no formulario

/*$sql = "select s.pro_codigo, 
			pro_nome, 
			coalesce(ireq_quantidade,0) as ireq_quantidade, 
			coalesce(ireq_qtde_solicitada,0) as ireq_qtde_solicitada, 
			sum(sal_qtde) AS sal_qtde , 
			ireq_codigo, 
			desc_status 
		from 
			reqtransf_naoconsolid AS req 
			join saldo as s 
			ON req.pro_codigo = s.pro_codigo 
			  where req_codigo = '$req_codigo'				
				AND s.set_codigo = '$_GET[set_codigo]'
			
		group by s.pro_codigo, 
			pro_nome, 
			ireq_quantidade, 
			ireq_qtde_solicitada, 
			ireq_codigo, 
			desc_status order by pro_nome";*/
$sql ="select   p.pro_codigo,
	pro_nome, coalesce(ireq_quantidade,0) as ireq_quantidade, 
	coalesce(ireq_qtde_solicitada,0) as ireq_qtde_solicitada, 
	sum(sal_qtde) AS sal_qtde , 
	ireq_codigo, 
	ireq_consolidado,
	ireq_lote,
	ireq_validade 
 from itens_requisicao AS ite 
  JOIN requisicao AS req
    ON req.req_codigo=ite.req_codigo
  join saldo as s 
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
		 ireq_consolidado,
		 ireq_lote,
		 ireq_validade
		 order by pro_nome";

  $sqlmovimento =  pg_query($sql);
  
// echo "<pre>$sql</pre>";

  echo "<br><br><form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=req_codigo value=$req_codigo>";
	//echo"<pre>".print_r($_REQUEST)."</pre>";
		
 	
  echo"<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Recepcao de Medicamentos </legend>
	     <table class='lista' align=center cellspacing=3 cellpadding=0 border=0>
	     <tr>
			<th>Produto</th>
			<th>Qtde. a Baixar</th>
			<th>Qtde.  Solic.</th>
			<th>Estoque Atual</th>
			<th>Lote</th>
			<th>Validade</th>	
			<th>&nbsp;</th>
		</tr>";
        while($row=pg_fetch_array($sqlmovimento)) {      
	        $temp = true;
	        $intquantidade = formata_valor0($row['ireq_quantidade']);
	        $intquantidadesol = formata_valor0($row['ireq_qtde_solicitada']);
	        $estoque = formata_valor0($row['sal_qtde']);
	        $totdisp = formata_valor0($row['qtdedisp']);
			$totliq = $estoque - $row['qtdedisp'];
			
			$pegandoLote = "SELECT sal_qtde,*
							  FROM itens_requisicao ir
							  JOIN requisicao r
							    ON ir.req_codigo = r.req_codigo
							  JOIN saldo as s
							    ON s.sal_lote = ir.ireq_lote
							    AND s.pro_codigo = ir.pro_codigo
							 WHERE r.req_codigo = '$req_codigo'
							   AND ir.pro_codigo = $row[pro_codigo]
							   AND r.set_saida = '$_GET[set_codigo]'";
			
			$exepegandoLote = pg_query($pegandoLote);
			$resPegandoLote = pg_fetch_array($exepegandoLote);
			$loteCerto = $resPegandoLote['ireq_lote'];
			$validadeCerta = $resPegandoLote['ireq_validade'];
			$quantidadeCerta = $resPegandoLote['sal_qtde'];
			
			$lotevalid = pg_fetch_array(pg_query("select ireq_lote, to_char(ireq_validade, 'dd/mm/yyyy')
		                                      from itens_requisicao
						      where ireq_codigo = $row[5]"));
		

	        $produto = $row[1];					      
		  if ( ($estoque <= 0) and ($estoque < $row['ireq_quantidade']) )
		    echo "<script>desativa();</script>";
			//<td  align=left style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$produto<br><b> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Lote:</b> &nbsp&nbsp&nbsp $loteCerto<br><b>Validade:</b> &nbsp&nbsp&nbsp&nbsp $validadeCerta</td>
		    echo "
		       <tr>
		          <td align=left>$produto<br><b></td>
		          <td align=center>$intquantidade</td>
		          <td align=center>$intquantidadesol</td>
		          <td align=center style='font-weight:bold'>$estoque</td>
		          <td align=center>$row[ireq_lote]</td>
		          <td align=center>$row[ireq_validade]</td>
			      <td>";
	          if (($row['ireq_quantidade'] <= $row['estoqueatual'] && $row['ireq_quantidade'] > 0)) {
	               echo "
		       <a href=$PHP_SELF?id_login=$id_login&acao=edit&ireq_codigo=$row[ireq_codigo]&req_codigo=$req_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/recepcao_materiais_on.jpg border=0></a> ";
	           }
				
				if($intquantidade == $intquantidadesol)
				{
	           echo "
		     	  <a href=$PHP_SELF?id_login=$id_login&acao=form_altera_item&ireq_codigo=$row[ireq_codigo]&req_codigo=$req_codigo&ireq_qtde_solicitada=$intquantidadesol&set_codigo=$_GET[set_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a>";
				}
		       echo" <a href=$PHP_SELF?id_login=$id_login&acao=cancela&ireq_codigo=$row[ireq_codigo]&req_codigo=$req_codigo&ireq_qtde_solicitada=$intquantidadesol&set_codigo=$_GET[set_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
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
	//echo "<pre>".print_r($_REQUEST,TRUE)."</pre>";
	$qtde = $_GET['ireq_qtde_solicitada'];	
	$sql = pg_query("select ireq_codigo, itens_requisicao.pro_codigo, pro_nome,  ireq_quantidade, ireq_qtde_solicitada
                  from itens_requisicao, produto
                  where itens_requisicao.pro_codigo = produto.pro_codigo
                  and   req_codigo = $req_codigo
                  and   ireq_codigo = $ireq_codigo");
   $row = pg_fetch_array($sql);
   $intquantidade = formata_valor0($row['ireq_quantidade']);

	$buscaValidade = "SELECT to_char(sal_validade,'dd/mm/yyyy') as datavalidade,* 
						FROM saldo
					   WHERE pro_codigo = {$row['pro_codigo']} 
					   	 AND sal_validade > CURRENT_DATE
						 AND sal_qtde > 0
						 AND set_codigo= $_GET[set_codigo]
			 order by sal_validade";	
		
	$exeBusca = pg_query($buscaValidade);  
   echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Alteracao do Item </legend>
	     <table width=600 align=center cellspacing=3 cellpadding=0 border=0>";
	      // <form name=altera_item method=post action=$PHP_SELF?set_codigo=$_GET[set_codigo]>
		//echo"<pre>".print_r($_REQUEST)."</pre>";
   echo"<input type=hidden name=action value=edit>
   		<input type='hidden' id='qtde' name='qtde' value='$qtde'>
		<input type=hidden name=acao value=edit_item>
		<input type=hidden name=id_login value=$id_login>
		<input type=hidden name=req_codigo value=$req_codigo>
		<input type=hidden name=ireq_codigo value=$ireq_codigo>
		<input type=hidden name=lote value=$lote>
	      <tr>
		<td width=20>Produto:</td>
    		<td><input type=text name=pro_nome readonly class=box size=90 value='$row[pro_nome]'></td>
	      </tr>
	     <tr>
     		<td width=20>Quantidade:</td>
    		<td><input type=text id=ireq_quantidade name=ireq_quantidade class=box size=20 value='$intquantidade'></td>
         </tr>
         <tr>
     		<td width=20>lote:</td>";
     		echo"<td>";
     		if(pg_num_rows($exeBusca) == NULL)
			{
				echo"Não há Medicamentos no Estoque";
			}else{
   			while($resBusca = pg_fetch_array($exeBusca)){
			$lote = $resBusca["sal_lote"];
			$validade = $resBusca["datavalidade"];
			$quantidade = $resBusca["sal_qtde"];
   			
     		 echo"
				<label>
					<input type='checkbox' name='loteVal' value='$quantidade|$lote|$validade'>				
						<span><strong>Lote:</strong> $lote  &nbsp &nbsp </span>
						<span><strong>Validade:</strong> $validade &nbsp &nbsp </span>
						<span><strong>Quantidade:</strong> $quantidade &nbsp &nbsp</span>
				</label><br>";
			
			}
				
			}	
    		echo"<td>
    	</tr>
	     <tr>
	       <td width=79><a href=recepcao_transferencia.php?id_login=$id_login&req_codigo=$req_codigo&ireq_codigo=$ireq_codigo&acao=form_edit&set_codigo=$_GET[set_codigo]>
	       <input type=image  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td width=95><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg onClick='return validaQtde(this,$_GET[set_codigo],$id_login,$req_codigo,$ireq_codigo,$row[pro_codigo])'></td>
	      </tr>
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
  $sql = pg_query("update itens_requisicao set " .
                  "ireq_consolidado = 'D'," .
                  ($id_login ? "usr_codigo='$id_login'" : "usr_codigo=null") . "  " .
                  "where ireq_codigo ='$ireq_codigo'");
reglog($id_login,"Editando Recepcao Medicamentos $ireq_codigo");
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
                        setTimeout(\"location='recepcao_transferencia.php'\", 2000);
                </SCRIPT>";
} else {
  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                                <tr bgcolor=F9f9f9>
                                        <td align=center style='font-weight:bold'>Houve um erro ao editar, tente novamente.</td>
                                </tr>
                        </table><br>";
                echo "<SCRIPT LANGUAGE=\"JavaScript\">
                        setTimeout(\"location='recepcao_transferencia.php'\", 2000);
                </SCRIPT>";
}
}

 if($acao=="cancela") {
//echo "<pre>".print_r($_GET,TRUE)."</pre>";
  $sql = pg_query("update itens_requisicao set " .
                  "ireq_consolidado = 'C'," .
                  ($id_login ? "usr_codigo='$id_login'" : "usr_codigo=null") . "  " .
                  ",ireq_lote='SEM_LOTE'".
                  ",ireq_validade='01/01/2200'".
                  ",ireq_quantidade = 0".
                  "where ireq_codigo ='$ireq_codigo'");
              
reglog($id_login,"Editando Recepcao Medicamentos $ireq_codigo");
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
                        setTimeout(\"location='recepcao_transferencia.php?acao=form_edit&req_codigo=$req_codigo&id_login=$id_login&set_codigo=$set_codigo'\", 2000);
                </SCRIPT>";
} else {
  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                                <tr bgcolor=F9f9f9>
                                        <td align=center style='font-weight:bold'>Editado com sucesso</td>
                                </tr>
                        </table><br>";
                echo "<SCRIPT LANGUAGE=\"JavaScript\">
                        setTimeout(\"location='recepcao_transferencia.php?acao=form_edit&req_codigo=$req_codigo&id_login=$id_login&set_codigo=$set_codigo'\", 2000);
                </SCRIPT>";
}
} //acao==cancela


 if($acao=="edit_item") {
 	//echo"<pre>".print_r($_GET,true)."</pre>"; 
 	 $loteValidadeQuantidade = explode("|", $resp);
	 		 $quantidade = $loteValidadeQuantidade[0];
	 		 $lote = $loteValidadeQuantidade[1];
	 		 $validade= $loteValidadeQuantidade[2];
 	//echo $_GET[resp];
 	if($contagem == 1)
 	{
	 	pg_query("update itens_requisicao set " .
						($ireq_quantidade ? "ireq_quantidade='$ireq_quantidade'" : "ireq_quantidade=null") . ", " .
						($id_login ? "usr_codigo='$id_login'" : "usr_codigo=null") . " ," .
						"ireq_lote = '$lote'".","
						."ireq_validade = '$validade'"
						."where ireq_codigo ='$ireq_codigo'");
					
 	}else{			
	 	$resposta = explode(",", $_GET[resp]);
	    
	 	for($i=0; $i<$resposta[$i].length; $i++)
	 	{		
	 		//echo $resposta[$i] ."<br>";
	 		 $loteValidadeQuantidade = explode("|", $resposta[$i]);
	 		 $quantidade = $loteValidadeQuantidade[0];
	 		 $lote = $loteValidadeQuantidade[1];
	 		 $validade= $loteValidadeQuantidade[2];
	 		// echo $quantidade."<br>";
	 		 
	 		/* echo $lote."<br>";
	 		 echo $validade."<br>";*/
	 		$solicitada = $ireq_quantidade;
	 		if ($quantidade < $ireq_quantidade) {
	 		 	
	 		 	$q[$i] = $quantidade;
	 		 	$ireq_quantidade -= $quantidade; 		 	
	 		}
	 	 	else if ($quantidade >= $ireq_quantidade) {
	 	 	 	$q[$i] = $ireq_quantidade;
	 	 	}	 	 	
			$v[$i] = $validade;
			$l[$i] = $lote;
	 	}
	 	$update = "update itens_requisicao set " .
                  ($q[0] ? "ireq_quantidade='$q[0]'" : "ireq_quantidade=null") . ", " .
                  ($id_login ? "usr_codigo='$id_login'" : "usr_codigo=null") . "  " .",ireq_lote = '$l[0]'".",ireq_validade = '$v[0]'".
                  "where ireq_codigo ='$ireq_codigo'";
 	   // echo $update;
        $sql = pg_query($update);
	for($i=1; $i<count($q); $i++){		
	    $insertUmNovoItem = "insert into itens_requisicao(pro_codigo,
																ireq_quantidade,
																ireq_qtde_solicitada,
																ireq_vlrunit,
																ireq_lote,
																ireq_validade,
																req_codigo,																
																ireq_consolidado) 
														VALUES (
																$pro_codigo, 
																$q[$i], 
																0, 
																0,
																'$l[$i]',
																'$v[$i]', 
																'$req_codigo', 				   												 
				   												'R')";
	 	 //echo $insertUmNovoItem;
		 $exeinsertUmNovoItem = pg_query($insertUmNovoItem);
	 	} 
 	}
 
       
reglog($id_login,"Alterando quantidade do produto na Recepcao Medicamentos $ireq_codigo");
if($sql)
{
  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                                <tr bgcolor=f9f9f9>
                                   <td align=center style='font-weight:bold'>Editado com sucesso</td>
                                </tr>
                        </table><br>";
                echo "<SCRIPT LANGUAGE=\"JavaScript\">
                      setTimeout(\"location='recepcao_transferencia.php?id_login=$id_login&req_codigo=$req_codigo&acao=form_edit&set_codigo=$_GET[set_codigo]&lote=$lote&validade=$validade&qtde=$qtde&pro_codigo=$pro_codigo'\", 2000);
                </SCRIPT>";
} else {
  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                                <tr bgcolor=F9f9f9>
                                        <td align=center style='font-weight:bold'>Editado com sucesso</td>
                                </tr>
                        </table><br>";
                echo "<SCRIPT LANGUAGE=\"JavaScript\">
                        setTimeout(\"location='recepcao_transferencia.php?id_login=$id_login&req_codigo=$req_codigo&acao=form_edit&set_codigo=$_GET[set_codigo]&lote=$lote&validade=$validade&qtde=$qtde&pro_codigo=$pro_codigo&resp=$resp'\", 2000);
                      </SCRIPT>";
}
//recepcao_transferencia.php?id_login=$id_login'
}

if($acao=="todos") {
	//echo"<pre>".print_r($_REQUEST,true)."</pre>";
	
	$selectNexval = "select nextval('seq_mov_codigo') as mov_codigo";
	$exeSelectNexval = pg_query($selectNexval);
	$resexeSelectNexval = pg_fetch_array($exeSelectNexval);
 	$mov_codigo = $resexeSelectNexval['mov_codigo'];
	
 	$exeBusca = pg_query($buscaValidade);
	$resBusca = pg_fetch_array($exeBusca);
	$ite_lote = $resBusca["sal_lote"];
	$ite_validade = $resBusca["datavalidade"];
	$quantidade = $resBusca["sal_qtde"]; 
	
	$sql = "select s.pro_codigo,			
			pro_nome, 
			coalesce(ireq_quantidade,0) as ireq_quantidade, 
			coalesce(ireq_qtde_solicitada,0) as ireq_qtde_solicitada, 
			sum(sal_qtde) AS sal_qtde , 
			ireq_codigo, 
			desc_status 
		from 
			reqtransf_naoconsolid AS req 
			join saldo as s 
			ON req.pro_codigo = s.pro_codigo 
			  where req_codigo = '$req_codigo' 
				AND sal_qtde > 0 
				AND s.set_codigo = '$cod_setor'			
		group by s.pro_codigo, 
			pro_nome, 
			ireq_quantidade, 
			ireq_qtde_solicitada, 
			ireq_codigo, 
			desc_status order by pro_nome";
	$exeSql = pg_query($sql);
	$resSql = pg_fetch_array($exeSql);
	
	$pro_codigo = $resSql['pro_codigo'];
	$ireq_quantidade = $resSql['ireq_quantidade'];
	$buscaValidade = "SELECT to_char(sal_validade,'dd/mm/yyyy') as datavalidade,* FROM saldo
	 WHERE 
		pro_codigo = $pro_codigo
	AND
		sal_validade > CURRENT_DATE
	AND
			sal_qtde > 0
		 order by sal_validade";

//$sql3 ="select * from itens_requisicao where  req_codigo = $req_codigo";
$sql3 ="SELECT *  FROM itens_requisicao ir
				  JOIN requisicao r
				    ON r.req_codigo = ir.req_codigo
				 WHERE ir.req_codigo = $req_codigo
				   ";

$exe_sql3 = pg_query($sql3);
$res_exe = pg_fetch_array($exe_sql3);

if($res_exe[ireq_lote]=="" && $res_exe[ireq_quantidade != 0])
	{
		
	   echo "<SCRIPT LANGUAGE=\"JavaScript\">
	   			alert('Lote não informado')		  				
             	setTimeout(\"location='recepcao_transferencia.php?id_login=$id_login&req_codigo=$req_codigo&acao=form_edit&set_codigo=$cod_setor&lote=$lote&validade=$validade&qtde=$qtde&pro_codigo=$pro_codigo'\",0);
             </SCRIPT>";
		exit();
	}
	
   $insert ="INSERT INTO 
				movimento(
				mov_codigo,
				mov_data,
				mov_tipo,
				set_entrada,
				set_saida,
				usr_codigo,
				mov_data_inclusao,				
				mov_entrada,
				mov_saida,
				mov_nr_nota,
				req_codigo)
				VALUES(
				$mov_codigo,
				'$req_data',
				'$tipo',
				$cod_setor_solicitante,
				$cod_setor,
				$id_login,
				'$data',
				'T',
				'T',
				$req_nr_nota,
				$req_codigo)";	
	$exeInsert = pg_query($insert);
	
	$sqlmovimento =  pg_query(" select  p.pro_codigo,
										pro_nome, coalesce(ireq_quantidade,0) as ireq_quantidade, 
										coalesce(ireq_qtde_solicitada,0) as ireq_qtde_solicitada, 
										sum(sal_qtde) AS sal_qtde , 
										ireq_codigo, 
										ireq_consolidado,
										ireq_lote,
										ireq_validade,
										sal_dose_lote 
								 from itens_requisicao AS ite 
								 JOIN requisicao AS req
								   ON req.req_codigo=ite.req_codigo
								 join saldo as s 
								   ON ite.pro_codigo = s.pro_codigo
								  AND s.set_codigo=req.set_saida
								  AND s.sal_lote=ireq_lote
								 join produto as p
								   ON ite.pro_codigo = p.pro_codigo
								where req.req_codigo = '$req_codigo' 
								group by p.pro_codigo,
										 pro_nome, 
										 ireq_quantidade, 
										 ireq_qtde_solicitada, 
										 ireq_codigo, 
										 ireq_consolidado,
										 ireq_lote, 
										 ireq_validade,
										 sal_dose_lote 
								 order by pro_nome
										 ");
  while($row=pg_fetch_array($sqlmovimento)) {
         $sql = pg_query("update itens_requisicao set " .
                  "ireq_consolidado = 'D'," .
                  ($id_login ? "usr_codigo='$id_login'" : "usr_codigo=null") . "  " .
                  "where ireq_codigo ='$row[ireq_codigo]'");
        reglog($id_login,"Consolidando Total Codigo $ireq_codigo");
        
  $insertItem = "INSERT INTO itens_movimento(				
				mov_codigo,
				pro_codigo,
				ite_lote,
				ite_validade,			
				ite_quantidade,			
				ite_dose				
				)VALUES(				
				$mov_codigo,
				$row[pro_codigo],
				'$row[ireq_lote]',
				'$row[ireq_validade]',				
				$row[ireq_quantidade],
				{$row['sal_dose_lote']}							
				)";
	$exeinsertItem = pg_query($insertItem) or die(pg_last_error()."<pre>$insertItem"); 
	
  }

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
                        setTimeout(\"location='recepcao_transferencia.php'\", 2000);
                </SCRIPT>";
} else {
  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                                <tr bgcolor=F9f9f9 style='font-weight:bold'>
                                        <td align=center>Editado com sucesso</td>
                                </tr>
                        </table><br>";
                echo "<SCRIPT LANGUAGE=\"JavaScript\">
                        setTimeout(\"location='recepcao_transferencia.php'\", 2000);
                </SCRIPT>";
}
}
//
//-> DEL <---------------------------------------------------------->

?>
</fieldset>
