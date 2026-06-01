<?php

$form = new classForm();
$common = new commonClass();
$table = new tableClass();

echo $common->incJquery();
// Atendimento Odontologico
echo $common->menuTab(Array("Odontograma"));
/**
@brief  Inclusao principal para montagem do sistema
*/
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
require_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
require_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
require_once $_SESSION[root].$_SESSION[modulo]."odonto.inc.php";

cabecario( $hotkey = true );

echo "
<!--<html>
<body bgcolor='#E6E6E6'>
<link href='estilo.css' rel='stylesheet' type='text/css'>-->";

//verauth($id_login);

echo  '
<script>
	root = "$_SESSION[root]";
	linkroot = "$_SESSION[linkroot]";
	comum = "$_SESSION[comum]";
	modulo = "$_SESSION[modulo]";
</script>
<script type="text/javascript" src="funcoes.js"></script>
<script type="text/javascript" src="../ajax_motor.js"></script>
<script type="text/javascript" src="odonto.js"></script>
';

$age_codigo 	= intval($_GET['age_codigo']);
//$age_codigo		= 2005119;
//$age_codigo		= 2005118;
$paci_row 		= db_getRow("SELECT u.usu_nome, u.usu_codigo
										FROM agendamento 
										NATURAL JOIN usuario as u WHERE age_codigo = $age_codigo");
echo $common->bodyTab('1');
$paci_nome 		= $paci_row[0];
if( ! $paci_nome )
{
	die('<p><strong>Escolha um paciente antes de prosseguir !</strong></p>');
}
?>


<div id="dente_faces" style="margin-top:10px; margin-left:70px; border:1px solid #000; padding: 6px; background: #fff; width:500px; height:380px; position:absolute; display: none; overflow:auto; z-index:99;">&nbsp;</div>



<table style="width:100%;border:0px solid red;" cellpadding="1" cellspacing="1">
<tr>
	<!-- adulto (1) -->
	<td>&nbsp;</td>
	<?php
	$age_data2 = db_get("SELECT age_data FROM agendamento WHERE age_codigo = $age_codigo");
	$sql = "SELECT MAX(od_codigo) FROM odonto WHERE age_codigo = $age_codigo AND od_data = '$age_data2'";
	$age_teste = db_get($sql);
	
	if( ! $age_teste )
	{
		db_query('Begin');
	
		$pk = db_get("SELECT NEXTVAL('odonto_od_codigo_seq');");
		
		$sql2 = "INSERT INTO odonto (od_codigo, od_data, age_codigo) VALUES ($pk, NOW(), $age_codigo)";
		
		db_query($sql2);
	
		//db_query('Rollback');
		db_query('Commit');	
	}
	
	$stmt = "SELECT dente_situacao,
					dente_face
			   FROM odonto_historico AS h
		 INNER JOIN odonto AS o 
	 			 ON o.od_codigo = h.od_codigo
		 INNER JOIN agendamento AS a ON a.age_codigo = o.age_codigo
			  WHERE a.usu_codigo = $paci_row[usu_codigo]
				AND dente_num = %d
	   	   ORDER BY od_hist_codigo DESC";	


	for( $i=8; $i >= 1; $i-- )
	{
		$dente_num = "1{$i}";
		
		$stmti = sprintf( $stmt, $dente_num );
		
		$row1 = db_getRow($stmti);
	
		print dente_row( $dente_num, $id_login, $age_codigo, $row1['dente_situacao'], $row1['dente_face'] );

	}
	?>
	<!-- /adulto (1) -->
	<td>&nbsp;</td>
	<!-- adulto (2) -->
	<?php

	for( $i=1; $i <= 8; $i++ )
	{
		$dente_num = "2{$i}";
		
		$stmti = sprintf( $stmt, $dente_num );
		
		$row1 = db_getRow($stmti);
	
		print dente_row( $dente_num, $id_login, $age_codigo, $row1['dente_situacao'], $row1['dente_face'] );

	}
	?>	
	<!-- /adulto (2) -->
</tr>
<tr>
	<!-- crianca (5) -->
	<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
	<?php

	for( $i=5; $i >= 1; $i-- )
	{
		$dente_num = "5{$i}";
		
		$stmti = sprintf( $stmt, $dente_num );
		
		$row1 = db_getRow($stmti);
	
		print dente_row( $dente_num, $id_login, $age_codigo, $row1['dente_situacao'], $row1['dente_face'] );
	}
	?>
	<!-- /crianca (5) -->
	<td>&nbsp;</td>
	<!-- crianca (6) -->
	<?php

	for( $i=1; $i <= 5; $i++ )
	{
		$dente_num = "6{$i}";
		
		$stmti = sprintf( $stmt, $dente_num );
		
		$row1 = db_getRow($stmti);
	
		print dente_row( $dente_num, $id_login, $age_codigo, $row1['dente_situacao'], $row1['dente_face'] );
	}
	?>
	<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
	<!-- /crianca (6) -->
</tr>
<tr>
	<td>DIREITO</td>
	<td colspan="17" class='c'><?php print str_repeat( '-', 100 );?></td>
	<td>ESQUERDO</td>
</tr>
<tr>
	<!-- crianca (8) -->
	<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
	<?php

	for( $i=5; $i >= 1; $i-- )
	{
		$dente_num = "8{$i}";
		
		$stmti = sprintf( $stmt, $dente_num );
		
		$row1 = db_getRow($stmti);
	
		print dente_row( $dente_num, $id_login, $age_codigo, $row1['dente_situacao'], $row1['dente_face'] );
	}
	?>
	<!-- crianca (8) -->
	<td>&nbsp;</td>
	<!-- crianca (7) -->
	<?php

	for( $i=1; $i <= 5; $i++ )
	{
		$dente_num = "7{$i}";
		
		$stmti = sprintf( $stmt, $dente_num );
		
		$row1 = db_getRow($stmti);
	
		print dente_row( $dente_num, $id_login, $age_codigo, $row1['dente_situacao'], $row1['dente_face'] );
	}
	?>
	<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
	<!-- /crianca (7) -->
</tr>
<tr>
	<!-- adulto (4) -->
	<td>&nbsp;</td>
	<?php

	for( $i=8; $i >= 1; $i-- )
	{
		$dente_num = "4{$i}";
		
		$stmti = sprintf( $stmt, $dente_num );
		
		$row1 = db_getRow($stmti);
	
		print dente_row( $dente_num, $id_login, $age_codigo, $row1['dente_situacao'], $row1['dente_face'] );
	}
	?>
	<!-- /adulto (4) -->
	<td>&nbsp;</td>
	<!-- adulto (3) -->
	<?php

	for( $i=1; $i <= 8; $i++ )
	{
		$dente_num = "3{$i}";

		$stmti = sprintf( $stmt, $dente_num );
		
		$row1 = db_getRow($stmti);
	
		print dente_row( $dente_num, $id_login, $age_codigo, $row1['dente_situacao'], $row1['dente_face'] );

	}
	?>	
	<!-- /adulto (3) -->
</tr>
</table>

<div id="dentes_div" style="margin:10px; width:550px;padding:4px;border:1px solid #ccc;">
	Dente: <span id="dentes" style="font-weight:bold;">&nbsp;</span>
</div>

<?php
echo $common->closeTab();
?>
<!-- <fieldset>
<legend>Legenda</legend>

<table>
<tr>
	<td valign='top'>-->
	<?php

	/*print "\n\t<table class='lista'>\n\t<tr><th width='50%'>Procedimento</th><th>Notacao</th></tr>";
	for( $i = 1; $i < 12; $i++ )
	{
		print "\n\t<tr><td>{$SITUACOES[$i]}</td><td>{$LEGENDAS[$i]}</td></tr>";
		if( $i == 6 )
			print "\n</table>\n</td>\n<td valign='top'>\n<table class='lista'><tr><th width='50%'>Procedimento</th><th>Notacao</th></tr>";
	}*/
	?>
<!--</table>
</td>
</tr>
</table>
</fieldset>

--></body>
</html>
