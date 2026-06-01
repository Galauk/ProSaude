<script language=javascript>

function imprimir() 
{
       window.print();
}
</script>

<body onload='imprimir()'>

<?php

//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
//----------------  Dados Recebidos  ---------------->


echo "<br>";
echo "Medico->".$med_codigo."<br>";
echo "Procedimento->".$proc_codigo."<br>";
echo "TipRel ->".$TipoRel."<br>";

if  ($TipoRel ==0)
    {
         $titulo="RELAT�RIO DE LABORAT�RIO E EXAMES CONTRATADOS POR ";    //       NOME DO RELAT�RIO
         $Proc_codigo=0;
    }
     else
    {
         $titulo="RELAT�RIO DE EXAMES CONTRATADOS POR LABORAT�RIO";    //       NOME DO RELAT�RIO
         $med_codigo=0;
    }
echo $titulo;    
$DtInicio = '';
$DtFinal = '';
echo $TipoRel;
if  ($TipoRel==1)   // POR LABORATORIO
{
    echo "estou dentro do LABORTORIO";
    if ($Proc_codigo==0){
    	$sql = "SELECT procedimento.proc_nome " .
    		"  FROM procedimento " .
    		" WHERE Procedimento.proc_codigo = $Proc_codigo";
    	$query=pg_query($sql);
    	while($row=pg_fetch_row($query)) 
    	{
		$UniNome=$row[0];
    	}
    }
    else 
    {  
    	$UniNome = "TODOS PROCEDIMENTOS";
    }
}
else
{
    if ($med_codigo) 
    {
    	$sql = "SELECT medico.med_nome,  med_tipoagendamento " .
    		"  FROM medico " .
    		" WHERE medico.med_codigo = $med_codigo";
    	$query=pg_query($sql);
    	while($row=pg_fetch_row($query)) 
    	{
    		$MedNome=$row[0];
    	}
    } 
	else 
    { 
    	$MedNome = "TODOS LABORAT�RIOS";
    }
}
// echo "Tipo Agendamento"."   ".$med_tipo;
 
//--- CABE�ALHO  RELAT�RIO

function cabeca($Tit, $dtIni, $dtFin, $MNome, $PNome)
{
echo "<hr>\n";
echo "<table width=100% border=0>\n";
echo "<tr>\n";
echo "<td width=10%><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/logo_apucarana.gif' width='60' height='60' > </td>\n";
echo "<td colspan=2 <font size=5 face=courier><b>GESTAO PUBLICA DE SAUDE</b></font></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td colspan=3><font size=2 face=courier div align='center'><b>$Tit</b></font></div></td> \n";
echo "</tr>\n";
//echo "<tr>\n";
//echo "<td> <font size=1 face=courier>PER&Iacute;ODO:</font></td>\n";
//echo "<td width=60%><font size=1 face=courier>$dtIni A $dtFin</font></td>\n";
echo "<td width=40><font size=1 face=courier>".date("d/m/Y h:i:s")."</font></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td><font size=1 face=courier>LABORAT&Oacute;RIO:</font></td>\n";
echo "<td colspan=2><font size=1 face=courier>$MNome</font></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td><font size=1 face=courier>UNIDADE:</font></td>\n";
echo "<td colspan=2><font size=1 face=courier> $PNome</font></td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<hr>";
 //---> IMPRESS�O DOS DADOS
echo " <table width=100% align=center cellspacing=0 cellpadding=0 border=0>\n";
echo " <tr>\n";
echo " </tr>\n";
}
$TotAgendaValor=0;

//------------------------------------------------

if  ($TipoRel ==0)
    {
    	{
		$sql="SELECT medico.med_codigo, medico.med_nome, medico.med_tipoagendamento
              FROM   medico
              WHERE  medico.med_codigo='$med_codigo'
             ORDER BY medico.med_nome";
        }
     }
	else
	{
		$sql="SELECT Procedimento.proc_codigo, procedimento.proc_nome, proc_valor
              FROM   Procedimento
              WHERE  Procedimento.proc_codigo=$proc_codigo";
	}

//-----------------------------------------------

    
//vSQL($sql,"1");

$lin = 999;

$query=pg_query($sql);

if (pg_num_rows($query) == 0) 
{
	echo "<hr>";
	echo "<font size=1 face=courier>N�O TEM DADOS PARA ESTES PAR�METROS<br><br>";
//	echo "<font size=1 face=courier>Data INICIAL->    ".$dt_inicial."<br>";
//	echo "<font size=1 face=courier>Data FINAL  ->    ".$dt_final."<br>";
	echo "<font size=1 face=courier>MEDICO      ->    ";if($med_codigo) { echo $med_codigo;}else{ echo 'TODOS';}     echo "<br>\n";
	echo "<font size=1 face=courier>UNIDADE     ->    ";if($uni_codigo) { echo $uni_codigo;}else{ echo 'TODOS';}     echo "<br>\n";
	echo "<font size=1 face=courier>TIPO RELATORIO -> ";if($TipoRel == 0){ echo 'Por Laborat�rio';}else{ echo 'Por Procedimento';} echo "<br>\n";
	echo "<hr>";
} 
else 
{
	while($row=pg_fetch_row($query)) 
	{
		if ($row[1] > 0) 
		{
			if ($lin == 999) 
			{
				cabeca($titulo, $dt_inicial, $dt_final, $MedNome, $ProcNome);
				$lin=0;
			}
			if ($TipoRel == 0)
			 {
               echo "<table width=100% cellspacing=0 cellpadding=0 border=0>\n";
			   echo "  <tr>\n";
			   echo "  <td width=15%><align=left><b> <font size=2 face=courier>&nbsp;  $row[0] </font></td>\n";
			   echo "  <td width=70%><align=center> <font size=2 face=courier><b>;$row[1] </font></b></td>\n";
               echo "  <td width=15%><align=center> <font size=2 face=courier><b>;$row[2] </font></b></td>\n";
			   echo "  </tr>\n";
             }
             else
             {
               echo "<table width=100% cellspacing=0 cellpadding=0 border=0>\n";
			   echo "  <tr>\n";
			   echo "  <td width=15%><align=left><b> <font size=2 face=courier>&nbsp;  $row[0] </font></td>\n";
			   echo "  <td width=70%><align=center> <font size=2 face=courier><b>;$row[1] </font></b></td>\n";
               echo "  <td width=15%><align=center> <font size=2 face=courier><b>;$row[2] </font></b></td>\n";
			   echo "  </tr>\n";
             }

	//		$TotAgenda=$TotAgenda+$row[1];   
			if ($TipoRel == 0)
			{
				$sqlEx="SELECT Procedimento.proc_codigo, procedimento.proc_nome, proc_valor
                        FROM   Procedimento, medico, laboratorio_procedimento
                        WHERE  procedimento.proc_codigo=laboratorio_procedimento.proc_codigo
                          and  medico.med_codigo=laboratorio_procedimento.med_codigo
                          and  medico.med_codigo=$med_codigo
                        ORDER BY procedimento.proc_nome";
             }
             else
             {
                $sqlEx="SELECT medico.med_codigo, medico.med_nome, medico.med_tipoagendamento
                        FROM   Procedimento, medico, laboratorio_procedimento
                        WHERE  procedimento.proc_codigo=laboratorio_procedimento.proc_codigo
                          and  medico.med_codigo=laboratorio_procedimento.med_codigo
                          and  procedimento.proc_codigo=$proc_codigo
                        ORDEER BY medico.med_nome";
              }

				$queryAgEx=pg_query($sqlEx);

				//				vsql($sqlEx,"1");
				if ((pg_num_rows($queryAgEx) > 0) || ($lin == 0))
				{
//					echo"</table>\n";
//					echo "<table width=100% align=center cellspacing=0 cellpadding=0 border=1>\n";
				}
				while($rowsqlEx=pg_fetch_row($queryAgEx)) 
				{
                        echo"</table>\n";
                        echo "<table width=100% align=center cellspacing=0 cellpadding=0 border=0>\n";
                        echo " <tr>\n";
					    echo "  <td width=15% align=left> <font size=1 face=courier> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  $rowsqlEx[0] </font></td>\n";
					    echo "  <td width=70%><align=right> <font size=1 face=courier>&nbsp;$rowsqlEx[1]</font> </td>\n";
					    echo "  <td width=15% align=left > <font size=1 face=courier>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;$rowsqlEx[2]</font> </td>\n";
					    echo "</table>";
					echo " </tr>\n";
					++$lin;
				}
    //				echo "<table><tr><td><hr>&nbsp;</td></tr></table> \n";
	    //			echo "<hr>\n";
//		   	    	echo "<table width=90% align=center cellspacing=0 cellpadding=0 border=1>\n";
		     		$lin=0;
//			     	echo " <tr>\n";
     //				echo "  <font size=2 face=courier><td align=right>Total Valor ->&nbsp;&nbsp;</td>\n";
     //				echo "  <font size=2 face=courier><td align=right>$TotAgendaValor</td>\n";
     //				echo "  <td width=110 align=right>        </td>\n";
     //				echo " </tr>\n";
     //				echo "</table>";
                   echo "<br>";
                   echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Valor ->  R$ ". $TotAgendaValor;
	               echo "<hr>\n";

			}
		}

}

?>

