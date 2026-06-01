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

if  ($TipoRel ==0)
{
    $titulo="RELATÓRIO DE LABORATÓRIOS E PROCEDIMENTOS CONTRATADOS";    //       NOME DO RELATÓRIO
}
else
{
    $titulo="RELATÓRIO DE PROCEDIMENTOS CONTRATADOS POR LABORATÓRIO";    //       NOME DO RELATÓRIO
}

if ($proc_codigo)
{
	$sqlnome = "SELECT Procedimento.proc_nome 
 		   FROM procedimento 
		   WHERE Procedimento.proc_codigo = '$proc_codigo'";
	$querynome=pg_query($sqlnome);
	while($rownome=pg_fetch_row($querynome)) 
	{
		$ProcNome=$rownome[0];
	}
}
else 
{  
	$ProcNome = "TODOS";  
}

if ($med_codigo) 
{
	$sqlnome = "SELECT medico.med_nome,  medico.med_tipoagendamento " .
		"  FROM medico " .
		" WHERE medico.med_codigo = $med_codigo";
	$querynome=pg_query($sqlnome);
	while($rownome=pg_fetch_row($querynome)) 
	{
		$MedNome=$rownome[0];
	}
} 
	else 
{ 
	$MedNome = "TODOS";
}


//--- CABEÇALHO  RELATÓRIO

function cabeca($Tit, $dtIni, $dtFin, $MNome, $PNome) 
{
echo "<hr>\n";
echo "<table width=100% border=0>\n";
echo "<tr>\n";
echo "<td width=10%><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/logo_apucarana.gif' width='60' height='60' > </td>\n";
echo "<td colspan=2 <font size=5 face=courier><b>GESTAO PUBLICA DE SAUDE</b></font></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td width=80% colspan=2><font size=3 face=courier div align='center'><b>$Tit</b></font></div></td> \n";
echo "<td width=20%><font size=1 face=courier>".date("d/m/Y h:i:s")."</font></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td><font size=1 face=courier>LABORAT&Oacute;RIO:</font></td>\n";
echo "<td colspan=2><font size=1 face=courier>$MNome</font></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td><font size=1 face=courier>PROCEDIMENTO:</font></td>\n";
echo "<td colspan=2><font size=1 face=courier> $PNome</font></td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<hr>";
 //---> IMPRESSÃO DOS DADOS
echo " <table width=100% align=center cellspacing=0 cellpadding=0 border=0>\n";
echo " <tr>\n";
echo " </tr>\n";
}

if  ($TipoRel==0)
{
	if ($med_codigo) 
	{
		$sqlnivel_1="select medico.med_nome, medico.med_tipoagendamento, medico.med_codigo
			from medico
			where medico.med_codigo='$med_codigo'";
	}
	else     
    	{
		$sqlnivel_1="select medico.med_nome, medico.med_tipoagendamento, medico.med_codigo
			from medico
			where medico.prestador_servico='S'
			order by medico.med_nome";			
	}
}
else 
{
	if ($proc_codigo) 
	{
 		$sqlnivel_1="select procedimento.proc_nome, procedimento.proc_valor,procedimento.proc_codigo
			from procedimento
			where procedimento.proc_codigo='$proc_codigo'";
	}
 	else   
    	{	
 		$sqlnivel_1="select procedimento.proc_nome, procedimento.proc_valor,procedimento.proc_codigo
			from procedimento
			where procedimento.proc_ativo='A'";			
	}
}

//vSQL($sql,"1");

$lin = 999;
$querynivel_1=pg_query($sqlnivel_1);
if (pg_num_rows($querynivel_1) == 0) 
{
	echo "<hr>";
	echo "<font size=1 face=courier>NÃO TEM DADOS PARA ESTES PARÂMETROS<br><br>";
	echo "<font size=1 face=courier>MEDICO      ->    ";
	if($med_codigo)
	{
		echo $med_codigo;
	}
	else
	{
		echo "TODOS OS LABORATÓRIOS";
	}
	echo "<br>\n";
	echo "<font size=1 face=courier>PROCEDIMENTO    ->    ";
	if($proc_codigo)
	{
		echo $proc_codigo;
	}
	else
	{ 
		echo "TODOS OS PROCEDIMENTOS";
	}
	echo "<br>\n";
	echo "<font size=1 face=courier>TIPO RELATORIO -> ";
	if($TipoRel == 0)
	{
		echo "LABORATORIOS E SEUS PROCEDIMENTOS CONTRATADOS";
	}
	else 
	{
		echo "PROCEDIMENTOS E LABORATORIOS CONTRATADOS PARA EXECUÇÃO";
	}
	echo "<br>\n";
	echo "<hr>";
}
else 
{
	while($nivel_1=pg_fetch_row($querynivel_1)) 
	{
		if ($lin == 999) 
		{
			cabeca($titulo, $dt_inicial, $dt_final, $MedNome, $ProcNome);
			$lin=0;
		} 
		if ($TipoRel == 0)   //  Por Laboratorio   --  contar procedimentos para o Laboratorio
		{
			$sqlcontar= "select distinct COUNT(laboratorio_procedimento.proc_codigo) 
						from medico, laboratorio_procedimento 
						where medico.med_codigo=laboratorio_procedimento.med_codigo
						  and laboratorio_procedimento.med_codigo='$nivel_1[2]'
						GROUP BY laboratorio_procedimento.proc_codigo";          
            $querycontar=pg_query($sqlcontar);
            while($rowcontar=pg_fetch_row($querycontar))
            {
			//ver como usar o valor recebido de um SQL... par comparar nos IF´s
                $QtRow=$rowcontar[0];
            }
		} 
		else
		{
			$sqlcontar= "select distinct COUNT(medico.med_codigo)
						from medico, laboratorio_procedimento 
						where medico.med_codigo=laboratorio_procedimento.med_codigo
						  and laboratorio_procedimento.proc_codigo='$nivel_1[2]' 
						GROUP BY medico.med_codigo";
			$querycontar=pg_query($sqlcontar);
            while($rowcontar=pg_fetch_row($querycontar))
            {
			//ver como usar o valor recebido de um SQL... par comparar nos IF´s
                $QtRow=$rowcontar[0];
            }
		}
		if ($QtRow > 0)
		{
			echo "<table width=100% cellspacing=0 cellpadding=0 border=0>\n";
	//		echo "<hr>";
			echo "<tr>\n";
			echo "<td width=10%><align=left><b> <font size=4 face=courier>&nbsp;$nivel_1[2] </font></td>\n";
			echo "<td width=75%><align=center> <font size=2 face=courier><b> $nivel_1[0] </td>\n"; 
			if($TipoRel == 0)
			{
				echo "<td width=15%><align=center> <font size=1 face=courier><b>Tipo de Contrato: $nivel_1[1] </font></b></td>\n </tr>";
			}
			else
			{ 
				echo "<td width=15%><align=center> <font size=1 face=courier><b>Valor: $nivel_1[1] </font></b></td>\n </tr>";
			}
			echo "<hr>";
			$QtRow = 0;
			// proc_classificacao_sus
			if ($TipoRel == 0)   // Por Laboratorio
			{
			    //modificado a pedido da Geise em 06-09-08
			    	//	$sqlEx="Select procedimento.proc_codigo, procedimento.proc_nome,  Proc_valor
				//	from procedimento, laboratorio_procedimento 
				//	where procedimento.proc_codigo=laboratorio_procedimento.proc_codigo
				//	  and laboratorio_procedimento.med_codigo='$nivel_1[2]'
				//	order by procedimento.proc_codigo";
				$sqlEx="Select procedimento.proc_classificacao_sus, procedimento.proc_nome,  Proc_valor
					from procedimento, laboratorio_procedimento 
					where procedimento.proc_codigo=laboratorio_procedimento.proc_codigo
					  and laboratorio_procedimento.med_codigo='$nivel_1[2]'
					order by procedimento.proc_nome";
				$queryAgEx=pg_query($sqlEx);
				while($rowsqlEx=pg_fetch_row($queryAgEx)) 
				{
					echo "</table>\n";
					echo "<table width=100% align=center cellspacing=0 cellpadding=0 border=0>\n";
					echo "<tr>\n";
				    echo "<td width=90% align=left> <font size=1 face=courier>  $rowsqlEx[0] - $rowsqlEx[1] </font></td>\n";
				    echo "<td width=10% align=left > <font size=1 face=courier>&nbsp;&nbsp;&nbsp; $rowsqlEx[2]</font> </td>\n";
				    echo "</table>";
				}
			}
			else
			{
				$sqlEx="Select medico.med_codigo, medico.med_nome,  medico.med_tipoagendamento
						from medico, laboratorio_procedimento 
						where medico.med_codigo=laboratorio_procedimento.med_codigo
						  and laboratorio_procedimento.proc_codigo='$nivel_1[2]' 
						order by medico.med_codigo";
				$queryAgEx=pg_query($sqlEx);
				while($rowsqlEx=pg_fetch_row($queryAgEx)) 
				{
			        echo "</table>\n";
			        echo "<table width=100% align=center cellspacing=0 cellpadding=0 border=0>\n";
			        echo "<tr>\n";
				    echo "<td width=90% align=left> <font size=1 face=courier> $rowsqlEx[0] - $rowsqlEx[1]</font></td>\n";
				    echo "<td width=10% align=left > <font size=1 face=courier>&nbsp;&nbsp;&nbsp; $rowsqlEx[2]</font> </td>\n";
				    echo "</table>";
			    }
			}
		}
	}
	echo "<hr><br>";
	echo "FINAL DO RELATÓRIO";
	echo "<hr><br>";
}
 //	echo "<hr><br>";
	
?>
