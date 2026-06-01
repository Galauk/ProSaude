<?php
// Atendimento Odontologico

/**
@brief  Inclusao principal para montagem do sistema
*/
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."odonto.inc.php";

cabecario( $hotkey = true );

verauth($id_login);

// 
//$paci_codigo 	= intval($_GET['paci_codigo']);
$paci_codigo 	= 300333;
$stmt			= 'SELECT usu_nome FROM usuario WHERE usu_codigo='.$paci_codigo;
$paci_nome		= db_get( $stmt );

if( ! $paci_nome )
{
	die('<p><strong>Escolha um paciente antes de prosseguir !</strong></p>');
}

// VERIFICA SE JAH EXISTE UMA OCORRÊNCIA DESTE PACIENTE PARA 'HOJE'
$stmt = "SELECT od_codigo FROM odonto 
	WHERE paci_codigo = $paci_codigo AND od_data = CURRENT_DATE";

$od_codigo = db_get( $stmt );

if( empty($od_codigo) )
{
	db_query('begin');

 	$stmt = "INSERT INTO odonto ( 
	paci_codigo, 
	usr_codigo, 
	od_data
	 ) VALUES ( 
	".intval($paci_codigo).", 
	".intval($id_login).", 
	CURRENT_DATE )";
	
	db_query($stmt);
	
	$od_codigo = db_get( 'SELECT MAX(od_codigo) FROM odonto' );
	
	db_query('commit');
}

echo  '
<script type="text/javascript" src="funcoes.js"></script>
<script type="text/javascript" src="ajax_motor.js"></script>
<script type="text/javascript" src="odonto.js"></script>

<p>Paciente: <strong>'.$paci_nome.'</strong></p>
';

?>

<div id="dente_faces" style="margin-top:10px; margin-left:70px; border:1px solid #000; padding: 6px; background: #fff; width:500px; height:380px; position:absolute; display: none; overflow:auto; z-index:99;">&nbsp;</div>

<fieldset style="margin:0">
	<legend>Legenda</legend>
	<?php
		foreach( $SITUACOES as $key => $value )
		{
			if( $key == 'H' ) continue;
			//if( $key == 'SEL' ) print '<br />';
			$img = strtolower($key) . '_leg.gif';
			print "\n<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/dente_sit_{$img}' alt='Legenda $value' style='vertical-align:absmiddle;'>$value &nbsp;";
		}
	?>
	</fieldset>

<fieldset>
<legend>Odontograma</legend>

<table style="width:570px;border:0px solid red;" cellpadding="2" cellspacing="2">
<tr style="height:94px;">
	<?php 
		for( $i=8; $i >= 1; $i-- )
		{
			// dente
			$dente_num = "1{$i}";
			
			// historico do dente
			$situacoes = array();
			
			$stmt = "SELECT dente_situacao
			FROM odonto_historico 
			WHERE od_codigo IN (SELECT od_codigo FROM odonto WHERE paci_codigo = $paci_codigo)
			AND dente_num = $dente_num";
			
			$qry = db_query($stmt);
			
			while( $row = pg_fetch_array($qry) )
				$situacoes[] = $row[0];
			
			print dente_row( $dente_num, $id_login, $od_codigo, $situacoes );
		}
	?>
	<td style="background:url(<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/dente_traco_vert.gif) repeat-y center;">&nbsp;</td>
	<?php
		for( $i=8; $i >= 1; $i-- )
		{
			// dente
			$dente_num = "2{$i}";
			
			// historico do dente
			$situacoes = array();
			
			$stmt = "SELECT dente_situacao
			FROM odonto_historico 
			WHERE od_codigo IN (SELECT od_codigo FROM odonto WHERE paci_codigo = $paci_codigo)
			AND dente_num = $dente_num";
			
			$qry = db_query($stmt);
			
			while( $row = pg_fetch_array($qry) )
				$situacoes[] = $row[0];
			
			print dente_row( $dente_num, $id_login, $od_codigo, $situacoes );
		}
	
	?>
	<td rowspan="3">
	
	<map name="map_dente_crianca">
		<area shape="rect" alt="62" href="javascript:;" coords="93,14,110,31" onmouseover="mostra_dente(this.alt,1)" onmouseout="mostra_dente(this.alt,0)"
		onclick="mapa_dentes('<?=$id_login?>','<?=$od_codigo?>',this.alt)" />
		<area shape="rect" alt="63" href="javascript:;" coords="102,35,119,52" onmouseover="mostra_dente(this.alt,1)" onmouseout="mostra_dente(this.alt,0)" onclick="mapa_dentes('<?=$id_login?>','<?=$od_codigo?>',this.alt)" />
		<area shape="rect" alt="64" href="javascript:;" coords="111,56,128,73" onmouseover="mostra_dente(this.alt,1)" onmouseout="mostra_dente(this.alt,0)" onclick="mapa_dentes('<?=$id_login?>','<?=$od_codigo?>',this.alt)" />
		<area shape="rect" alt="65" href="javascript:;" coords="113,79,130,96" onmouseover="mostra_dente(this.alt,1)" onmouseout="mostra_dente(this.alt,0)" onclick="mapa_dentes('<?=$id_login?>','<?=$od_codigo?>',this.alt)" />
		<area shape="rect" alt="71" href="javascript:;" coords="112,105,129,122" onmouseover="mostra_dente(this.alt,1)" onmouseout="mostra_dente(this.alt,0)" onclick="mapa_dentes('<?=$id_login?>','<?=$od_codigo?>',this.alt)" />
		<area shape="rect" alt="72" href="javascript:;" coords="111,126,128,143" onmouseover="mostra_dente(this.alt,1)" onmouseout="mostra_dente(this.alt,0)" onclick="mapa_dentes('<?=$id_login?>','<?=$od_codigo?>',this.alt)" />
		<area shape="rect" alt="73" href="javascript:;" coords="102,148,119,165" onmouseover="mostra_dente(this.alt,1)" onmouseout="mostra_dente(this.alt,0)" onclick="mapa_dentes('<?=$id_login?>','<?=$od_codigo?>',this.alt)" />
		<area shape="rect" alt="72" href="javascript:;" coords="93,170,110,187" onmouseover="mostra_dente(this.alt,1)" onmouseout="mostra_dente(this.alt,0)" onclick="mapa_dentes('<?=$id_login?>','<?=$od_codigo?>',this.alt)" />
		<area shape="rect" alt="71" href="javascript:;" coords="70,180,87,197" onmouseover="mostra_dente(this.alt,1)" onmouseout="mostra_dente(this.alt,0)" onclick="mapa_dentes('<?=$id_login?>','<?=$od_codigo?>',this.alt)" />
		<area shape="rect" alt="81" href="javascript:;" coords="46,181,63,198" onmouseover="mostra_dente(this.alt,1)" onmouseout="mostra_dente(this.alt,0)" onclick="mapa_dentes('<?=$id_login?>','<?=$od_codigo?>',this.alt)" />
		<area shape="rect" alt="82" href="javascript:;" coords="23,170,40,187" onmouseover="mostra_dente(this.alt,1)" onmouseout="mostra_dente(this.alt,0)" onclick="mapa_dentes('<?=$id_login?>','<?=$od_codigo?>',this.alt)" />
		<area shape="rect" alt="83" href="javascript:;" coords="14,149,31,166" onmouseover="mostra_dente(this.alt,1)" onmouseout="mostra_dente(this.alt,0)" onclick="mapa_dentes('<?=$id_login?>','<?=$od_codigo?>',this.alt)" />
		<area shape="rect" alt="84" href="javascript:;" coords="6,126,23,143" onmouseover="mostra_dente(this.alt,1)" onmouseout="mostra_dente(this.alt,0)" onclick="mapa_dentes('<?=$id_login?>','<?=$od_codigo?>',this.alt)" />
		<area shape="rect" alt="85" href="javascript:;" coords="4,105,21,122" onmouseover="mostra_dente(this.alt,1)" onmouseout="mostra_dente(this.alt,0)" onclick="mapa_dentes('<?=$id_login?>','<?=$od_codigo?>',this.alt)" />
		<area shape="rect" alt="55" href="javascript:;" coords="3,78,20,95" onmouseover="mostra_dente(this.alt,1)" onmouseout="mostra_dente(this.alt,0)" onclick="mapa_dentes('<?=$id_login?>','<?=$od_codigo?>',this.alt)" />
		<area shape="rect" alt="54" href="javascript:;" coords="6,57,23,74" onmouseover="mostra_dente(this.alt,1)" onmouseout="mostra_dente(this.alt,0)" onclick="mapa_dentes('<?=$id_login?>','<?=$od_codigo?>',this.alt)" />
		<area shape="rect" alt="53" href="javascript:;" coords="15,35,32,52" onmouseover="mostra_dente(this.alt,1)" onmouseout="mostra_dente(this.alt,0)" onclick="mapa_dentes('<?=$id_login?>','<?=$od_codigo?>',this.alt)" />
		<area shape="rect" alt="52" href="javascript:;" coords="24,13,41,30" onmouseover="mostra_dente(this.alt,1)" onmouseout="mostra_dente(this.alt,0)" onclick="mapa_dentes('<?=$id_login?>','<?=$od_codigo?>',this.alt)" />
		<area shape="rect" alt="61" href="javascript:;" coords="70,2,87,19" onmouseover="mostra_dente(this.alt,1)" onmouseout="mostra_dente(this.alt,0)" onclick="mapa_dentes('<?=$id_login?>','<?=$od_codigo?>',this.alt)" />
		<area shape="rect" alt="51" href="javascript:;" coords="46,2,63,19" onmouseover="mostra_dente(this.alt,1)" onmouseout="mostra_dente(this.alt,0)" onclick="mapa_dentes('<?=$id_login?>','<?=$od_codigo?>',this.alt)" />
		</map>
		
		<img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/dentes_crianca.jpg" usemap="#map_dente_crianca" border="0" alt="Dentes" />
	</td>
</tr>
<tr>
	<td colspan="17" style="background:url(<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/dente_traco_vert.gif) repeat-y center;">
		<img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/dente_numeros.gif" alt="Números" />
	</td>
</tr>
<tr style="height:94px;">
	<?php
		for( $i=8; $i >= 1; $i-- )
		{
			// dente
			$dente_num = "3{$i}";
			
			// historico do dente
			$situacoes = array();
			
			$stmt = "SELECT dente_situacao
			FROM odonto_historico 
			WHERE od_codigo IN (SELECT od_codigo FROM odonto WHERE paci_codigo = $paci_codigo)
			AND dente_num = $dente_num";
			
			$qry = db_query($stmt);
			
			while( $row = pg_fetch_array($qry) )
				$situacoes[] = $row[0];
			
			print dente_row( $dente_num, $id_login, $od_codigo, $situacoes );
		}
	?>
	<td style="background:url(<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/dente_traco_vert.gif) repeat-y center;">&nbsp;</td>
	<?php 
		for( $i=8; $i >= 1; $i-- )
		{
			// dente
			$dente_num = "4{$i}";
			
			// historico do dente
			$situacoes = array();
			
			$stmt = "SELECT dente_situacao
			FROM odonto_historico 
			WHERE od_codigo IN (SELECT od_codigo FROM odonto WHERE paci_codigo = $paci_codigo)
			AND dente_num = $dente_num";
			
			$qry = db_query($stmt);
			
			while( $row = pg_fetch_array($qry) )
				$situacoes[] = $row[0];
			
			print dente_row( $dente_num, $id_login, $od_codigo, $situacoes );
		}
	?>
</tr>
</table>


<div id="dentes_div" style="margin:10px; width:550px;padding:4px;border:1px solid #ccc;">
	Dente: <span id="dentes" style="font-weight:bold;">&nbsp;</span>
</div>

</fieldset>

<!--<fieldset>
<legend>Anamnese</legend>

<table>
	<?php for($i=1; $i < 10; $i ++ ) { ?>
	<tr>
		<td width="80"><label for="p<?=$i;?>">Pergunta <?=$i;?></label></td>
		<td><input type="text" name="p<?=$i;?>" id="p<?=$i;?>" class="box" value="Resposta 	<?=$i;?>" /></td>
	</tr>
	<?php } ?>
</table>

</fieldset>-->

</body>
</html>
