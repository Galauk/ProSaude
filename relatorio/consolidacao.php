<?php
/**MEXENDO
 * @author  Leandro 11/07/2007 - 10:41
*/
?>
<script language=javascript>

function imprimir()
{
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

$mov_codigo=$_GET[mov_codigo];

$titulo="MOVIMENTAÇÕES NÃO CONSOLIDADAS";    //NOME DO RELATÓRIO

function cabeca($Tit, $tpCab, $mov_data) {

        //---------  Cabeçalho do Relatorio  ----------------->

        if ($tpCab == '0')
        {
            include 'cabecalho.php';  
        }
        //---------  Cabeçalho dos Dados  ----------------->

        if ($tpCab == '1')
        {
            echo "<table style='font-size:11px;font-family:Tahoma,Arial;' border=0 width='100%' align=center cellspacing=0 cellpadding=1 topmargin=0 leftmargin=0>
                    <tr>
                        <td width='40%' colspan=1 align=left ><b>Produto</b></td>
                        <td width='20%' align=left><b>Quantidade a Baixar</b></td>
                        <td width='20%' align=left><b>Quantidade Solicitada</b></td>
                        <td width='10%' align=center><b>Estoque<br>($mov_data)</b></td>
                        <td width='10%' align=center><b>Estoque<br>hoje</b></td>
                        <!-- <td width='30%' align=left><b>Consolidado</b></td> -->
                    </tr>";
        }
}

//-------------  Rotina Captação dos Dados  --------------->

$sql = "SELECT mov_n.pro_nome, coalesce(mov_n.ite_quantidade,0),  coalesce(mov_n.ite_qtde_solicitada,0), 
calcula_estoque(mov_n.pro_codigo, mov_n.codsetor, mov_n.mov_data) as estoque,
calcula_estoque(mov_n.pro_codigo, mov_n.codsetor, '2007-07-28') AS estoqueatual, mov_n.data,
mov.mov_nr_nota, mov_n.desc_movimentacao, mov_n.setor, mov_n.nomesetorsolicit
FROM mov_naoconsolid AS mov_n, movimento AS mov, setor AS set
WHERE mov_n.mov_codigo = mov.mov_codigo
AND set.set_codigo = mov.set_entrada
AND mov_n.mov_codigo = $mov_codigo";

//print $sql;

$query=db_query($sql);

//----------------  Rotina de Impressão  ------------------>

$lin = 999;
$total = 0;

// Zebragem
$controle = 0;

while($row=pg_fetch_row($query)) {
      if ($lin > 20) {
          if ($lin== 999) {
              cabeca($titulo, '0');
              echo "<table  width='100%' cellspacing=0 cellpadding=0 border=0 style='font-size:12px;font-family:Tahoma,Arial;'>
                    <tr>
                      <td width='52%' colspan='2'>Tipo Movimento:&nbsp; <b>".$row[7]."</b></td>
                    </tr>
                    <tr>
                      <td width='60%'>Cod. Movimento:&nbsp; <b>".$row[6]."</b></td>
                      <td width='40%'>Data: <b>".$row[5]."</b></td>
                    </tr>
                    <tr>
                      <td width='60%'>Centro Estocador: <b>".$row[8]."</b></td>
                      <td width='40%'>Setor Solicitante: <b>".$row[9]."</b></td>
                    </tr>
                  </table>
                  <br />";
            }
         $mov_data = $row[5];
         cabeca($titulo, '1', $mov_data);
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
		
      echo " <tr bgcolor='$cor'>
                <td align=left>$row[0]</td>
                <td align=left>".number_format($row[1],0,",",".")."</td>
                <td align=left>".number_format($row[2],0,",",".")."</td>
                <td align=left>".number_format($row[3],0,",",".")."</td>
                <td align=left>".number_format($row[4],0,",",".")."</td>
            </tr>";
}
echo "</table>";

?>
