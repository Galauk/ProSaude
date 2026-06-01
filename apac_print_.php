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
</head>
<body onLoad="self.print()">

<div align="center">
  <?

echo "<input type='hidden' name='apac_codigo' value='$apac_codigo' /><br />";

	$sq = "SELECT a.apac_codigo, a.apac_num, 

	(CASE WHEN a.pac_codigo IS NOT NULL THEN p0.usu_nome || ', ' || p0.usu_cpf
	WHEN a.pac_apac_codigo IS NOT NULL THEN p1.pac_nome || ', ' || p1.pac_cpf_cns
	ELSE 'none' END) as pac_nome,

	(CASE WHEN a.uni_sol_codigo IS NOT NULL THEN u0.uni_desc || ', ' || u0.uni_codigo
	WHEN a.uni_sol_apac_codigo IS NOT NULL THEN u1.uni_desc || ', ' || u1.uni_codigo
	ELSE 'none' END) as uni_sol_desc,

	(CASE WHEN a.med_sol_codigo IS NOT NULL THEN m0.med_nome || ', ' || m0.med_cpf
	WHEN med_sol_apac_codigo IS NOT NULL THEN m1.med_nome || ', ' || m1.med_cpf
	ELSE 'none' END) as med_nome,

	(CASE WHEN orgao_codigo IS NOT NULL THEN u2.uni_desc || ', ' || u2.uni_codigo
	WHEN orgao_apac_codigo IS NOT NULL THEN u3.uni_desc || ', ' || u3.uni_codigo
	ELSE 'none' END) as orgao_desc,

	(CASE WHEN uni_pres_codigo IS NOT NULL THEN u4.uni_desc || ', ' || u4.uni_cnpj || ', ' || u4.uni_codigo
	WHEN uni_pres_apac_codigo IS NOT NULL THEN u5.uni_desc || ', ' || u5.uni_cnpj || ', ' || u5.uni_codigo
	ELSE 'none' END) as uni_pres_desc,

	(CASE WHEN med_aud_codigo IS NOT NULL THEN m2.med_nome || ', ' || m2.med_cpf
	WHEN med_aud_apac_codigo IS NOT NULL THEN m3.med_nome || ', ' || m3.med_cpf
	ELSE 'none' END) as med_aud_nome,

	TO_CHAR(apac_periodo_validade,'DD/MM/YYYY') as periodo_validade,
	
	apac_segunda_via

	FROM apac AS a
		
	LEFT JOIN usuario AS p0 ON p0.usu_codigo = a.pac_codigo
	LEFT JOIN apac_paciente AS p1 ON p1.pac_codigo = a.pac_apac_codigo
	
	LEFT JOIN unidade AS u0 ON u0.uni_codigo = a.uni_sol_codigo
	LEFT JOIN apac_unidade AS u1 ON u1.uni_codigo = a.uni_sol_apac_codigo
	
	LEFT JOIN medico AS m0 ON m0.med_codigo = a.med_sol_codigo
	LEFT JOIN apac_medico AS m1 ON m1.med_codigo = a.med_sol_apac_codigo
	
	LEFT JOIN unidade AS u2 on u2.uni_codigo = a.orgao_codigo
	LEFT JOIN apac_unidade AS u3 on u3.uni_codigo = a.orgao_apac_codigo
	
	LEFT JOIN unidade AS u4 ON u4.uni_codigo = a.uni_pres_codigo
	LEFT JOIN apac_unidade AS u5 ON u5.uni_codigo = a.uni_pres_apac_codigo
	
	LEFT JOIN medico AS m2 ON m2.med_codigo = a.med_aud_codigo
	LEFT JOIN apac_medico AS m3 ON m3.med_codigo = a.med_aud_apac_codigo 
		
	WHERE a.apac_codigo = $apac_codigo ";
	
	$row = db_query($sq);
	$res = pg_fetch_array($row);
	
	//var_dump($res);
	//$arr = split( ',' , $row[INDICE] );
		$arr1 = split( ',' , $res['pac_nome'] );
		//-----------------------------------------
		$arr2 = split( ',' , $res['uni_sol_desc'] );
		//-----------------------------------------
		$arr3 = split( ',' , $res['med_nome'] );
		//-----------------------------------------
		$arr4 = split( ',' , $res['uni_pres_desc'] );
		
		//echo $arr3[0]; // nome do médico
			
		$ql_proc = "SELECT a.apac_codigo, a.proc_codigo, p.proc_codigo, p.proc_nome ".
					"FROM apac_procedimento AS a ".
					"INNER JOIN procedimento AS p ON a.proc_codigo=p.proc_codigo ".
					"WHERE apac_codigo=$apac_codigo";
		
		$query = db_query($ql_proc);
		
		//-----------------------------------------
		/*echo $res['uni_prestadora_nome_r'];
		echo $res['uni_prestadora_cnpj_r'];
		echo $res['uni_prestadora_codigo_r'];
		
		echo "<br />";
		
		$arr5 = split( ',' , $res['med_aud_nome'] );
		echo $arr5[0]; // nome do médico
		echo $arr5[1]; // cpf
		
		echo $res['med_aud_cpf_r'];
		
		echo $res['id_login'];*/

?>
  <table width="750px" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <td width="70%">&nbsp;</td>
      <td width="30%"><br />
          <div align="left">&nbsp;&nbsp;<? echo $res['apac_num']; ?>número apac<br />
          <br />
          <br />
          <br />
      </div></td>
    </tr>
    <tr>
      <td width="70%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo $arr1[0]; // nome do paciente ?></div><br /></td>
      <td width="30%"><div align="left">&nbsp;&nbsp;<? echo $arr1[1]; // cpf do paciente ?></div><br /></td>
    </tr>
  </table><br /><br /><br />
  <table width="750px" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <td width="80%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo $arr2[0]; // unidade solicitante nome ?></div><br /></td>
      <td width="20%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo $arr2[1]; // codigo ?></div><br /></td>
    </tr>
  </table><br /><br />
  <table width="750px" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <td width="35%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo $arr3[1]; // cpf do medico solicitante ?></div><br /></td>
      <td width="65%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo $arr3[0]; // nome do medico solicitante ?></div><br /></td>
    </tr>
  </table><br /><br /><br /><br />
  <table width="750px" border="0" cellspacing="1" cellpadding="1">
  <?
  		$i = 0;
  		while ($resp_p = pg_fetch_array($query))
		{
			if ($i == 6) {
				break;
			}
			?>
			<tr>
				<td width="78%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo $resp_p['proc_nome']; ?></div></td>
				<td width="22%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo $resp_p['proc_codigo']; ?></div></td>
			</tr>
		<?
			$i++;
		}
		for ( $i; $i<7; $i++ )
		{ ?>
			<tr>
				<td width="78%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--</div></td>
				<td width="22%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--</div></td>
			</tr>		
	<?	}  ?>
  </table><br /><br /><br />
  <table width="750px" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <td width="80%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SECRETARIA DE SAÚDE</div><br /></td>
      <td width="20%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;código</div><br /></td>
    </tr>
  </table><br />
  <table width="750px" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <td width="50%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo $arr4[0]; //nome unidade prest. de serv.?></div><br /></td>
      <td width="30%"><div align="left">&nbsp;&nbsp;&nbsp;<? echo $arr4[0]; //nome unidade prest. de serv.?></div><br /></td>
      <td width="20%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo $arr4[0]; // unidade prest. de serv.?></div><br /></td>
    </tr>
  </table><br />
  <table width="750px" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <td width="30%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo $res['periodo_validade']; ?></div></td>
      <td width="40%"><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;cpf do autorizado</div></td>
      <td width="30%">&nbsp;</td>
    </tr>
  </table>
</div>
</body>
</html>