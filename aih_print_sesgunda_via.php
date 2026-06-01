<?php
/**
 * Imprime a 'sesgunda' via da AIH 
*/ 
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();
?>
<html>
<head><title>AIH</title>

<style type='text/css' media='all'>
<!--
body{
	display:none; 
}
-->
</style>

<style type='text/css' media='print'>
<!--
.pg_print {
	font-size:20px; 
	font-family:Arial;
}
body{
	font-size:20px; 
	display:block; 
}
-->
</style>
</head>

<body onLoad="self.print()">
<?
echo"

	<input type='hidden' name='aih_codigo' value='$aih_codigo' /><br />";

	
	//-----------------------------------------------------------------------------------------------
	$sq = 	"SELECT 
			
				(CASE WHEN a.pac_aih_codigo IS NOT NULL THEN p1.pac_nome
				WHEN a.usu_codigo IS NOT NULL THEN p0.usu_nome
				ELSE 'none' END) AS nome_paciente, 
				
				(CASE WHEN a.pac_aih_codigo IS NOT NULL THEN p1.pac_prontuario
				WHEN a.usu_codigo IS NOT NULL THEN p0.usu_prontuario
				ELSE 'none' END) AS prontuario, 
			
				(CASE WHEN a.pac_aih_codigo IS NOT NULL THEN COALESCE(p1.pac_ibge_codigo,'NÃO TEM')
				WHEN a.usu_codigo IS NOT NULL THEN COALESCE(p0.muni_cd_cod_ibge_resid,'NÃO TEM') 
				ELSE 'none' END) AS ibge, 
			
				proc.proc_classificacao_sus, aih_n_doc_prof_autorizador, med_codigo_solicitante, aih_numero_aih, aih_prontuario_hospital, aih_mes_compet, aih_ano_compet
				
			FROM aih AS a
			
				LEFT JOIN usuario AS p0 ON p0.usu_codigo = a.usu_codigo
				LEFT JOIN aih_paciente AS p1 ON p1.pac_codigo = a.pac_aih_codigo
			
				LEFT JOIN procedimento AS proc ON proc.proc_codigo = a.aih_desc_proc_soli
				
			WHERE aih_codigo=$aih_codigo ";
		
	$row = db_query($sq);
	$res = pg_fetch_array($row);

	//-----------------------------------------------------------------------------------------------
	$stmt = "SELECT med_cnpj FROM medico WHERE med_codigo=".$res['med_codigo_solicitante'];
	$query = db_query($stmt);
	$row = pg_fetch_array($query);
	
	//-----------------------------------------------------------------------------------------------
	$numero_aih = $res['aih_numero_aih'];
	//list ($num, $digito) = split ('[-]', $numero_aih);
	$num = $numero_aih;
	
	$data = date('d/m/Y H:m:s');
		
echo"
	<table width='800' border='0' cellspacing='2' cellpadding='2' align='center'>
		  <tr>
			<td><div align='center'><span class='pg_print'>SEGUNDA VIA <br>
			  *----------------------------------------------------------------------------------------------------------------------------------*</span>
			  </div>
			  <table width='800' border='0' cellpadding='0' cellspacing='0' class='pg_print'>
                <tr>
                  <td width='1%'>&nbsp;</td>
                  <td width='43%'><p align='center'><font size='2'>SECRETARIA MUNICIPAL DA SAUDE</font><br />
                    <font size='2'>SUS - SISTEMA UNICO DE SAUDE</p>
                  <p align='center' class='style2'><font size='2'>AUTORIZA&Ccedil;&Atilde;O DE INTERNA&Ccedil;&Atilde;O HOSPITALAR</p></td>
                  <td colspan='2'><blockquote>
                      <p align='left'><font size='2'>Numero da AIH: "; print $num; echo " </font><br />";

                      //  <font size='2'>Identifica&ccedil;&atilde;o: "; print $digito; echo "</font></p>
                      echo"  <font size='2'>Identifica&ccedil;&atilde;o: 7 </font></p>
                  </blockquote></td>
                  <td width='0%'>&nbsp;</td>
                </tr>
                <tr>
                  <td colspan='5'><div align='center'></div></td>
                </tr>
                <tr>
                  <td width='1%' rowspan='8'>&nbsp;</td>
                  <td width='43%'><div align='center'><font size='2'>SO PODE SER FORNECIDO MEDIANTE<br />
                  APRESENTACAO DE LAUDO</font></div></td>
                  <td colspan='2'><div align='left'>
                      <blockquote>
                        <p><font size='2'>Numero do Prontuario/Same: "; print $res['aih_prontuario_hospital']; echo "<br />
                          Orgao Emissor.....................: PR.40.245 </font></p>
                      </blockquote>
                  </div></td>
                  <td width='0%' rowspan='8'>&nbsp;</td>
                </tr>
                <tr>
                  <td colspan='3'><br>
                    <font size='2'>Nome do Paciente....................: "; print $res['nome_paciente']; echo "</font><br />
                    <font size='2'>Procedimento Solicitado...........: "; print $res['proc_classificacao_sus']; echo "</font><br />
                   <font size='2'> Cod. Munic. Residencia (IBGE): "; print $res['ibge']; echo "</font><br />
                 <font size='2'> CPF do Medico Autorizador.....: "; print $res['aih_n_doc_prof_autorizador']; echo "</font></td>
                </tr>
                <tr>
                  <td width='43%'>&nbsp;</td>
                  <td width='1%'>&nbsp;</td>
                  <td width='55%'><div align='center'>----------------------------------------------------------------------------------------------<br>
                    <font size='2'>CNPJ HOSPITAL<br />
                    "; print $row['med_cnpj']; echo "</font><br>
                    <br>
                  ----------------------------------------------------------------------------------------------</div></td>
                </tr>
                <tr>
                  <td width='43%'><div align='center'><font size='2'>ASSINATURA DO MEDICO AUTORIZADOR</font></div></td>
                  <td colspan='2'>&nbsp;</td>
                </tr>
                <tr>
                  <td width='43%'><div align='center'><br /><br /><br />
                    -----------------------------------------------------------<br>
                    <br>
                  </div></td>
                  <td colspan='2'>&nbsp;</td>
                </tr>
                <tr>
                  <td colspan='3'><div align='center'><font size='2'>INFORMACOES APRESENTADAS EM MEIO MAGNETICO</font></div></td>
                </tr>
                <tr>
                   <td width='43%'><font size='2'>"; print $data; echo " &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
		  </font></td>
                  <td>&nbsp;</td>
                  <td><font size='2'> Competencia: "; print $res['aih_mes_compet'] . '/' . $res['aih_ano_compet']; 
		  echo "</font></td>
                </tr>
                <tr>
                  <td width='43%'>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
              </table>
			  <div align='center'><span class='pg_print'>*----------------------------------------------------------------------------------------------------------------------------------*</span></div>
		    </td>
		  </tr>
		</table>
		";
		
		?>
  
</body>
</html>
		
