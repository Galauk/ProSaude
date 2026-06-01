<head>
	<script language=javascript>
		function imprimir() {
			window.print();
		}
	</script>

	<style type="text/css">
		.bordas {
			border:1px solid;
		}
	</style>
</head>


<?php
session_start();
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
echo "<link href='".$_SESSION[linkroot].$_SESSION[modulo]."estilo.css' rel='stylesheet' type='text/css'>";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
global $movimento_tipo;
//----------------  Dados Recebidos  ---------------->

$nr_movimento = $req_nr_nota;
//$titulo="Exibição de Movimentação";    //       NOME DO RELATÓRIO

$sql =  "SELECT r.req_codigo, 
			   to_char(r.req_data, 'DD/MM/YY') as data, 
			   case when req_tipo = 'E' then 'Entrada'
	         		when req_tipo = 'S' then 'Requisicao de Saida'
	         		when req_tipo = 'T' then 'Transferencia'
        	   end as tipoentrada ,
        	   r.for_codigo,
        	   r.usu_codigo, 
        	   r.req_desconto, 
        	   r.req_observacao, 
        	   r.cond_codigo, 
        	   r.ate_codigo, 
        	   r.set_entrada, 
        	   r.set_saida, 
        	   r.req_nr_nota, 
        	   to_char(r.req_dt_nota, 'DD/MM/YY'), 
        	   r.usr_codigo, 
        	   to_char(r.req_total_nota, '999,999,999D99'),  
        	   to_char(r.req_data_inclusao, 'DD/MM/YY'), 
        	   case when req_entrada = 'E' then 'Nota Fiscal'
        	   end as tipoentrada, 
        	   case when req_saida = 'S' then 'Requisicao de Saida de Consumo'
        	   end as tiposaida 
          FROM requisicao r
         WHERE r.req_codigo = $req_nr_nota";
$query = pg_query($sql);

while($row = pg_fetch_array($query)) {
	$pessoa = ' ';
	$req_codigo        = $row['req_codigo'];
	$req_data          = $row['data'];
	$req_tipo          = $row['tipoentrada'];
	$usu_codigo        = $row['usu_codigo'];
  if ($usu_codigo != null)
	{
		$sqlpessoa = "SELECT u.usu_nome 
						FROM usuario u 
					   WHERE u.usu_codigo = $usu_codigo";
		$query=pg_query($sqlpessoa);
		while($row4=pg_fetch_array($query)) {
			$pessoa='Usuario: ' . $row4['usu_nome'];
		}
	}
	 $set_saida         = $row[10];

              if ($set_saida != null) {
                  $sqlsetor = "SELECT s.set_nome  
                  				 FROM setor s 
                  				WHERE s.set_codigo = $set_saida";
    		      $query=pg_query($sqlsetor);
    		      while($row10=pg_fetch_array($query)) {
    		            $pessoa=$row10['set_nome'];
    		      }
    		  }


          $req_desconto      = $row['req_desconto'];
          $req_observacao    = $row['req_observacao'];
          $cond_codigo       = $row['cond_codigo'];

              if ($cond_codigo != null) {
                  $sqlcond = "SELECT c.cond_descricao  
                  				FROM condpagto c 
                  			   WHERE c.cond_codigo = $cond_codigo";
    		      $query=pg_query($sqlcond);
    		      while($row7=pg_fetch_array($query)) {
    		            $condicao=$row7['cond_descricao'];
    		      }
    		  }


          $ate_codigo        = $row['ate_codigo'];
          $set_entrada       = $row['set_entrada'];

              if ($set_entrada != null) {
                  $sqlsetor = "SELECT s.set_nome
                  				 FROM setor s 
                  				WHERE s.set_codigo = $set_entrada";
    		      $query=pg_query($sqlsetor);
    		      while($row9=pg_fetch_array($query)) {
    		            $setor=$row9['set_nome'];
    		      }
    		  }


          $req_nr_nota       = $row['req_nr_nota'];
          $req_dt_nota       = $row['req_dt_nota'];
          $usr_codigo        = $row['usr_codigo'];

              if ($usr_codigo != null) {
                  $sqlusuario = "SELECT u.usr_nome
                  				   FROM usuarios u 
                  				  WHERE u.usr_codigo = $usr_codigo";
    		      $query=pg_query($sqlusuario);
    		      while($row13=pg_fetch_array($query)) {
    		            $usuario=$row13[0];
    		      }
    		  }


          $req_total_nota    = $row['req_total_nota'];
          $req_data_inclusao = $row['req_data_inclusao'];
          $req_entrada       = $row['tipoentrada'];
          $req_saida         = $row['tiposaida'];
          if ($req_entrada != null){
          	$ent_saida = $req_entrada;
          }
          else {
          	$ent_saida = $req_saida ;
          }

	

	//---------  Cabeçalho do Relatorio  ----------------->

}
	echo "<body >";
//	echo "<pre>".print_r($row,1);
	$msg = $row[tiposaida];
	
	$Tit = "Relat&oacute;rio da Requisi&ccedil;&atilde;o";
	$dt_fim = $req_data;
	
	
	cabecario_rel4($msg,$mov_nr_nota,$pessoa,$setor,$usuario,$mov_tipo,$setor,$ent_saida,$req_data);
	//------------------------------------------------------------------>
	// -> Exibição dos itens
	//------------------------------------------------------------------>
	
	$cabec = 1;
	$sqlit = "SELECT ir.ireq_codigo,
				 ir.req_codigo,
				 ir.pro_codigo,
				 p.pro_nome,
				 ir.ireq_vlrdesc,
				 ir.ireq_lote,
				 ir.ireq_validade,
				 g.gru_nome,
				 ir.ireq_status,
				 to_char(coalesce(ir.ireq_quantidade,0),'9999999') as qtde,
				 ir.ireq_vlrunit,
				 to_char(coalesce(ir.ireq_vlrunit,0) * coalesce(ir.ireq_quantidade,0), '99,999,999D99'),
				 p.pro_custo as custo_saida, 
				 to_char(coalesce(p.pro_custo,0) * coalesce(ir.ireq_quantidade,0), '99,999,999.99') as vlr_tt_sai,
				 r.req_tipo as movimentacao,
				 ir.ireq_consolidado,
				 to_char(coalesce(ir.ireq_qtde_solicitada,0),'9999999') as qtdesolic
			FROM itens_requisicao ir, 
				 produto p,
				 requisicao r,
				 grupo g
		   WHERE p.pro_codigo = ir.pro_codigo
		     AND ir.req_codigo = r.req_codigo
		     AND g.gru_codigo = p.gru_codigo 
		     AND r.req_codigo = '$nr_movimento'
		   ORDER BY gru_nome, pro_nome";

//------------------------------------------------------------------>
// -> Cálculo do total de Entrada
//------------------------------------------------------------------>


$sqlttit ="SELECT to_char(sum(ir.ireq_vlrunit * ir.ireq_quantidade), '99,999,999D99'),
				  to_char(sum(ir.ireq_vlrdesc * ir.ireq_quantidade), '99,999,999D99') 
			 FROM itens_requisicao ir, 
			 	  produto p,
			 	  requisicao r
            WHERE p.pro_codigo = ir.pro_codigo 
              AND ir.req_codigo = r.req_codigo 
              AND r.req_codigo = '$nr_movimento'";
	
	
	//------------------------------------------------------------------>
	// -> Cálculo do total de Saída
	//------------------------------------------------------------------>
	

$sqlttit_sai ="SELECT to_char(sum(p.pro_custo * ir.ireq_quantidade), '99,999,999D99') as vlr_tt_sai
				 FROM itens_requisicao ir,
				 	  produto p, 
				 	  requisicao r 
				WHERE p.pro_codigo = ir.pro_codigo 
				  AND ir.req_codigo = r.req_codigo
				  AND r.req_codigo = '$nr_movimento' ";
//echo  $sqlttit_sai;
         $query=pg_query($sqlit);
         $movimento_tipo = $req_tipo;
         $total_it = 0;
	 $totalitens = 0;
		 while($rowit=pg_fetch_array($query))
		 {	
		if( $rowit["movimentacao"] != 'T' ){
		    $movimento_tipo = $req_tipo;
		    $ireq_codigo     =  $rowit['ireq_codigo'];
    		$req_codigo      =  $rowit['req_codigo'];
    		$pro_codigo      =  $rowit['pro_codigo'];
    		$pro_nome        =  $rowit['pro_nome'];
    		$ireq_vlrdesc    =  $rowit['ireq_vlrdesc'];
    		if ($ireq_vlrdesc == null )
    		{
    		   $ireq_vlrdesc = 0;
    		}
    		$ireq_lote       =  $rowit['ireq_lote'];
    		$ireq_validade   =  $rowit['ireq_validade'];
			$grupo    		 =  $rowit['gru_nome'];
    		$ireq_status     =  $rowit['ireq_status'];
    		$ireq_quantidade =  $rowit['qtde'];
    		$ireq_solicitada =  $rowit['qtdesolic'];
    		$ireq_vlrunit    =  $rowit['ireq_vlrunit'];
    		$tot_item        =  $rowit['vlr_tt_sai'];
		$cancelado = '';
		if ($rowit['ireq_consolidado'] == 'C') {
		   $cancelado = ' - ITEM CANCELADO ';
		   //$ireq_quantidade = 0;
		}
    		if ($movimento_tipo != 'Entrada')
             {
 		       $ireq_vlrunit = $rowit['custo_saida'];
  		       $tot_item    = $rowit['vlr_tt_sai'];
      		 }
			if ($cabec == 1)
			{
				echo "
				<table width='100%'  class=\"lista\">
					<tr>
						<th colspan='8'>Movimentação Número: $nr_movimento</th>
					</tr>
					<tr>
						<th>C&oacute;digo</th>
						<th>Grupo</th>
						<th>Descri&ccedil;&atilde;o</th>
						<th>Qtde Baixar</th>						
					</tr> 
					";
				$cabec = 0;
			}
			
			//alteracao <th width='13%' align = 'right'>$tot_item</td>
			echo "
				<tr>
					<td>$pro_codigo</td>
					<td>$grupo</td>
					<td>$pro_nome <b> $cancelado</b></td>
					<td>$ireq_quantidade</td>
				</tr>" ;
		}
		else
		{
         $movimento_tipo = $req_tipo;         

    		$ireq_codigo     =  $rowit['ireq_codigo'];
    		$req_codigo      =  $rowit['req_codigo'];
    		$pro_codigo      =  $rowit['pro_codigo'];
    		$pro_nome        =  $rowit['pro_nome'];
    		$ireq_vlrdesc    =  $rowit['ireq_vlrdesc'];
    		if ($ireq_vlrdesc == null )
    		{
    		   $ireq_vlrdesc = 0;
    		}
    		$ireq_lote       =  $rowit['ireq_lote'];
    		$ireq_validade   =  $rowit['ireq_validade'];
    		$ireq_status     =  $rowit['ireq_status'];
    		$ireq_quantidade =  $rowit['qtde'];
    		$ireq_solicitada =  $rowit['qtdesolic'];
    		$ireq_vlrunit    =  $rowit['ireq_vlrunit'];
    		$tot_item       =  $rowit['vlr_tt_sai'];
     $pegaValidade = "select   p.pro_codigo,
							pro_nome, coalesce(ireq_quantidade,0) as ireq_quantidade, 
							coalesce(ireq_qtde_solicitada,0) as ireq_qtde_solicitada, 
							sum(sal_qtde) AS sal_qtde , 
							ireq_codigo, 
							ireq_consolidado,
							ireq_lote,
							ireq_validade 
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
								 ireq_consolidado,
								 ireq_lote, 
								 ireq_validade
								 order by pro_nome";
    
         $exePega = pg_query($pegaValidade);
        while($resExe=pg_fetch_array($exePega))
        {
         $validade = $resExe['ireq_validade'];
         $lote =$resExe['ireq_lote'];
		if ($rowit[14] == 'C')
		   $pro_nome .= ' - ITEM CANCELADO ';
    		if ($movimento_tipo != 'Entrada')
             {
 		      $ireq_vlrunit = $rowit['custo_saida'];
  		       $tot_item    = $rowit['vlr_tt_sai'];
      		 }


             if ($cabec == 1)
               {
                echo "<table border='1' width='100%' style='font-size:12px;font-family:Tahoma,Arial;'>
					  <tr>
					  <td width='100%' align='center'><b>Itens da Requisicao de Materiais</b></td>
					  </tr> </table>";

                echo "<table border='1' width='100%' style='font-size:12px;font-family:Tahoma,Arial;'>
                      <tr>
                      <td width='7%'>Codigo</td>
                      <td width='60%'>Descricao</td>
                      <td width='8%'>Lote</td>
                      <td width='10%' align = 'center'>Validade</td>
                      <td width='8%' align = 'center'>Qtde</td>
                      <td width='8%' align = 'center'>Entregue</td>
                      </tr>
                      </table> ";
               $cabec = 0;
               }

               echo "<table border='0' width='100%' style='font-size:12px;font-family:Tahoma,Arial;'>
               <tr>
               <td width='7%'>$resExe[pro_codigo]</td>
               <td width='60%'>$resExe[pro_nome]</td>
               <td width='8%'>$lote</td>
               <td width='10%'>$validade&nbsp;</td>
               <td width='8%' align = 'right'>$resExe[ireq_quantidade]&nbsp;&nbsp;&nbsp;</td>
               <td width='8%' align = 'right'>[<u>&#175;</u>]&nbsp;</td>
               </tr>
               </table>" ;
               
         }//fim do if
         }
        
       // break;
	}//fim do while
	echo "</table>";
	if ($req_tipo != 'Entrada')
    		      {
                   $query=pg_query($sqlttit_sai);
				   while($row_total_sai=pg_fetch_array($query))
			          {
		    		  $total_it       =  $row_total_sai[0];
    		          if ($total_desc == null )
    		             {
    		             $total_desc = 0;
    		             }
    		          }

			      }
			   else
			      {
                   $query=pg_query($sqlttit);
				   while($row_total=pg_fetch_array($query))
			          {
		    		  $total_it       =  $row_total[0];
    		          $total_desc     =  $row_total[1];
    		          if ($total_desc == null )
    		             {
    		             $total_desc = 0;
    		             }
    		          }
    		      }

//	$total_it = number_format($total_it, 2, ',', '.');
	if(pg_num_rows($query) == 0){
		echo "<p><font color='red'><i>Nenhum Produto foi lançado</i></font></p>";		
	}
	echo "<br><br><br>";
		echo"<table  width='100%'>
		<tr>
			<td  align = 'center'>_____________________________________</td>

			
		</tr>
		<tr>
			<td  align = 'center'>$usuario</td>

		</tr>
	</table>";
	rodape_rel();
?>