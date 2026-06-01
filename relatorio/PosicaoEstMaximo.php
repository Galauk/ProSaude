<?php
/**
 * @version Renato 6/7/2007 - 10:30
*/
?>
<script language=javascript>

function imprimir() {
       window.print();
}
</script>

<body>

<?php
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
//----------------  Dados Recebidos  ---------------->

$dt_i=$dt_inicial;
$dt_f=$dt_final;

//echo "Inicial-->".$dt_inicial."<br>";
//echo "Final---->".$dt_final."<br>";
//echo "Setor---->".$set_codigo."<br>";
//echo "Grupo---->".$gru_codigo."<br>";

$titulo="PRODUTOS QUE ATINGIRAM O ESTOQUE MAXIMO";    //       NOME DO RELATÓRIO

if ($set_codigo) {
    $sql = "SELECT setor.set_codigo, setor.set_nome " .  //       Pega Setor
           "  FROM setor " .
           " WHERE setor.set_codigo = $set_codigo";
    $query=pg_query($sql);
    while($rowSetor=pg_fetch_array($query)) {
          $SetNome=$rowSetor[1];
    }
} else {  $SetNome = "TODOS";  }

if ($gru_codigo) {
    $sql = "SELECT grupo.gru_codigo, grupo.gru_nome " .  //       Pega Grupo
           "  FROM grupo " .
           " WHERE grupo.gru_codigo = $gru_codigo";
    $query=pg_query($sql);
    while($rowGrupo=pg_fetch_array($query)) {
          $GruNome=$rowGrupo[1];
    }
} else {  $GruNome = "TODOS";  }


function cabeca($Tit, $dtIni, $dtFin, $SetNo, $GruNo, $tpCab, $zerado) {

        //---------  Cabeçalho do Relatorio  ----------------->

         if ($tpCab == '0') {
             echo "<table  width=720 cellspacing=0 cellpadding=0 border=0 align=center>
	 	            <tr>
	     	         <td width=250><font size=1 face=courier>GESTÃO PÚBLICA DE SAÚDE</font></td>
         	         <td width= 10><font size=1 face=courier align=right>".date("d/m/Y h:i:s")."</font></td>
	    	        </tr>
 	    	        <tr>
 	     	         <td colspan=2><font size=1 face=courier>".strtoupper($Tit)."</font></td>
 	    	        </tr>
 	    	        <tr>
 	     	         <td colspan=2><font size=1 face=courier>ESTOQUE EM: $dtFin</font></td>
 	    	        </tr>
 	    	        <tr>
 	     	         <td colspan=2><font size=1 face=courier>SETOR:   $SetNo</font></td>
 	    	        </tr>
 	    	        <tr>
 	     	         <td colspan=2><font size=1 face=courier>Grupo:   $GruNo</font></td>
 	    	        </tr>
                    ";
 	    	 if ($pro_codigo) {
 	    	     echo "<tr>
 	     	            <td colspan=2><font size=1 face=courier>PRODUTO:   $ProNo</font></td>
 	    	           </tr>";
             }
 	    	 echo " <tr>
		             <td>&nbsp;</td>
		             <td>&nbsp;</td>
	    	        </tr>
 	               </table>";

 	         echo "<table style='font-size:11px;font-family:Tahoma,Arial;' border=0 width=720 align=center cellspacing=0 cellpadding=1 topmargin=0 leftmargin=0>\n";
         }
        //---------  Cabeçalho dos Dados  ----------------->

         if ($tpCab == '1') {
             echo " <tr>\n";
		 	 echo "  <td width= '5%' colspan=1 align=right>                         </td>\n";
		 	 echo "  <td width='50%' colspan=1 align=left > Produto                 </td>\n";
			 echo "  <td width='20%'           align=left> Estoque Atual&nbsp;&nbsp;&nbsp;</td>\n";
			 echo "  <td width= '20%'> Estoque Maximo                                        </td>\n";
			 echo "  <td width= '10%' align=left> Diferenca &nbsp;&nbsp;&nbsp;          </td>\n";
			 echo " </tr>\n";
			 echo " <tr>\n";
			 echo "  <td align=center cellspacing=0 cellpadding=0 colspan=6>&nbsp;</td>\n";
             echo " </tr>\n";
         }
}

//-------------  Rotina Captação dos Dados  --------------->


//         ---- Captação Produto/Movimentos --->

$sql  = "SELECT pro_nome,
		round(cast (calcula_estoque(produto.pro_codigo, $set_codigo, '$dt_final') as numeric), 0),
        prset_maximo,
		round(cast (calcula_estoque(produto.pro_codigo, $set_codigo, '$dt_final') as numeric), 0) - prset_maximo as dif
		FROM produto, produto_setor ";
if ($gru_codigo) {
       $sql .= " WHERE produto.pro_codigo = produto_setor.pro_codigo
				 AND gru_codigo = $gru_codigo
				 AND prset_maximo is not null ";
   }
else {
       $sql .= " WHERE produto.pro_codigo = produto_setor.pro_codigo
				 AND prset_maximo is not null ";
}
$sql .= " AND produto_setor.set_codigo = $set_codigo
		  AND round(cast (calcula_estoque(produto.pro_codigo, $set_codigo, '$dt_final') as numeric), 0)  > prset_maximo
		  ORDER BY produto.pro_nome ";
//vSQL($sql,"1");

//echo "<pre>$sql</pre>";

$query=pg_query($sql);

if (pg_num_rows($query) == 0) {
    echo "NÃO TEM DADOS PARA ESTES PARÂMETROS<br><br>";
    echo "&nbsp;&nbsp;&nbsp;Data ->".$dt_final."<br>";
    echo "&nbsp;&nbsp;&nbsp;Setor     ->".$set_codigo."<br>";
}

//----------------  Rotina de Impressão  ------------------>

$lin = 999;

// Zebragem
$controle = 0;

while($row=pg_fetch_row($query)) {
      if ($lin > 20) {
          if ($lin== 999) {
              cabeca($titulo, $dt_inicial, $dt_final, $SetNome, $GruNome, '0', $zerado);
            }
         cabeca($titulo, $dt_inicial, $dt_final, $SetNome, $GruNome , '1', $zerado);

         echo " <tr>\n";
		 echo "  <td></td>\n";
		 echo " </tr>\n";
         $lin=0;
      }

 //---- Pega Saldo --->
		$c1 = "";
		$c2 = "#A6A6A6";
		
		if ($controle == 0) {
		  $cor = $c1;
		  $controle++;
		} else {
		  $cor = $c2;
		  $controle = 0;
		}
		
      $minimo=formata_valor0($row[2]);
      $diferenca=formata_valor0($row[3]);
      echo " <tr bgcolor='$cor'>\n";
      echo "  <td colspan=2 align=left>$row[0] </td>\n";
      echo "  <td align=left>$row[1]   </td>\n";
      echo "  <td align=left>$minimo               </td>\n";
      echo "  <td align=left>$diferenca    </td>\n";
	  echo " </tr>\n";

}

echo "</table>";

?>
