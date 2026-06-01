<?php

	require_once '../global.php';
	setError(1);
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
	<title>Exporta&ccedil;&atilde;o BPA</title>
	<?php $form = new classForm(); ?>
	<?php $table = new tableClass(); ?>
	<?php $common = new commonClass(); ?>
	<?php echo $common->incJquery(); ?>
	<script type="text/javascript" src="/WebSocialComum/library/js/ajax_motor.js"></script>
</head>
	<body>
	<?php 
	
		echo $common->menuTab(array("Boletim de Produção Ambulatorial"));		
		echo $common->bodyTab('1');
		
		echo $form->openForm("","GET","form");
		$sqlCnes = "SELECT DISTINCT uni.uni_codigo,uni_desc
					  FROM bpa
					  JOIN unidade AS uni
					    ON uni.uni_codigo=bpa.uni_codigo
					 ORDER BY uni_desc";
		
		echo $form->inputSelect("uni_codigo",null,"CNES",$sqlCnes);
		
		
		// listar os meses
		$sqlMes = "SELECT DISTINCT DATE_PART('YEAR',bpa_data)||'-'||DATE_PART('MONTH',bpa_data) AS key,
	               	      DATE_PART('MONTH',bpa_data)||'/'||DATE_PART('YEAR',bpa_data) AS mes_ano 
		             FROM BPA
		            ORDER BY key DESC";
		
		echo $form->inputSelect("mes_ref",null,"Mês",$sqlMes);
		echo $common->commonButton("Enviar",NULL,"selecionar.png", "onclick=\"document.form.submit();");
		echo $form->closeForm();
		echo $common->closeTab();
		
		if(isset($_GET['uni_codigo']) && isset($_GET['mes_ref']) && $_GET['uni_codigo'] && $_GET['mes_ref']){
			
			list($ano,$mes) = explode("-",$_GET['mes_ref']);
			$data1 = "$ano-$mes-01";
			$mk = mktime(0,0,0,$mes+1,0,$ano);
			$data2 = date("Y-m-d",$mk);
			$dataStr = "01/$mes/$ano ~ ".date("d/m/Y",$mk);
			
			$uni_codigo = $_GET['uni_codigo'];
			
			// Dados da unidade (link)
			$sql = "SELECT uni_desc,uni_cnes FROM unidade WHERE uni_codigo=$uni_codigo";
			$query = pg_query($sql);
			$uni = pg_fetch_object($query);
			
					
			$sql = "SELECT usr.usr_nome,
					       usu.usu_nome,
					       TO_CHAR(bpa.bpa_data,'DD/MM/YYYY') as bpa_data,
					       proc.proc_nome,
					       ci.ci_descricao,
					       bpa.bpa_autorizacao,
					       bpa.bpa_tipo,
					       cd10.cd10_codigo_cid,
					       MIN(esp.esp_nome) as esp_nome
					  FROM bpa
					  JOIN procedimento AS proc
					    ON proc.proc_codigo=bpa.proc_codigo
					  JOIN unidade AS uni
					    ON uni.uni_codigo=bpa.uni_codigo
					  JOIN usuarios AS usr
					    ON usr.usr_codigo=bpa.usr_codigo
					  JOIN medico_especialidade AS mes
					    ON mes.med_codigo=usr.usr_codigo					    
					  JOIN especialidade AS esp
					    ON esp.esp_codigo=mes.esp_codigo
					  JOIN rl_procedimento_ocupacao AS rlpo 
					    ON rlpo.co_procedimento=proc.proc_codigo_sus
					   AND esp.cod_cbo=rlpo.co_ocupacao
					  JOIN usuario AS usu
					    ON usu.usu_codigo=bpa.usu_codigo
					  JOIN ci
					    ON ci.ci_codigo=bpa.ci_codigo
				 LEFT JOIN cid10 AS cd10
				        ON cd10.cd10_codigo=bpa.bpa_cd10_codigo
		             WHERE bpa.uni_codigo='$uni_codigo'
		           	   AND bpa.bpa_data BETWEEN '$data1' AND '$data2'
		           	 GROUP BY usr.usr_nome,
					       usu.usu_nome,
					       TO_CHAR(bpa.bpa_data,'DD/MM/YYYY'),
					       proc.proc_nome,
					       ci.ci_descricao,
					       bpa.bpa_autorizacao,
					       bpa.bpa_tipo,
					       cd10.cd10_codigo_cid
		           	 ORDER BY bpa_data,bpa_tipo";

			$query = pg_query($sql);
			
			if(!pg_num_rows($query)):
				echo "<em>Nenhum procedimento encontrado na unidade e data informada.</em>";
			else: ?>
			<h2><?=$uni->uni_desc." - $dataStr";?></h2>
			<table class="grid ui-widget ui-widget-content ui-corner-all" width="100%">
				<tr class="ui-widget-header">
					<th>Paciente</th>
					<th>Data</th>
					<th>Procedimento</th>
					<th>CID</th>
					<th>Tipo</th>
				</tr>
				<?php while($r = pg_fetch_object($query)): ?>
				<tr>
					<td class="ui-widget ui-widget-content"><?=$r->usu_nome;?></td>
					<td class="ui-widget ui-widget-content"><?=$r->bpa_data;?>
					<td class="ui-widget ui-widget-content"><?=$r->proc_nome;?></td>
					<td class="ui-widget ui-widget-content"><?=$r->cd10_codigo_cid;?></td>
					<td class="ui-widget ui-widget-content"><?=$r->bpa_tipo=="C"?"Consolidado":"Individualizado";?></td>
				</tr>
				<?php endwhile; ?>
			</table>
			<?php echo $common->commonButton("Download",NULL,"adicionar_on.png", "onclick=\"window.location.href='exportaBPA.php?mes=$mes&ano=$ano&cnes=".$uni->uni_cnes."';");?>
				
			<?php endif;
		}
	?>
	</body>
</html>