<?php
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
Cabecario();

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
	font-size:12px; 
	display:block; 
}
-->
</style>
</head>

<body onLoad="self.print()">
<?php

	$stmt = "SELECT med_cnpj FROM medico WHERE med_codigo=".$med_codigo_solicitante_h;
	$query = db_query($stmt);
	$row = pg_fetch_array($query);
	
	$numero_aih = $aih_numero_aih;
	list ($num, $digito) = split ('[-]', $numero_aih);
	
	$data = date('d/m/Y H:m:s');
	
echo"
	<input type='hidden' name='aih_paciente_nome' id='aih_paciente_nome' value='$aih_paciente_nome' />
	<input type='hidden' name='id_login' id='id_login' value='$id_login' />
	<input type='hidden' name='aih_ibge_codigo' id='aih_ibge_codigo' value='$aih_ibge_codigo' />
	<input type='hidden' name='aih_classificacao_sus' id='aih_classificacao_sus' value='$aih_classificacao_sus' />
	<input type='hidden' name='aih_n_doc_prof_autorizador' id='aih_n_doc_prof_autorizador' value='$aih_n_doc_prof_autorizador' />
	<input type='hidden' name='aih_prontuario_hospital' id='aih_prontuario_hospital' value='$aih_prontuario_hospital' />
	<input type='hidden' name='aih_cod_orgao_emissor' id='aih_cod_orgao_emissor' value='$aih_cod_orgao_emissor' />
	
	<table width='800' border='0' cellspacing='2' cellpadding='2' align='center'>
		  <tr>
			<td><div align='center'><span class='pg_print'>*----------------------------------------------------------------------------------------------------------------------------------*</span>
			  </div>
			  <table width='800' border='0' cellpadding='0' cellspacing='0' class='pg_print'>
                <tr>
                  <td width='1%'>&nbsp;</td>
                  <td width='43%'><p align='center'><font size='2'>SECRETARIA MUNICIPAL DA SAUDE</font><br />
                    <font size='2'>SUS - SISTEMA UNICO DE SAUDE</font></p>
                  <p align='center'><font size='2'>AUTORIZA&Ccedil;&Atilde;O DE INTERNA&Ccedil;&Atilde;O HOSPITALAR</font></p></td>
                  <td colspan='2'><blockquote>
                      <p align='left'><font size='2'>Numero da AIH: ";
                        //print $num;
                        print $numero_aih;
                        echo " </font><br />";
                       // <font size='2'>Identifica&ccedil;&atilde;o: "; print $digito; echo "</font></p>
                  echo"      <font size='2'>Identifica&ccedil;&atilde;o: 7 </font></p>
                  </blockquote></td>
                  <td width='0%'>&nbsp;</td>
                </tr>
                <tr>
                  <td colspan='5'><div align='center'>*----------------------------------------------------------------------------------------------------------------------------------*</div></td>
                </tr>
                <tr>
                  <td width='1%' rowspan='8'>&nbsp;</td>
                  <td width='43%'><div align='center'><font size='2'>SO PODE SER FORNECIDO MEDIANTE</font><br />
                  <font size='2'>APRESENTACAO DE LAUDO</font></div></td>
                  <td colspan='2'><div align='left'>
                      <blockquote>
                        <p><font size='2'>Numero do Prontuario/Same: "; print $aih_prontuario_hospital; echo "</font><br />
                          <font size='2'>Orgao Emissor.....................: PR.40.245 </font></p>
                      </blockquote>
                  </div></td>
                  <td width='0%' rowspan='8'>&nbsp;</td>
                </tr>
                <tr>
                  <td colspan='3'><br>
                    <font size='2'>Nome do Paciente....................: "; print $aih_paciente_nome; echo "</font><br />
                    <font size='2'>Procedimento Solicitado...........: "; print $aih_classificacao_sus; echo"</font><br />
                    <font size='2'>Cod. Munic. Residencia (IBGE): "; print $aih_ibge_codigo; echo "</font><br />
                    <font size='2'>CPF do Medico Autorizador.....: "; print $aih_n_doc_prof_autorizador; echo "</font></td>
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
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td width='43%'>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td><font size='2'> Competencia: "; print $res['aih_mes_compet'] . '/' . $res['aih_ano_compet']; 
		  echo "</font></td>
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
		
