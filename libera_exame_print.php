<link href="estilo.css" rel="stylesheet" type="text/css">
<?
//
// Connect to PostgreSQL.
//
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
    
$sql = "select * from usuarios where usr_codigo = $id_login";
$exe = pg_query($sql);
$res_exe = pg_fetch_array($exe);
$usr = $res_exe['usr_nome'];


$usu = db_getRow("SELECT usu_datanasc,usu_sexo,usu_cisvir,usu_prontuario,usu_nome,usu_codigo,usu_end_rua,usu_end_cidade FROM usuario WHERE usu_codigo='$usu_codigo'");
$id = db_getRow("select calcula_idade($usu[usu_codigo])");
$idade = $id[0];
if($usu[usu_sexo]=="M") { 
   $sexo = "Masculino";
} else {
   $sexo = "Feminino";
}
// verificar as datas 
if( empty($imprimir) )
{
	$stmt = "SELECT DISTINCT agexl_data,to_char(agexl_data,'DD/MM/YYYY') as agexl_data_ from agendamento_exame_lista 
		WHERE agex_codigo=$agex_codigo";
	$qry = db_query( $stmt );
	
	print "
	<html>
	<body>";
     if(pg_num_rows($qry)=="1") 
     { 
   	print "
	<h3>Impressão da Guia de Exame do Paciente \"$usu[usu_nome]\"</h3>
	";
     } 
       else 
     { 
   	print "
	<h3>Paciente \"$usu[usu_nome]\" marcou os Exames para os dias:</h3>
	";
     }

	while( $row = pg_fetch_array($qry) )
	{
		print "<a href='?agex_codigo=$agex_codigo&usu_codigo=$usu_codigo&imprimir=$row[0]'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/print.gif border=0 align=absmiddle></a>
		       &nbsp;<font size=2>$row[1]</font></a>&nbsp;";
	}
	
	print "
	</body>
	</html>
	";
	die();
}


//
?>
<body OnLoad='imp()'>


<script type="text/javascript">
function imp()
{
	window.print();
	setTimeout( 'history.back()', 3 * 1000 );
}
</script>
<table width=100% cellspacing=0 cellpadding=0 border=0>
<tr>
 <td colspan=5><font face=verdana size=2><b>AMS - AUTARQUIA MUNICIPAL DE SA&Uacute;DE DE APUCARANA, PARAN&Aacute;<br>GUIA DE EXAMES<b></font></td>
</tr>
<hr>
<tr>
 <td colspan=5><font face=verdana size=2>-------------------------------------------------------------------------------------------------------</font></td>
</tr>
</table>
<table width='100%' cellspacing='1' cellpadding='1' border='0'>
	<tr>
		<td  width="30"><font size=3 face='courier'>DATA.....:</font></td>
			
			<?php
			
			// sql alterado
			// NATURAL JOIN procedimento AS p
			$stmt = "SELECT to_char(libexl_dt_cadastro,'dd/mm/yyyy'), TRANSLATE(proc_nome, 'ZZZ-', '') as proc_nome,m.med_nome
			FROM liberacao_exame_lista AS al
			INNER JOIN procedimento AS p ON al.proc_codigo = p.proc_codigo
			LEFT JOIN medico AS m ON p.med_codigo = m.med_codigo
			INNER JOIN liberacao_exame AS ae ON ae.libex_codigo = al.libex_codigo
			WHERE al.libex_codigo = $agex_codigo 
			";
			#AND agexl_data = '$imprimir'
			
			$qry = db_query( $stmt );
			$qry2 = db_query( $stmt );
			$rr = pg_fetch_array($qry2);
			echo "
				<td width=550><font size=3 face='courier'><b>07:00am&nbsp;-&nbsp;$rr[0]</b></font></td>
				";
			/*while( $row = pg_fetch_array($qry) )
			{
			
				echo "<font size=3 face='courier'>&nbsp;<b>$row[1]</b></font>&nbsp;|&nbsp;";
			}*/
			?>
	</tr>
</table><br>

<?php

$stmt = "SELECT ae.libex_codigo,
			m.med_nome, 
			m.med_end_telefone, 
			m.med_endereco,
			m.med_end_numero, 
			r.rua_nome,
			c.cid_nome
		FROM liberacao_exame AS ae
		LEFT JOIN medico AS m ON m.med_codigo = ae.med_codigo_responsavel
		LEFT JOIN cidade as c ON c.cid_codigo = m.cid_codigo
		LEFT JOIN rua AS r ON m.rua_codigo = r.rua_codigo
		LEFT JOIN especialidade AS e ON e.esp_codigo = ae.esp_codigo_responsavel
		WHERE ae.libex_codigo=$agex_codigo";
$row0 = db_getRow( $stmt );
echo $stmt;

?>

<table width='100%' cellspacing='1' cellpadding='1' border='0'>
	<tr>
		<td width=20><font size=3 face='courier'>Codigo Libera&ccedil;&atilde;o:</font></td>
		<td width=580><font size=3 face='courier'>&nbsp;<b><?=$row0[0]?></b></font></td> 
	</tr>
	<tr>
		<td width=20><font size=3 face='courier'>LOCAL....:</font></td>
		<td width=580><font size=3 face='courier'>&nbsp;<b><?=$row0[1]?></b></font></td> 
	</tr>
	<tr>
		<td><font size=3 face='courier'>ENDEREÇO.:</font></td>
		<td><font size=3 face='courier'>&nbsp;<b><? echo $row0[3].",". $row0[4];?></b></font></td> 
	</tr>
	<tr>
		 <td width=65><font size=3 face='courier'>CIDADE...:</font></td>
		 <td><font size=3 face='courier'>&nbsp;<b><?=$row0[6]?></b></font></td>
		 <td width=65><font size=3 face='courier'>TELEFONE...:</font></td>
		 <td><font size=3 face='courier'>&nbsp;<b><?=$row0[2]?></b></font></td>
	</tr>
</table><br>



<table width=100% cellspacing=0 cellpadding=0 border=0>
<tr>
 <td width=110><font size=3 face='courier'>PACIENTE.:</font></td>
 <td width=450><font size=3 face='courier'><b><?=$usu[usu_nome]?></b></font></td>
 <td width=30><font size=3 face='courier'>IDADE:</font></td>
 <td><font size=3 face='courier'><b><?=$idade?> anos</b></font></td>  
</tr>

</table>

<table width=100% cellspacing=0 cellpadding=0 border=0>
<tr>
 <td width=65><font size=3 face='courier'>ENDERE&Ccedil;O.:</font></td>
 <td><font size=3 face='courier'>&nbsp;<b><?=$usu[usu_end_rua]?></b></font></td>
</tr>
<tr>
 <td width=65><font size=3 face='courier'>CIDADE...:</font></td>
 <td><font size=3 face='courier'>&nbsp;<b><?=$usu[usu_end_cidade]?></b></font></td>

</tr>
</table>

<br />

<table width='100%' cellspacing='1' cellpadding='1' border='0'>
<tr>	
	<td width="50" valign=top style="padding-top:5px"><font size=3 face='courier'>PROCEDIMENTO(S).:</font></td> 
	<td>
<?php

// sql alterado
// NATURAL JOIN procedimento AS p
$stmt = "SELECT to_char(agexl_data,'dd/mm/yyyy'), TRANSLATE(proc_nome, 'ZZZ-', '') as proc_nome, m.med_nome
FROM agendamento_exame_lista AS al
INNER JOIN procedimento AS p ON al.proc_codigo = p.proc_codigo
LEFT JOIN medico AS m ON p.med_codigo = m.med_codigo
INNER JOIN agendamento_exame AS ae ON ae.agex_codigo = al.agex_codigo
WHERE al.agex_codigo = $agex_codigo 
";
#AND agexl_data = '$imprimir'

$qry = db_query( $stmt );
$qry2 = db_query( $stmt );
$rr = pg_fetch_array($qry2);

echo "<table border=1>
	<tr>";
$cont = 1;	
while( $row = pg_fetch_array($qry) )
{
	echo "<td width=250 align='center'><font size=2 face='courier'>&nbsp;<b>$row[1]</b></font></td>";
	if ($cont%4 == 0){
		echo "</tr><tr>";
	}
	$cont++;
}
echo "</tr>";
echo "</table>";
?>
	</td>
</tr>
</table>
<br><br><br>
<table>
<tr>
	<td align="center"><font face='courier' size=3>____________________<br></font></td>
</tr>
<tr>
	<td align="center"><?=$usr?></td>
        </tr>
</table>
	


</body>
<!--  -->
