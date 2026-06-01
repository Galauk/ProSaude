<!-- --------------  Fun��es javascript  --------------- -->

<SCRIPT Language="Javascript">
function imprimir(){
   window.print();
}
</script>

<body onload='imprimir()'>

<?php
//-------------------  Includes  -------------------------->

	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

//----------------  Monta Dados Recebidos  ---------------->
//echo "INICIAL->".$dt_inicial."<br>";
//echo "FINAL  ->".$dt_final."<br>";
//echo "Agente->".$agt_codigo."<br>";

$titulo="Medicamentos por Paciente";    //       NOME DO RELAT�RIO

//------------------  Fun��es php  ------------------------>

$texto = "<table  width=100% cellspacing=0 cellpadding=0 border=0 align=center>
	 	           <tr>
	     	        <td width=130><font size=2 face=courier>GEST&Atilde;O P&Uacute;BLICA DE SA&Uacute;DE</font></td>
         	        <td width= 10><font size=2 face=courier align=right>".date("d/m/Y h:i:s")."</font></td>
	    	       </tr>
 	    	       <tr>
 	     	        <td colspan=2><font size=3 face=courier>".strtoupper($titulo)."</font></td>
 	    	       </tr>
 	              </table>
 	  <table style=\"font-size:12px;font-family:Tahoma,Arial;\" width=100% align=center cellspacing=0 cellpadding=0 border=0 topmargin=0 leftmargin=0>\n
	<tr><td>&nbsp;</td></tr>
           <tr>\n
           <td width=250 colspan=1 style=\"font-weight:bold\">Nome do Paciente</td>\n
           <td width=250 colspan=1 style=\"font-weight:bold\">Produto</td>\n
	  <td width=250 colspan=1 style=\"font-weight:bold\">Quantidade</td>\n  
           <td width=250 colspan=1 style=\"font-weight:bold\">Periodicidade</td>\n
           </tr>
	  <tr><td>&nbsp;</td></tr>\n";

//----------------  Rotina de Impress�o  ------------------>
$sql = pg_query("SELECT * FROM usuario WHERE usu_codigo = $pac_codigo");
$usuario_nome = pg_fetch_row($sql);
$sql = pg_query("SELECT * FROM produto WHERE pro_codigo = $med_codigo");
$medicamento_nome = pg_fetch_row($sql);
$sql = pg_query("SELECT * FROM programa_atendimento WHERE prg_codigo = $prg_codigo");
$programa_nome = pg_fetch_array($sql);
$lin=999;
if ($_GET['pac_codigo']!="" && $_GET['med_codigo']!='todos')
{
	$decisao= "d.usu_codigo = $pac_codigo AND e.pro_codigo = $med_codigo AND";
}
else if ($_GET['pac_codigo']!="" && $_GET['med_codigo']=='todos')
{
	$medicamento_nome[2] = "Todos";
	$decisao= "d.usu_codigo = $pac_codigo AND";
}
else if ($_GET['pac_codigo']=="" && $_GET['med_codigo']!='todos')
{
	$usuario_nome = "Todos";
	$decisao= "e.pro_codigo = $med_codigo AND";
}
else if ($_GET['pac_codigo']=="" && $_GET['med_codigo']=='todos')
{
	$medicamento_nome = "Todos";
	$usuario_nome = "Todos";
	$decisao = "";
}

// Se o programa for todos...
if ($_GET['prg_codigo'] == -1) {
    $sql="SELECT a.usu_nome,b.prg_nome,c.pro_nome,d.ctp_periodo,d.ctp_quantidade 
                    FROM usuario a, programa_atendimento b, produto c, cota_paciente d, programa_produto e
                    WHERE a.usu_codigo = d.usu_codigo 
                    AND e.prgp_codigo = d.prgp_codigo
                    AND e.pro_codigo = c.pro_codigo
                    ORDER BY d.usu_codigo, b.prg_nome ASC";
} else {
    $sql="SELECT a.usu_nome,b.prg_nome,c.pro_nome,d.ctp_periodo,d.ctp_quantidade 
                    FROM usuario a, programa_atendimento b, produto c, cota_paciente d, programa_produto e
                    WHERE $decisao e.prg_codigo=$prg_codigo
                    AND a.usu_codigo = d.usu_codigo 
                    AND e.prgp_codigo = d.prgp_codigo
                    AND e.pro_codigo = c.pro_codigo
                    ORDER BY d.usu_codigo, b.prg_nome ASC";
}

$query=pg_query($sql);

if (pg_num_rows($query) == 0) {

    echo "N&Atilde;O FORAM ENCONTRADAS INFORMACOES COM ESTES PAR&Acirc;METROS<br><br>";
    echo "Programa Atend. ->".$programa_nome[1]."<br>";
    echo "Usuario ->".$usuario_nome[1]."<br>";
    echo "Medicamento->".$medicamento_nome[2]."<br>";
}
else {
	echo $texto;
	$nome = "";
    while($row=pg_fetch_row($query)) {

	if ($row[0] != $nome)
	{
		echo "<td width=30%>$row[0]</td>\n";
		$nome = $row[0];
	}
	else
	{
		echo "<td width=30%>&nbsp;</td>";
	}
     echo "  <td width= 30%>$row[2]</td>\n";      
      echo "  <td width=10%>".number_format($row[4],0,',','.')."</td>\n";
      echo "  <td width=10% align=\"center\">$row[3]</td>\n";
      echo " </tr>\n";
      $tot++;

}    
echo "</table>";
}



?>
