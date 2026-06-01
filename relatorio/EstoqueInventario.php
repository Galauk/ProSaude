<link href='../estilo.css' rel='stylesheet' type='text/css'>
<link href='../estilo_janela.css' rel='stylesheet' type='text/css'>
<?php
function inv_data($dat) {
	$d=explode("-",$dat);
	$dat=$d[2]."-".$d[1]."-".$d[0]."<br>";
	return "$dat";
}

//------------------------------------------------------------------>
// -> Includes
//------------------------------------------------------------------>
session_start();
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
 include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

$stmt = "SELECT uni_codigo FROM usuarios WHERE usr_codigo = $id_login";
$stmt = pg_query($stmt);
$dados = pg_fetch_array($stmt);


//echo "<body>
//     <link href='../estilo.css' rel='stylesheet' type='text/css'>";

//----------------  Monta Dados Recebidos  ---------------->

$titulo="Lista de Produtos para Contagem de Estoque";    //       NOME DO RELATÓRIO

//echo "Data INICIAL->".$dt_inicial."<br>";
//echo "Data FINAL  ->".$dt_final."<br>";
//echo "Hora INICIAL->".$hr_inicial."<br>";
//echo "Hora FINAL  ->".$hr_final."<br>";

$set_codigo = $codSetor;
$gru_codigo = $codGrupo;

$hr_inicial = '00:00';
$hr_final = '23:59';

$dias = array("31", "28", "31", "30", "31", "30","31", "31", "30", "31", "30", "31");

$mes = pg_fetch_array(pg_query("select extract (month from date(now()))"));
$ano = pg_fetch_array(pg_query("select extract (year from date(now()))"));

if ($mes[0] < 10) {
   $mes1 = '0'.$mes[0];
}
else {
   $mes1 = $mes[0];
}

//echo $dt_inicial;

if (!$dt_final) {
    $datafinal = pg_fetch_array(pg_query("select to_char(date(now()), 'dd/mm/yyyy')"));
    $dt_final = $datafinal[0];
}

    echo " <br> <br> ";
//------------------------------------------------------------------>
// -> Funções php
//------------------------------------------------------------------>

function cabeca($Tit, $dtIni, $dtFin, $hrIni, $hrFin, $Grupo, $Cab, $Setor) {
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	$common = new commonClass();
//--->        Cabeçalho do Sistema

       if ($Cab == 0) {
			echo "<center>".$common->commonButton("Imprimir   ", null, "print.png", "onclick=\"javascript:window.print();this.style.display='none';\"")."</center><br>";
		  //echo "<center><input type=\"button\" value=\"Imprimir\" onclick=\"javascript:window.print();this.style.display='none';\"></center><br>";
          echo "<table style='font-size:12px;font-family:Verdana,Arial;' width=100% cellspacing=0 cellpadding=0 border=0 align=center opmargin=0 leftmargin=0>
	 	       <tr>
	     	        <td width=130><font size=2 face=courier>GESTÃO PÚBLICA DE SAÚDE</font></td>
         	        <td width= 10><font size=2 face=courier align=right>".date("d/m/Y h:i:s")."</font></td>
	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=2 face=courier> ".strtoupper($Tit)."</font></td>
 	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=2 face=courier> GRUPO: ".strtoupper($Grupo)."</font></td>
 	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=2 face=courier> SETOR: ".strtoupper($Setor)."</font></td>
 	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=2 face=courier>Data: $dtFin </font></td>
 	    	       </tr>
 	    	       <tr>
		        <td>&nbsp;</td>
		        <td>&nbsp;</td>
	    	       </tr>
 	              </table>";
 	    echo "<table style=\"font-size:12px;font-family:verdana,arial;\" width=100% align=center cellspacing=0 cellpadding=0 border=1 topmargin=0 leftmargin=0>\n";
 	    }

//--->        Cabeçalho dos Dados

       if ($Cab == 0) {
           echo " <tr style='font-weight:bold;' align=center>\n";
           echo "  <td style='padding:5px;' width=40%>Produto </td>\n";
			echo "<td>Lote</td>\n";
			echo "<td>Validade</td>\n";
			echo "<td>1&ordf; Cont.</td>\n";
			echo "<td>2&ordf; Cont.</td>\n";
			echo "<td>3&ordf; Cont.</td>\n";
           echo " </tr>\n";

        }
}

//----------------  Rotina de Impressão  ---------------->
	$sql = "SELECT DISTINCT a.pro_codigo, 
							a.pro_nome, 
							b.gru_codigo, 
							b.gru_nome,
							d.set_nome,
							a.pro_validade,
							s.sal_lote,
							to_char(s.sal_validade, 'DD/MM/YYYY') as sal_validade,
							s.sal_qtde
			  FROM produto a, 
				   grupo b, 
				   produto_setor c,
				   setor d,
				   saldo s
			 WHERE a.gru_codigo = b.gru_codigo
			   AND a.pro_codigo = c.pro_codigo
			   AND c.set_codigo = d.set_codigo
			   AND a.pro_codigo = s.pro_codigo
			   AND s.set_codigo = d.set_codigo
			   AND s.sal_qtde > 0"
             .($dados[0]=="" ? "" : " AND d.uni_codigo = ".$dados[0]).
             " AND b.gru_codigo = $_GET[gru_codigo]
			   AND d.set_codigo = $_GET[set_codigo]
			   AND a.pro_situacao = 'A'
			 ORDER BY a.pro_nome";

$query=pg_query($sql) or die (pg_last_error());
$lin=999;

if (pg_num_rows($query) == 0) {
    echo "<table style='font-size:12px;font-family:Verdana,Arial;' width=100% align=left cellspacing=2 cellpadding=0 border=0 topmargin=0 leftmargin=0>\n";
    echo "  <tr><td align=center colspan=5>NÃO TEM DADOS PARA ESTES PARÂMETROS</td></tr>\n";
    echo "  <tr><td align=right  colspan=5>&nbsp;</td></tr>\n";
    echo "  <tr><td align=right  width=25%></td>\n";
    echo "      <td align=right  width=20%>Data </td>\n";
    echo "      <td align=left>$dt_final</td><td>&nbsp;</td></tr>\n";

    echo "  <tr><td align=right></td>\n";
    echo "</table>\n";
}
else {
	$total = 0;
	$cont = 0;
	while($row=pg_fetch_array($query)) {

		if ($lin== 999) {
			cabeca($titulo, $dt_inicial, $dt_final, $hr_inicial, $hr_final, $row[3], '0', $row['set_nome']);
			$lin=9;
		}
		if ($aux != $row['pro_nome'] && $aux2 == "S" && $cont > 0){
			for($i = 0; $i < 5; $i++){
				echo " <tr>\n";
		          echo "  <td style='padding:5px;'>$aux</td>\n";
		          echo "  <td>&nbsp;&nbsp;</td>\n";
		          echo "  <td>&nbsp;&nbsp;</td>\n";
		          echo "  <td>&nbsp;&nbsp;</td>\n";
		          echo "  <td>&nbsp;&nbsp;</td>\n";
		          echo "  <td>&nbsp;&nbsp;</td>\n";
		        echo " </tr>\n";
			}
		}
		echo " <tr>\n";
			echo "<td style='padding:5px;'>$row[pro_nome]</td>\n";
			echo "<td style='padding:5px;'>".($row[pro_validade] == "S" ? $row[sal_lote] : smartyUpper("N&atilde;o se Aplica"))."</td>\n";
			echo "<td style='padding:5px;'>".($row[pro_validade] == "S" ? $row[sal_validade] : smartyUpper("N&atilde;o se Aplica"))."</td>\n";
			echo "<td style='padding:5px;'>&nbsp;&nbsp;</td>\n";
			echo "<td style='padding:5px;'>&nbsp;&nbsp;</td>\n";
			echo "<td style='padding:5px;'>&nbsp;&nbsp;</td>\n";
		echo "</tr>\n";
		$aux = $row['pro_nome'];
		$aux2 = $row['pro_validade'];
		$cont++;
      }
	if ($aux2 == "S"){
		for($i = 0; $i < 5; $i++){
			echo " <tr>\n";
	          echo "  <td style='padding:5px;'>$aux</td>\n";
	          echo "  <td>&nbsp;&nbsp;</td>\n";
	          echo "  <td>&nbsp;&nbsp;</td>\n";
	          echo "  <td>&nbsp;&nbsp;</td>\n";
	          echo "  <td>&nbsp;&nbsp;</td>\n";
	          echo "  <td>&nbsp;&nbsp;</td>\n";
	        echo " </tr>\n";
		}
	}
    echo "</table>\n";


}
echo "<body>\n";
echo "<body>\n";
?>
