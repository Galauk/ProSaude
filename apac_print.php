<?php
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
Cabecario();
?>
<html>
<head><title>APAC</title>

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
	font-size:18px; 
	font-family:Arial;
}
body{
	display:block; 
}
-->
</style>
<!-- <link href="estilo_print.css" rel="stylesheet" type="text/css"> -->
</head>

<body onLoad="self.print()"><div align="center">

	<input type='hidden' name='id_login' id='id_login' value='$id_login' />
	<input type='hidden' name='apac_num_r' id='apac_num_r' value='$apac_num_r' />
	<input type='hidden' name='paci_nome' id='paci_nome' value='$paci_nome' />
	<input type='hidden' name='paci_cpf_r' id='paci_cpf_r' value='$paci_cpf_r' />
	<input type='hidden' name='uni_nome_r' id='uni_nome_r' value='$uni_nome_r' />
	<input type='hidden' name='uni_codigo_r' id='uni_codigo_r' value='$uni_codigo_r' />
	<input type='hidden' name='med_cpf_r' id='med_cpf_r' value='$med_cpf_r' />
	<input type='hidden' name='med_nome_r' id='med_nome_r' value='$med_nome_r' />
	<input type='hidden' name='uni_prestadora_nome_r' id='uni_prestadora_nome_r' value='$uni_prestadora_nome_r' />
	<input type='hidden' name='uni_prestadora_cnpj_r' id='uni_prestadora_cnpj_r' value='$uni_prestadora_cnpj_r' />
	<input type='hidden' name='uni_prestadora_codigo_r' id='uni_prestadora_codigo_r' value='$uni_prestadora_codigo_r' />
	<input type='hidden' name='periodo_val' id='periodo_val' value='$periodo_val' />
	<input type='hidden' name='periodo_val_fim' id='periodo_val_fim' value='$periodo_val_fim' />
	<input type='hidden' name='med_aud_cpf_r' id='med_aud_cpf_r' value='$med_aud_cpf_r' />

  <table width="750px" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <td width="70%">&nbsp;</td>
      <td width="30%">
          <div align="left">&nbsp;&nbsp;<h1><? echo $apac_num_r; ?></h1>
      </div></td>
    </tr>
    <tr>
      <td width="70%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=2><? echo $paci_nome; // nome do paciente ?></div><br /></td>
      <td width="30%"><div align="left">&nbsp;&nbsp;<font size=2><? echo $paci_cpf_r; // cpf do paciente ?></div><br /></td>
    </tr>
  </table><br /><br />
  <table width="750px" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <td width="80%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=2><? echo $uni_nome_r; // unidade solicitante nome ?></div><br /></td>
      <td width="20%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=2><? echo $uni_codigo_r; // codigo ?></div><br /></td>
    </tr>
  </table><br />
  <table width="750px" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <td width="35%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=2><? echo $med_cpf_r; // cpf do medico solicitante ?></div><br /></td>
      <td width="65%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=2><? echo $med_nome_r; // nome do medico solicitante ?></div><br /></td>
    </tr>
  </table><br />
  <table width="750px" border="0" cellspacing="1" cellpadding="1">
  <?
		/*  
  		if ($proc_lista != '' && $proc_lista_apac != ''){
  
			$ql_proc = "SELECT p.proc_codigo, p.proc_nome
						FROM procedimento AS p
						WHERE p.proc_codigo IN ($proc_lista)
						UNION
						SELECT a.proc_codigo, a.proc_nome
						FROM apac_procedimento_cad AS a
						WHERE a.proc_codigo IN ($proc_lista_apac)
						ORDER BY 2 ";
					
		}else if ($proc_lista != '' && $proc_lista_apac == ''){

			$ql_proc = "SELECT p.proc_codigo, p.proc_nome
						FROM procedimento AS p
						WHERE p.proc_codigo IN ($proc_lista)
						ORDER BY 2 ";

		}else if ($proc_lista == '' && $proc_lista_apac != ''){

			$ql_proc = "SELECT a.proc_codigo, a.proc_nome
						FROM apac_procedimento_cad AS a
						WHERE a.proc_codigo IN ($proc_lista_apac)
						ORDER BY 2 ";
		
		}*/
  		if ($proc_lista != '' && $proc_lista_apac != ''){
  
			$ql_proc = "SELECT p.proc_classificacao_sus, p.proc_nome
						FROM procedimento AS p
						WHERE p.proc_codigo IN ($proc_lista)
						UNION
						SELECT a.proc_numero, a.proc_nome
						FROM apac_procedimento_cad AS a
						WHERE a.proc_codigo IN ($proc_lista_apac)
						ORDER BY 2 ";
					
		}else if ($proc_lista != '' && $proc_lista_apac == ''){

			$ql_proc = "SELECT p.proc_classificacao_sus, p.proc_nome
						FROM procedimento AS p
						WHERE p.proc_codigo IN ($proc_lista)
						ORDER BY 2 ";

		}else if ($proc_lista == '' && $proc_lista_apac != ''){

			$ql_proc = "SELECT a.proc_numero, a.proc_nome
						FROM apac_procedimento_cad AS a
						WHERE a.proc_codigo IN ($proc_lista_apac)
						ORDER BY 2 ";
		
		}

		$query = db_query($ql_proc);

  		$i = 0;
  		while ($resp_p = pg_fetch_array($query))
		{
			if ($i == 6) {
				break;
			}
			?>
			<tr>
				<td width="78%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=2><? echo $resp_p[1]; ?></div></td>
				<td width="22%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=2><? echo $resp_p[0]; ?></div></td>
			</tr>
		<?
			$i++;
		}
		for ( $i; $i<7; $i++ )
		{ ?>
			<tr>
				<td width="78%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--</div></td>
				<td width="22%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--</div></td>
			</tr>
	<?	}  ?>
  </table><br /><br />
  <table width="750px" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <td width="80%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=2>AUTARQUIA MUNICIPAL DE SAÚDE</div><br /></td>
      <td width="20%"><div align="left">&nbsp;</div><br /></td>
    </tr>
  </table><br />
  <table width="750px" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <td width="50%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=2><? echo $uni_prestadora_nome_r; ?></div><br /></td>
      <td width="30%"><div align="left">&nbsp;&nbsp;&nbsp;<font size=2><? echo $uni_prestadora_cnpj_r; ?></div><br /></td>
      <td width="20%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=2><? echo $uni_prestadora_codigo_r; ?></div><br /></td>
    </tr>
  </table><br />
  <table width="750px" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <td width="30%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=2><? echo $periodo_val; ?> à <? print $periodo_val_fim; ?></div></td>
      <td width="40%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=2><? echo $med_aud_cpf_r; ?></div></td>
      <td width="30%">&nbsp;</td>
    </tr>
  </table>
</div>
</body>
</html>
		
