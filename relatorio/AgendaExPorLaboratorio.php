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
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
//----------------  Dados Recebidos  ---------------->

//echo "Data Inicial->".$dt_inicial."<br>";
//echo "Data Final ->".$dt_final."<br>";
//echo "Medico->".$med_codigo."<br>";
//echo "Unidade->".$uni_codigo."<br>";
//echo "TipRel ->".$TipRel."<br>";

$titulo="AGENDAMENTOS DE EXAMES POR LABORATÓRIOS E POR UNIDADES";    //       NOME DO RELATÓRIO
$dt_i=$dt_inicial;
$dt_f=$dt_final;

if ($uni_codigo) 
{
	$sql = "SELECT unidade.uni_desc " .
 		"  FROM unidade " .
		" WHERE unidade.uni_codigo = $uni_codigo";
	$query=pg_query($sql);
	while($row=pg_fetch_row($query)) 
	{
		$UniNome=$row[0];
	}
}
else 
{  
	$UniNome = "TODAS";  
}

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
	$MedNome = "TODOS";
}

 echo "Tipo Agendamento"."   ".$med_tipo;
 
//--- CABEÇALHO  RELATÓRIO
function cabeca($Tit, $dtIni, $dtFin, $MNome, $UNome) 
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
echo "<tr>\n";
echo "<td> <font size=1 face=courier>PER&Iacute;ODO:</font></td>\n";
echo "<td width=60%><font size=1 face=courier>$dtIni A $dtFin</font></td>\n";
echo "<td width=40><font size=1 face=courier>".date("d/m/Y h:i:s")."</font></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td><font size=1 face=courier>LABORAT&Oacute;RIO:</font></td>\n";
echo "<td colspan=2><font size=1 face=courier>$MNome</font></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td><font size=1 face=courier>UNIDADE:</font></td>\n";
echo "<td colspan=2><font size=1 face=courier> $UNome</font></td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<hr>";
 //---> IMPRESSÃO DOS DADOS
echo " <table width=100% align=center cellspacing=0 cellpadding=0 border=0>\n";
echo " <tr>\n";
echo " </tr>\n";
}
$TotAgendaValor=0;
if ($med_codigo) 
{
	if ($uni_codigo) 
	{
		$sql="select medico.med_nome,  count(*) , medico.med_tipoagendamento
			from agendamento_exame, agente, agendamento_exame_lista, medico
			where agexl_data between '$dt_inicial' and '$dt_final' 
			  and agendamento_exame.agt_codigo=agente.agt_codigo
			  and medico.med_codigo=agendamento_exame_lista.med_codigo
			  and agendamento_exame.agex_codigo=agendamento_exame_lista.agex_codigo
			  and agente.uni_codigo='$uni_codigo'
			  and medico.med_codigo='$med_codigo'
			GROUP BY medico.med_nome ,medico.med_tipoagendamento";
	}
	else 
	{
		$sql="select medico.med_nome,  count(*) , medico.med_tipoagendamento
			from agendamento_exame, agente, agendamento_exame_lista, medico
			where agexl_data between '$dt_inicial' and '$dt_final'
			  and agendamento_exame.agt_codigo=agente.agt_codigo
			  and medico.med_codigo=agendamento_exame_lista.med_codigo
			  and agendamento_exame.agex_codigo=agendamento_exame_lista.agex_codigo
			  and medico.med_codigo='$med_codigo'
			GROUP BY medico.med_nome ,medico.med_tipoagendamento";
	}
}
else 
{
	if ($uni_codigo) 
	{
 		$sql="select medico.med_nome,  count(*) , medico.med_tipoagendamento
			from agendamento_exame, agente, agendamento_exame_lista, medico
			where agexl_data between '$dt_inicial' and '$dt_final'
			  and agendamento_exame.agt_codigo=agente.agt_codigo
			  and medico.med_codigo=agendamento_exame_lista.med_codigo
			  and agendamento_exame.agex_codigo=agendamento_exame_lista.agex_codigo
			  and agente.uni_codigo='$uni_codigo'
			GROUP BY medico.med_nome, medico.med_tipoagendamento ";
	}
	else
	{
		$sql="select medico.med_nome,  count(*) , medico.med_tipoagendamento
			from agendamento_exame, agente, agendamento_exame_lista, medico
			where agexl_data between '$dt_inicial' and '$dt_final'
			  and agendamento_exame.agt_codigo=agente.agt_codigo
			  and medico.med_codigo=agendamento_exame_lista.med_codigo
			  and agendamento_exame.agex_codigo=agendamento_exame_lista.agex_codigo
			GROUP BY medico.med_nome, medico.med_tipoagendamento ";
	}
}
    
//vSQL($sql,"1");

$lin = 999;

$query=pg_query($sql);

if (pg_num_rows($query) == 0) 
{
	echo "<hr>";
	echo "<font size=1 face=courier>NÃO TEM DADOS PARA ESTES PARÂMETROS<br><br>";
	echo "<font size=1 face=courier>Data INICIAL->    ".$dt_inicial."<br>";
	echo "<font size=1 face=courier>Data FINAL  ->    ".$dt_final."<br>";
	echo "<font size=1 face=courier>MEDICO      ->    ";if($med_codigo) { echo $med_codigo;}else{ echo 'TODOS';}     echo "<br>\n";
	echo "<font size=1 face=courier>UNIDADE     ->    ";if($uni_codigo) { echo $uni_codigo;}else{ echo 'TODOS';}     echo "<br>\n";
	echo "<font size=1 face=courier>TIPO RELATORIO -> ";if($TipRel == 0){ echo 'Sintetico';}else{ echo 'Analitico';} echo "<br>\n";
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
				cabeca($titulo, $dt_inicial, $dt_final, $MedNome, $UniNome);
				$lin=0;
			}
			echo "<table width=100% cellspacing=0 cellpadding=0 border=0>\n";
			echo "  <tr>\n";
			echo "  <td width=71%><align=left><b> <font size=2 face=courier>&nbsp;  $row[0] </font></td>\n";
			echo "  <td width=29%><align=center> <font size=2 face=courier><b> Agendamentos: &nbsp;&nbsp;&nbsp;$row[1] </font></b></td>\n";
			echo "  </tr>\n";

	//		$TotAgenda=$TotAgenda+$row[1];   
			if ($TipRel == 1)
			{
				$sqlEx="Select procedimento.proc_nome,  Count(*), Proc_valor*count(*), medico.med_tipoagendamento
					from agendamento_exame, 
						agendamento_exame_lista, 
						procedimento, 
						medico, 
						agente
					where agendamento_exame.agex_codigo = agendamento_exame_lista.agex_codigo
					  and agendamento_exame.agt_codigo = agente.agt_codigo
					  and agendamento_exame_lista.proc_codigo = procedimento.proc_codigo
					  and agendamento_exame_lista.med_codigo=medico.med_codigo
					  and medico.med_nome = '$row[0]'
					  and agendamento_exame_lista.agexl_data between '$dt_inicial' and '$dt_final' ";

				if ($uni_codigo) 
				{
					$sqlEx.="amd agendamento_exame_lista = '$uni_codigo'";
				}
				$sqlEx.="GROUP BY Saude.procedimento.proc_nome, proc_valor, med_tipoagendamento ";

				$queryAgEx=pg_query($sqlEx);

				//				vsql($sqlEx,"1");
				if ((pg_num_rows($queryAgEx) > 0) || ($lin == 0))
				{
//					echo"</table>\n";
//					echo "<table width=100% align=center cellspacing=0 cellpadding=0 border=1>\n";
				}
				while($rowsqlEx=pg_fetch_row($queryAgEx)) 
				{
  					if 	($rowsqlEx[3]=='V')
					{
					    $TotAgendaValor=$TotAgendaValor+$rowsqlEx[2];
                        echo"</table>\n";
                        echo "<table width=100% align=center cellspacing=0 cellpadding=0 border=0>\n";
                        echo " <tr>\n";
					    echo "  <td width=80% align=left> <font size=1 face=courier> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  $rowsqlEx[0] </font></td>\n";
					    echo "  <td width=5%><align=right> <font size=1 face=courier>&nbsp;$rowsqlEx[1]</font> </td>\n";
					    echo "  <td width=15% align=left > <font size=1 face=courier>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;$rowsqlEx[2]</font> </td>\n";
					    echo "</table>";
					    $Mvlr = 'S';
					}
					else
                    {
                        echo"</table>\n";
                        echo "<table width=100% align=center cellspacing=0 cellpadding=0 border=0>\n";
                        echo " <tr>\n";
					    echo "  <td width=85% align=left> <font size=1 face=courier> $row[2]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  $rowsqlEx[0] </font></td>\n";
					    echo "  <td width=15% align=left > <font size=1 face=courier>&nbsp;$rowsqlEx[1]</font> </td>\n";
					    echo "</table>";
                    }
					echo " </tr>\n";
					++$lin;
				}
    //				echo "<table><tr><td><hr>&nbsp;</td></tr></table> \n";
				if 	($Mvlr=='S')
				{
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
	               $TotAgendaValor=0;
	               echo "<hr>\n";
                   $Mvlr='N';
				}
				else
				{
				echo "<hr><br>";
				}
			}
		}
	}
}
?>
