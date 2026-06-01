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
//echo "inicio: " . $dt_inicial;
//echo "    Final: " . $dt_final;
//echo "    Laboratorio: " . $med_codigo;
//echo "    Procedimento: " . $proc_codigo;
//echo "    Organizado por: " . $TipRel;
//echo "    Tipo Relatorio: " . $SinAnal;

//&med_codigo='+gMedico+'proc_codigo='+gProc+'&TipRel='+gTipoRel+'&SinAnal=
$TipoRel = 0;

if  ($TipoRel ==0)
{
       if ($SinAnal == 0)
       {
              $titulo="RELATÓRIO SINTÉTICO DE QUANTIDADE DE AGENDAMENTOS POR LABORATÓRIOS POR PERÍODO";    //       NOME DO RELATÓRIO
       }
       else
       {
              $titulo="RELATÓRIO ANALÍTICO DE QUANTIDADE DE AGENDAMENTOS POR LABORATÓRIOS POR PERÍODO";
       }
}
else
{
       if ($SinAnal == 0)
       {
              $titulo="RELATÓRIO SINTÉTICO DE QUANTIDADE DE AGENDAMENTOS POR PROCEDIMENTOS POR PERÍODO";    //       NOME DO RELATÓRIO
       }
       else
       {
              $titulo="RELATÓRIO ANALÍTICO DE QUANTIDADE DE AGENDAMENTOS POR PROCEDIMENTOS POR PERÍODO";
       }
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
//echo "<br>\n";
//echo $ProcNome;

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

//echo "<br>\n";
//echo $MedNome;

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
echo "<td width=80% colspan=2><font size=2 face=courier div align='center'><b>$Tit</b></font></div></td> \n";
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
echo "<tr>\n";
echo "<td><font size=1 face=courier>PER&Iacute;ODO:</font></td>\n";
echo "<td><font size=1 face=courier>".$dtIni." at&eacute ".$dtFin."</font></td>\n";
echo "</tr>\n";
echo "</table>\n";
//echo "<hr>";
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

//vSQL($sqlnivel_1,'1');
//echo $sqlnivel_1;
$lin = 999;
$querynivel_1=pg_query($sqlnivel_1);
if (pg_num_rows($querynivel_1) == 0) 
{
	echo "<hr>";
	echo "<font size=1 face=courier>NÃO TEM DADOS PARA ESTES PARÂMETROS<br><br>";
	echo "<font size=1 face=courier>MEDICO :    ";
	if($med_codigo)
	{
		echo $med_codigo;
	}
	else
	{
		echo "TODOS OS LABORATÓRIOS";
	}
	echo "<br>\n";
	echo "<font size=1 face=courier>PROCEDIMENTO:    ";
	if($proc_codigo)
	{
		echo $proc_codigo;
	}
	else
	{ 
		echo "TODOS OS PROCEDIMENTOS";
	}
	echo "<br>\n";
	echo "<font size=1 face=courier>TIPO RELATORIO: ";
	echo $titulo;
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
				  and laboratorio_procedimento.med_codigo='$nivel_1[2]'";
		     if ($proc_codigo)
			{
			    $sqlcontar = $sqlcontar . "and laboratorio_procedimento.proc_codigo = '$proc_codigo' ";
			}
		     $sqlcontar = $sqlcontar . "GROUP BY laboratorio_procedimento.proc_codigo";
		     //echo $sqlcontar;
		     $querycontar=pg_query($sqlcontar);
             //      echo $sqlcontar;
                     while($rowcontar=pg_fetch_row($querycontar))
                     {
                            //ver como usar o valor recebido de um SQL... par comparar nos IF´s
                            $QtRow=$rowcontar[0];
             //               echo "<br>";
             //               echo $QtRow;                            
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
       //                     echo "<br>";
       //                     echo $QtRow;
                     }
	      }
              if ($QtRow > 0)
              {
              /*       echo "<table width=100% cellspacing=0 cellpadding=0 border=0>\n";
	//	     echo "<hr>";
                     echo "<tr>\n";
                     echo "<td width=10%><align=left><b> <font size=4 face=courier>&nbsp;$nivel_1[2] </font></td>\n";
		     echo "<td width=75%><align=center> <font size=2 face=courier><b> $nivel_1[0] </td>\n";  
                     if($TipoRel == 0)
		     {
                            echo "<td width=15%><align=center> <font size=1 face=courier><b>Tipo de Contrato: $nivel_1[1] </font></b></td>\n </tr>";
		     }/
                     else
                     { 
                            echo "<td width=15%><align=center> <font size=1 face=courier><b>Valor: $nivel_1[1] </font></b></td>\n </tr>";
		     }
                     echo "<hr>"; */
                     $QtRow = 0;
                     if ($TipoRel == 0)   // Por Laboratorio
                     {
                            $SqlContProc = "SELECT count(agendamento_exame_lista.proc_codigo)
                                            FROM agendamento_exame_lista
                                            WHERE med_codigo = '$nivel_1[2]'
                                              and agexl_data between '$dt_inicial' and '$dt_final'";
			    if ($proc_codigo)
			    {
				$SqlContProc = $SqlContProc . "and agendamento_exame_lista.proc_codigo = '$proc_codigo' ";
			    }
                                     //echo  $SqlContProc;
                                   $queryContlab = pg_query($SqlContProc);
                            while ($rowsqllab=pg_fetch_row($queryContlab))
                            {
                                   $ttLab = $rowsqllab[0]; 
                                  // echo "rowsqllab: " . $ttLab;
                                   if ($ttLab > 0)
                                   {
                                          echo "<table width=100% cellspacing=0 cellpadding=0 border=0>\n";
	//	                          echo "<hr>";
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
 
 
                                         // echo "sql para ver a quantidade: " . $queryContProc;
                                          echo "</table>\n";
                                          echo "<table width=100% align=center cellspacing=0 cellpadding=0 border=0>\n";
                                          echo "<tr>\n";
                                          //echo "<hr>";
                                          echo "<tr>\n";
                                          echo "<td width=7% align=left> <font size=1 face=courier>  Código</font></td>\n";
                                          echo "<td width=65% align=left> <font size=1 face=courier>Procedimento </font></td>\n";
                                          echo "<td width=10% align=right > <font size=1 face=courier>Custo</font> </td>\n";
                                          echo "<td width=5% align=right > <font size=1 face=courier>Qtde</font> </td>\n";
                                          echo "<td width=13% align=right > <font size=1 face=courier>Vlr Total</font> </td>\n";
                                          echo "<tr>\n";
                                          echo "</table>\n";
                                          echo "<hr>\n";
					  //alteração pedida por geise em 06-09-08   execução Lucio sanches
					  
					  //$sqlEx="Select procedimento.proc_codigo, procedimento.proc_nome,  Proc_valor
                                          //	from procedimento, laboratorio_procedimento 
                                          //	where procedimento.proc_codigo=laboratorio_procedimento.proc_codigo
                                          //	  and laboratorio_procedimento.med_codigo='$nivel_1[2]'";
						//  if ($proc_codigo)
						//{
						//    $sqlEx = $sqlEx . "and procedimento.proc_codigo = '$proc_codigo' ";
						//}
                                                // $sqlEx = $sqlEx . "group by procedimento.proc_codigo, procedimento.proc_nome,  Proc_valor
                                          	//order by procedimento.proc_codigo";
					  
                                          $sqlEx="Select procedimento.proc_codigo, procedimento.proc_nome,  Proc_valor,procedimento.proc_classificacao_sus 
                                          	from procedimento, laboratorio_procedimento
                                          	where procedimento.proc_codigo=laboratorio_procedimento.proc_codigo
                                          	  and laboratorio_procedimento.med_codigo='$nivel_1[2]'";
						  if ($proc_codigo)
						{
						    $sqlEx = $sqlEx . "and procedimento.proc_codigo = '$proc_codigo' ";
						}
                                                 $sqlEx = $sqlEx . "group by procedimento.proc_codigo, procedimento.proc_nome,  Proc_valor,procedimento.proc_classificacao_sus
                                          	order by procedimento.proc_nome";
						
						//echo $sqlEx;
                                          $queryAgEx=pg_query($sqlEx);
					  $VlrTTProc = 0;
					  $TTQtProc  = 0;
                                          while($rowsqlEx=pg_fetch_row($queryAgEx)) 
                                          {
                                                 $SqlContProc = "SELECT count(agendamento_exame_lista.proc_codigo)
                                                                 FROM agendamento_exame_lista
                                                                 WHERE med_codigo = '$nivel_1[2]'
                                                                   and agexl_data between '$dt_inicial' and '$dt_final'
                                                                   and proc_codigo = '$rowsqlEx[0]'";
                                                 $queryContProc = pg_query($SqlContProc);
                                                 while($ContProc=pg_fetch_array($queryContProc))
                                                 {
                                                        $totProc = $ContProc[0];
                                                        if ($totProc > 0)
                                                        {
                                                               $total =   $rowsqlEx[2] * $totProc;
							       $VlrTTProc = $VlrTTProc + $total;
							       $TTQtProc  = $TTQtProc  + $totProc;
							       
                                                               $TTForma = number_format($total, 2, '.',',');
                                                               echo "</table>\n";
                                                               echo "<table width=100% align=center cellspacing=0 cellpadding=0 border=0>\n";
                                                               echo "<tr>\n";
							       
							       if ($SinAnal == 1)
								{
								    echo "<br><br>\n";
								    //echo "<td width=7% align=left> <font size=1 face=courier>  $rowsqlEx[0]-</font></td>\n";
								    echo "<td width=10% align=left> <font size=1 face=courier>  $rowsqlEx[3] -</font></td>\n";
								    echo "<td width=65% align=left> <font size=0><b> <face=courier>$rowsqlEx[1] </b></font></td>\n";
								}
								else
								{
								    //echo "<td width=7% align=left> <font size=1 face=courier>  $rowsqlEx[0]-</font></td>\n";
								    echo "<td width=10% align=left> <font size=1 face=courier>  $rowsqlEx[3] -</font></td>\n";
								    echo "<td width=65% align=left> <font size=0 face=courier>$rowsqlEx[1] </font></td>\n";
								}
                                                               echo "<td width=10% align=right > <font size=1 face=courier> $rowsqlEx[2]</font> </td>\n";
                                                               echo "<td width=5% align=right > <font size=1 face=courier>$totProc</font> </td>\n";
                                                               echo "<td width=13% align=right > <font size=1 face=courier>$TTForma</font> </b></td>\n";
                                                               echo "<tr>\n";
                                                               echo "</table>";
							       // DAQUI PRA FRENTE O ANALÍTICO //
							       if ($SinAnal == 1)
							       {
								    echo "</table>\n";
                                                                    echo "<table width=100% align=center cellspacing=0 cellpadding=0 border=0>\n";
                                                                    echo "<tr>\n";
                                                                    echo "<td width=10% align=left> <font size=1 face=courier>  Prontuário</font></td>\n";
								    echo "<td width=40% align=left> <font size=1 face=courier> Paciente </font></td>\n";
								    echo "<td width=40% align=left > <font size=1 face=courier> Nome da Mãe</font> </td>\n";
								    echo "<td width=10% align=left > <font size=1 face=courier>Dt Agenda</font> </td>\n";
								    echo "<tr>\n";
								    //echo "</table>";
							           $SqlPaciente = "Select usuario.usu_prontuario, usuario.usu_nome,
								                        usuario.usu_mae, to_char(agendamento_exame_lista.agexl_data, 'dd-mm-yy')
								   FROM agendamento_exame_lista, usuario
								   WHERE agendamento_exame_lista.usu_codigo = usuario.usu_codigo
								     and med_codigo = '$nivel_1[2]'
								     and agexl_data between '$dt_inicial' and '$dt_final'
								     and agendamento_exame_lista.proc_codigo=$rowsqlEx[0]
								   order by agendamento_exame_lista.agexl_data,usuario.usu_nome";
								   //echo $SqlPaciente;
								   $querySqlPaciente = pg_query($SqlPaciente);
								   while($ListPac=pg_fetch_array($querySqlPaciente))
								   {
								   // echo "</table>\n";
                                                                   // echo "<table width=100% align=center cellspacing=0 cellpadding=0 border=0>\n";
                                                                    echo "<tr>\n";
                                                                    echo "<td width=10% align=left> <font size=1 face=courier> $ListPac[0]</font></td>\n";
								    echo "<td width=40% align=left> <font size=1 face=courier> $ListPac[1]</font></td>\n";
								    echo "<td width=40% align=left > <font size=1 face=courier> $ListPac[2]</font> </td>\n";
								    echo "<td width=10% align=right > <font size=1 face=courier> $ListPac[3]</font> </td>\n";
								    echo "<tr>\n";
								    //echo "</table>";
								    
								     //echo $SqlPaciente;
								   } 
							       }
                                                        }
                                                 }
                                          }
					  if ($SinAnal == 0)
					  {
					  echo "</table>\n";
					  echo "<hr>\n";
					  echo "<table width=100% align=center cellspacing=0 cellpadding=0 border=0>\n";
                                          echo "<tr>\n";
					  echo "<td width=65% align=left> <b><font size=1 face=courier>Total Valores: </font></td>\n";
                                          echo "<td width=10% align=right > <font size=1 face=courier>$TTQtProc</font> </td>\n";
					  echo "<td width=10% align=right > <font size=1 face=courier>" . number_format($VlrTTProc, 2, '.',',') . "</font> </td>\n";
					  echo "<tr></table>\n";
					  echo "<hr>\n";
					  }
					  else
					  {
					    echo "<tr>\n";
					   // echo "</table>\n";
					  }
					  
                                   }
			    }
			
                     }
		     // p
              }  //ap
       }  //ap
}//ap
 
  	echo "<table>\n";
   	echo "<hr><br>";
	echo "FINAL DO RELATÓRIO";
 	echo "<hr><br>";
	
?>
