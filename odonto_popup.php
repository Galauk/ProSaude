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


cabecario( $hotkey = false );

// codigo do atendmento
$age_codigo 		= intval($_GET['age_codigo']);
$stmt 				= "SELECT u.usu_nome, u.usu_codigo
										FROM agendamento 
										NATURAL JOIN usuario as U WHERE age_codigo = $age_codigo";

$paci_row 		= db_getRow($stmt);
$id_login 			=	intval($_GET['id_login']);


// nome do dente
$dente_nome = pega_dente( $dente_num );

// topo
echo "
<p style=\"text-align:right;margin:0;padding:0;\" onclick=\"return fechar()\">
	<a href='javascript:;'>[x] fechar </a></p>
<h3>({$dente_num}) {$dente_nome}</h3>";

?>

<form action="#" onsubmit="return form_submit(<?="'$id_login','$age_codigo','$dente_num'"?>)">

<table style="border:0;width:490px" >
<tr>
	<td width="190" height="80">
		<fieldset style="heigth:100%;">
		<legend>Face</legend>
			<map name="mapeamento_face">
			<area shape="poly" alt="5" href="javascript:;" coords="26,23,43,23,43,41,26,41"
			onmouseover="mostra_face(this,<?=$dente_num?>,1)" onmouseout="mostra_face(this,<?=$dente_num?>,0)"
			onclick="escolhe_face(this,<?=$dente_num?>)" />
			<area shape="poly" alt="4" href="javascript:;" coords="12,11,24,22,24,41,13,53"
			onmouseover="mostra_face(this,<?=$dente_num?>,1)" onmouseout="mostra_face(this,<?=$dente_num?>,0)" 
			onclick="escolhe_face(this,<?=$dente_num?>)" />
			<area shape="poly" alt="3" href="javascript:;" coords="14,54,25,43,43,43,54,54"
			onmouseover="mostra_face(this,<?=$dente_num?>,1)" onmouseout="mostra_face(this,<?=$dente_num?>,0)"
			onclick="escolhe_face(this,<?=$dente_num?>)" />
			<area shape="poly" alt="2" href="javascript:;" coords="55,11,54,52,44,41,45,23"
			onmouseover="mostra_face(this,<?=$dente_num?>,1)" onmouseout="mostra_face(this,<?=$dente_num?>,0)"
			onclick="escolhe_face(this,<?=$dente_num?>)" />
			<area shape="poly" alt="1" href="javascript:;" coords="15,11,52,11,43,21,26,21"
			onmouseover="mostra_face(this,<?=$dente_num?>,1)" onmouseout="mostra_face(this,<?=$dente_num?>,0)"
			onclick="escolhe_face(this,<?=$dente_num?>)" />
			</map>
			<img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/dentes_faces.jpg" usemap="#mapeamento_face" border="0" alt="Faces" />
			<div id="faces_div">
				Face: <span id="face" style="font-weight:bold">&nbsp;</span>
			</div>
		</fieldset>
	</td>
	<td style="width:340px;">
		<fieldset style="heigth:100%;">
		<legend>Procedimento</legend>
	
			<select name="situacao" id="situacao" size="5" class="box" style="width:100%">
				<?php 
					foreach( $SITUACOES as $val => $txt ) 
						print "\n\t\t\t<option value='$val'>$txt</pption>";
				?>
			</select>
		</fieldset>
	</td>
</tr>
<tr>
	<td colspan="2">
		<fieldset>
		<legend>Anota&ccedil;&ocirc;es</legend>
		
			<input type="hidden" name="face_escolhida" id="face_escolhida" value="N" />
			<div> Face(s) escolhida(s): <strong id="face_escolhida_t"><em>nenhuma</em></strong></div>
		
			<textarea rows="4" style="width:100%" cols="40" id="anotacoes" name="anotacoes" class="box"></textarea>
		
			<br />
			
			<label><input type="checkbox" id="finalizado" name="finalizado" value="S" />Finalizar</label>
		
		</fieldset>
		<br> 
		<input type="image" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/adicionar_on.jpg" alt="Adicionar" />
		<input type="image" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/voltar_on.gif" onclick="return limpar()" alt="Limpar ?" />

	</td>
</tr>
<tr>
	<td colspan="2">
		<fieldset>
		<legend>Hist&oacute;rico do Dente</legend>
		<table class="lista">
			<tr bgcolor="#F4F2E7">
				<th width="70">Data</th>
				<th width="70">Face(s)</th>
				<th width="100">Procedimento</th>
				<th>Anotações</th>
			</tr>
		<?php
			/*$stmt = "SELECT TO_CHAR(od_hist_data,'dd/mm/yyyy') AS dt, dente_num, dente_face, dente_situacao, 
			dente_anotacao, od_finalizado  
			FROM odonto_historico 
			WHERE age_codigo IN (SELECT age_codigo FROM odonto WHERE paci_codigo = $paci_codigo)
			AND dente_num = $dente_num
			ORDER BY od_hist_data DESC";*/
			
			$stmt = "SELECT TO_CHAR(od_hist_data,'dd/mm/yyyy') AS dt, dente_num, dente_face, dente_situacao, 
			dente_anotacao, h.od_finalizado    
			FROM odonto_historico AS h
			INNER JOIN odonto AS o ON o.od_codigo = h.od_codigo
			INNER JOIN agendamento AS a ON a.age_codigo = o.age_codigo
			WHERE a.usu_codigo IN 
				(SELECT usu_codigo FROM agendamento WHERE usu_codigo = $paci_row[usu_codigo])
			AND dente_num = $dente_num";
			
			$qry = db_query( $stmt );
			
			while( $row = pg_fetch_array($qry) )
			{
				print "
				<tr".($row['od_finalizado'] == 'S' ? ' style="color:#090; font-weight:bold;"' : '' ).">
					<td>$row[dt]</td>
					<td>".($row['dente_face'] == "N" ? '<em>nenhuma</em>' :  arruma_faces_l($row['dente_face']) )."</td>
					<td>".$SITUACOES[ $row['dente_situacao'] ]."</td>
					<td>".
						($row['od_finalizado'] == 'S' ? ' [tratamento finalizado]<br />' : '' ).
						nl2br($row['dente_anotacao'])."</td>
				</tr>";
			}
			
		?>
		</table>
		</fieldset>
	</td>
</tr>
</table>

</form>

</body>
</html>

