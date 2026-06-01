<script language=javascript>

function imprimir() {
       window.print();
}
</script>

<?php
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
global $movimento_tipo;
//----------------  Dados Recebidos  ---------------->

$nr_movimento = $req_nr_nota;

$sql = "SELECT r.req_codigo, 
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
         WHERE r.req_codigo = $nr_movimento";


if(isset($_GET['sql']) && $_GET['sql'] == 1)
	echo $sql;
	
$query = pg_query($sql);

while($row=pg_fetch_array($query)) {
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
    		      $query2=pg_query($sqlpessoa);
    		      while($row4=pg_fetch_array($query2)) {
    		            $pessoa='Usuario: ' . $row4['usu_nome'];
    		      }
    		  }
          $set_saida         = $row[10];

              if ($set_saida != null) {
                  $sqlsetor = "SELECT s.set_nome  
                  				 FROM setor s 
                  				WHERE s.set_codigo = $set_saida";
    		      $query3=pg_query($sqlsetor);
    		      while($row10=pg_fetch_array($query3)) {
    		            $pessoa='C. Estocador Origem:  ' . $row10['set_nome'];
    		      }
    		  }


          $req_desconto      = $row['req_desconto'];
          $req_observacao    = $row['req_observacao'];
          $cond_codigo       = $row['cond_codigo'];

              if ($cond_codigo != null) {
                  $sqlcond = "SELECT c.cond_descricao  
                  				FROM condpagto c 
                  			   WHERE c.cond_codigo = $cond_codigo";
    		      $query4=pg_query($sqlcond);
    		      while($row7=pg_fetch_array($query4)) {
    		            $condicao=$row7['cond_descricao'];
    		      }
    		  }


          $ate_codigo        = $row['ate_codigo'];
          $set_entrada       = $row['set_entrada'];

              if ($set_entrada != null) {
                  $sqlsetor = "SELECT s.set_nome
                  				 FROM setor s 
                  				WHERE s.set_codigo = $set_entrada";
    		      $query5=pg_query($sqlsetor);
    		      while($row9=pg_fetch_array($query5)) {
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
    		      $query6=pg_query($sqlusuario);
    		      while($row13=pg_fetch_array($query6)) {
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




        //---------  Cabe�alho do Relatorio  ----------------->

echo "<body >";
            /* echo "<table  width=100% cellspacing=0 cellpadding=0 border=0 >
	 	           <tr>
	     	        <td width=77%><font size=5 face=courier>GEST�O P�BLICA DE SA�DE</font></td>
         	        <td width= 23%><font size=3 face=courier align=right>".date("d/m/Y h:i:s")."</font></td>
	    	       </tr>
 	              </table>";
	*/
	$Tit = "RELAT&Oacute;RIO DA REQUISI&Ccedil;&Atilde;O";
	$dt_fim = $req_data;
	include "cabecalho.php";

echo " <table  width='100%' cellspacing=0 cellpadding=0 border=0 style='font-size:12px;font-family:Tahoma,Arial;'>
  <tr>
    <td width='52%' colspan='2'>Cod. Requisicao:&nbsp; <b>$req_nr_nota</b>&nbsp;&nbsp;&nbsp; em&nbsp;&nbsp;
      $req_data&nbsp;&nbsp;&nbsp;&nbsp; <b>- $ent_saida</b></td>
    <td width='25%'>Tipo:&nbsp; <b>$req_tipo</b></td>



  </tr>
  <tr>
    <td width='52%'>$pessoa</td>
    <td width='25%'>".( $req_tipo == "Entrada" ? "Atendimento: $ate_codigo" : "&nbsp;" )."</td>
    <td width='23%'>setor: $setor&nbsp;</td>
  </tr>
  <tr>
    <td width='52%'>OBS.:&nbsp; $req_observacao&nbsp;</td>
    <td width='25%'>&nbsp;</td>
    <td width='23%'>Usu�rio: $usuario</td>
  </tr>
</table>
<!-- <p>Dados da NF:</p> -->
<table border=0 width='100%' style='font-size:12px;font-family:Tahoma,Arial;'>
  <tr>
    <td width='20%'>Requisicao nr. <b>$req_nr_nota</b>&nbsp; </td>
    <td width='20%'>Dt. Inclusao: $req_data_inclusao</td>
    <td width='17%'>".( $req_tipo == "Entrada" ? "Desc.:&nbsp; $req_desconto" : "&nbsp;" )."</td>
    <td width='20%'>".( $req_tipo == "Entrada" ? "Valor: R$ $req_total_nota" : "&nbsp;" )."</td>
    <td width='23%'>".( $req_tipo == "Entrada" ? "Condi��o:&nbsp; $condicao" : "&nbsp;" )."</td>  </tr>
</table>
";
}
//------------------------------------------------------------------>
// -> Exibi��o dos itens
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
// -> C�lculo do total de Entrada
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
// -> C�lculo do total de Sa�da
//------------------------------------------------------------------>



$sqlttit_sai ="SELECT to_char(sum(p.pro_custo * ir.ireq_quantidade), '99,999,999D99') as vlr_tt_sai
				 FROM itens_requisicao ir,
				 	  produto p, 
				 	  requisicao r 
				WHERE p.pro_codigo = ir.pro_codigo 
				  AND ir.req_codigo = r.req_codigo
				  AND r.req_codigo = '$nr_movimento' ";
//echo  $sqlttit_sai;
if(isset($_GET['sql']) && $_GET['sql'] == 2)
	echo $sqlit;

         $query7=pg_query($sqlit);
         $movimento_tipo = $req_tipo;
         $total_it = 0;
	 $totalitens = 0;
		 while($rowit=pg_fetch_array($query7))
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
                echo "<table border='1' width='100%'>
					  	<tr>
					  		<td width='100%' align='center'><b>Itens da Requisi&ccedil;&atilde;o de Materiais</b></td>
					  	</tr>
					  </table>";

                echo "<table border='1' width='100%'>
	                      <tr>
		                      <td width='10%'>C&oacute;digo</td>
							  <td width='30%'>Grupo</td>
		                      <td width='50%'>Descri&ccedil;&atilde;o</td>
		                      <td width='10%' align = 'right'>Qtde Baixar</td>
	                      </tr>
                      </table> ";
               $cabec = 0;
               }

               echo "<table border='1' width='100%'>
		               <tr>
			               <td width='10%'>$pro_codigo</b></td>
						   <td width='30%'>$grupo</b></td>
			               <td width='50%'>$pro_nome <b> $cancelado</b></td>
			               <td width='10%' align = 'right'><b>$ireq_quantidade</b></td>
		               </tr>
		             </table>" ;
               
         }else{ 
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
								 order by pro_nome 
						 limit 1";
    
        // $exePega = pg_query($pegaValidade);
        //while($resExe=pg_fetch_array($exePega))
        //{
        
         $validade = $rowit['ireq_validade'];
         $lote =$rowit['ireq_lote'];
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
               <td width='7%'>$rowit[pro_codigo]</td>
               <td width='60%'>$rowit[pro_nome]</td>
               <td width='8%'>$lote</td>
               <td width='10%'>$validade&nbsp;</td>
               <td width='8%' align = 'right'>$rowit[qtde]&nbsp;&nbsp;&nbsp;</td>
               <td width='8%' align = 'right'>[<u>&#175;</u>]&nbsp;</td>
               </tr>
               </table>" ;
               
        $_falci = true;
         }//fim do if
         //}
       // break;
		 }//fim do while
    		   if ($req_tipo != 'Entrada')
    		      {
                   $query8=pg_query($sqlttit_sai);
				   while($row_total_sai=pg_fetch_array($query8))
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
                   $query9=pg_query($sqlttit);
				   while($row_total=pg_fetch_array($query9))
			          {
		    		  $total_it       =  $row_total[0];
    		          $total_desc     =  $row_total[1];
    		          if ($total_desc == null )
    		             {
    		             $total_desc = 0;
    		             }
    		          }
    		      }
    		    $totalitens=pg_num_rows($query7);

          echo " <table border='1' width='100%'>
                 <tr>
                 <td width='70%'><b>Total de Itens</td>
                 <td width='30%' align = 'right'><b>$totalitens</b></td>
                 </tr>
                 </table> <br><br>";
	echo"<table  width='100%'>
		<tr>
			<td  align = 'center'>_____________________________________</td>

			
		</tr>
		<tr>
			<td  align = 'center'>$usuario</td>

		</tr>
	</table>
</body> ";
?>
